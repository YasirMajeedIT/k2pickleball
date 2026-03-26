<?php

declare(strict_types=1);

namespace App\Modules\Settings;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Database\Connection;
use App\Core\Security\Validator;

final class SettingsController extends Controller
{
    private SettingsRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new SettingsRepository($db);
    }

    /**
     * GET /api/settings — all settings for the org
     */
    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        $settings = $this->repo->getAll($orgId);
        return $this->success($settings);
    }

    /**
     * GET /api/settings/{group} — settings for a group
     */
    public function group(Request $request, string $group): Response
    {
        $orgId = $request->organizationId();
        $group = Sanitizer::slug($group);

        // ?detailed=1 returns full metadata (key, value, type, description)
        if ($request->input('detailed')) {
            $settings = $this->repo->getGroupDetailed($orgId, $group);
            return $this->success($settings);
        }

        $settings = $this->repo->getGroup($orgId, $group);
        return $this->success($settings);
    }

    /**
     * PUT /api/settings/{group} — update settings for a group
     */
    public function update(Request $request, string $group): Response
    {
        $orgId = $request->organizationId();
        $group = Sanitizer::slug($group);
        $data = $request->all();

        // Basic validation
        if (empty($data)) {
            return $this->validationError(['settings' => ['No settings provided']]);
        }

        $this->repo->setMany($orgId, $group, $data);

        $settings = $this->repo->getGroup($orgId, $group);
        return $this->success($settings, 'Settings updated');
    }

    /**
     * DELETE /api/settings/{group}/{key}
     */
    public function destroy(Request $request, string $group, string $key): Response
    {
        $orgId = $request->organizationId();
        $group = Sanitizer::slug($group);
        $key = Sanitizer::string($key);

        $this->repo->delete($orgId, $group, $key);
        return $this->success(null, 'Setting deleted');
    }
}
