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
use App\Core\Auth\AuthService;
use App\Core\Services\Container;
use App\Modules\AuditLogs\AuditLogRepository;

final class UserController extends Controller
{
    private UserRepository $repo;
    private AuditLogRepository $auditLog;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new UserRepository($db);
        $this->auditLog = new AuditLogRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));
        $status = $request->input('status');

        $result = $this->repo->findByOrganization($orgId, $search ?: null, $status, $page, $perPage);

        foreach ($result['data'] as &$user) {
            unset($user['password_hash']);
        }
        unset($user);

        // Eager-load facilities for all users on this page
        $userIds = array_column($result['data'], 'id');
        $facilitiesMap = $this->repo->findFacilitiesForUsers($userIds);
        foreach ($result['data'] as &$user) {
            $user['facilities'] = $facilitiesMap[$user['id']] ?? [];
        }
        unset($user);

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
        $facilityIds = array_filter(array_map('intval', (array) ($request->input('facility_ids', []))));
        $sendInvite = (bool) ($request->input('send_invite', true));

        $rules = [
            'email' => 'required|email|max:255',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|phone',
            'professional_title' => 'nullable|string|max:100',
            'membership_id' => 'nullable|string|max:50',
            'certification_level' => 'nullable|string|max:100',
            'years_experience' => 'nullable|integer|min:0|max:60',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|phone',
            'bio' => 'nullable|string|max:5000',
            'status' => 'nullable|in:active,inactive,suspended,pending',
            'role_id' => 'nullable|integer',
        ];

        // Password is optional — if omitted, user gets an invitation email
        if ($request->input('password')) {
            $rules['password'] = 'required|password';
        }

        $data = Validator::validate($request->all(), $rules);

        $data['email'] = Sanitizer::email($data['email']);
        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);
        $data['phone'] = isset($data['phone']) ? Sanitizer::string($data['phone']) : null;
        $data['professional_title'] = isset($data['professional_title']) ? Sanitizer::string($data['professional_title']) : null;
        $data['membership_id'] = isset($data['membership_id']) ? Sanitizer::string($data['membership_id']) : null;
        $data['certification_level'] = isset($data['certification_level']) ? Sanitizer::string($data['certification_level']) : null;
        $data['years_experience'] = isset($data['years_experience']) && $data['years_experience'] !== '' ? (int) $data['years_experience'] : null;
        $data['emergency_contact_name'] = isset($data['emergency_contact_name']) ? Sanitizer::string($data['emergency_contact_name']) : null;
        $data['emergency_contact_phone'] = isset($data['emergency_contact_phone']) ? Sanitizer::string($data['emergency_contact_phone']) : null;
        $data['bio'] = isset($data['bio']) ? trim((string) $data['bio']) : null;

        if ($this->repo->emailExists($data['email'])) {
            return $this->validationError(['email' => ['Email is already registered']]);
        }

        $orgId = $request->organizationId();
        $roleId = isset($data['role_id']) ? (int) $data['role_id'] : null;
        unset($data['role_id']);

        // Platform super-admins may provide an explicit organization_id in the body
        if (!$orgId && !empty($request->input('organization_id'))) {
            $orgId = (int) $request->input('organization_id');
        }

        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $orgId;

        // If password provided, hash it and mark active; otherwise invitation flow
        $isInvitation = empty($data['password']);
        if (!$isInvitation) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, [
                'cost' => Config::get('auth.password.bcrypt_cost', 12),
            ]);
            $data['status'] = $data['status'] ?? 'active';
            $data['email_verified_at'] = gmdate('Y-m-d H:i:s');
        } else {
            $data['password_hash'] = '';
            $data['status'] = 'pending';
        }
        unset($data['password']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);

        if ($roleId) {
            $this->repo->assignRole($id, $roleId, $orgId);
        }

        if (!empty($facilityIds) && $orgId) {
            $this->repo->syncFacilities($id, $facilityIds, $orgId);
        }

        // Send invitation email for password-less users
        if ($isInvitation && $sendInvite) {
            try {
                $container = Container::getInstance();
                $authService = $container->make(AuthService::class);
                $authService->sendInvitation($data['email'], $data['first_name']);
            } catch (\Throwable $e) {
                error_log('[Invite] Failed to send invitation to ' . $data['email'] . ': ' . $e->getMessage());
            }
        }

        $user = $this->repo->findWithRoles($id);

        $this->auditLog->log(
            $request->organizationId(),
            $request->userId(),
            'created',
            'user',
            $id,
            null,
            $user,
            $request->ip(),
            $request->header('User-Agent')
        );

        return $this->created($user, $isInvitation ? 'User created — invitation email sent' : 'User created');
    }

    /**
     * POST /api/users/{id}/resend-invite
     * Resend invitation email to a pending user.
     */
    public function resendInvite(Request $request, int $id): Response
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        if (!empty($user['email_verified_at'])) {
            return $this->error('User has already accepted their invitation.', 422);
        }

        try {
            $container = Container::getInstance();
            $authService = $container->make(AuthService::class);
            $authService->sendInvitation($user['email'], $user['first_name']);
        } catch (\Throwable $e) {
            return $this->error('Failed to send invitation: ' . $e->getMessage(), 500);
        }

        $this->auditLog->log(
            $request->organizationId(),
            $request->userId(),
            'resent_invite',
            'user',
            $id,
            null,
            ['email' => $user['email']],
            $request->ip(),
            $request->header('User-Agent')
        );

        return $this->success(null, 'Invitation email resent');
    }

    public function update(Request $request, int $id): Response
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        $facilityIds = array_filter(array_map('intval', (array) ($request->input('facility_ids', []))));
        $syncFacilities = $request->input('facility_ids') !== null;

        $data = Validator::validate($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'nullable|password',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|phone',
            'professional_title' => 'nullable|string|max:100',
            'membership_id' => 'nullable|string|max:50',
            'certification_level' => 'nullable|string|max:100',
            'years_experience' => 'nullable|integer|min:0|max:60',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|phone',
            'bio' => 'nullable|string|max:5000',
            'status' => 'nullable|in:active,inactive,suspended',
            'role_id' => 'nullable|integer',
        ]);

        $data['email'] = Sanitizer::email($data['email']);
        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);
        $data['phone'] = isset($data['phone']) ? Sanitizer::string($data['phone']) : null;
        $data['professional_title'] = isset($data['professional_title']) ? Sanitizer::string($data['professional_title']) : null;
        $data['membership_id'] = isset($data['membership_id']) ? Sanitizer::string($data['membership_id']) : null;
        $data['certification_level'] = isset($data['certification_level']) ? Sanitizer::string($data['certification_level']) : null;
        $data['years_experience'] = isset($data['years_experience']) && $data['years_experience'] !== '' ? (int) $data['years_experience'] : null;
        $data['emergency_contact_name'] = isset($data['emergency_contact_name']) ? Sanitizer::string($data['emergency_contact_name']) : null;
        $data['emergency_contact_phone'] = isset($data['emergency_contact_phone']) ? Sanitizer::string($data['emergency_contact_phone']) : null;
        $data['bio'] = isset($data['bio']) ? trim((string) $data['bio']) : null;

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

        if ($syncFacilities) {
            $orgId = $request->organizationId();
            $this->repo->syncFacilities($id, $facilityIds, $orgId);
        }

        $user = $this->repo->findWithRoles($id);

        $this->auditLog->log(
            $request->organizationId(),
            $request->userId(),
            'updated',
            'user',
            $id,
            null,
            $user,
            $request->ip(),
            $request->header('User-Agent')
        );

        return $this->success($user, 'User updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        $this->auditLog->log(
            $request->organizationId(),
            $request->userId(),
            'deleted',
            'user',
            $id,
            $user,
            null,
            $request->ip(),
            $request->header('User-Agent')
        );

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
