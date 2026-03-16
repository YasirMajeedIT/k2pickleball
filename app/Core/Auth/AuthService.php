<?php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\Connection;
use App\Core\Exceptions\AuthenticationException;
use App\Core\Exceptions\ValidationException;
use App\Core\Services\Config;
use App\Core\Services\Mailer;

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

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            $this->recordFailedLogin((int) $user['id']);
            throw new AuthenticationException('Invalid email or password');
        }

        // Check account lock after successful credential verification.
        if ($user['locked_until'] !== null && strtotime($user['locked_until']) > time()) {
            throw new AuthenticationException('Account is locked. Please try again later.');
        }

        // Enforce account status after credentials are verified.
        switch ($user['status']) {
            case 'active':
                break;
            case 'inactive':
                throw new AuthenticationException('Your account is currently inactive. Please contact the administrator.');
            case 'suspended':
                throw new AuthenticationException('Your account has been suspended. Please contact the administrator for assistance.');
            default:
                throw new AuthenticationException('Your account is currently inactive. Please contact the administrator.');
        }

        // Check email verification for active accounts.
        if (empty($user['email_verified_at'])) {
            throw new AuthenticationException('Please verify your email address before signing in. Check your inbox for the verification link.');
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
            'uuid'             => $uuid,
            'organization_id'  => $data['organization_id'] ?? null,
            'email'            => $data['email'],
            'password_hash'    => $passwordHash,
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'phone'            => $data['phone'] ?? null,
            'status'           => 'active',
            'email_verified_at'=> null,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);  

        // Send verification email
        $verifyToken = $this->createEmailVerificationToken($data['email']);
        $this->sendVerificationEmail(
            $data['email'],
            $data['first_name'],
            $verifyToken
        );

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
     * Logout: revoke all refresh tokens for this user.
     */
    public function logout(string $refreshToken): void
    {
        // Find the user from the refresh token, then revoke ALL their tokens
        $tokenHash = hash('sha256', $refreshToken);
        $record = $this->db->fetch(
            "SELECT `user_id` FROM `refresh_tokens` WHERE `token_hash` = ?",
            [$tokenHash]
        );
        if ($record) {
            $this->jwt->revokeAllUserTokens((int) $record['user_id']);
        } else {
            $this->jwt->revokeRefreshToken($refreshToken);
        }
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

        // Send reset email (also fetch first_name for personalisation)
        $userInfo = $this->db->fetch("SELECT `first_name` FROM `users` WHERE `email` = ? LIMIT 1", [$email]);
        $this->sendPasswordResetEmail($email, $userInfo['first_name'] ?? '', $token);

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

    // ---- Email Verification ----

    /**
     * Create a verification token for the given email and store it.
     */
    public function createEmailVerificationToken(string $email): string
    {
        // Invalidate previous tokens
        $this->db->query(
            "UPDATE `email_verifications` SET `used_at` = NOW() WHERE `email` = ? AND `used_at` IS NULL",
            [$email]
        );

        $token    = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 86400); // 24 hours

        $this->db->insert('email_verifications', [
            'email'      => $email,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    /**
     * Verify an email using a verification token.
     * Returns the user record on success.
     */
    public function verifyEmail(string $token): array
    {
        $tokenHash = hash('sha256', $token);

        $record = $this->db->fetch(
            "SELECT * FROM `email_verifications` WHERE `token_hash` = ? AND `used_at` IS NULL AND `expires_at` > NOW() LIMIT 1",
            [$tokenHash]
        );

        if ($record === null) {
            throw new AuthenticationException('Invalid or expired verification link. Please request a new one.');
        }

        // Mark token as used
        $this->db->query(
            "UPDATE `email_verifications` SET `used_at` = NOW() WHERE `id` = ?",
            [$record['id']]
        );

        // Mark user email as verified
        $this->db->query(
            "UPDATE `users` SET `email_verified_at` = NOW(), `updated_at` = NOW() WHERE `email` = ?",
            [$record['email']]
        );

        $user = $this->db->fetch("SELECT * FROM `users` WHERE `email` = ? LIMIT 1", [$record['email']]);

        // Send welcome email
        try {
            $this->sendWelcomeEmail(
                $user['email'],
                $user['first_name'] ?? '',
                $user['last_name'] ?? ''
            );
        } catch (\Throwable $e) {
            // Non-fatal: log but don't block verification
            error_log('[Mailer] Welcome email failed: ' . $e->getMessage());
        }

        return $user;
    }

    /**
     * Resend verification email.
     */
    public function resendVerification(string $email): void
    {
        $user = $this->db->fetch(
            "SELECT `first_name`, `email_verified_at` FROM `users` WHERE `email` = ? LIMIT 1",
            [$email]
        );

        if ($user === null) {
            // Prevent email enumeration — silently return
            return;
        }

        if (!empty($user['email_verified_at'])) {
            throw new ValidationException(['email' => 'This email address is already verified.']);
        }

        $token = $this->createEmailVerificationToken($email);
        $this->sendVerificationEmail($email, $user['first_name'] ?? '', $token);
    }

    // ---- Email Helpers ----

    private function sendVerificationEmail(string $email, string $firstName, string $token): void
    {
        try {
            $appUrl    = rtrim($_ENV['APP_URL'] ?? 'http://localhost/k2pickleball', '/');
            $verifyUrl = $appUrl . '/verify-email?token=' . urlencode($token);
            $mailer    = Mailer::getInstance();
            $html      = $mailer->renderTemplate('verify-email', [
                'firstName'      => $firstName,
                'verifyUrl'      => $verifyUrl,
                'appUrl'         => $appUrl,
                'recipientEmail' => $email,
                'expiresIn'      => '24 hours',
                'subject'        => 'Verify your email address — K2 Pickleball',
            ]);
            $mailer->send($email, $firstName, 'Verify your email address — K2 Pickleball', $html);
        } catch (\Throwable $e) {
            error_log('[Mailer] Verification email failed to ' . $email . ': ' . $e->getMessage());
        }
    }

    private function sendWelcomeEmail(string $email, string $firstName, string $lastName): void
    {
        $appUrl    = rtrim($_ENV['APP_URL'] ?? 'http://localhost/k2pickleball', '/');
        $mailer    = Mailer::getInstance();
        $html      = $mailer->renderTemplate('welcome', [
            'firstName'      => $firstName,
            'lastName'       => $lastName,
            'appUrl'         => $appUrl,
            'portalUrl'      => $appUrl . '/admin',
            'recipientEmail' => $email,
            'subject'        => 'Welcome to K2 Pickleball!',
        ]);
        $mailer->send($email, $firstName . ' ' . $lastName, 'Welcome to K2 Pickleball!', $html);
    }

    private function sendPasswordResetEmail(string $email, string $firstName, string $token): void
    {
        try {
            $appUrl   = rtrim($_ENV['APP_URL'] ?? 'http://localhost/k2pickleball', '/');
            $resetUrl = $appUrl . '/reset-password?token=' . urlencode($token);
            $mailer   = Mailer::getInstance();
            $html     = $mailer->renderTemplate('forgot-password', [
                'firstName'      => $firstName,
                'resetUrl'       => $resetUrl,
                'appUrl'         => $appUrl,
                'recipientEmail' => $email,
                'expiresIn'      => '1 hour',
                'subject'        => 'Reset your K2 Pickleball password',
            ]);
            $mailer->send($email, $firstName, 'Reset your K2 Pickleball password', $html);
        } catch (\Throwable $e) {
            error_log('[Mailer] Password reset email failed to ' . $email . ': ' . $e->getMessage());
        }
    }

    // ---- Private helpers ----

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
