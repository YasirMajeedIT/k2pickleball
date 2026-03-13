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

    public function __construct(Connection $db)
    {
        parent::__construct();
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

        return $this->created($org, 'Organization created');
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

        $this->repo->delete($id);

        return $this->success(null, 'Organization deleted');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
