<?php

declare(strict_types=1);

namespace App\Core\Extensions;

use App\Core\Database\Connection;

/**
 * Singleton registry for discovering and managing platform extensions.
 * Provides runtime checks for extension activation and facility-level settings.
 */
final class ExtensionRegistry
{
    private static ?self $instance = null;

    /** @var array<string, ExtensionInterface> slug → extension */
    private array $extensions = [];

    private Connection $db;

    private function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public static function getInstance(?Connection $db = null): self
    {
        if (self::$instance === null) {
            if ($db === null) {
                throw new \RuntimeException('ExtensionRegistry requires a Connection on first initialization');
            }
            self::$instance = new self($db);
            self::$instance->discoverExtensions();
        }
        return self::$instance;
    }

    /** Reset singleton (for testing). */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /** Register an extension instance manually. */
    public function register(ExtensionInterface $extension): void
    {
        $this->extensions[$extension->slug()] = $extension;
    }

    /** Get a registered extension by slug. */
    public function get(string $slug): ?ExtensionInterface
    {
        return $this->extensions[$slug] ?? null;
    }

    /** Get all registered extensions. */
    public function all(): array
    {
        return $this->extensions;
    }

    /** Check if an extension is installed and active for an organization. */
    public function isActiveForOrg(string $slug, int $organizationId): bool
    {
        $row = $this->db->fetch(
            "SELECT oe.is_active
             FROM organization_extensions oe
             JOIN extensions e ON oe.extension_id = e.id
             WHERE e.slug = ? AND oe.organization_id = ? AND oe.is_active = 1 AND e.is_active = 1",
            [$slug, $organizationId]
        );
        return !empty($row);
    }

    /** Get the organization_extensions record with joined extension info. */
    public function getOrgExtension(string $slug, int $organizationId): ?array
    {
        return $this->db->fetch(
            "SELECT oe.*, e.slug, e.name, e.settings_schema
             FROM organization_extensions oe
             JOIN extensions e ON oe.extension_id = e.id
             WHERE e.slug = ? AND oe.organization_id = ?",
            [$slug, $organizationId]
        );
    }

    /** Get per-facility settings for an extension. */
    public function getFacilitySettings(string $slug, int $organizationId, int $facilityId): ?array
    {
        $row = $this->db->fetch(
            "SELECT fes.settings
             FROM facility_extension_settings fes
             JOIN organization_extensions oe ON fes.organization_extension_id = oe.id
             JOIN extensions e ON oe.extension_id = e.id
             WHERE e.slug = ? AND oe.organization_id = ? AND fes.facility_id = ?",
            [$slug, $organizationId, $facilityId]
        );
        if ($row && $row['settings']) {
            return json_decode($row['settings'], true) ?: [];
        }
        return null;
    }

    /**
     * Auto-discover extension classes from the Modules/Extensions directory.
     * Convention: each extension lives in its own subdirectory and has an Extension class
     * e.g. app/Modules/Extensions/SquareTerminal/SquareTerminalExtension.php
     */
    private function discoverExtensions(): void
    {
        $extensionsDir = dirname(__DIR__, 2) . '/Modules/Extensions';
        if (!is_dir($extensionsDir)) {
            return;
        }

        $dirs = glob($extensionsDir . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $baseName = basename($dir);
            $className = "App\\Modules\\Extensions\\{$baseName}\\{$baseName}Extension";
            if (class_exists($className)) {
                $instance = new $className();
                if ($instance instanceof ExtensionInterface) {
                    $this->register($instance);
                }
            }
        }
    }
}
