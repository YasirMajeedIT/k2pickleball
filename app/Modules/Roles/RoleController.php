<?php

declare(strict_types=1);

namespace App\Modules\Roles;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class RoleController extends Controller
{
    private RoleRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new RoleRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        $roles = $this->repo->findForOrganization($orgId ?: null);
        return $this->success($roles);
    }

    public function show(Request $request, int $id): Response
    {
        $role = $this->repo->findWithPermissions($id);
        if (!$role) {
            throw new NotFoundException('Role not found');
        }
        return $this->success($role);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name' => 'required|string|max:100',
            'slug' => 'required|slug|max:100',
            'description' => 'nullable|string|max:500',
            'is_system' => 'nullable|boolean',
            'permissions' => 'nullable|array',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['slug'] = Sanitizer::slug($data['slug']);
        $data['organization_id'] = $request->organizationId() ?: null;
        $data['is_system'] = 0; // User-created roles are never system roles
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $permissionIds = $data['permissions'] ?? [];
        unset($data['permissions']);

        $id = $this->repo->create($data);

        if (!empty($permissionIds)) {
            $this->repo->syncPermissions($id, $permissionIds);
        }

        $role = $this->repo->findWithPermissions($id);
        return $this->created($role, 'Role created');
    }

    public function update(Request $request, int $id): Response
    {
        $role = $this->repo->findById($id);
        if (!$role) {
            throw new NotFoundException('Role not found');
        }

        $isSystem = !empty($role['is_system']);

        if ($isSystem && !$request->isSuperAdmin()) {
            return $this->error('Only super admins can modify system roles', 403);
        }

        if ($isSystem) {
            // System roles: only allow updating permissions
            $data = Validator::validate($request->all(), [
                'permissions' => 'nullable|array',
            ]);
            $permissionIds = $data['permissions'] ?? [];

            if (!empty($permissionIds)) {
                $this->repo->syncPermissions($id, $permissionIds);
            }
        } else {
            $data = Validator::validate($request->all(), [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string|max:500',
                'permissions' => 'nullable|array',
            ]);

            $data['name'] = Sanitizer::string($data['name']);

            $permissionIds = $data['permissions'] ?? [];
            unset($data['permissions']);

            $this->repo->update($id, $data);

            if (!empty($permissionIds)) {
                $this->repo->syncPermissions($id, $permissionIds);
            }
        }

        $role = $this->repo->findWithPermissions($id);
        return $this->success($role, 'Role updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $role = $this->repo->findById($id);
        if (!$role) {
            throw new NotFoundException('Role not found');
        }

        if (!empty($role['is_system'])) {
            return $this->error('System roles cannot be deleted', 403);
        }

        $this->repo->delete($id);
        return $this->success(null, 'Role deleted');
    }

    /**
     * GET /api/permissions — list all available permissions
     */
    public function permissions(Request $request): Response
    {
        $permissions = $this->repo->getAllPermissions();
        return $this->success($permissions);
    }
}
