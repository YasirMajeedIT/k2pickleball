<?php

declare(strict_types=1);

namespace App\Modules\Courts;

use App\Core\Database\Repository;

final class CourtRepository extends Repository
{
    protected string $table = 'courts';

    public function findByUuid(string $uuid): ?array
    {
        return $this->query()->where('uuid', $uuid)->first();
    }

    public function findByFacility(int $orgId, int $facilityId, ?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $where = [
            'c.organization_id = ?',
            'c.facility_id = ?',
        ];
        $params = [$orgId, $facilityId];

        if ($search) {
            $where[] = '(c.name LIKE ? OR f.name LIKE ?)';
            $like = "%{$search}%";
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) AS total
            FROM courts c
            INNER JOIN facilities f ON f.id = c.facility_id
            WHERE {$whereSql}";

        $dataSql = "SELECT
                c.*,
                f.name AS facility_name
            FROM courts c
            INNER JOIN facilities f ON f.id = c.facility_id
            WHERE {$whereSql}
            ORDER BY c.name ASC
            LIMIT {$perPage} OFFSET {$offset}";

        $count = $this->db->fetch($countSql, $params);
        $data = $this->db->fetchAll($dataSql, $params);

        return [
            'data' => $data,
            'total' => (int) ($count['total'] ?? 0),
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) ceil(((int) ($count['total'] ?? 0)) / max(1, $perPage)),
        ];
    }

    public function findAvailable(int $facilityId): array
    {
        return $this->query()
            ->where('facility_id', $facilityId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function findByOrganization(int $orgId, int $page = 1, int $perPage = 20): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $countSql = "SELECT COUNT(*) AS total
            FROM courts c
            INNER JOIN facilities f ON f.id = c.facility_id
            WHERE c.organization_id = ?";

        $dataSql = "SELECT
                c.*,
                f.name AS facility_name
            FROM courts c
            INNER JOIN facilities f ON f.id = c.facility_id
            WHERE c.organization_id = ?
            ORDER BY c.name ASC
            LIMIT {$perPage} OFFSET {$offset}";

        $count = $this->db->fetch($countSql, [$orgId]);
        $data = $this->db->fetchAll($dataSql, [$orgId]);

        return [
            'data' => $data,
            'total' => (int) ($count['total'] ?? 0),
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) ceil(((int) ($count['total'] ?? 0)) / max(1, $perPage)),
        ];
    }
}
