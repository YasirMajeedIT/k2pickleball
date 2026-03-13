<?php

declare(strict_types=1);

namespace App\Modules\AuditLogs;

use App\Core\Database\Connection;

final class AuditLogRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function log(
        int $orgId,
        ?int $userId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): int {
        return $this->db->insert('activity_logs', [
            'organization_id' => $orgId,
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function findByOrganization(int $orgId, array $filters = [], int $page = 1, int $perPage = 50): array
    {
        $where = ["`al`.`organization_id` = ?"];
        $params = [$orgId];

        if (!empty($filters['user_id'])) {
            $where[] = "`al`.`user_id` = ?";
            $params[] = (int) $filters['user_id'];
        }
        if (!empty($filters['action'])) {
            $where[] = "`al`.`action` = ?";
            $params[] = $filters['action'];
        }
        if (!empty($filters['entity_type'])) {
            $where[] = "`al`.`entity_type` = ?";
            $params[] = $filters['entity_type'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "`al`.`created_at` >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "`al`.`created_at` <= ?";
            $params[] = $filters['date_to'];
        }

        $whereSql = implode(' AND ', $where);

        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `activity_logs` `al` WHERE {$whereSql}",
            $params
        );

        $offset = ($page - 1) * $perPage;
        $data = $this->db->fetchAll(
            "SELECT `al`.*, `u`.`first_name`, `u`.`last_name`, `u`.`email`
             FROM `activity_logs` `al`
             LEFT JOIN `users` `u` ON `u`.`id` = `al`.`user_id`
             WHERE {$whereSql}
             ORDER BY `al`.`created_at` DESC
             LIMIT ? OFFSET ?",
            [...$params, $perPage, $offset]
        );

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
    }

    public function findByEntity(string $entityType, int $entityId, int $page = 1, int $perPage = 50): array
    {
        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `activity_logs` WHERE `entity_type` = ? AND `entity_id` = ?",
            [$entityType, $entityId]
        );

        $offset = ($page - 1) * $perPage;
        $data = $this->db->fetchAll(
            "SELECT `al`.*, `u`.`first_name`, `u`.`last_name`, `u`.`email`
             FROM `activity_logs` `al`
             LEFT JOIN `users` `u` ON `u`.`id` = `al`.`user_id`
             WHERE `al`.`entity_type` = ? AND `al`.`entity_id` = ?
             ORDER BY `al`.`created_at` DESC
             LIMIT ? OFFSET ?",
            [$entityType, $entityId, $perPage, $offset]
        );

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
    }
}
