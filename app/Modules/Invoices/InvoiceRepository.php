<?php

declare(strict_types=1);

namespace App\Modules\Invoices;

use App\Core\Database\Connection;

final class InvoiceRepository
{
    public function __construct(private Connection $db) {}

    // ─────────────────────────────────────────
    // Invoice CRUD
    // ─────────────────────────────────────────

    public function findByOrganization(
        int $orgId,
        array $filters = [],
        int $page = 1,
        int $perPage = 20
    ): array {
        $where  = ['bi.organization_id = ?'];
        $params = [$orgId];

        if (!empty($filters['status'])) {
            $where[]  = 'bi.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['facility_id'])) {
            $where[]  = 'bi.facility_id = ?';
            $params[] = (int) $filters['facility_id'];
        }
        if (!empty($filters['search'])) {
            $s        = '%' . $filters['search'] . '%';
            $where[]  = '(bi.invoice_number LIKE ? OR bi.customer_first_name LIKE ? OR bi.customer_last_name LIKE ? OR bi.customer_email LIKE ? OR bi.customer_company LIKE ?)';
            $params   = array_merge($params, [$s, $s, $s, $s, $s]);
        }
        if (!empty($filters['from_date'])) {
            $where[]  = 'bi.issue_date >= ?';
            $params[] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where[]  = 'bi.issue_date <= ?';
            $params[] = $filters['to_date'];
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $perPage;

        $total = $this->db->fetch(
            "SELECT COUNT(*) AS cnt FROM `booking_invoices` bi WHERE {$whereStr}",
            $params
        )['cnt'] ?? 0;

        $data = $this->db->fetchAll(
            "SELECT bi.*,
                    f.name AS facility_name,
                    CONCAT(bi.customer_first_name, ' ', bi.customer_last_name) AS customer_full_name
             FROM `booking_invoices` bi
             LEFT JOIN `facilities` f ON f.id = bi.facility_id
             WHERE {$whereStr}
             ORDER BY bi.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return ['data' => $data ?? [], 'total' => (int) $total];
    }

    public function findById(int $id): ?array
    {
        $invoice = $this->db->fetch(
            "SELECT bi.*,
                    f.name  AS facility_name,
                    u.first_name AS created_by_first, u.last_name AS created_by_last
             FROM `booking_invoices` bi
             LEFT JOIN `facilities` f ON f.id = bi.facility_id
             LEFT JOIN `users` u ON u.id = bi.created_by
             WHERE bi.id = ?",
            [$id]
        );

        if (!$invoice) {
            return null;
        }

        $invoice['items']    = $this->findItems($id);
        $invoice['payments'] = $this->findPayments($id);

        return $invoice;
    }

    public function create(array $data): int
    {
        $this->db->execute(
            "INSERT INTO `booking_invoices`
             (uuid, organization_id, facility_id, player_id, created_by,
              invoice_number, customer_type, customer_first_name, customer_last_name,
              customer_company, customer_email, customer_phone,
              subtotal, tax_rate, tax_amount, discount_amount, total,
              amount_paid, amount_due, currency, status, payment_type,
              issue_date, due_date, notes, internal_notes, auto_reminders_enabled)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,?,?,?,?,?,?,?,?,?)",
            [
                $data['uuid'],
                $data['organization_id'],
                $data['facility_id']     ?? null,
                $data['player_id']       ?? null,
                $data['created_by']      ?? null,
                $data['invoice_number'],
                $data['customer_type']   ?? 'individual',
                $data['customer_first_name'] ?? null,
                $data['customer_last_name']  ?? null,
                $data['customer_company']    ?? null,
                $data['customer_email']      ?? null,
                $data['customer_phone']      ?? null,
                $data['subtotal']        ?? 0,
                $data['tax_rate']        ?? 0,
                $data['tax_amount']      ?? 0,
                $data['discount_amount'] ?? 0,
                $data['total']           ?? 0,
                $data['total']           ?? 0, // amount_due = total initially
                $data['currency']        ?? 'USD',
                $data['status']          ?? 'draft',
                $data['payment_type']    ?? 'full',
                $data['issue_date']      ?? date('Y-m-d'),
                $data['due_date']        ?? null,
                $data['notes']           ?? null,
                $data['internal_notes']  ?? null,
                $data['auto_reminders_enabled'] ?? 1,
            ]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = [];

        $updatable = [
            'facility_id','player_id',
            'customer_type','customer_first_name','customer_last_name',
            'customer_company','customer_email','customer_phone',
            'subtotal','tax_rate','tax_amount','discount_amount','total','amount_due',
            'currency','status','payment_type',
            'issue_date','due_date','notes','internal_notes','auto_reminders_enabled',
        ];

        foreach ($updatable as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "`{$col}` = ?";
                $params[] = $data[$col];
            }
        }

        if (empty($fields)) return;

        $params[] = $id;
        $this->db->execute(
            "UPDATE `booking_invoices` SET " . implode(', ', $fields) . " WHERE id = ?",
            $params
        );
    }

    public function delete(int $id): void
    {
        $this->db->execute("DELETE FROM `booking_invoices` WHERE id = ?", [$id]);
    }

    public function markSent(int $id): void
    {
        $this->db->execute(
            "UPDATE `booking_invoices` SET status = 'sent', updated_at = NOW() WHERE id = ? AND status = 'draft'",
            [$id]
        );
    }

    // ─────────────────────────────────────────
    // Line Items
    // ─────────────────────────────────────────

    public function findItems(int $invoiceId): array
    {
        return $this->db->fetchAll(
            "SELECT bii.*,
                    st.title AS session_type_name,
                    sc.scheduled_at AS class_scheduled_at
             FROM `booking_invoice_items` bii
             LEFT JOIN `session_types` st ON st.id = bii.session_type_id
             LEFT JOIN `st_classes` sc ON sc.id = bii.class_id
             WHERE bii.invoice_id = ?
             ORDER BY bii.sort_order, bii.id",
            [$invoiceId]
        ) ?? [];
    }

    public function syncItems(int $invoiceId, array $items): void
    {
        $this->db->execute("DELETE FROM `booking_invoice_items` WHERE invoice_id = ?", [$invoiceId]);

        foreach ($items as $sort => $item) {
            $qty  = (float) ($item['quantity']   ?? 1);
            $price= (float) ($item['unit_price']  ?? 0);
            $this->db->execute(
                "INSERT INTO `booking_invoice_items`
                 (invoice_id, item_type, session_type_id, class_id, description, quantity, unit_price, total, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $invoiceId,
                    $item['item_type']        ?? 'custom',
                    $item['session_type_id']  ?? null,
                    $item['class_id']         ?? null,
                    $item['description']      ?? '',
                    $qty,
                    $price,
                    round($qty * $price, 2),
                    $sort,
                ]
            );
        }
    }

    // ─────────────────────────────────────────
    // Payments
    // ─────────────────────────────────────────

    public function findPayments(int $invoiceId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `booking_invoice_payments`
             WHERE invoice_id = ? ORDER BY created_at ASC",
            [$invoiceId]
        ) ?? [];
    }

    public function createPayment(array $data): int
    {
        $this->db->execute(
            "INSERT INTO `booking_invoice_payments`
             (uuid, invoice_id, organization_id, amount, currency, payment_method,
              status, square_payment_id, square_receipt_url, notes, processed_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)",
            [
                $data['uuid'],
                $data['invoice_id'],
                $data['organization_id'],
                $data['amount'],
                $data['currency']           ?? 'USD',
                $data['payment_method']     ?? 'card',
                $data['status']             ?? 'completed',
                $data['square_payment_id']  ?? null,
                $data['square_receipt_url'] ?? null,
                $data['notes']              ?? null,
                $data['processed_at']       ?? date('Y-m-d H:i:s'),
            ]
        );
        return $this->db->lastInsertId();
    }

    /** Recalculate amount_paid and amount_due, then update status. */
    public function recalcPaymentStatus(int $invoiceId): void
    {
        $invoice = $this->db->fetch(
            "SELECT total FROM `booking_invoices` WHERE id = ?",
            [$invoiceId]
        );
        if (!$invoice) return;

        $paid = (float) ($this->db->fetch(
            "SELECT COALESCE(SUM(amount),0) AS paid
             FROM `booking_invoice_payments`
             WHERE invoice_id = ? AND status = 'completed'",
            [$invoiceId]
        )['paid'] ?? 0);

        $total = (float) $invoice['total'];
        $due   = max(0, $total - $paid);

        if ($paid >= $total && $total > 0) {
            $status = 'paid';
            $paidAt = date('Y-m-d H:i:s');
        } elseif ($paid > 0) {
            $status = 'partially_paid';
            $paidAt = null;
        } else {
            // Keep existing status (might be sent, overdue, etc)
            $cur = $this->db->fetch("SELECT status FROM `booking_invoices` WHERE id = ?", [$invoiceId]);
            $status = in_array($cur['status'] ?? '', ['draft','sent','overdue']) ? ($cur['status'] ?? 'sent') : 'sent';
            $paidAt = null;
        }

        $this->db->execute(
            "UPDATE `booking_invoices`
             SET amount_paid = ?, amount_due = ?, status = ?, paid_at = ?
             WHERE id = ?",
            [$paid, $due, $status, $paidAt, $invoiceId]
        );
    }

    // ─────────────────────────────────────────
    // Session Types (for invoice line item picker)
    // ─────────────────────────────────────────

    public function findSessionTypesForPicker(int $orgId, ?int $facilityId = null): array
    {
        $where  = ['st.organization_id = ?', 'st.is_active = 1'];
        $params = [$orgId];

        if ($facilityId) {
            $where[]  = 'st.facility_id = ?';
            $params[] = $facilityId;
        }

        $whereStr = implode(' AND ', $where);

        return $this->db->fetchAll(
            "SELECT st.id, st.title, st.standard_price, st.session_type, f.name AS facility_name
             FROM `session_types` st
             LEFT JOIN `facilities` f ON f.id = st.facility_id
             WHERE {$whereStr}
             ORDER BY st.title ASC",
            $params
        ) ?? [];
    }

    public function findClassesForSessionType(int $sessionTypeId, int $orgId): array
    {
        return $this->db->fetchAll(
            "SELECT sc.id, sc.scheduled_at, sc.capacity, sc.status,
                    CONCAT(u.first_name,' ',u.last_name) AS coach_name
             FROM `st_classes` sc
             LEFT JOIN `users` u ON u.id = sc.coach_id
             WHERE sc.session_type_id = ?
             ORDER BY sc.scheduled_at DESC
             LIMIT 100",
            [$sessionTypeId]
        ) ?? [];
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    public function generateInvoiceNumber(int $orgId): string
    {
        $seq = ((int) ($this->db->fetch(
            "SELECT COUNT(*) AS cnt FROM `booking_invoices` WHERE organization_id = ?",
            [$orgId]
        )['cnt'] ?? 0)) + 1;

        return 'INV-' . date('Y') . '-' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
