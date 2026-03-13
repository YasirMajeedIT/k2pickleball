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
}
