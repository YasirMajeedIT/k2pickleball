<?php

declare(strict_types=1);

namespace App\Modules\Discounts;

use App\Core\Database\Repository;

final class DiscountRepository extends Repository
{
    protected string $table = 'st_discount_rules';

    public function findByOrganization(int $orgId, ?int $facilityId = null, ?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($facilityId) {
            $q->where('facility_id', $facilityId);
        }

        if ($search) {
            $q->whereLike('name', "%{$search}%");
        }

        $q->orderBy('created_at', 'DESC');
        return $q->paginate($page, $perPage);
    }

    public function findByUuid(string $uuid): ?array
    {
        return $this->query()->where('uuid', $uuid)->first();
    }

    public function findByCouponCode(string $code, int $orgId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE coupon_code = ? AND organization_id = ? AND is_active = 1 LIMIT 1",
            [$code, $orgId]
        );
    }

    /**
     * Get the session type IDs assigned to a discount rule.
     */
    public function getSessionTypes(int $discountRuleId): array
    {
        return $this->db->fetchAll(
            "SELECT session_type_id FROM st_discount_session_types WHERE discount_rule_id = ?",
            [$discountRuleId]
        );
    }

    /**
     * Sync session types for a discount rule.
     */
    public function syncSessionTypes(int $discountRuleId, array $sessionTypeIds): void
    {
        $this->db->query("DELETE FROM `st_discount_session_types` WHERE `discount_rule_id` = ?", [$discountRuleId]);

        foreach ($sessionTypeIds as $stId) {
            $this->db->insert('st_discount_session_types', [
                'discount_rule_id' => $discountRuleId,
                'session_type_id' => (int) $stId,
            ]);
        }
    }

    /**
     * Increment usage count for a discount rule.
     */
    public function incrementUsage(int $id): void
    {
        $this->db->query(
            "UPDATE `{$this->table}` SET `used_count` = `used_count` + 1 WHERE `id` = ?",
            [$id]
        );
    }
}
