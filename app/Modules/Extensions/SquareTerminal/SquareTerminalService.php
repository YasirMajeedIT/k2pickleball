<?php

declare(strict_types=1);

namespace App\Modules\Extensions\SquareTerminal;

use App\Core\Exceptions\PaymentException;
use App\Core\Services\Config;

/**
 * Square Terminal POS service.
 * Wraps the Square SDK Terminal API for device management and checkout processing.
 * Self-contained — does not depend on or affect SquarePaymentService.
 */
final class SquareTerminalService
{
    private \Square\SquareClient $client;
    private string $locationId;
    private bool $isSandbox;

    public function __construct(?string $accessToken = null, ?string $locationId = null)
    {
        $environment = Config::get('payments.square.environment', 'sandbox');
        $this->isSandbox = $environment === 'sandbox';

        $this->client = new \Square\SquareClient([
            'accessToken' => $accessToken ?: Config::get('payments.square.access_token'),
            'environment' => $environment,
        ]);
        $this->locationId = $locationId ?: Config::get('payments.square.location_id', '');
    }

    /**
     * List paired terminal devices for the current location.
     */
    public function listDevices(): array
    {
        if ($this->isSandbox) {
            return [
                [
                    'id' => 'sandbox_device_001',
                    'name' => 'Sandbox Terminal',
                    'status' => 'PAIRED',
                    'location_id' => $this->locationId,
                ],
            ];
        }

        $response = $this->client->getDevicesApi()->listDevices();

        if (!$response->isSuccess()) {
            $errors = $response->getErrors();
            throw new PaymentException($errors ? $errors[0]->getDetail() : 'Failed to list devices');
        }

        $devices = [];
        $deviceList = $response->getResult()->getDevices() ?? [];
        foreach ($deviceList as $device) {
            $devices[] = [
                'id' => $device->getId(),
                'name' => $device->getName(),
                'status' => $device->getStatus() ?? 'unknown',
                'location_id' => method_exists($device, 'getLocationId') ? $device->getLocationId() : null,
            ];
        }

        return $devices;
    }

    /**
     * Create a device code for pairing a new terminal.
     */
    public function createDeviceCode(string $name): array
    {
        if ($this->isSandbox) {
            return [
                'id' => 'sandbox_code_' . bin2hex(random_bytes(4)),
                'code' => 'SANDBOX-' . strtoupper(bin2hex(random_bytes(3))),
                'name' => $name,
                'status' => 'UNPAIRED',
                'pair_by' => date('c', strtotime('+5 minutes')),
                'sandbox' => true,
            ];
        }

        $body = new \Square\Models\CreateDeviceCodeRequest(
            bin2hex(random_bytes(16))
        );
        $deviceCode = new \Square\Models\DeviceCode('TERMINAL_API');
        $deviceCode->setName($name);
        if ($this->locationId) {
            $deviceCode->setLocationId($this->locationId);
        }
        $body->setDeviceCode($deviceCode);

        $response = $this->client->getDevicesApi()->createDeviceCode($body);

        if (!$response->isSuccess()) {
            $errors = $response->getErrors();
            throw new PaymentException($errors ? $errors[0]->getDetail() : 'Failed to create device code');
        }

        $code = $response->getResult()->getDeviceCode();
        return [
            'id' => $code->getId(),
            'code' => $code->getCode(),
            'name' => $code->getName(),
            'status' => $code->getStatus(),
            'pair_by' => $code->getPairBy(),
            'sandbox' => false,
        ];
    }

    /**
     * Create a terminal checkout (sends payment request to device).
     */
    public function createCheckout(string $deviceId, int $amountCents, string $currency, array $options = []): array
    {
        if ($this->isSandbox) {
            $mockId = 'sandbox_checkout_' . bin2hex(random_bytes(8));
            return [
                'checkout_id' => $mockId,
                'status' => 'COMPLETED',
                'payment_id' => 'sandbox_pay_' . bin2hex(random_bytes(8)),
                'sandbox' => true,
            ];
        }

        $money = new \Square\Models\Money();
        $money->setAmount($amountCents);
        $money->setCurrency($currency);

        $checkoutOptions = new \Square\Models\DeviceCheckoutOptions($deviceId);

        $checkout = new \Square\Models\TerminalCheckout($money, $checkoutOptions);

        if (!empty($options['note'])) {
            $checkout->setNote(substr($options['note'], 0, 250));
        }
        if (!empty($options['reference_id'])) {
            $checkout->setReferenceId(substr($options['reference_id'], 0, 40));
        }

        $body = new \Square\Models\CreateTerminalCheckoutRequest(
            bin2hex(random_bytes(16)),
            $checkout
        );

        $response = $this->client->getTerminalApi()->createTerminalCheckout($body);

        if (!$response->isSuccess()) {
            $errors = $response->getErrors();
            throw new PaymentException($errors ? $errors[0]->getDetail() : 'Failed to create terminal checkout');
        }

        $result = $response->getResult()->getCheckout();
        return [
            'checkout_id' => $result->getId(),
            'status' => $result->getStatus(),
            'payment_id' => $result->getPaymentIds() ? $result->getPaymentIds()[0] : null,
            'sandbox' => false,
        ];
    }

    /**
     * Poll checkout status.
     */
    public function getCheckoutStatus(string $checkoutId): array
    {
        if ($this->isSandbox && str_starts_with($checkoutId, 'sandbox_checkout_')) {
            return [
                'checkout_id' => $checkoutId,
                'status' => 'COMPLETED',
                'payment_id' => 'sandbox_pay_' . bin2hex(random_bytes(8)),
                'sandbox' => true,
            ];
        }

        $response = $this->client->getTerminalApi()->getTerminalCheckout($checkoutId);

        if (!$response->isSuccess()) {
            $errors = $response->getErrors();
            throw new PaymentException($errors ? $errors[0]->getDetail() : 'Failed to get checkout status');
        }

        $result = $response->getResult()->getCheckout();
        return [
            'checkout_id' => $result->getId(),
            'status' => $result->getStatus(),
            'payment_id' => $result->getPaymentIds() ? $result->getPaymentIds()[0] : null,
            'sandbox' => false,
        ];
    }

    /**
     * Cancel a pending terminal checkout.
     */
    public function cancelCheckout(string $checkoutId): array
    {
        if ($this->isSandbox && str_starts_with($checkoutId, 'sandbox_checkout_')) {
            return ['checkout_id' => $checkoutId, 'status' => 'CANCELED', 'sandbox' => true];
        }

        $response = $this->client->getTerminalApi()->cancelTerminalCheckout($checkoutId);

        if (!$response->isSuccess()) {
            $errors = $response->getErrors();
            throw new PaymentException($errors ? $errors[0]->getDetail() : 'Failed to cancel checkout');
        }

        $result = $response->getResult()->getCheckout();
        return [
            'checkout_id' => $result->getId(),
            'status' => $result->getStatus(),
            'sandbox' => false,
        ];
    }
}
