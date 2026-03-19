<?php

declare(strict_types=1);

namespace App\Modules\Organizations;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class OrganizationController extends Controller
{
    private OrganizationRepository $repo;
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
        $this->repo = new OrganizationRepository($db);
    }

    /**
     * GET /api/organizations
     */
    public function index(Request $request): Response
    {
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));

        if ($request->isSuperAdmin()) {
            $result = $this->repo->findAllOrgs($search ?: null, $page, $perPage);
        } else {
            $result = $this->repo->findActive($search ?: null, $page, $perPage);
        }

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    /**
     * GET /api/organizations/{id}
     */
    public function show(Request $request, int $id): Response
    {
        $org = $this->repo->findWithDomains($id);

        if (!$org) {
            throw new NotFoundException('Organization not found');
        }

        return $this->success($org);
    }

    /**
     * POST /api/organizations
     */
    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|slug|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|phone',
            'website' => 'nullable|url|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'timezone' => 'nullable|timezone',
            'settings' => 'nullable|json',
        ]);

        // Sanitize
        $data['name'] = Sanitizer::string($data['name']);
        $data['slug'] = Sanitizer::slug($data['slug']);
        $data['email'] = Sanitizer::email($data['email']);

        // Map zip_code to zip
        $data['zip'] = $data['zip_code'] ?? null;
        unset($data['zip_code']);

        // Check slug uniqueness
        if ($this->repo->slugExists($data['slug'])) {
            return $this->validationError(['slug' => ['Slug is already in use']]);
        }

        // Generate UUID
        $data['uuid'] = $this->generateUuid();
        $data['status'] = 'active';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);
        $org = $this->repo->findById($id);

        // Seed mandatory system categories for the new organization
        $this->seedSystemCategories($id);

        // Link the creating user to this organization if they don't have one
        $userId = $request->getAttribute('user_id');
        if ($userId) {
            $user = $request->getAttribute('user');
            if (empty($user['organization_id'])) {
                $this->db->query(
                    "UPDATE `users` SET `organization_id` = ?, `updated_at` = NOW() WHERE `id` = ?",
                    [$id, $userId]
                );
            }
        }

        return $this->created($org, 'Organization created');
    }

    /**
     * Seed mandatory system categories for a new organization.
     */
    private function seedSystemCategories(int $orgId): void
    {
        $systemCategories = [
            [
                'system_slug' => 'book-a-court',
                'name'        => 'Book a Court',
                'color'       => '#d4af37',
                'description' => 'Reserve a court for your group. Pick your date, time, and court — instant confirmation.',
            ],
        ];

        foreach ($systemCategories as $i => $cat) {
            $exists = $this->db->fetch(
                "SELECT `id` FROM `categories` WHERE `organization_id` = ? AND `system_slug` = ?",
                [$orgId, $cat['system_slug']]
            );
            if ($exists) continue;

            $uuid = $this->generateUuid();
            $this->db->query(
                "INSERT INTO `categories` (`uuid`, `organization_id`, `name`, `color`, `sort_order`, `is_taxable`, `is_system`, `system_slug`, `is_active`, `description`, `created_at`, `updated_at`)
                 VALUES (?, ?, ?, ?, ?, 0, 1, ?, 1, ?, NOW(), NOW())",
                [$uuid, $orgId, $cat['name'], $cat['color'], $i + 1, $cat['system_slug'], $cat['description']]
            );
        }
    }

    /**
     * PUT /api/organizations/{id}
     */
    public function update(Request $request, int $id): Response
    {
        $org = $this->repo->findById($id);
        if (!$org) {
            throw new NotFoundException('Organization not found');
        }

        $data = Validator::validate($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|slug|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|phone',
            'website' => 'nullable|url|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'timezone' => 'nullable|timezone',
            'settings' => 'nullable|json',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['slug'] = Sanitizer::slug($data['slug']);
        $data['email'] = Sanitizer::email($data['email']);

        // Map zip_code to zip
        $data['zip'] = $data['zip_code'] ?? null;
        unset($data['zip_code']);

        if ($this->repo->slugExists($data['slug'], $id)) {
            return $this->validationError(['slug' => ['Slug is already in use']]);
        }

        $this->repo->update($id, $data);
        $org = $this->repo->findById($id);

        return $this->success($org, 'Organization updated');
    }

    /**
     * DELETE /api/organizations/{id}
     */
    public function destroy(Request $request, int $id): Response
    {
        $org = $this->repo->findById($id);
        if (!$org) {
            throw new NotFoundException('Organization not found');
        }

        $counts = $this->repo->getRelatedCounts($id);
        if ($counts['subscriptions'] > 0) {
            return $this->error('Cannot delete organization with active subscriptions. Cancel subscriptions first.', 409);
        }
        if ($counts['users'] > 0) {
            return $this->error(
                "Organization has {$counts['users']} user(s) and {$counts['facilities']} facility(ies). Use force=1 to confirm deletion.",
                409
            );
        }

        $this->repo->delete($id);

        return $this->success(null, 'Organization deleted');
    }

    /**
     * PATCH /api/organizations/{id}/status
     */
    public function updateStatus(Request $request, int $id): Response
    {
        $org = $this->repo->findById($id);
        if (!$org) {
            throw new NotFoundException('Organization not found');
        }

        $data = Validator::validate($request->all(), [
            'status' => 'required|in:active,inactive,suspended,trial,cancelled',
        ]);

        $this->repo->updateStatus($id, $data['status']);
        $org = $this->repo->findById($id);

        return $this->success($org, 'Organization status updated to ' . $data['status']);
    }

    /**
     * GET /api/organizations/{id}/details — full details for platform view
     */
    public function details(Request $request, int $id): Response
    {
        $org = $this->repo->findWithDetails($id);
        if (!$org) {
            throw new NotFoundException('Organization not found');
        }
        return $this->success($org);
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
