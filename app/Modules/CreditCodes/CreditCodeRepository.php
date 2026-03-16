<?php

declare(strict_types=1);

namespace App\Modules\CreditCodes;

use App\Core\Database\Repository;

final class CreditCodeRepository extends Repository
{
    protected string $table = 'credit_codes';

    public function findByOrganization(int $orgId, ?string $search = null, ?int $facilityId = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($search) {
            $q->whereRaw("(name LIKE ? OR code LIKE ?)", ["%{$search}%", "%{$search}%"]);
        }

        if ($facilityId) {
            $q->where('facility_id', $facilityId);
        }

        $q->orderBy('created_at', 'DESC');
        return $q->paginate($page, $perPage);
    }

    public function codeExistsForFacility(int $facilityId, string $code, ?int $excludeId = null): bool
    {
        $q = $this->query()->where('facility_id', $facilityId)->where('code', $code);
        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }
        return $q->exists();
    }

    public function findWithUsages(int $id): ?array
    {
        $record = $this->findById($id);
        if (!$record) {
            return null;
        }

        $record['usages'] = $this->db->fetchAll(
            "SELECT u.*, p.first_name, p.last_name, p.email as player_email
             FROM `credit_code_usages` u
             LEFT JOIN `players` p ON p.id = u.player_id
             WHERE u.credit_code_id = ?
             ORDER BY u.used_at DESC",
            [$id]
        );

        return $record;
    }

    public function addUsage(array $data): int
    {
        return $this->db->insert('credit_code_usages', $data);
    }

    public function generateUniqueCode(int $facilityId): string
    {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while ($this->codeExistsForFacility($facilityId, $code));

        return $code;
    }
}
