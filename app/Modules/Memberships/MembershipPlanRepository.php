<?php

declare(strict_types=1);

namespace App\Modules\Memberships;

use App\Core\Database\Connection;

final class MembershipPlanRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    // ── Plan CRUD ───────────────────────────────────────

    public function findByOrganization(int $orgId, ?string $search = null, ?int $facilityId = null, int $page = 1, int $perPage = 20): array
    {
        $where = ['mp.organization_id = ?'];
        $params = [$orgId];

        if ($search) {
            $where[] = '(mp.name LIKE ? OR mp.description LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($facilityId) {
            $where[] = 'mp.facility_id = ?';
            $params[] = $facilityId;
        }

        $whereSql = implode(' AND ', $where);

        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM membership_plans mp WHERE {$whereSql}",
            $params
        );

        $offset = ($page - 1) * $perPage;
        $data = $this->db->fetchAll(
            "SELECT mp.*, f.name as facility_name,
                    (SELECT COUNT(*) FROM player_memberships pm WHERE pm.membership_plan_id = mp.id AND pm.status = 'active') as active_members
             FROM membership_plans mp
             LEFT JOIN facilities f ON f.id = mp.facility_id
             WHERE {$whereSql}
             ORDER BY mp.sort_order ASC, mp.name ASC
             LIMIT ? OFFSET ?",
            [...$params, $perPage, $offset]
        );

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT mp.*, f.name as facility_name
             FROM membership_plans mp
             LEFT JOIN facilities f ON f.id = mp.facility_id
             WHERE mp.id = ?",
            [$id]
        ) ?: null;
    }

    public function findWithBenefits(int $id): ?array
    {
        $plan = $this->findById($id);
        if (!$plan) return null;

        $plan['included_categories'] = $this->db->fetchAll(
            "SELECT mpc.*, c.name as category_name, c.color as category_color
             FROM membership_plan_categories mpc
             JOIN categories c ON c.id = mpc.category_id
             WHERE mpc.membership_plan_id = ? AND mpc.benefit_type = 'included'
             ORDER BY c.name",
            [$id]
        );

        $plan['discounted_categories'] = $this->db->fetchAll(
            "SELECT mpc.*, c.name as category_name, c.color as category_color
             FROM membership_plan_categories mpc
             JOIN categories c ON c.id = mpc.category_id
             WHERE mpc.membership_plan_id = ? AND mpc.benefit_type = 'discounted'
             ORDER BY c.name",
            [$id]
        );

        $plan['included_session_types'] = $this->db->fetchAll(
            "SELECT mpst.*, st.title as session_type_name, st.duration, st.standard_price
             FROM membership_plan_session_types mpst
             JOIN session_types st ON st.id = mpst.session_type_id
             WHERE mpst.membership_plan_id = ? AND mpst.benefit_type = 'included'
             ORDER BY st.title",
            [$id]
        );

        $plan['discounted_session_types'] = $this->db->fetchAll(
            "SELECT mpst.*, st.title as session_type_name, st.duration, st.standard_price
             FROM membership_plan_session_types mpst
             JOIN session_types st ON st.id = mpst.session_type_id
             WHERE mpst.membership_plan_id = ? AND mpst.benefit_type = 'discounted'
             ORDER BY st.title",
            [$id]
        );

        $plan['active_members'] = (int) ($this->db->fetch(
            "SELECT COUNT(*) as cnt FROM player_memberships WHERE membership_plan_id = ? AND status = 'active'",
            [$id]
        )['cnt'] ?? 0);

        return $plan;
    }

    public function create(array $data): int
    {
        return $this->db->insert('membership_plans', $data);
    }

    public function update(int $id, array $data): void
    {
        $this->db->update('membership_plans', $data, ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->db->query("DELETE FROM membership_plans WHERE id = ?", [$id]);
    }

    // ── Category benefits ───────────────────────────────

    public function syncCategoryBenefits(int $planId, array $included, array $discounted): void
    {
        $this->db->query("DELETE FROM membership_plan_categories WHERE membership_plan_id = ?", [$planId]);

        foreach ($included as $item) {
            $this->db->insert('membership_plan_categories', [
                'membership_plan_id' => $planId,
                'category_id' => (int) $item['category_id'],
                'benefit_type' => 'included',
                'price' => isset($item['price']) ? round((float) $item['price'], 2) : null,
                'usage_limit' => $item['usage_limit'] ?? null,
                'usage_period' => $item['usage_period'] ?? 'unlimited',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        foreach ($discounted as $item) {
            $this->db->insert('membership_plan_categories', [
                'membership_plan_id' => $planId,
                'category_id' => (int) $item['category_id'],
                'benefit_type' => 'discounted',
                'discount_percentage' => isset($item['discount_percentage']) ? round((float) $item['discount_percentage'], 2) : null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    // ── Session Type benefits ───────────────────────────

    public function syncSessionTypeBenefits(int $planId, array $included, array $discounted): void
    {
        $this->db->query("DELETE FROM membership_plan_session_types WHERE membership_plan_id = ?", [$planId]);

        foreach ($included as $item) {
            $this->db->insert('membership_plan_session_types', [
                'membership_plan_id' => $planId,
                'session_type_id' => (int) $item['session_type_id'],
                'benefit_type' => 'included',
                'price' => isset($item['price']) ? round((float) $item['price'], 2) : null,
                'usage_limit' => $item['usage_limit'] ?? null,
                'usage_period' => $item['usage_period'] ?? 'unlimited',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        foreach ($discounted as $item) {
            $this->db->insert('membership_plan_session_types', [
                'membership_plan_id' => $planId,
                'session_type_id' => (int) $item['session_type_id'],
                'benefit_type' => 'discounted',
                'discount_percentage' => isset($item['discount_percentage']) ? round((float) $item['discount_percentage'], 2) : null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    // ── Player Memberships ──────────────────────────────

    public function findMembershipsByOrganization(int $orgId, ?string $search = null, ?string $status = null, int $page = 1, int $perPage = 20): array
    {
        $where = ['pm.organization_id = ?'];
        $params = [$orgId];

        if ($search) {
            $where[] = '(p.first_name LIKE ? OR p.last_name LIKE ? OR mp.name LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($status) {
            $where[] = 'pm.status = ?';
            $params[] = $status;
        }

        $whereSql = implode(' AND ', $where);

        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt
             FROM player_memberships pm
             JOIN players p ON p.id = pm.player_id
             JOIN membership_plans mp ON mp.id = pm.membership_plan_id
             WHERE {$whereSql}",
            $params
        );

        $offset = ($page - 1) * $perPage;
        $data = $this->db->fetchAll(
            "SELECT pm.*, mp.name as plan_name, mp.price as plan_price, mp.duration_type,
                    p.first_name, p.last_name, p.email as player_email,
                    f.name as facility_name
             FROM player_memberships pm
             JOIN membership_plans mp ON mp.id = pm.membership_plan_id
             JOIN players p ON p.id = pm.player_id
             JOIN facilities f ON f.id = pm.facility_id
             WHERE {$whereSql}
             ORDER BY pm.created_at DESC
             LIMIT ? OFFSET ?",
            [...$params, $perPage, $offset]
        );

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
    }

    public function findMembershipById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT pm.*, mp.name as plan_name, mp.price as plan_price, mp.duration_type,
                    p.first_name, p.last_name, p.email as player_email,
                    f.name as facility_name
             FROM player_memberships pm
             JOIN membership_plans mp ON mp.id = pm.membership_plan_id
             JOIN players p ON p.id = pm.player_id
             JOIN facilities f ON f.id = pm.facility_id
             WHERE pm.id = ?",
            [$id]
        ) ?: null;
    }

    public function createMembership(array $data): int
    {
        return $this->db->insert('player_memberships', $data);
    }

    public function updateMembership(int $id, array $data): void
    {
        $this->db->update('player_memberships', $data, ['id' => $id]);
    }

    public function togglePlanStatus(int $id, bool $active): void
    {
        $this->db->update('membership_plans', [
            'is_active' => $active ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
    }

    public function reorderPlans(array $order): void
    {
        foreach ($order as $index => $planId) {
            $this->db->update('membership_plans', ['sort_order' => $index], ['id' => (int) $planId]);
        }
    }
}
