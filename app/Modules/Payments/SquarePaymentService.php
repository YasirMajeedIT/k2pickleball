<?php

declare(strict_types=1);

namespace App\Modules\Payments;

use App\Core\Exceptions\PaymentException;
use App\Core\Services\Config;

/**
 * Square payment gateway service.
 * Wraps the Square SDK for payment processing.
 */
final class SquarePaymentService
{
    private \Square\SquareClient $client;
    private string $locationId;

    public function __construct()
    {
        $this->client = new \Square\SquareClient([
            'accessToken' => Config::get('payments.square.access_token'),
            'environment' => Config::get('payments.square.environment', 'sandbox'),
        ]);
        $this->locationId = Config::get('payments.square.location_id', '');
    }

    /**
     * Create a payment using a card nonce or customer card on file.
     */
    public function createPayment(int $amountCents, string $currency, string $sourceId, array $options = []): array
    {
        $body = new \Square\Models\CreatePaymentRequest(
            $sourceId,
            $this->idempotencyKey()
        );

        $money = new \Square\Models\Money();
        $money->setAmount($amountCents);
        $money->setCurrency($currency);
        $body->setAmountMoney($money);

        if (!empty($options['customer_id'])) {
            $body->setCustomerId($options['customer_id']);
        }
        if (!empty($options['reference_id'])) {
            $body->setReferenceId(substr($options['reference_id'], 0, 40));
        }
        if (!empty($options['note'])) {
            $body->setNote(substr($options['note'], 0, 500));
        }
        if ($this->locationId) {
            $body->setLocationId($this->locationId);
        }

        $response = $this->client->getPaymentsApi()->createPayment($body);

        if (!$response->isSuccess()) {
            $errors = $response->getErrors();
            $msg = $errors ? $errors[0]->getDetail() : 'Payment failed';
            throw new PaymentException($msg);
        }

        $payment = $response->getResult()->getPayment();

        return [
            'id' => $payment->getId(),
            'status' => $payment->getStatus(),
            'amount' => $payment->getAmountMoney()->getAmount(),
            'currency' => $payment->getAmountMoney()->getCurrency(),
            'receipt_url' => $payment->getReceiptUrl(),
            'created_at' => $payment->getCreatedAt(),
        ];
    }

    /**
     * Refund a payment.
     */
    public function refundPayment(string $paymentId, int $amountCents, string $currency, ?string $reason = null): array
    {
        $money = new \Square\Models\Money();
        $money->setAmount($amountCents);
        $money->setCurrency($currency);

        $body = new \Square\Models\RefundPaymentRequest(
            $this->idempotencyKey(),
            $money
        );
        $body->setPaymentId($paymentId);
        if ($reason) {
            $body->setReason($reason);
        }

        $response = $this->client->getRefundsApi()->refundPayment($body);

        if (!$response->isSuccess()) {
            $errors = $response->getErrors();
            $msg = $errors ? $errors[0]->getDetail() : 'Refund failed';
            throw new PaymentException($msg);
        }

        $refund = $response->getResult()->getRefund();

        return [
            'id' => $refund->getId(),
            'status' => $refund->getStatus(),
            'amount' => $refund->getAmountMoney()->getAmount(),
            'payment_id' => $refund->getPaymentId(),
        ];
    }

    /**
     * Create a Square customer.
     */
    public function createCustomer(string $email, string $firstName, string $lastName, ?string $phone = null): array
    {
        $body = new \Square\Models\CreateCustomerRequest();
        $body->setEmailAddress($email);
        $body->setGivenName($firstName);
        $body->setFamilyName($lastName);
        if ($phone) {
            $body->setPhoneNumber($phone);
        }
        $body->setIdempotencyKey($this->idempotencyKey());

        $response = $this->client->getCustomersApi()->createCustomer($body);

        if (!$response->isSuccess()) {
            $errors = $response->getErrors();
            $msg = $errors ? $errors[0]->getDetail() : 'Customer creation failed';
            throw new PaymentException($msg);
        }

        $customer = $response->getResult()->getCustomer();

        return [
            'id' => $customer->getId(),
            'email' => $customer->getEmailAddress(),
            'first_name' => $customer->getGivenName(),
            'last_name' => $customer->getFamilyName(),
        ];
    }

    /**
     * Save a card on file for a customer.
     */
    public function createCard(string $customerId, string $sourceId, string $cardholderName): array
    {
        $card = new \Square\Models\Card();
        $card->setCustomerId($customerId);
        $card->setCardholderName($cardholderName);

        $body = new \Square\Models\CreateCardRequest(
            $this->idempotencyKey(),
            $sourceId,
            $card
        );

        $response = $this->client->getCardsApi()->createCard($body);

        if (!$response->isSuccess()) {
            $errors = $response->getErrors();
            $msg = $errors ? $errors[0]->getDetail() : 'Card save failed';
            throw new PaymentException($msg);
        }

        $result = $response->getResult()->getCard();

        return [
            'id' => $result->getId(),
            'last_4' => $result->getLast4(),
            'brand' => $result->getCardBrand(),
            'exp_month' => $result->getExpMonth(),
            'exp_year' => $result->getExpYear(),
        ];
    }

    /**
     * Verify a Square webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $url): bool
    {
        $sigKey = Config::get('payments.square.webhook_signature_key', '');
        if (empty($sigKey)) {
            return false;
        }

        return \Square\Utils\WebhooksHelper::isValidWebhookEventSignature(
            $payload,
            $signature,
            $sigKey,
            $url
        );
    }

    private function idempotencyKey(): string
    {
        return bin2hex(random_bytes(16));
    }
}
