<?php

declare(strict_types=1);

namespace App\Modules\Navigation;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;

final class NavigationController extends Controller
{
    private NavigationRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new NavigationRepository($db);
    }

    /**
     * GET /api/navigation — list all items for admin management.
     */
    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        $items = $this->repo->findAllForOrganization($orgId);
        return $this->success($items);
    }

    /**
     * PUT /api/navigation — bulk update/sync navigation items.
     * Expects: { items: [ { label, url, type, sort_order, is_visible, ... }, ... ] }
     */
    public function sync(Request $request): Response
    {
        $orgId = $request->organizationId();
        $data = Validator::validate($request->all(), [
            'items' => 'required|array',
        ]);

        $items = $data['items'] ?? [];
        $sanitized = [];

        foreach ($items as $item) {
            $sanitized[] = [
                'label'           => Sanitizer::string($item['label'] ?? ''),
                'url'             => $item['url'] ?? null,
                'type'            => $item['type'] ?? 'link',
                'target'          => $item['target'] ?? '_self',
                'icon'            => $item['icon'] ?? null,
                'parent_id'       => $item['parent_id'] ?? null,
                'category_id'     => $item['category_id'] ?? null,
                'is_system'       => (int) ($item['is_system'] ?? 0),
                'system_key'      => $item['system_key'] ?? null,
                'is_visible'      => (int) ($item['is_visible'] ?? 1),
                'sort_order'      => (int) ($item['sort_order'] ?? 0),
                'visibility_rule' => $item['visibility_rule'] ?? null,
            ];
        }

        $this->repo->syncNavigation($orgId, $sanitized);

        $updated = $this->repo->findAllForOrganization($orgId);
        return $this->success($updated, 'Navigation updated');
    }
}
