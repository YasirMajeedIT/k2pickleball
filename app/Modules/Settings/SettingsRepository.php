<?php

declare(strict_types=1);

namespace App\Modules\Settings;

use App\Core\Database\Connection;

final class SettingsRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Get a setting value.
     */
    public function get(int $orgId, string $group, string $key, mixed $default = null): mixed
    {
        $row = $this->db->fetch(
            "SELECT `value`, `type` FROM `settings` WHERE `organization_id` = ? AND `group_name` = ? AND `key_name` = ?",
            [$orgId, $group, $key]
        );

        if ($row === null) {
            return $default;
        }

        return $this->castValue($row['value'], $row['type']);
    }

    /**
     * Get all settings in a group.
     */
    public function getGroup(int $orgId, string $group): array
    {
        $rows = $this->db->fetchAll(
            "SELECT `key_name`, `value`, `type` FROM `settings` WHERE `organization_id` = ? AND `group_name` = ? ORDER BY `key_name`",
            [$orgId, $group]
        );

        $result = [];
        foreach ($rows as $row) {
            $result[$row['key_name']] = $this->castValue($row['value'], $row['type']);
        }
        return $result;
    }

    /**
     * Get all settings in a group with full metadata (key, value, type, description).
     */
    public function getGroupDetailed(int $orgId, string $group): array
    {
        return $this->db->fetchAll(
            "SELECT `key_name` as `key`, `value`, `type`, `description` FROM `settings` WHERE `organization_id` = ? AND `group_name` = ? ORDER BY `key_name`",
            [$orgId, $group]
        );
    }

    /**
     * Get all settings for an organization.
     */
    public function getAll(int $orgId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT `group_name`, `key_name`, `value`, `type` FROM `settings` WHERE `organization_id` = ? ORDER BY `group_name`, `key_name`",
            [$orgId]
        );

        $result = [];
        foreach ($rows as $row) {
            $result[$row['group_name']][$row['key_name']] = $this->castValue($row['value'], $row['type']);
        }
        return $result;
    }

    /**
     * Set a setting value (upsert).
     */
    public function set(int $orgId, string $group, string $key, mixed $value, string $type = 'string'): void
    {
        $existing = $this->db->fetch(
            "SELECT `id` FROM `settings` WHERE `organization_id` = ? AND `group_name` = ? AND `key_name` = ?",
            [$orgId, $group, $key]
        );

        $stringValue = $this->serializeValue($value, $type);

        if ($existing) {
            $this->db->update('settings', [
                'value' => $stringValue,
                'type' => $type,
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $existing['id']]);
        } else {
            $this->db->insert('settings', [
                'organization_id' => $orgId,
                'group_name' => $group,
                'key_name' => $key,
                'value' => $stringValue,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Set multiple settings at once.
     */
    public function setMany(int $orgId, string $group, array $settings): void
    {
        foreach ($settings as $key => $value) {
            $type = is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : (is_array($value) ? 'json' : 'string'));
            $this->set($orgId, $group, $key, $value, $type);
        }
    }

    /**
     * Delete a setting.
     */
    public function delete(int $orgId, string $group, string $key): void
    {
        $this->db->query(
            "DELETE FROM `settings` WHERE `organization_id` = ? AND `group_name` = ? AND `key_name` = ?",
            [$orgId, $group, $key]
        );
    }

    private function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer', 'int' => (int) $value,
            'float', 'double' => (float) $value,
            'boolean', 'bool' => in_array(strtolower($value), ['1', 'true', 'yes'], true),
            'json', 'array' => json_decode($value, true) ?? [],
            default => $value,
        };
    }

    private function serializeValue(mixed $value, string $type): string
    {
        return match ($type) {
            'json', 'array' => is_string($value) ? $value : json_encode($value),
            'boolean', 'bool' => $value ? '1' : '0',
            default => (string) $value,
        };
    }
}
