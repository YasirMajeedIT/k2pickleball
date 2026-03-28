<?php

declare(strict_types=1);

namespace App\Modules\ScheduleSettings;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;
use App\Modules\Settings\SettingsRepository;

/**
 * Admin API for managing schedule page display settings.
 * GET  /api/schedule-settings          — Read all schedule_page settings
 * PUT  /api/schedule-settings          — Bulk-update schedule_page settings
 * GET  /api/schedule-settings/preview  — Preview data (resources, categories) for config UI
 */
final class ScheduleSettingsController extends Controller
{
    private Connection $db;
    private SettingsRepository $settings;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
        $this->settings = new SettingsRepository($db);
    }

    /**
     * GET /api/schedule-settings
     */
    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        $settings = $this->settings->getGroupDetailed($orgId, 'schedule_page');
        return $this->success($settings);
    }

    /**
     * PUT /api/schedule-settings
     */
    public function update(Request $request): Response
    {
        $orgId = $request->organizationId();
        $data = $request->all();

        if (empty($data)) {
            return $this->validationError(['settings' => ['No settings provided']]);
        }

        // Whitelist accepted keys
        $allowed = [
            'page_title', 'page_subtitle', 'default_view', 'enabled_views',
            'show_time', 'show_title', 'show_category', 'show_spots', 'show_price',
            'show_coach', 'show_description', 'show_courts', 'show_duration',
            'show_skill_level', 'show_session_number', 'show_hot_deal_badge',
            'show_early_bird_badge', 'show_category_filter', 'show_resource_filters',
            'resource_filter_ids', 'inline_booking', 'require_login',
            'payment_methods', 'color_scheme',
        ];

        $filtered = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed, true)) {
                $filtered[$key] = $value;
            }
        }

        if (empty($filtered)) {
            return $this->validationError(['settings' => ['No valid settings provided']]);
        }

        $this->settings->setMany($orgId, 'schedule_page', $filtered);

        $updated = $this->settings->getGroupDetailed($orgId, 'schedule_page');
        return $this->success($updated, 'Schedule page settings updated');
    }

    /**
     * GET /api/schedule-settings/preview
     * Returns resources + categories for the admin config UI.
     */
    public function preview(Request $request): Response
    {
        $orgId = $request->organizationId();

        // Resources with values
        $resources = $this->db->fetchAll(
            "SELECT r.`id`, r.`name`, r.`field_type`, r.`description`
             FROM `resources` r
             WHERE r.`organization_id` = ?
             ORDER BY r.`name` ASC",
            [$orgId]
        );
        foreach ($resources as &$res) {
            $res['values'] = $this->db->fetchAll(
                "SELECT `id`, `name`, `sort_order` FROM `resource_values`
                 WHERE `resource_id` = ? ORDER BY `sort_order` ASC",
                [$res['id']]
            );
        }

        // Categories
        $categories = $this->db->fetchAll(
            "SELECT `id`, `name`, `color`, `is_system`
             FROM `categories`
             WHERE `organization_id` = ? AND `is_active` = 1
             ORDER BY `name` ASC",
            [$orgId]
        );

        // Current settings
        $settings = $this->settings->getGroup($orgId, 'schedule_page');

        return $this->success([
            'resources' => $resources,
            'categories' => $categories,
            'settings' => $settings,
        ]);
    }
}
