<?php

declare(strict_types=1);

namespace App\Modules\Payments;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\Config;

/**
 * Square webhook handler.
 * Receives and processes webhook events from Square.
 */
use App\Core\Database\Connection;

final class WebhookController
{
    private PaymentRepository $repo;

    public function __construct(Connection $db)
    {
        $this->repo = new PaymentRepository($db);
    }

    /**
     * POST /api/webhooks/square
     */
    public function handle(Request $request): Response
    {
        $payload = file_get_contents('php://input');
        $signature = $request->header('X-Square-Hmacsha256-Signature') ?? '';

        // Verify signature
        $square = new SquarePaymentService();
        $webhookUrl = Config::get('payments.square.webhook_url', '');

        if (!$square->verifyWebhookSignature($payload, $signature, $webhookUrl)) {
            return Response::error('Invalid signature', 401);
        }

        $event = json_decode($payload, true);
        if (!$event || !isset($event['type'])) {
            return Response::error('Invalid payload', 400);
        }

        $this->processEvent($event);

        return Response::json(['received' => true]);
    }

    private function processEvent(array $event): void
    {
        $type = $event['type'] ?? '';
        $data = $event['data']['object'] ?? [];

        match ($type) {
            'payment.completed' => $this->handlePaymentCompleted($data),
            'payment.failed' => $this->handlePaymentFailed($data),
            'refund.completed' => $this->handleRefundCompleted($data),
            'refund.failed' => $this->handleRefundFailed($data),
            default => null, // Ignore unknown events
        };

        // Log the webhook event
        $this->logWebhookEvent($event);
    }

    private function handlePaymentCompleted(array $data): void
    {
        $payment = $data['payment'] ?? [];
        $gatewayId = $payment['id'] ?? '';

        if ($gatewayId) {
            $existing = $this->repo->findByGatewayId($gatewayId);
            if ($existing) {
                $this->repo->update((int) $existing['id'], [
                    'status' => 'completed',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function handlePaymentFailed(array $data): void
    {
        $payment = $data['payment'] ?? [];
        $gatewayId = $payment['id'] ?? '';

        if ($gatewayId) {
            $existing = $this->repo->findByGatewayId($gatewayId);
            if ($existing) {
                $this->repo->update((int) $existing['id'], [
                    'status' => 'failed',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function handleRefundCompleted(array $data): void
    {
        $refund = $data['refund'] ?? [];
        $paymentId = $refund['payment_id'] ?? '';

        if ($paymentId) {
            $existing = $this->repo->findByGatewayId($paymentId);
            if ($existing) {
                $this->repo->update((int) $existing['id'], [
                    'status' => 'refunded',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function handleRefundFailed(array $data): void
    {
        // Log but don't change status — manual review needed
    }

    private function logWebhookEvent(array $event): void
    {
        $logDir = dirname(__DIR__, 3) . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/webhooks.log';
        $entry = date('Y-m-d H:i:s') . ' | ' . ($event['type'] ?? 'unknown') . ' | ' . json_encode($event) . "\n";
        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }
}
