<?php

declare(strict_types=1);

namespace App\Modules\SiteSettings;

use App\Core\Database\Connection;

final class SiteSettingsRepository
{
    private Connection $db;

    /** In-memory cache for the current request */
    private ?array $cache = null;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Get a single setting value, cast to its declared type.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->getAll();
        if (!isset($all[$key])) {
            return $default;
        }
        return $all[$key]['typed_value'];
    }

    /**
     * Get all site settings as key => row map. Cached per request.
     */
    public function getAll(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $rows = $this->db->fetchAll('SELECT * FROM site_settings ORDER BY setting_key');
        $map = [];
        foreach ($rows as $row) {
            $row['typed_value'] = $this->castValue($row['setting_value'], $row['setting_type']);
            $map[$row['setting_key']] = $row;
        }
        $this->cache = $map;
        return $map;
    }

    /**
     * Update a single setting (upsert).
     */
    public function set(string $key, mixed $value, string $type = 'string', ?string $description = null): void
    {
        $stringValue = $this->serializeValue($value, $type);

        $existing = $this->db->fetch(
            'SELECT id FROM site_settings WHERE setting_key = ?',
            [$key]
        );

        if ($existing) {
            $data = ['setting_value' => $stringValue, 'setting_type' => $type];
            if ($description !== null) {
                $data['description'] = $description;
            }
            $this->db->update('site_settings', $data, ['id' => $existing['id']]);
        } else {
            $this->db->insert('site_settings', [
                'setting_key'   => $key,
                'setting_value' => $stringValue,
                'setting_type'  => $type,
                'description'   => $description ?? '',
            ]);
        }

        $this->cache = null; // bust cache
    }

    /**
     * Bulk update settings from an associative array.
     */
    public function setMany(array $settings): void
    {
        foreach ($settings as $key => $item) {
            $value = $item['value'] ?? $item;
            $type  = $item['type'] ?? 'string';
            $desc  = $item['description'] ?? null;
            $this->set($key, $value, $type, $desc);
        }
    }

    private function castValue(?string $raw, string $type): mixed
    {
        if ($raw === null) {
            return null;
        }
        return match ($type) {
            'boolean' => in_array($raw, ['1', 'true', 'yes'], true),
            'integer' => (int) $raw,
            'json'    => json_decode($raw, true) ?? [],
            default   => $raw,
        };
    }

    private function serializeValue(mixed $value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json'    => is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE),
            default   => (string) $value,
        };
    }
}
