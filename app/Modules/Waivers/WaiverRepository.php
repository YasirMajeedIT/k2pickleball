<?php

declare(strict_types=1);

namespace App\Modules\Waivers;

use App\Core\Database\Repository;

final class WaiverRepository extends Repository
{
    protected string $table = 'waivers';

    public function findByOrganization(int $orgId, ?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($search) {
            $q->whereLike('title', "%{$search}%");
        }

        $q->orderBy('version', 'DESC');
        return $q->paginate($page, $perPage);
    }

    public function findByUuid(string $uuid): ?array
    {
        return $this->query()->where('uuid', $uuid)->first();
    }

    /**
     * Get the currently active waiver for the organization.
     */
    public function findActive(int $orgId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `waivers` WHERE `organization_id` = ? AND `is_active` = 1 LIMIT 1",
            [$orgId]
        );
    }

    /**
     * Activate one waiver and deactivate all others for the same org.
     */
    public function activate(int $id, int $orgId): void
    {
        // Deactivate all for this org
        $this->db->query(
            "UPDATE `waivers` SET `is_active` = 0, `updated_at` = NOW() WHERE `organization_id` = ?",
            [$orgId]
        );
        // Activate the target
        $this->db->query(
            "UPDATE `waivers` SET `is_active` = 1, `updated_at` = NOW() WHERE `id` = ? AND `organization_id` = ?",
            [$id, $orgId]
        );
    }

    /**
     * Deactivate all waivers for the org (used when deleting the active one).
     */
    public function deactivateAll(int $orgId): void
    {
        $this->db->query(
            "UPDATE `waivers` SET `is_active` = 0, `updated_at` = NOW() WHERE `organization_id` = ?",
            [$orgId]
        );
    }
}
