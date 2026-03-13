<?php

declare(strict_types=1);

namespace App\Modules\Organizations;

use App\Core\Database\Repository;

final class OrganizationRepository extends Repository
{
    protected string $table = 'organizations';
    protected bool $tenantScoped = false; // Organizations are top-level entities

    /**
     * Find organization by slug.
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->query()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Find organization by UUID.
     */
    public function findByUuid(string $uuid): ?array
    {
        return $this->query()
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Find all organizations (all statuses) with optional search.
     */
    public function findAllOrgs(?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query();

        if ($search) {
            $q->whereLike('name', "%{$search}%");
        }

        return $q->orderBy('created_at', 'DESC')->paginate($page, $perPage);
    }

    /**
     * Find active organizations with optional search.
     */
    public function findActive(?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('status', 'active');

        if ($search) {
            $q->whereLike('name', "%{$search}%");
        }

        return $q->paginate($page, $perPage);
    }

    /**
     * Check if a slug is available.
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $q = $this->query()->where('slug', $slug);
        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }
        return $q->exists();
    }

    /**
     * Get organization with domain information.
     */
    public function findWithDomains(int $id): ?array
    {
        $org = $this->findById($id);
        if (!$org) {
            return null;
        }

        $org['domains'] = $this->db->fetchAll(
            "SELECT * FROM `organization_domains` WHERE `organization_id` = ? AND `verified_at` IS NOT NULL",
            [$id]
        );

        return $org;
    }

    /**
     * Get organization with full details for platform view.
     */
    public function findWithDetails(int $id): ?array
    {
        $org = $this->findWithDomains($id);
        if (!$org) {
            return null;
        }

        $org['user_count'] = (int) ($this->db->fetch(
            "SELECT COUNT(*) as cnt FROM users WHERE organization_id = ?", [$id]
        )['cnt'] ?? 0);

        $org['facility_count'] = (int) ($this->db->fetch(
            "SELECT COUNT(*) as cnt FROM facilities WHERE organization_id = ?", [$id]
        )['cnt'] ?? 0);

        $org['subscription'] = $this->db->fetch(
            "SELECT s.*, p.name as plan_name, p.price_monthly, p.price_yearly
             FROM subscriptions s LEFT JOIN plans p ON s.plan_id = p.id
             WHERE s.organization_id = ? ORDER BY s.created_at DESC LIMIT 1",
            [$id]
        );

        $org['extensions'] = $this->db->fetchAll(
            "SELECT oe.*, e.name, e.slug, e.description, e.version
             FROM organization_extensions oe
             JOIN extensions e ON oe.extension_id = e.id
             WHERE oe.organization_id = ? AND oe.is_active = 1",
            [$id]
        );

        return $org;
    }

    /**
     * Update organization status.
     */
    public function updateStatus(int $id, string $status): void
    {
        $this->db->update('organizations', [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
    }

    /**
     * Get counts of related entities for cascade protection.
     */
    public function getRelatedCounts(int $id): array
    {
        return [
            'users' => (int) ($this->db->fetch("SELECT COUNT(*) as cnt FROM users WHERE organization_id = ?", [$id])['cnt'] ?? 0),
            'facilities' => (int) ($this->db->fetch("SELECT COUNT(*) as cnt FROM facilities WHERE organization_id = ?", [$id])['cnt'] ?? 0),
            'subscriptions' => (int) ($this->db->fetch("SELECT COUNT(*) as cnt FROM subscriptions WHERE organization_id = ? AND status = 'active'", [$id])['cnt'] ?? 0),
        ];
    }
}
