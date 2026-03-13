<?php

declare(strict_types=1);

namespace App\Modules\Users;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;
use App\Core\Services\Config;

final class UserController extends Controller
{
    private UserRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new UserRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));
        $status = $request->input('status');

        $result = $this->repo->findByOrganization($orgId, $search ?: null, $status, $page, $perPage);

        // Strip password hashes from results
        foreach ($result['data'] as &$user) {
            unset($user['password_hash']);
        }

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $user = $this->repo->findWithRoles($id);
        if (!$user) {
            throw new NotFoundException('User not found');
        }
        return $this->success($user);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|password',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|phone',
            'status' => 'nullable|in:active,inactive,suspended,pending',
            'role_id' => 'nullable|integer',
        ]);

        $data['email'] = Sanitizer::email($data['email']);
        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);

        if ($this->repo->emailExists($data['email'])) {
            return $this->validationError(['email' => ['Email is already registered']]);
        }

        $orgId = $request->organizationId();
        $roleId = isset($data['role_id']) ? (int) $data['role_id'] : null;
        unset($data['role_id']);

        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $orgId;
        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, [
            'cost' => Config::get('auth.password.bcrypt_cost', 12),
        ]);
        unset($data['password']);
        $data['status'] = $data['status'] ?? 'active';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);

        // Assign role if provided
        if ($roleId && $orgId) {
            $this->repo->assignRole($id, $roleId, $orgId);
        }

        $user = $this->repo->findWithRoles($id);

        return $this->created($user, 'User created');
    }

    public function update(Request $request, int $id): Response
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        $data = Validator::validate($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'nullable|password',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|phone',
            'status' => 'nullable|in:active,inactive,suspended,pending',
            'role_id' => 'nullable|integer',
        ]);

        $data['email'] = Sanitizer::email($data['email']);
        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);

        if ($this->repo->emailExists($data['email'], $id)) {
            return $this->validationError(['email' => ['Email is already registered']]);
        }

        // Handle password update
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, [
                'cost' => Config::get('auth.password.bcrypt_cost', 12),
            ]);
        }
        unset($data['password']);

        // Handle role update
        $roleId = isset($data['role_id']) ? (int) $data['role_id'] : null;
        unset($data['role_id']);

        $this->repo->update($id, $data);

        if ($roleId) {
            $orgId = $request->organizationId();
            $this->repo->assignRole($id, $roleId, $orgId);
        }

        $user = $this->repo->findWithRoles($id);

        return $this->success($user, 'User updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        $this->repo->delete($id);
        return $this->success(null, 'User deleted');
    }

    /**
     * POST /api/users/{id}/roles
     */
    public function assignRole(Request $request, int $id): Response
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        $data = Validator::validate($request->all(), [
            'role_id' => 'required|integer',
        ]);

        $this->repo->assignRole($id, (int) $data['role_id'], $request->organizationId());

        $user = $this->repo->findWithRoles($id);
        return $this->success($user, 'Role assigned');
    }

    /**
     * DELETE /api/users/{id}/roles/{roleId}
     */
    public function removeRole(Request $request, int $id, int $roleId): Response
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        $this->repo->removeRole($id, $roleId, $request->organizationId());

        $user = $this->repo->findWithRoles($id);
        return $this->success($user, 'Role removed');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
