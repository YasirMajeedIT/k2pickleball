<?php

declare(strict_types=1);

namespace App\Modules\Payments;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class PaymentController extends Controller
{
    private PaymentRepository $repo;
    private SquarePaymentService $square;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new PaymentRepository($db);
        $this->square = new SquarePaymentService();
    }

    /**
     * GET /api/payments
     */
    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);

        $result = $this->repo->findByOrganization($orgId, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    /**
     * GET /api/payments/{id}
     */
    public function show(Request $request, int $id): Response
    {
        $payment = $this->repo->findById($id);
        if (!$payment) {
            throw new NotFoundException('Payment not found');
        }
        return $this->success($payment);
    }

    /**
     * POST /api/payments — process a payment via Square
     */
    public function charge(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'source_id' => 'required|string',
            'amount' => 'required|integer|min:1',
            'currency' => 'nullable|string|max:3',
            'description' => 'nullable|string|max:500',
            'customer_id' => 'nullable|string',
        ]);

        $orgId = $request->organizationId();
        $currency = strtoupper($data['currency'] ?? 'USD');
        $referenceId = 'PAY-' . strtoupper(bin2hex(random_bytes(6)));

        // Process via Square
        $result = $this->square->createPayment(
            (int) $data['amount'],
            $currency,
            $data['source_id'],
            [
                'customer_id' => $data['customer_id'] ?? null,
                'reference_id' => $referenceId,
                'note' => $data['description'] ?? null,
            ]
        );

        // Store payment record
        $paymentId = $this->repo->create([
            'uuid' => $this->generateUuid(),
            'organization_id' => $orgId,
            'user_id' => $request->userId(),
            'amount' => $data['amount'],
            'currency' => $currency,
            'status' => strtolower($result['status']),
            'description' => $data['description'] ?? null,
            'square_payment_id' => $result['id'],
            'square_receipt_url' => $result['receipt_url'] ?? null,
            'idempotency_key' => bin2hex(random_bytes(16)),
            'metadata' => json_encode($result),
            'processed_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Record transaction
        $this->repo->createTransaction([
            'uuid' => $this->generateUuid(),
            'organization_id' => $orgId,
            'payment_id' => $paymentId,
            'type' => 'charge',
            'amount' => $data['amount'],
            'currency' => $currency,
            'description' => $data['description'] ?? null,
            'square_transaction_id' => $result['id'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $payment = $this->repo->findById($paymentId);
        return $this->created($payment, 'Payment processed');
    }

    /**
     * POST /api/payments/{id}/refund
     */
    public function refund(Request $request, int $id): Response
    {
        $payment = $this->repo->findById($id);
        if (!$payment) {
            throw new NotFoundException('Payment not found');
        }

        $data = Validator::validate($request->all(), [
            'amount' => 'nullable|integer|min:1',
            'reason' => 'nullable|string|max:500',
        ]);

        $refundAmount = (int) ($data['amount'] ?? $payment['amount']);

        $result = $this->square->refundPayment(
            $payment['square_payment_id'],
            $refundAmount,
            $payment['currency'],
            $data['reason'] ?? null
        );

        // Update payment status
        $newStatus = $refundAmount >= (int) $payment['amount'] ? 'refunded' : 'partially_refunded';
        $this->repo->update($id, ['status' => $newStatus]);

        // Record refund transaction
        $this->repo->createTransaction([
            'uuid' => $this->generateUuid(),
            'organization_id' => $payment['organization_id'],
            'payment_id' => $id,
            'type' => 'refund',
            'amount' => $refundAmount,
            'currency' => $payment['currency'],
            'description' => $data['reason'] ?? 'Refund',
            'square_transaction_id' => $result['id'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->success($result, 'Refund processed');
    }

    // -- Payment Methods --

    public function paymentMethods(Request $request): Response
    {
        $methods = $this->repo->findPaymentMethods($request->organizationId());
        return $this->success($methods);
    }

    public function storePaymentMethod(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'type' => 'required|in:card,bank_account',
            'square_card_id' => 'nullable|string',
            'brand' => 'nullable|string|max:50',
            'last_four' => 'nullable|string|max:4',
            'exp_month' => 'nullable|integer',
            'exp_year' => 'nullable|integer',
            'cardholder_name' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        $orgId = $request->organizationId();

        $id = $this->repo->createPaymentMethod([
            'organization_id' => $orgId,
            'user_id' => $request->userId(),
            'type' => $data['type'],
            'square_card_id' => $data['square_card_id'],
            'brand' => $data['brand'] ?? null,
            'last_four' => $data['last_four'] ?? null,
            'exp_month' => $data['exp_month'] ?? null,
            'exp_year' => $data['exp_year'] ?? null,
            'cardholder_name' => $data['cardholder_name'] ?? null,
            'is_default' => !empty($data['is_default']) ? 1 : 0,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (!empty($data['is_default'])) {
            $this->repo->setDefaultPaymentMethod($id, $orgId);
        }

        $method = $this->repo->findPaymentMethod($id);
        return $this->created($method, 'Payment method added');
    }

    public function deletePaymentMethod(Request $request, int $id): Response
    {
        $method = $this->repo->findPaymentMethod($id);
        if (!$method) {
            throw new NotFoundException('Payment method not found');
        }

        $this->repo->deletePaymentMethod($id);
        return $this->success(null, 'Payment method removed');
    }

    // -- Transactions --

    public function transactions(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);

        $result = $this->repo->findTransactions($orgId, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
