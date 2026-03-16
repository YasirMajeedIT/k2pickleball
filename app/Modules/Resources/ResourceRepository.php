<?php

declare(strict_types=1);

namespace App\Modules\Resources;

use App\Core\Database\Repository;

final class ResourceRepository extends Repository
{
    protected string $table = 'resources';

    public function findByOrganization(int $orgId, ?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($search) {
            $q->whereLike('name', "%{$search}%");
        }

        $q->orderBy('name', 'ASC');
        return $q->paginate($page, $perPage);
    }

    public function findWithValues(int $id): ?array
    {
        $resource = $this->findById($id);
        if (!$resource) {
            return null;
        }

        $resource['values'] = $this->db->fetchAll(
            "SELECT * FROM `resource_values` WHERE `resource_id` = ? ORDER BY `sort_order` ASC, `name` ASC",
            [$id]
        );

        return $resource;
    }

    public function nameExists(int $orgId, string $name, ?int $excludeId = null): bool
    {
        $q = $this->query()->where('organization_id', $orgId)->where('name', $name);
        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }
        return $q->exists();
    }

    public function createValue(array $data): int
    {
        return $this->db->insert('resource_values', $data);
    }

    public function updateValue(int $id, array $data): void
    {
        $this->db->update('resource_values', $data, ['id' => $id]);
    }

    public function deleteValue(int $id): void
    {
        $this->db->delete('resource_values', ['id' => $id]);
    }

    public function deleteValuesByResource(int $resourceId): void
    {
        $this->db->execute("DELETE FROM `resource_values` WHERE `resource_id` = ?", [$resourceId]);
    }

    public function findValueById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM `resource_values` WHERE `id` = ?", [$id]);
    }

    public function getMaxValueSortOrder(int $resourceId): int
    {
        $result = $this->db->fetch(
            "SELECT MAX(`sort_order`) as max_sort FROM `resource_values` WHERE `resource_id` = ?",
            [$resourceId]
        );
        return (int) ($result['max_sort'] ?? 0);
    }
}
