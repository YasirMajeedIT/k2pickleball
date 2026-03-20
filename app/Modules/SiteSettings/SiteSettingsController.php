<?php

declare(strict_types=1);

namespace App\Modules\SiteSettings;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;

final class SiteSettingsController extends Controller
{
    private SiteSettingsRepository $repo;

    public function __construct(Connection $db)
    {
        $this->repo = new SiteSettingsRepository($db);
    }

    /**
     * Platform admin — get all site settings.
     */
    public function index(Request $request): Response
    {
        $all = $this->repo->getAll();
        $out = [];
        foreach ($all as $key => $row) {
            $out[] = [
                'key'         => $key,
                'value'       => $row['typed_value'],
                'type'        => $row['setting_type'],
                'description' => $row['description'] ?? '',
            ];
        }
        return $this->success($out);
    }

    /**
     * Platform admin — update site settings (bulk).
     * Expects { settings: { key: value, ... } }
     */
    public function update(Request $request): Response
    {
        $settings = $request->input('settings');
        if (!is_array($settings)) {
            return $this->error('Invalid settings payload', 422);
        }

        // Whitelist of allowed setting keys with their types
        $allowed = [
            'maintenance_mode'            => 'boolean',
            'maintenance_message'         => 'string',
            'password_protection_enabled' => 'boolean',
            'site_password'               => 'string',
            'maintenance_allowed_ips'     => 'json',
        ];

        foreach ($settings as $key => $value) {
            if (!isset($allowed[$key])) {
                continue; // skip unknown keys
            }
            $type = $allowed[$key];

            // Sanitize string values
            if ($type === 'string' && is_string($value)) {
                $value = Sanitizer::string($value);
            }

            $this->repo->set($key, $value, $type);
        }

        return $this->success(['message' => 'Site settings updated']);
    }

    /**
     * PUBLIC — check site status (for maintenance gate).
     * Returns minimal data about site availability.
     */
    public function status(Request $request): Response
    {
        $maintenance = $this->repo->get('maintenance_mode', false);
        $passwordProtected = $this->repo->get('password_protection_enabled', false);

        return $this->success([
            'maintenance_mode'   => $maintenance,
            'maintenance_message' => $maintenance ? $this->repo->get('maintenance_message', '') : null,
            'password_protected' => $passwordProtected,
        ]);
    }

    /**
     * PUBLIC — verify site access password.
     */
    public function verifyPassword(Request $request): Response
    {
        Validator::validate($request->all(), [
            'password' => 'required|string|max:255',
        ]);

        $sitePassword = $this->repo->get('site_password', '');
        $provided = $request->input('password');

        if ($sitePassword !== '' && hash_equals($sitePassword, $provided)) {
            return $this->success(['granted' => true]);
        }

        return $this->error('Invalid password', 403);
    }
}
