<?php

declare(strict_types=1);

namespace App\Modules\Players;

use App\Core\Database\Repository;

final class PlayerRepository extends Repository
{
    protected string $table = 'players';

    public function findByOrganization(int $orgId, ?string $search = null, ?string $status = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($search) {
            $q->whereRaw("(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)", ["%{$search}%", "%{$search}%", "%{$search}%"]);
        }

        if ($status) {
            $q->where('status', $status);
        }

        $q->orderBy('created_at', 'DESC');
        return $q->paginate($page, $perPage);
    }

    public function emailExists(int $orgId, string $email, ?int $excludeId = null): bool
    {
        $q = $this->query()->where('organization_id', $orgId)->where('email', $email);
        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }
        return $q->exists();
    }
}
