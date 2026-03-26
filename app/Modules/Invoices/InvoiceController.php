<?php

declare(strict_types=1);

namespace App\Modules\Invoices;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;
use App\Core\Services\Mailer;
use App\Modules\Payments\SquarePaymentService;
use App\Core\Exceptions\PaymentException;

final class InvoiceController extends Controller
{
    private InvoiceRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new InvoiceRepository($db);
    }

    // ───────────────────────────────────────────────────────
    // GET /api/booking-invoices
    // ───────────────────────────────────────────────────────
    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);

        $filters = [
            'status'      => $request->get('status'),
            'facility_id' => $request->get('facility_id'),
            'search'      => $request->get('search'),
            'from_date'   => $request->get('from_date'),
            'to_date'     => $request->get('to_date'),
        ];

        $result = $this->repo->findByOrganization($orgId, $filters, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    // ───────────────────────────────────────────────────────
    // POST /api/booking-invoices
    // ───────────────────────────────────────────────────────
    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'customer_type'        => 'nullable|string',
            'customer_first_name'  => 'nullable|string|max:100',
            'customer_last_name'   => 'nullable|string|max:100',
            'customer_company'     => 'nullable|string|max:200',
            'customer_email'       => 'nullable|email',
            'customer_phone'       => 'nullable|string|max:50',
            'facility_id'          => 'nullable|integer',
            'player_id'            => 'nullable|integer',
            'issue_date'           => 'nullable|string',
            'due_date'             => 'nullable|string',
            'tax_rate'             => 'nullable|numeric',
            'discount_amount'      => 'nullable|numeric',
            'payment_type'         => 'nullable|string',
            'notes'                => 'nullable|string',
            'internal_notes'       => 'nullable|string',
            'items'                => 'required|array',
            'send_immediately'     => 'nullable|boolean',
            'auto_reminders_enabled' => 'nullable|boolean',
        ]);

        $orgId = $request->organizationId();

        [$subtotal, $taxAmount, $total] = $this->calcTotals(
            $data['items'] ?? [],
            (float) ($data['tax_rate'] ?? 0),
            (float) ($data['discount_amount'] ?? 0)
        );

        $status = ($data['send_immediately'] ?? false) ? 'sent' : 'draft';

        $invoiceId = $this->repo->create([
            'uuid'                   => $this->repo->generateUuid(),
            'organization_id'        => $orgId,
            'facility_id'            => $data['facility_id']   ?? null,
            'player_id'              => $data['player_id']      ?? null,
            'created_by'             => $request->userId(),
            'invoice_number'         => $this->repo->generateInvoiceNumber($orgId),
            'customer_type'          => $data['customer_type']  ?? 'individual',
            'customer_first_name'    => $data['customer_first_name'] ?? null,
            'customer_last_name'     => $data['customer_last_name']  ?? null,
            'customer_company'       => $data['customer_company']    ?? null,
            'customer_email'         => $data['customer_email']      ?? null,
            'customer_phone'         => $data['customer_phone']      ?? null,
            'subtotal'               => $subtotal,
            'tax_rate'               => $data['tax_rate']       ?? 0,
            'tax_amount'             => $taxAmount,
            'discount_amount'        => $data['discount_amount']?? 0,
            'total'                  => $total,
            'currency'               => 'USD',
            'status'                 => $status,
            'payment_type'           => $data['payment_type']   ?? 'full',
            'issue_date'             => $data['issue_date']      ?? date('Y-m-d'),
            'due_date'               => $data['due_date']        ?? null,
            'notes'                  => $data['notes']           ?? null,
            'internal_notes'         => $data['internal_notes']  ?? null,
            'auto_reminders_enabled' => $data['auto_reminders_enabled'] ?? 1,
        ]);

        $this->repo->syncItems($invoiceId, $data['items'] ?? []);

        $invoice = $this->repo->findById($invoiceId);

        // Send email if requested
        if (($data['send_immediately'] ?? false) && !empty($data['customer_email'])) {
            $this->sendInvoiceEmail($invoice);
        }

        return $this->created($invoice, 'Invoice created successfully');
    }

    // ───────────────────────────────────────────────────────
    // GET /api/booking-invoices/session-types
    // ───────────────────────────────────────────────────────
    public function sessionTypes(Request $request): Response
    {
        $orgId      = $request->organizationId();
        $facilityId = $request->get('facility_id') ? (int) $request->get('facility_id') : null;
        $data       = $this->repo->findSessionTypesForPicker($orgId, $facilityId);
        return $this->success($data);
    }

    // ───────────────────────────────────────────────────────
    // GET /api/booking-invoices/{id}/classes
    // ───────────────────────────────────────────────────────
    public function classesForSessionType(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();
        $data  = $this->repo->findClassesForSessionType($id, $orgId);
        return $this->success($data);
    }

    // ───────────────────────────────────────────────────────
    // GET /api/booking-invoices/{id}
    // ───────────────────────────────────────────────────────
    public function show(Request $request, int $id): Response
    {
        $invoice = $this->repo->findById($id);
        if (!$invoice) {
            throw new NotFoundException('Invoice not found');
        }
        return $this->success($invoice);
    }

    // ───────────────────────────────────────────────────────
    // PUT /api/booking-invoices/{id}
    // ───────────────────────────────────────────────────────
    public function update(Request $request, int $id): Response
    {
        $invoice = $this->repo->findById($id);
        if (!$invoice) {
            throw new NotFoundException('Invoice not found');
        }
        if (in_array($invoice['status'], ['paid', 'refunded'])) {
            return $this->error('Cannot edit a paid or refunded invoice', 422);
        }

        $data = Validator::validate($request->all(), [
            'customer_type'        => 'nullable|string',
            'customer_first_name'  => 'nullable|string|max:100',
            'customer_last_name'   => 'nullable|string|max:100',
            'customer_company'     => 'nullable|string|max:200',
            'customer_email'       => 'nullable|email',
            'customer_phone'       => 'nullable|string|max:50',
            'facility_id'          => 'nullable|integer',
            'player_id'            => 'nullable|integer',
            'issue_date'           => 'nullable|string',
            'due_date'             => 'nullable|string',
            'tax_rate'             => 'nullable|numeric',
            'discount_amount'      => 'nullable|numeric',
            'payment_type'         => 'nullable|string',
            'notes'                => 'nullable|string',
            'internal_notes'       => 'nullable|string',
            'items'                => 'nullable|array',
            'auto_reminders_enabled' => 'nullable|boolean',
        ]);

        if (!empty($data['items'])) {
            [$subtotal, $taxAmount, $total] = $this->calcTotals(
                $data['items'],
                (float) ($data['tax_rate'] ?? $invoice['tax_rate']),
                (float) ($data['discount_amount'] ?? $invoice['discount_amount'])
            );
            $data['subtotal']       = $subtotal;
            $data['tax_amount']     = $taxAmount;
            $data['total']          = $total;
            $data['amount_due']     = max(0, $total - (float) $invoice['amount_paid']);
            $this->repo->syncItems($id, $data['items']);
        }

        $this->repo->update($id, $data);

        $updated = $this->repo->findById($id);
        return $this->success($updated, 'Invoice updated');
    }

    // ───────────────────────────────────────────────────────
    // DELETE /api/booking-invoices/{id}
    // ───────────────────────────────────────────────────────
    public function destroy(Request $request, int $id): Response
    {
        $invoice = $this->repo->findById($id);
        if (!$invoice) {
            throw new NotFoundException('Invoice not found');
        }
        if (in_array($invoice['status'], ['paid', 'partially_paid'])) {
            return $this->error('Cannot delete an invoice with recorded payments', 422);
        }
        $this->repo->delete($id);
        return $this->noContent();
    }

    // ───────────────────────────────────────────────────────
    // POST /api/booking-invoices/{id}/send
    // ───────────────────────────────────────────────────────
    public function sendInvoice(Request $request, int $id): Response
    {
        $invoice = $this->repo->findById($id);
        if (!$invoice) {
            throw new NotFoundException('Invoice not found');
        }
        if (empty($invoice['customer_email'])) {
            return $this->error('Invoice has no customer email address', 422);
        }

        $this->sendInvoiceEmail($invoice);
        $this->repo->markSent($id);

        return $this->success(null, 'Invoice sent successfully');
    }

    // ───────────────────────────────────────────────────────
    // GET /api/booking-invoices/{id}/payments
    // ───────────────────────────────────────────────────────
    public function getPayments(Request $request, int $id): Response
    {
        $invoice = $this->repo->findById($id);
        if (!$invoice) {
            throw new NotFoundException('Invoice not found');
        }
        return $this->success($invoice['payments']);
    }

    // ───────────────────────────────────────────────────────
    // POST /api/booking-invoices/{id}/pay
    // Process a payment (Square card OR manual)
    // ───────────────────────────────────────────────────────
    public function processPayment(Request $request, int $id): Response
    {
        $invoice = $this->repo->findById($id);
        if (!$invoice) {
            throw new NotFoundException('Invoice not found');
        }
        if ($invoice['status'] === 'paid') {
            return $this->error('Invoice is already fully paid', 422);
        }
        if ($invoice['status'] === 'cancelled') {
            return $this->error('Cannot process payment for a cancelled invoice', 422);
        }

        $data = Validator::validate($request->all(), [
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'source_id'      => 'nullable|string',   // Square nonce — required when method=card
            'notes'          => 'nullable|string|max:500',
        ]);

        $amount = (float) $data['amount'];
        $due    = (float) $invoice['amount_due'];

        if ($amount > $due + 0.001) {
            return $this->error("Payment amount ($amount) exceeds amount due ($due)", 422);
        }

        $orgId    = $request->organizationId();
        $method   = $data['payment_method'];
        $squareId = null;
        $receiptUrl = null;

        // ── Square card payment ──
        if ($method === 'card') {
            if (empty($data['source_id'])) {
                return $this->error('source_id is required for card payments', 422);
            }

            try {
                $square  = new SquarePaymentService();
                $result  = $square->createPayment(
                    (int) round($amount * 100),
                    $invoice['currency'] ?? 'USD',
                    $data['source_id'],
                    [
                        'reference_id' => $invoice['invoice_number'],
                        'note'         => 'Invoice ' . $invoice['invoice_number'],
                    ]
                );
                $squareId   = $result['id'];
                $receiptUrl = $result['receipt_url'] ?? null;
            } catch (PaymentException $e) {
                return $this->error('Payment failed: ' . $e->getMessage(), 402);
            }
        }

        // ── Record payment ──
        $this->repo->createPayment([
            'uuid'               => $this->repo->generateUuid(),
            'invoice_id'         => $id,
            'organization_id'    => $orgId,
            'amount'             => $amount,
            'currency'           => $invoice['currency'] ?? 'USD',
            'payment_method'     => $method,
            'status'             => 'completed',
            'square_payment_id'  => $squareId,
            'square_receipt_url' => $receiptUrl,
            'notes'              => $data['notes'] ?? null,
            'processed_at'       => date('Y-m-d H:i:s'),
        ]);

        // ── Recalc totals on invoice ──
        $this->repo->recalcPaymentStatus($id);

        $updated = $this->repo->findById($id);
        return $this->success($updated, 'Payment recorded successfully');
    }

    // ───────────────────────────────────────────────────────
    // Helpers
    // ───────────────────────────────────────────────────────

    /**
     * Calculate subtotal, tax, and total from line items.
     * @return array{float, float, float} [subtotal, taxAmount, total]
     */
    private function calcTotals(array $items, float $taxRate, float $discountAmount): array
    {
        $subtotal = 0.0;
        foreach ($items as $item) {
            $qty   = (float) ($item['quantity']  ?? 1);
            $price = (float) ($item['unit_price'] ?? 0);
            $subtotal += $qty * $price;
        }
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $total     = round(max(0, $subtotal + $taxAmount - $discountAmount), 2);
        return [$subtotal, $taxAmount, $total];
    }

    private function sendInvoiceEmail(array $invoice): void
    {
        $name  = trim(($invoice['customer_first_name'] ?? '') . ' ' . ($invoice['customer_last_name'] ?? ''));
        if (empty($name)) $name = $invoice['customer_company'] ?? 'Customer';
        $email = $invoice['customer_email'] ?? '';
        if (!$email) return;

        $number = htmlspecialchars($invoice['invoice_number'] ?? '');
        $total  = number_format((float) ($invoice['total'] ?? 0), 2);
        $due    = htmlspecialchars($invoice['due_date'] ?? 'N/A');
        $status = ucfirst($invoice['status'] ?? 'sent');

        // Build items table HTML
        $itemRows = '';
        foreach ($invoice['items'] ?? [] as $item) {
            $desc = htmlspecialchars($item['description'] ?? '');
            $qty  = number_format((float) ($item['quantity']  ?? 1), 2);
            $up   = number_format((float) ($item['unit_price'] ?? 0), 2);
            $t    = number_format((float) ($item['total']      ?? 0), 2);
            $itemRows .= "<tr style='border-bottom:1px solid #e2e8f0;'>
                <td style='padding:10px 12px;'>{$desc}</td>
                <td style='padding:10px 12px;text-align:center;'>{$qty}</td>
                <td style='padding:10px 12px;text-align:right;'>\${$up}</td>
                <td style='padding:10px 12px;text-align:right;font-weight:600;'>\${$t}</td>
              </tr>";
        }

        $taxRow = '';
        if ((float) ($invoice['tax_amount'] ?? 0) > 0) {
            $taxRow = "<tr><td colspan='3' style='padding:6px 12px;text-align:right;color:#64748b;'>Tax</td>
                           <td style='padding:6px 12px;text-align:right;'>\$" . number_format((float)$invoice['tax_amount'], 2) . "</td></tr>";
        }
        $discRow = '';
        if ((float) ($invoice['discount_amount'] ?? 0) > 0) {
            $discRow = "<tr><td colspan='3' style='padding:6px 12px;text-align:right;color:#64748b;'>Discount</td>
                            <td style='padding:6px 12px;text-align:right;color:#10b981;'>-\$" . number_format((float)$invoice['discount_amount'], 2) . "</td></tr>";
        }

        $notes = !empty($invoice['notes']) ? '<p style="margin:16px 0 0;color:#475569;">' . nl2br(htmlspecialchars($invoice['notes'])) . '</p>' : '';

        $html = <<<HTML
<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Inter,system-ui,sans-serif;background:#f8fafc;">
<div style="max-width:600px;margin:32px auto;background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;">
  <div style="background:linear-gradient(135deg,#6366f1,#4f46e5);padding:28px 32px;">
    <h1 style="margin:0;color:#fff;font-size:22px;font-weight:700;">Invoice {$number}</h1>
    <p style="margin:4px 0 0;color:rgba(255,255,255,0.8);font-size:14px;">Due: {$due}</p>
  </div>
  <div style="padding:28px 32px;">
    <p style="margin:0 0 16px;color:#1e293b;">Hello {$name},</p>
    <p style="margin:0 0 24px;color:#475569;">Please find your invoice details below. Total amount due: <strong>\${$total}</strong></p>
    <table style="width:100%;border-collapse:collapse;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
      <thead>
        <tr style="background:#f8fafc;">
          <th style="padding:10px 12px;text-align:left;font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;">Description</th>
          <th style="padding:10px 12px;text-align:center;font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;">Qty</th>
          <th style="padding:10px 12px;text-align:right;font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;">Price</th>
          <th style="padding:10px 12px;text-align:right;font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;">Total</th>
        </tr>
      </thead>
      <tbody>{$itemRows}</tbody>
      <tfoot>
        {$taxRow}{$discRow}
        <tr style="background:#f8fafc;">
          <td colspan="3" style="padding:10px 12px;text-align:right;font-weight:700;color:#1e293b;">Total Due</td>
          <td style="padding:10px 12px;text-align:right;font-weight:700;font-size:16px;color:#6366f1;">\${$total}</td>
        </tr>
      </tfoot>
    </table>
    {$notes}
    <p style="margin:24px 0 0;font-size:12px;color:#94a3b8;">If you have questions, please reply to this email.</p>
  </div>
</div>
</body></html>
HTML;

        $text = "Invoice {$number} — Total: \${$total} — Due: {$due}\n\nPlease contact us if you have any questions.";

        try {
            Mailer::getInstance()->send($email, $name, "Invoice {$number} — \${$total} Due", $html, $text);
        } catch (\Exception $e) {
            // Log but don't fail the request
            error_log("[Invoice] Email send failed for invoice {$number}: " . $e->getMessage());
        }
    }
}
