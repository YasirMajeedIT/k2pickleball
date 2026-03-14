<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Auth\JwtService;
use App\Core\Database\Connection;
use App\Core\Http\MiddlewareInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;

/**
 * Authentication middleware.
 * Validates JWT tokens and sets user/role context on the request.
 * Allows unauthenticated access to public routes.
 */
final class AuthMiddleware implements MiddlewareInterface
{
    private JwtService $jwt;
    private Connection $db;

    /** Routes that don't require authentication */
    private const PUBLIC_ROUTES = [
        '/api/auth/login',
        '/api/auth/register',
        '/api/auth/forgot-password',
        '/api/auth/reset-password',
        '/api/auth/refresh',
        '/api/webhooks/square',
        '/api/health',
        '/',
    ];

    /** Route prefixes that don't require authentication */
    private const PUBLIC_PREFIXES = [
        '/admin',
        '/platform',
        '/portal',
        '/product',
        '/about',
        '/contact',
        '/demo',
        '/pricing',
        '/login',
        '/register',
        '/forgot-password',
        '/reset-password',
    ];

    public function __construct(JwtService $jwt, Connection $db)
    {
        $this->jwt = $jwt;
        $this->db = $db;
    }

    public function handle(Request $request, callable $next): Response
    {
        // Skip auth for public routes
        if ($this->isPublicRoute($request)) {
            return $next($request);
        }

        // Try JWT bearer token
        $token = $request->bearerToken();

        if ($token === null) {
            // Try API token from header
            $apiToken = $request->header('x-api-token');
            if ($apiToken !== null) {
                return $this->authenticateWithApiToken($request, $apiToken, $next);
            }

            return Response::unauthorized('Authentication required');
        }

        return $this->authenticateWithJwt($request, $token, $next);
    }

    private function authenticateWithJwt(Request $request, string $token, callable $next): Response
    {
        $payload = $this->jwt->validateToken($token);

        if ($payload === null) {
            return Response::unauthorized('Invalid or expired token');
        }

        // Load user from database
        $user = $this->db->fetch(
            "SELECT `id`, `uuid`, `organization_id`, `email`, `first_name`, `last_name`, `status` FROM `users` WHERE `id` = ? AND `status` = 'active'",
            [$payload['sub']]
        );

        if ($user === null) {
            return Response::unauthorized('User account not found or inactive');
        }

        // Load user roles and permissions
        $roles = $this->loadUserRoles((int) $user['id'], $request->organizationId());
        $permissions = $this->loadUserPermissions($roles);

        // Set auth context
        $request->setAttribute('user', $user);
        $request->setAttribute('user_id', (int) $user['id']);
        $request->setAttribute('user_roles', array_column($roles, 'slug'));
        $request->setAttribute('user_permissions', $permissions);

        // If user has org from JWT but request doesn't have tenant (API subdomain)
        if ($request->organizationId() === null && $user['organization_id'] !== null) {
            $request->setAttribute('organization_id', (int) $user['organization_id']);
        }

        return $next($request);
    }

    private function authenticateWithApiToken(Request $request, string $token, callable $next): Response
    {
        $tokenHash = hash('sha256', $token);

        $apiToken = $this->db->fetch(
            "SELECT `at`.*, `u`.`id` as `uid`, `u`.`uuid` as `user_uuid`, `u`.`email`, `u`.`first_name`, `u`.`last_name`, `u`.`organization_id`, `u`.`status`
             FROM `api_tokens` `at`
             JOIN `users` `u` ON `u`.`id` = `at`.`user_id`
             WHERE `at`.`token_hash` = ?
               AND `at`.`revoked_at` IS NULL
               AND (`at`.`expires_at` IS NULL OR `at`.`expires_at` > NOW())
               AND `u`.`status` = 'active'",
            [$tokenHash]
        );

        if ($apiToken === null) {
            return Response::unauthorized('Invalid API token');
        }

        // Update last used timestamp
        $this->db->query(
            "UPDATE `api_tokens` SET `last_used_at` = NOW() WHERE `id` = ?",
            [$apiToken['id']]
        );

        $user = [
            'id' => $apiToken['uid'],
            'uuid' => $apiToken['user_uuid'],
            'email' => $apiToken['email'],
            'first_name' => $apiToken['first_name'],
            'last_name' => $apiToken['last_name'],
            'organization_id' => $apiToken['organization_id'],
            'status' => $apiToken['status'],
        ];

        $roles = $this->loadUserRoles((int) $user['id'], $request->organizationId());
        $permissions = $this->loadUserPermissions($roles);

        // Filter permissions by token abilities
        $abilities = json_decode($apiToken['abilities'] ?? '[]', true) ?: [];
        if (!empty($abilities) && !in_array('*', $abilities, true)) {
            $permissions = array_intersect($permissions, $abilities);
        }

        $request->setAttribute('user', $user);
        $request->setAttribute('user_id', (int) $user['id']);
        $request->setAttribute('user_roles', array_column($roles, 'slug'));
        $request->setAttribute('user_permissions', $permissions);
        $request->setAttribute('api_token', true);

        if ($request->organizationId() === null && $user['organization_id'] !== null) {
            $request->setAttribute('organization_id', (int) $user['organization_id']);
        }

        return $next($request);
    }

    private function loadUserRoles(int $userId, ?int $organizationId): array
    {
        $sql = "SELECT `r`.`id`, `r`.`slug`, `r`.`name`
                FROM `user_roles` `ur`
                JOIN `roles` `r` ON `r`.`id` = `ur`.`role_id`
                WHERE `ur`.`user_id` = ?";
        $params = [$userId];

        if ($organizationId !== null) {
            $sql .= " AND `ur`.`organization_id` = ?";
            $params[] = $organizationId;
        }

        return $this->db->fetchAll($sql, $params);
    }

    private function loadUserPermissions(array $roles): array
    {
        if (empty($roles)) {
            return [];
        }

        // Check for super_admin — gets all permissions
        $roleSlugs = array_column($roles, 'slug');
        if (in_array('super-admin', $roleSlugs, true) || in_array('super_admin', $roleSlugs, true)) {
            return ['*'];
        }

        $roleIds = array_column($roles, 'id');
        $placeholders = implode(',', array_fill(0, count($roleIds), '?'));

        $permissions = $this->db->fetchAll(
            "SELECT DISTINCT `p`.`slug`
             FROM `role_permissions` `rp`
             JOIN `permissions` `p` ON `p`.`id` = `rp`.`permission_id`
             WHERE `rp`.`role_id` IN ({$placeholders})",
            $roleIds
        );

        return array_column($permissions, 'slug');
    }

    private function isPublicRoute(Request $request): bool
    {
        $path = $request->path();

        if (in_array($path, self::PUBLIC_ROUTES, true)) {
            return true;
        }

        foreach (self::PUBLIC_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
