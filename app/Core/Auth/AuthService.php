<?php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\Connection;
use App\Core\Exceptions\AuthenticationException;
use App\Core\Exceptions\ValidationException;
use App\Core\Services\Config;

/**
 * Authentication service.
 * Handles login, registration, password reset flows.
 */
final class AuthService
{
    private Connection $db;
    private JwtService $jwt;

    public function __construct(Connection $db, JwtService $jwt)
    {
        $this->db = $db;
        $this->jwt = $jwt;
    }

    /**
     * Authenticate a user and return token pair.
     */
    public function login(string $email, string $password, ?string $ip = null, ?string $userAgent = null): array
    {
        $user = $this->db->fetch(
            "SELECT * FROM `users` WHERE `email` = ? LIMIT 1",
            [$email]
        );

        if ($user === null) {
            // Use constant-time comparison to prevent timing attacks
            password_verify($password, '$2y$12$dummyhashtopreventtimingattacksxxxxxxxxxx');
            throw new AuthenticationException('Invalid email or password');
        }

        // Check account lock
        if ($user['locked_until'] !== null && strtotime($user['locked_until']) > time()) {
            throw new AuthenticationException('Account is locked. Please try again later.');
        }

        // Check account status
        if ($user['status'] !== 'active') {
            throw new AuthenticationException('Account is not active. Please contact support.');
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            $this->recordFailedLogin((int) $user['id']);
            throw new AuthenticationException('Invalid email or password');
        }

        // Reset failed attempts and update login info
        $this->db->update('users', [
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip,
        ], ['id' => $user['id']]);

        // Load roles
        $orgId = $user['organization_id'] ? (int) $user['organization_id'] : null;
        $roles = $this->loadUserRoles((int) $user['id'], $orgId);
        $roleSlugs = array_column($roles, 'slug');

        // Generate tokens
        $accessToken = $this->jwt->generateAccessToken(
            (int) $user['id'],
            $orgId,
            $roleSlugs
        );
        $refreshToken = $this->jwt->generateRefreshToken((int) $user['id'], $ip, $userAgent);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => Config::get('auth.jwt.access_ttl', 1800),
            'user' => [
                'id' => $user['id'],
                'uuid' => $user['uuid'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'organization_id' => $user['organization_id'],
                'roles' => $roleSlugs,
            ],
        ];
    }

    /**
     * Register a new user.
     */
    public function register(array $data): array
    {
        // Check for duplicate email
        $existing = $this->db->fetch(
            "SELECT `id` FROM `users` WHERE `email` = ?",
            [$data['email']]
        );

        if ($existing !== null) {
            throw new ValidationException(['email' => 'Email address is already registered']);
        }

        $uuid = $this->generateUuid();
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, [
            'cost' => Config::get('auth.password.bcrypt_cost', 12),
        ]);

        $userId = $this->db->insert('users', [
            'uuid' => $uuid,
            'organization_id' => $data['organization_id'] ?? null,
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Assign default role if organization is specified
        if (!empty($data['organization_id']) && !empty($data['role_slug'])) {
            $role = $this->db->fetch(
                "SELECT `id` FROM `roles` WHERE `slug` = ? AND (`organization_id` = ? OR `organization_id` IS NULL) LIMIT 1",
                [$data['role_slug'], $data['organization_id']]
            );

            if ($role !== null) {
                $this->db->insert('user_roles', [
                    'user_id' => $userId,
                    'role_id' => $role['id'],
                    'organization_id' => $data['organization_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return [
            'id' => $userId,
            'uuid' => $uuid,
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ];
    }

    /**
     * Logout: revoke the refresh token.
     */
    public function logout(string $refreshToken): void
    {
        $this->jwt->revokeRefreshToken($refreshToken);
    }

    /**
     * Generate a password reset token and store it.
     */
    public function forgotPassword(string $email): ?string
    {
        $user = $this->db->fetch(
            "SELECT `id`, `email` FROM `users` WHERE `email` = ? AND `status` = 'active'",
            [$email]
        );

        // Always return success to prevent email enumeration
        if ($user === null) {
            return null;
        }

        // Invalidate existing reset tokens
        $this->db->query(
            "UPDATE `password_resets` SET `used_at` = NOW() WHERE `email` = ? AND `used_at` IS NULL",
            [$email]
        );

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

        $this->db->insert('password_resets', [
            'email' => $email,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    /**
     * Reset password using a valid reset token.
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $tokenHash = hash('sha256', $token);

        $reset = $this->db->fetch(
            "SELECT * FROM `password_resets` WHERE `token_hash` = ? AND `used_at` IS NULL AND `expires_at` > NOW() LIMIT 1",
            [$tokenHash]
        );

        if ($reset === null) {
            throw new AuthenticationException('Invalid or expired reset token');
        }

        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, [
            'cost' => Config::get('auth.password.bcrypt_cost', 12),
        ]);

        // Update password
        $this->db->query(
            "UPDATE `users` SET `password_hash` = ?, `updated_at` = NOW() WHERE `email` = ?",
            [$passwordHash, $reset['email']]
        );

        // Mark token as used
        $this->db->query(
            "UPDATE `password_resets` SET `used_at` = NOW() WHERE `id` = ?",
            [$reset['id']]
        );

        // Revoke all refresh tokens for this user
        $user = $this->db->fetch("SELECT `id` FROM `users` WHERE `email` = ?", [$reset['email']]);
        if ($user) {
            $this->jwt->revokeAllUserTokens((int) $user['id']);
        }

        return true;
    }

    /**
     * Change password for authenticated user.
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->db->fetch(
            "SELECT `password_hash` FROM `users` WHERE `id` = ?",
            [$userId]
        );

        if ($user === null || !password_verify($currentPassword, $user['password_hash'])) {
            throw new AuthenticationException('Current password is incorrect');
        }

        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, [
            'cost' => Config::get('auth.password.bcrypt_cost', 12),
        ]);

        $this->db->update('users', [
            'password_hash' => $passwordHash,
        ], ['id' => $userId]);

        return true;
    }

    // -- Private helpers --

    private function recordFailedLogin(int $userId): void
    {
        $this->db->query(
            "UPDATE `users` SET `failed_login_attempts` = `failed_login_attempts` + 1, `updated_at` = NOW() WHERE `id` = ?",
            [$userId]
        );

        // Lock account after 5 failed attempts for 15 minutes
        $user = $this->db->fetch("SELECT `failed_login_attempts` FROM `users` WHERE `id` = ?", [$userId]);
        if ($user && (int) $user['failed_login_attempts'] >= 5) {
            $lockUntil = date('Y-m-d H:i:s', time() + 900);
            $this->db->update('users', ['locked_until' => $lockUntil], ['id' => $userId]);
        }
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

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
