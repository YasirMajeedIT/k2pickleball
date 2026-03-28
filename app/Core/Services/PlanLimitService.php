<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Database\Connection;

/**
 * Checks whether an organization has exceeded plan limits
 * for facilities, courts, and users.
 */
final class PlanLimitService
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Get the active plan for an organization (via subscriptions table).
     * Returns null if no active subscription or plan found.
     */
    public function getActivePlan(int $orgId): ?array
    {
        return $this->db->fetch(
            "SELECT p.*
             FROM `subscriptions` s
             JOIN `plans` p ON p.`id` = s.`plan_id`
             WHERE s.`organization_id` = ?
               AND s.`status` IN ('active', 'trialing')
             ORDER BY s.`created_at` DESC
             LIMIT 1",
            [$orgId]
        );
    }

    /**
     * Check if the org can create another facility.
     * Returns ['allowed' => bool, 'current' => int, 'limit' => int|null, 'plan' => string]
     */
    public function canCreateFacility(int $orgId): array
    {
        $plan = $this->getActivePlan($orgId);
        if (!$plan) {
            return ['allowed' => false, 'current' => 0, 'limit' => 0, 'plan' => 'none',
                    'message' => 'No active subscription. Please subscribe to a plan.'];
        }

        $limit = $plan['max_facilities'] !== null ? (int) $plan['max_facilities'] : null;

        // null = unlimited
        if ($limit === null) {
            return ['allowed' => true, 'current' => 0, 'limit' => null, 'plan' => $plan['name']];
        }

        $count = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `facilities` WHERE `organization_id` = ?",
            [$orgId]
        );
        $current = (int) ($count['cnt'] ?? 0);

        if ($current >= $limit) {
            return ['allowed' => false, 'current' => $current, 'limit' => $limit, 'plan' => $plan['name'],
                    'message' => "Facility limit reached ({$current}/{$limit}). Please upgrade your plan."];
        }

        return ['allowed' => true, 'current' => $current, 'limit' => $limit, 'plan' => $plan['name']];
    }

    /**
     * Check if the org can create another court.
     */
    public function canCreateCourt(int $orgId): array
    {
        $plan = $this->getActivePlan($orgId);
        if (!$plan) {
            return ['allowed' => false, 'current' => 0, 'limit' => 0, 'plan' => 'none',
                    'message' => 'No active subscription. Please subscribe to a plan.'];
        }

        $limit = $plan['max_courts'] !== null ? (int) $plan['max_courts'] : null;

        if ($limit === null) {
            return ['allowed' => true, 'current' => 0, 'limit' => null, 'plan' => $plan['name']];
        }

        $count = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `courts` WHERE `organization_id` = ?",
            [$orgId]
        );
        $current = (int) ($count['cnt'] ?? 0);

        if ($current >= $limit) {
            return ['allowed' => false, 'current' => $current, 'limit' => $limit, 'plan' => $plan['name'],
                    'message' => "Court limit reached ({$current}/{$limit}). Please upgrade your plan."];
        }

        return ['allowed' => true, 'current' => $current, 'limit' => $limit, 'plan' => $plan['name']];
    }

    /**
     * Check if the org can create another user.
     */
    public function canCreateUser(int $orgId): array
    {
        $plan = $this->getActivePlan($orgId);
        if (!$plan) {
            return ['allowed' => false, 'current' => 0, 'limit' => 0, 'plan' => 'none',
                    'message' => 'No active subscription. Please subscribe to a plan.'];
        }

        $limit = $plan['max_users'] !== null ? (int) $plan['max_users'] : null;

        if ($limit === null) {
            return ['allowed' => true, 'current' => 0, 'limit' => null, 'plan' => $plan['name']];
        }

        $count = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `users` WHERE `organization_id` = ?",
            [$orgId]
        );
        $current = (int) ($count['cnt'] ?? 0);

        if ($current >= $limit) {
            return ['allowed' => false, 'current' => $current, 'limit' => $limit, 'plan' => $plan['name'],
                    'message' => "User limit reached ({$current}/{$limit}). Please upgrade your plan."];
        }

        return ['allowed' => true, 'current' => $current, 'limit' => $limit, 'plan' => $plan['name']];
    }

    /**
     * Get full usage summary for an organization.
     */
    public function getUsageSummary(int $orgId): array
    {
        $plan = $this->getActivePlan($orgId);

        $facilities = $this->db->fetch("SELECT COUNT(*) as cnt FROM `facilities` WHERE `organization_id` = ?", [$orgId]);
        $courts     = $this->db->fetch("SELECT COUNT(*) as cnt FROM `courts` WHERE `organization_id` = ?", [$orgId]);
        $users      = $this->db->fetch("SELECT COUNT(*) as cnt FROM `users` WHERE `organization_id` = ?", [$orgId]);

        return [
            'plan' => $plan ? [
                'name'  => $plan['name'],
                'slug'  => $plan['slug'],
            ] : null,
            'facilities' => [
                'current' => (int) ($facilities['cnt'] ?? 0),
                'limit'   => $plan && $plan['max_facilities'] !== null ? (int) $plan['max_facilities'] : null,
            ],
            'courts' => [
                'current' => (int) ($courts['cnt'] ?? 0),
                'limit'   => $plan && $plan['max_courts'] !== null ? (int) $plan['max_courts'] : null,
            ],
            'users' => [
                'current' => (int) ($users['cnt'] ?? 0),
                'limit'   => $plan && $plan['max_users'] !== null ? (int) $plan['max_users'] : null,
            ],
        ];
    }
}
