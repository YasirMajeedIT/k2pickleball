<?php

declare(strict_types=1);

namespace App\Modules\GiftCertificates;

use App\Core\Database\Repository;

final class GiftCertificateRepository extends Repository
{
    protected string $table = 'gift_certificates';

    public function findByOrganization(int $orgId, ?string $search = null, ?int $facilityId = null, int $page = 1, int $perPage = 15): array
    {
        $query = $this->query()
            ->where('organization_id', $orgId);

        if ($search) {
            $query->whereRaw(
                '(certificate_name LIKE ? OR code LIKE ? OR buyer_email LIKE ? OR recipient_email LIKE ?)',
                ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"]
            );
        }

        if ($facilityId) {
            $query->where('facility_id', $facilityId);
        }

        $query->orderBy('created_at', 'DESC');

        return $query->paginate($page, $perPage);
    }

    public function codeExistsForFacility(int $facilityId, string $code, ?int $excludeId = null): bool
    {
        $query = $this->query()
            ->where('facility_id', $facilityId)
            ->where('code', $code);

        if ($excludeId) {
            $query->whereRaw('id != ?', [$excludeId]);
        }

        $result = $query->paginate(1, 1);
        return $result['total'] > 0;
    }

    public function findWithUsages(int $id): ?array
    {
        $record = $this->findById($id);
        if (!$record) {
            return null;
        }

        $record['usages'] = $this->db->fetchAll(
            "SELECT u.* FROM gift_certificate_usage u WHERE u.gift_certificate_id = ? ORDER BY u.usage_date DESC",
            [$id]
        );

        return $record;
    }

    public function addUsage(array $data): int
    {
        return $this->db->insert('gift_certificate_usage', $data);
    }

    public function generateUniqueCode(int $facilityId): string
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(5)));
        } while ($this->codeExistsForFacility($facilityId, $code));

        return $code;
    }
}
