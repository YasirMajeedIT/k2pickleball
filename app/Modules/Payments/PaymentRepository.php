<?php

declare(strict_types=1);

namespace App\Modules\Payments;

use App\Core\Database\Repository;

final class PaymentRepository extends Repository
{
    protected string $table = 'payments';

    public function findByOrganization(int $orgId, int $page = 1, int $perPage = 20): array
    {
        return $this->query()
            ->where('organization_id', $orgId)
            ->orderBy('created_at', 'DESC')
            ->paginate($page, $perPage);
    }

    public function findByReference(string $reference): ?array
    {
        return $this->query()->where('idempotency_key', $reference)->first();
    }

    public function findByGatewayId(string $gatewayId): ?array
    {
        return $this->query()->where('square_payment_id', $gatewayId)->first();
    }

    // -- Payment Methods --

    public function findPaymentMethods(int $orgId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `payment_methods` WHERE `organization_id` = ? AND `status` != 'revoked' ORDER BY `is_default` DESC, `created_at` DESC",
            [$orgId]
        );
    }

    public function findPaymentMethod(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `payment_methods` WHERE `id` = ? AND `status` != 'revoked'",
            [$id]
        );
    }

    public function createPaymentMethod(array $data): int
    {
        return $this->db->insert('payment_methods', $data);
    }

    public function deletePaymentMethod(int $id): void
    {
        $this->db->update('payment_methods', [
            'status' => 'revoked',
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
    }

    public function setDefaultPaymentMethod(int $id, int $orgId): void
    {
        // Reset all to non-default
        $this->db->query(
            "UPDATE `payment_methods` SET `is_default` = 0 WHERE `organization_id` = ?",
            [$orgId]
        );
        // Set the chosen one as default
        $this->db->update('payment_methods', ['is_default' => 1], ['id' => $id]);
    }

    // -- Transactions --

    public function createTransaction(array $data): int
    {
        return $this->db->insert('transactions', $data);
    }

    public function findTransactions(int $orgId, int $page = 1, int $perPage = 20): array
    {
        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `transactions` WHERE `organization_id` = ?",
            [$orgId]
        );

        $data = $this->db->fetchAll(
            "SELECT * FROM `transactions` WHERE `organization_id` = ? ORDER BY `created_at` DESC LIMIT ? OFFSET ?",
            [$orgId, $perPage, ($page - 1) * $perPage]
        );

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
    }
}
