<?php

declare(strict_types=1);

namespace App\Modules\Extensions\SquareTerminal;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;
use App\Core\Extensions\ExtensionRegistry;
use App\Core\Security\Sanitizer;

/**
 * API controller for Square Terminal POS extension.
 * All endpoints verify extension is active before processing.
 */
final class SquareTerminalController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * GET /api/square-terminal/status
     * Check if terminal extension is active + get facility device info.
     */
    public function status(Request $request): Response
    {
        $orgId = $request->organizationId();
        $facilityId = (int) $request->input('facility_id', 0);

        $registry = ExtensionRegistry::getInstance($this->db);
        $active = $registry->isActiveForOrg('square-terminal-pos', $orgId);

        if (!$active) {
            return $this->success(['active' => false, 'device' => null]);
        }

        $deviceSettings = null;
        if ($facilityId) {
            $deviceSettings = $registry->getFacilitySettings('square-terminal-pos', $orgId, $facilityId);
        }

        return $this->success([
            'active' => true,
            'device' => $deviceSettings,
        ]);
    }

    /**
     * GET /api/square-terminal/devices
     * List paired terminal devices.
     */
    public function listDevices(Request $request): Response
    {
        $this->ensureActive($request);

        try {
            $service = new SquareTerminalService();
            $devices = $service->listDevices();
            return $this->success($devices);
        } catch (\Exception $e) {
            return $this->error('Failed to list devices: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/square-terminal/devices/pair
     * Create device pairing code.
     */
    public function pairDevice(Request $request): Response
    {
        $this->ensureActive($request);

        $name = Sanitizer::string($request->input('name', 'Terminal'));

        try {
            $service = new SquareTerminalService();
            $result = $service->createDeviceCode($name);
            return $this->created($result, 'Device pairing code created');
        } catch (\Exception $e) {
            return $this->error('Failed to create pairing code: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/square-terminal/checkout
     * Create terminal checkout (sends payment to device).
     */
    public function createCheckout(Request $request): Response
    {
        $this->ensureActive($request);

        $orgId = $request->organizationId();
        $facilityId = (int) $request->input('facility_id', 0);

        // Resolve device ID from request or facility settings
        $registry = ExtensionRegistry::getInstance($this->db);
        $deviceId = $request->input('device_id');
        if (!$deviceId && $facilityId) {
            $settings = $registry->getFacilitySettings('square-terminal-pos', $orgId, $facilityId);
            $deviceId = $settings['device_id'] ?? null;
        }

        if (!$deviceId) {
            return $this->error('No terminal device configured for this facility', 422);
        }

        $amountCents = (int) $request->input('amount_cents', 0);
        $currency = $request->input('currency', 'USD');

        if ($amountCents <= 0) {
            return $this->error('Amount must be greater than zero', 422);
        }

        try {
            $service = new SquareTerminalService();
            $result = $service->createCheckout($deviceId, $amountCents, $currency, [
                'note' => Sanitizer::string($request->input('note', '')),
                'reference_id' => $request->input('reference_id', ''),
            ]);

            return $this->success($result, 'Terminal checkout created');
        } catch (\Exception $e) {
            return $this->error('Terminal checkout failed: ' . $e->getMessage(), 422);
        }
    }

    /**
     * GET /api/square-terminal/checkout/{checkoutId}
     * Poll checkout status.
     */
    public function checkoutStatus(Request $request, string $checkoutId): Response
    {
        $this->ensureActive($request);

        try {
            $service = new SquareTerminalService();
            $result = $service->getCheckoutStatus($checkoutId);
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error('Failed to get checkout status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/square-terminal/checkout/{checkoutId}/cancel
     * Cancel a pending checkout.
     */
    public function cancelCheckout(Request $request, string $checkoutId): Response
    {
        $this->ensureActive($request);

        try {
            $service = new SquareTerminalService();
            $result = $service->cancelCheckout($checkoutId);
            return $this->success($result, 'Checkout cancelled');
        } catch (\Exception $e) {
            return $this->error('Failed to cancel checkout: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Ensure the Square Terminal extension is active for the requesting org.
     */
    private function ensureActive(Request $request): void
    {
        $orgId = $request->organizationId();
        $registry = ExtensionRegistry::getInstance($this->db);
        if (!$registry->isActiveForOrg('square-terminal-pos', $orgId)) {
            throw new NotFoundException('Square Terminal extension is not active for this organization');
        }
    }
}
