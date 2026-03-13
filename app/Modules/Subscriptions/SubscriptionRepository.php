<?php

declare(strict_types=1);

namespace App\Modules\Subscriptions;

use App\Core\Database\Repository;

final class SubscriptionRepository extends Repository
{
    protected string $table = 'subscriptions';

    public function findActiveByOrganization(int $orgId): ?array
    {
        return $this->query()
            ->where('organization_id', $orgId)
            ->where('status', 'active')
            ->first();
    }

    public function findByOrganization(int $orgId, int $page = 1, int $perPage = 20): array
    {
        return $this->query()
            ->where('organization_id', $orgId)
            ->orderBy('created_at', 'DESC')
            ->paginate($page, $perPage);
    }

    public function findWithPlan(int $id): ?array
    {
        $sub = $this->findById($id);
        if (!$sub) {
            return null;
        }

        $sub['plan'] = $this->db->fetch(
            "SELECT * FROM `plans` WHERE `id` = ?",
            [$sub['plan_id']]
        );

        return $sub;
    }

    // -- Plans --

    public function findAllPlans(bool $activeOnly = true): array
    {
        $sql = "SELECT * FROM `plans`";
        if ($activeOnly) {
            $sql .= " WHERE `is_active` = 1";
        }
        $sql .= " ORDER BY `sort_order` ASC, `price_monthly` ASC";
        return $this->db->fetchAll($sql);
    }

    public function findPlanById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM `plans` WHERE `id` = ?", [$id]);
    }

    public function findPlanBySlug(string $slug): ?array
    {
        return $this->db->fetch("SELECT * FROM `plans` WHERE `slug` = ?", [$slug]);
    }

    // -- Invoices --

    public function findInvoices(int $orgId, int $page = 1, int $perPage = 20): array
    {
        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `invoices` WHERE `organization_id` = ?",
            [$orgId]
        );

        $data = $this->db->fetchAll(
            "SELECT * FROM `invoices` WHERE `organization_id` = ? ORDER BY `created_at` DESC LIMIT ? OFFSET ?",
            [$orgId, $perPage, ($page - 1) * $perPage]
        );

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
    }

    public function createInvoice(array $data): int
    {
        return $this->db->insert('invoices', $data);
    }
}
