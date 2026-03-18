<?php

declare(strict_types=1);

namespace App\Modules\Extensions;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;
use App\Core\Extensions\ExtensionRegistry;
use App\Core\Security\Validator;

/**
 * Org-admin extension management.
 * Browse catalog, install/uninstall, configure org and facility settings.
 */
final class ExtensionController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * GET /api/extensions/catalog
     * List all extensions visible to org admins (platform-active only).
     */
    public function catalog(Request $request): Response
    {
        $orgId = $request->organizationId();

        $extensions = $this->db->fetchAll(
            "SELECT e.id, e.name, e.slug, e.description, e.version, e.category, e.icon,
                    e.price_monthly, e.price_yearly, e.settings_schema,
                    oe.id AS org_ext_id, oe.is_active AS installed_active, oe.installed_at, oe.settings AS org_settings
             FROM extensions e
             LEFT JOIN organization_extensions oe ON oe.extension_id = e.id AND oe.organization_id = ?
             WHERE e.is_active = 1
             ORDER BY e.sort_order ASC, e.name ASC",
            [$orgId]
        );

        foreach ($extensions as &$ext) {
            $ext['is_installed'] = !empty($ext['org_ext_id']) && !empty($ext['installed_active']);
            $ext['settings_schema'] = $ext['settings_schema'] ? json_decode($ext['settings_schema'], true) : [];
            $ext['org_settings'] = $ext['org_settings'] ? json_decode($ext['org_settings'], true) : [];
        }

        return $this->success($extensions);
    }

    /**
     * POST /api/extensions/{slug}/install
     */
    public function install(Request $request, string $slug): Response
    {
        $orgId = $request->organizationId();

        $ext = $this->db->fetch("SELECT * FROM extensions WHERE slug = ? AND is_active = 1", [$slug]);
        if (!$ext) {
            throw new NotFoundException('Extension not found or inactive');
        }

        $existing = $this->db->fetch(
            "SELECT id, is_active FROM organization_extensions WHERE organization_id = ? AND extension_id = ?",
            [$orgId, $ext['id']]
        );

        if ($existing && $existing['is_active']) {
            return $this->error('Extension already installed', 409);
        }

        if ($existing) {
            $this->db->update('organization_extensions', [
                'is_active' => 1,
                'installed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $existing['id']]);
        } else {
            $this->db->insert('organization_extensions', [
                'organization_id' => $orgId,
                'extension_id' => $ext['id'],
                'is_active' => 1,
                'settings' => null,
                'installed_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Lifecycle hook
        $registry = ExtensionRegistry::getInstance($this->db);
        $extClass = $registry->get($slug);
        if ($extClass) {
            $extClass->onInstall($orgId);
        }

        return $this->success(null, 'Extension installed');
    }

    /**
     * POST /api/extensions/{slug}/uninstall
     */
    public function uninstall(Request $request, string $slug): Response
    {
        $orgId = $request->organizationId();

        $ext = $this->db->fetch("SELECT * FROM extensions WHERE slug = ?", [$slug]);
        if (!$ext) {
            throw new NotFoundException('Extension not found');
        }

        $oe = $this->db->fetch(
            "SELECT id FROM organization_extensions WHERE organization_id = ? AND extension_id = ? AND is_active = 1",
            [$orgId, $ext['id']]
        );
        if (!$oe) {
            throw new NotFoundException('Extension not installed');
        }

        // Lifecycle hook
        $registry = ExtensionRegistry::getInstance($this->db);
        $extClass = $registry->get($slug);
        if ($extClass) {
            $extClass->onUninstall($orgId);
        }

        $this->db->update('organization_extensions', [
            'is_active' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $oe['id']]);

        // Clean up facility-level settings
        $this->db->query(
            "DELETE FROM facility_extension_settings WHERE organization_extension_id = ?",
            [$oe['id']]
        );

        return $this->success(null, 'Extension uninstalled');
    }

    /**
     * GET /api/extensions/{slug}/settings
     * Get extension settings for the org + all facility settings.
     */
    public function settings(Request $request, string $slug): Response
    {
        $orgId = $request->organizationId();

        $oe = $this->db->fetch(
            "SELECT oe.*, e.settings_schema, e.name, e.slug
             FROM organization_extensions oe
             JOIN extensions e ON oe.extension_id = e.id
             WHERE e.slug = ? AND oe.organization_id = ? AND oe.is_active = 1",
            [$slug, $orgId]
        );
        if (!$oe) {
            throw new NotFoundException('Extension not installed');
        }

        // Load ALL org facilities, then merge any existing extension settings
        $allFacilities = $this->db->fetchAll(
            "SELECT id, name FROM facilities WHERE organization_id = ? ORDER BY name ASC",
            [$orgId]
        );

        $facilitySettingsRows = $this->db->fetchAll(
            "SELECT fes.facility_id, fes.settings
             FROM facility_extension_settings fes
             WHERE fes.organization_extension_id = ?",
            [$oe['id']]
        );

        $settingsMap = [];
        foreach ($facilitySettingsRows as $row) {
            $settingsMap[(int)$row['facility_id']] = $row['settings'] ? json_decode($row['settings'], true) : [];
        }

        $facilities = [];
        foreach ($allFacilities as $f) {
            $facilities[] = [
                'id' => (int)$f['id'],
                'name' => $f['name'],
                'settings' => $settingsMap[(int)$f['id']] ?? [],
            ];
        }

        return $this->success([
            'extension' => [
                'name' => $oe['name'],
                'slug' => $oe['slug'],
                'settings_schema' => $oe['settings_schema'] ? json_decode($oe['settings_schema'], true) : [],
            ],
            'org_settings' => $oe['settings'] ? json_decode($oe['settings'], true) : [],
            'facilities' => $facilities,
        ]);
    }

    /**
     * PUT /api/extensions/{slug}/settings
     * Update org-level extension settings.
     */
    public function updateSettings(Request $request, string $slug): Response
    {
        $orgId = $request->organizationId();

        $oe = $this->db->fetch(
            "SELECT oe.id FROM organization_extensions oe
             JOIN extensions e ON oe.extension_id = e.id
             WHERE e.slug = ? AND oe.organization_id = ? AND oe.is_active = 1",
            [$slug, $orgId]
        );
        if (!$oe) {
            throw new NotFoundException('Extension not installed');
        }

        $settings = $request->input('settings', []);

        // Let extension validate
        $registry = ExtensionRegistry::getInstance($this->db);
        $extClass = $registry->get($slug);
        if ($extClass) {
            $settings = $extClass->validateSettings($settings);
        }

        $this->db->update('organization_extensions', [
            'settings' => json_encode($settings),
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $oe['id']]);

        return $this->success(null, 'Settings updated');
    }

    /**
     * PUT /api/extensions/{slug}/facilities/{facilityId}/settings
     * Update per-facility extension settings.
     */
    public function updateFacilitySettings(Request $request, string $slug, int $facilityId): Response
    {
        $orgId = $request->organizationId();

        $oe = $this->db->fetch(
            "SELECT oe.id FROM organization_extensions oe
             JOIN extensions e ON oe.extension_id = e.id
             WHERE e.slug = ? AND oe.organization_id = ? AND oe.is_active = 1",
            [$slug, $orgId]
        );
        if (!$oe) {
            throw new NotFoundException('Extension not installed');
        }

        // Verify facility belongs to this org
        $facility = $this->db->fetch(
            "SELECT id FROM facilities WHERE id = ? AND organization_id = ?",
            [$facilityId, $orgId]
        );
        if (!$facility) {
            throw new NotFoundException('Facility not found');
        }

        $settings = $request->input('settings', []);

        $existing = $this->db->fetch(
            "SELECT id FROM facility_extension_settings WHERE organization_extension_id = ? AND facility_id = ?",
            [$oe['id'], $facilityId]
        );

        if ($existing) {
            $this->db->update('facility_extension_settings', [
                'settings' => json_encode($settings),
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $existing['id']]);
        } else {
            $this->db->insert('facility_extension_settings', [
                'organization_extension_id' => $oe['id'],
                'facility_id' => $facilityId,
                'settings' => json_encode($settings),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->success(null, 'Facility settings updated');
    }

    /**
     * GET /api/extensions/check/{slug}
     * Quick check if extension is active for current org.
     */
    public function check(Request $request, string $slug): Response
    {
        $orgId = $request->organizationId();
        $registry = ExtensionRegistry::getInstance($this->db);
        $active = $registry->isActiveForOrg($slug, $orgId);

        return $this->success([
            'slug' => $slug,
            'is_active' => $active,
        ]);
    }
}
