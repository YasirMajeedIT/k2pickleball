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

    public function findByFacility(int $facilityId, ?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('facility_id', $facilityId);

        if ($search) {
            $q->whereLike('name', "%{$search}%");
        }

        $q->orderBy('name', 'ASC');
        return $q->paginate($page, $perPage);
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
        return $this->query()
            ->where('organization_id', $orgId)
            ->orderBy('name', 'ASC')
            ->paginate($page, $perPage);
    }
}
