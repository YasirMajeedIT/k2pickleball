<?php

declare(strict_types=1);

namespace App\Modules\Facilities;

use App\Core\Database\Repository;

final class FacilityRepository extends Repository
{
    protected string $table = 'facilities';

    public function findByUuid(string $uuid): ?array
    {
        return $this->query()->where('uuid', $uuid)->first();
    }

    public function findByOrganization(int $orgId, ?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($search) {
            $q->whereLike('name', "%{$search}%");
        }

        $q->orderBy('name', 'ASC');
        return $q->paginate($page, $perPage);
    }

    public function findActiveByOrganization(int $orgId): array
    {
        return $this->query()
            ->where('organization_id', $orgId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function findWithCourts(int $id): ?array
    {
        $facility = $this->findById($id);
        if (!$facility) {
            return null;
        }

        $facility['courts'] = $this->db->fetchAll(
            "SELECT * FROM `courts` WHERE `facility_id` = ? ORDER BY `name` ASC",
            [$id]
        );

        return $facility;
    }
}
