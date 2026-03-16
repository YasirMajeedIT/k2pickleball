<?php

declare(strict_types=1);

namespace App\Modules\Categories;

use App\Core\Database\Repository;

final class CategoryRepository extends Repository
{
    protected string $table = 'categories';

    public function findByOrganization(int $orgId, ?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($search) {
            $q->whereLike('name', "%{$search}%");
        }

        $q->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC');
        return $q->paginate($page, $perPage);
    }

    public function nameExists(int $orgId, string $name, ?int $excludeId = null): bool
    {
        $q = $this->query()->where('organization_id', $orgId)->where('name', $name);
        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }
        return $q->exists();
    }

    public function getMaxSortOrder(int $orgId): int
    {
        $result = $this->db->fetch(
            "SELECT MAX(`sort_order`) as max_sort FROM `{$this->table}` WHERE `organization_id` = ?",
            [$orgId]
        );
        return (int) ($result['max_sort'] ?? 0);
    }
}
