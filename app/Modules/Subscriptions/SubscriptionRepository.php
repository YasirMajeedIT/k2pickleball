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

    /**
     * Find all subscriptions across all organizations (for platform admin).
     */
    public function findAllSubscriptions(?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];
        if ($search) {
            $where = "WHERE o.name LIKE ?";
            $params[] = "%{$search}%";
        }

        $countSql = "SELECT COUNT(*) as cnt FROM subscriptions s LEFT JOIN organizations o ON s.organization_id = o.id {$where}";
        $total = $this->db->fetch($countSql, $params);

        $dataSql = "SELECT s.*, o.name as organization_name, p.name as plan_name
                    FROM subscriptions s
                    LEFT JOIN organizations o ON s.organization_id = o.id
                    LEFT JOIN plans p ON s.plan_id = p.id
                    {$where}
                    ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
        $dataParams = array_merge($params, [$perPage, $offset]);
        $data = $this->db->fetchAll($dataSql, $dataParams);

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
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

    public function findAllPlansPaginated(?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $where = '';
        $params = [];
        if ($search) {
            $where = "WHERE name LIKE ?";
            $params[] = "%{$search}%";
        }

        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM plans {$where}", $params);
        $data = $this->db->fetchAll(
            "SELECT * FROM plans {$where} ORDER BY sort_order ASC, price_monthly ASC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
    }

    public function createPlan(array $data): int
    {
        return $this->db->insert('plans', $data);
    }

    public function updatePlan(int $id, array $data): void
    {
        $this->db->update('plans', $data, ['id' => $id]);
    }

    public function deletePlan(int $id): void
    {
        $this->db->delete('plans', ['id' => $id]);
    }

    public function planSlugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM plans WHERE slug = ?";
        $params = [$slug];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $result = $this->db->fetch($sql, $params);
        return (int) ($result['cnt'] ?? 0) > 0;
    }

    // -- Invoices --

    public function findInvoices(int $orgId, int $page = 1, int $perPage = 20): array
    {
        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `invoices` WHERE `organization_id` = ?",
            [$orgId]
        );

        $data = $this->db->fetchAll(
            "SELECT i.*, p.name AS plan_name
             FROM `invoices` i
             LEFT JOIN `subscriptions` s ON i.subscription_id = s.id
             LEFT JOIN `plans` p ON s.plan_id = p.id
             WHERE i.organization_id = ?
             ORDER BY i.created_at DESC LIMIT ? OFFSET ?",
            [$orgId, $perPage, ($page - 1) * $perPage]
        );

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
    }

    public function createInvoice(array $data): int
    {
        return $this->db->insert('invoices', $data);
    }
}
