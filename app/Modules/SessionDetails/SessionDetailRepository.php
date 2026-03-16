<?php

declare(strict_types=1);

namespace App\Modules\SessionDetails;

use App\Core\Database\Repository;

final class SessionDetailRepository extends Repository
{
    protected string $table = 'sessions';

    public function findByOrganization(int $orgId, ?string $search = null, ?int $categoryId = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($search) {
            $q->whereLike('session_name', "%{$search}%");
        }

        if ($categoryId) {
            $q->where('category_id', $categoryId);
        }

        $q->orderBy('session_name', 'ASC');
        return $q->paginate($page, $perPage);
    }

    public function findByCategory(int $orgId, int $categoryId): array
    {
        return $this->db->fetchAll(
            "SELECT id, session_name, session_tagline, is_active
             FROM `sessions`
             WHERE `organization_id` = ? AND `category_id` = ? AND `is_active` = 1
             ORDER BY `session_name` ASC",
            [$orgId, $categoryId]
        );
    }

    public function nameExistsInCategory(string $name, int $orgId, int $categoryId, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM `sessions` WHERE `session_name` = ? AND `organization_id` = ? AND `category_id` = ?";
        $params = [$name, $orgId, $categoryId];

        if ($excludeId) {
            $sql .= " AND `id` != ?";
            $params[] = $excludeId;
        }

        $row = $this->db->fetch($sql, $params);
        return ((int) ($row['cnt'] ?? 0)) > 0;
    }

    /**
     * Get facility IDs linked to a session detail.
     */
    public function getFacilityIds(int $sessionId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT `facility_id` FROM `session_facilities` WHERE `session_id` = ?",
            [$sessionId]
        );
        return array_map(fn($r) => (int) $r['facility_id'], $rows);
    }

    /**
     * Get facility IDs for multiple session details at once (bulk).
     */
    public function getFacilityIdsForSessions(array $sessionIds): array
    {
        if (empty($sessionIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($sessionIds), '?'));
        $rows = $this->db->fetchAll(
            "SELECT `session_id`, `facility_id` FROM `session_facilities` WHERE `session_id` IN ({$placeholders})",
            $sessionIds
        );
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['session_id']][] = (int) $row['facility_id'];
        }
        return $map;
    }

    /**
     * Sync facility associations for a session detail (replace all).
     */
    public function syncFacilities(int $sessionId, array $facilityIds): void
    {
        $this->db->query("DELETE FROM `session_facilities` WHERE `session_id` = ?", [$sessionId]);

        foreach ($facilityIds as $fid) {
            $this->db->insert('session_facilities', [
                'session_id'  => $sessionId,
                'facility_id' => (int) $fid,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
