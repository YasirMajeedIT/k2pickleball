<?php

declare(strict_types=1);

namespace App\Modules\SessionTypes;

use App\Core\Database\Repository;

final class SessionTypeRepository extends Repository
{
    protected string $table = 'session_types';

    public function findByOrganization(int $orgId, ?string $search = null, ?int $facilityId = null, ?int $categoryId = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($search) {
            $q->whereLike('title', "%{$search}%");
        }

        if ($facilityId) {
            $q->where('facility_id', $facilityId);
        }

        if ($categoryId) {
            $q->where('category_id', $categoryId);
        }

        $q->orderBy('created_at', 'DESC');
        return $q->paginate($page, $perPage);
    }

    public function findWithResourceValues(int $id): ?array
    {
        $record = $this->findById($id);
        if (!$record) {
            return null;
        }

        $record['resource_values'] = $this->db->fetchAll(
            "SELECT rv.*, r.name as resource_name, r.field_type
             FROM `session_type_resource_values` strv
             JOIN `resource_values` rv ON rv.id = strv.resource_value_id
             JOIN `resources` r ON r.id = rv.resource_id
             WHERE strv.session_type_id = ?
             ORDER BY r.name ASC, rv.sort_order ASC",
            [$id]
        );

        return $record;
    }

    public function syncResourceValues(int $sessionTypeId, array $resourceValueIds): void
    {
        // Delete existing pivot entries
        $this->db->query(
            "DELETE FROM `session_type_resource_values` WHERE `session_type_id` = ?",
            [$sessionTypeId]
        );

        // Insert new entries
        foreach ($resourceValueIds as $rvId) {
            $this->db->insert('session_type_resource_values', [
                'session_type_id'    => $sessionTypeId,
                'resource_value_id'  => (int) $rvId,
            ]);
        }
    }

    public function getResourceValueIds(int $sessionTypeId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT `resource_value_id` FROM `session_type_resource_values` WHERE `session_type_id` = ?",
            [$sessionTypeId]
        );
        return array_column($rows, 'resource_value_id');
    }
}
