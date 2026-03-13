<?php

declare(strict_types=1);

namespace App\Modules\Files;

use App\Core\Database\Repository;

final class FileRepository extends Repository
{
    protected string $table = 'files';

    public function findByUuid(string $uuid): ?array
    {
        return $this->query()->where('uuid', $uuid)->first();
    }

    public function findByOrganization(int $orgId, ?string $type = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($type) {
            $q->where('context', $type);
        }

        $q->orderBy('created_at', 'DESC');
        return $q->paginate($page, $perPage);
    }

    public function totalSizeByOrganization(int $orgId): int
    {
        $result = $this->db->fetch(
            "SELECT COALESCE(SUM(`size`), 0) as total FROM `files` WHERE `organization_id` = ?",
            [$orgId]
        );
        return (int) ($result['total'] ?? 0);
    }
}
