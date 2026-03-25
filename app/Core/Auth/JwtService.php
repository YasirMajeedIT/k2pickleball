<?php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\Connection;
use App\Core\Services\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

/**
 * JWT token service.
 * Handles access token generation/validation and refresh token lifecycle.
 */
final class JwtService
{
    private string $secret;
    private string $algo;
    private int $accessTtl;
    private int $refreshTtl;
    private string $issuer;
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
        $authConfig = Config::all('auth');
        $this->secret = $authConfig['jwt']['secret'] ?? '';
        $this->algo = $authConfig['jwt']['algo'] ?? 'HS256';
        $this->accessTtl = $authConfig['jwt']['access_ttl'] ?? 1800;
        $this->refreshTtl = $authConfig['jwt']['refresh_ttl'] ?? 2592000;
        $this->issuer = $authConfig['jwt']['issuer'] ?? 'k2pickleball';
    }

    /**
     * Generate an access token for a user.
     */
    public function generateAccessToken(int $userId, ?int $organizationId = null, array $roles = []): string
    {
        $now = time();

        $payload = [
            'iss' => $this->issuer,
            'sub' => $userId,
            'iat' => $now,
            'exp' => $now + $this->accessTtl,
            'nbf' => $now,
            'org' => $organizationId,
            'roles' => $roles,
            'jti' => bin2hex(random_bytes(16)),
        ];

        return JWT::encode($payload, $this->secret, $this->algo);
    }

    /**
     * Generate a refresh token and store its hash in the database.
     */
    public function generateRefreshToken(int $userId, ?string $ip = null, ?string $userAgent = null, bool $remember = false): string
    {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        // "Keep me signed in" = 90 days, normal = default TTL (30 days)
        $ttl = $remember ? 7776000 : $this->refreshTtl;
        $expiresAt = date('Y-m-d H:i:s', time() + $ttl);

        $this->db->insert('refresh_tokens', [
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'ip_address' => $ip,
            'user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    /**
     * Validate and decode an access token.
     * Returns the decoded payload as array, or null if invalid.
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algo));
            return (array) $decoded;
        } catch (ExpiredException $e) {
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Refresh a token pair.
     * Validates the refresh token, revokes it, and returns new access + refresh tokens.
     */
    public function refreshTokenPair(string $refreshToken, ?string $ip = null, ?string $userAgent = null): ?array
    {
        $tokenHash = hash('sha256', $refreshToken);

        $record = $this->db->fetch(
            "SELECT `rt`.*, `u`.`organization_id`, `u`.`status` as `user_status`
             FROM `refresh_tokens` `rt`
             JOIN `users` `u` ON `u`.`id` = `rt`.`user_id`
             WHERE `rt`.`token_hash` = ?
               AND `rt`.`revoked_at` IS NULL
               AND `rt`.`expires_at` > NOW()",
            [$tokenHash]
        );

        if ($record === null) {
            return null;
        }

        if ($record['user_status'] !== 'active') {
            $this->revokeRefreshToken($refreshToken);
            return null;
        }

        // Revoke the old refresh token (rotate)
        $this->revokeRefreshToken($refreshToken);

        $userId = (int) $record['user_id'];
        $orgId = $record['organization_id'] ? (int) $record['organization_id'] : null;

        // Load roles
        $roles = $this->loadUserRoleSlugs($userId, $orgId);

        // Generate new pair
        $newAccessToken = $this->generateAccessToken($userId, $orgId, $roles);
        $newRefreshToken = $this->generateRefreshToken($userId, $ip, $userAgent);

        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $this->accessTtl,
        ];
    }

    /**
     * Revoke a refresh token.
     */
    public function revokeRefreshToken(string $refreshToken): void
    {
        $tokenHash = hash('sha256', $refreshToken);

        $this->db->query(
            "UPDATE `refresh_tokens` SET `revoked_at` = NOW() WHERE `token_hash` = ?",
            [$tokenHash]
        );
    }

    /**
     * Revoke all refresh tokens for a user.
     */
    public function revokeAllUserTokens(int $userId): void
    {
        $this->db->query(
            "UPDATE `refresh_tokens` SET `revoked_at` = NOW() WHERE `user_id` = ? AND `revoked_at` IS NULL",
            [$userId]
        );
    }

    /**
     * Clean up expired refresh tokens.
     */
    public function cleanExpiredTokens(): int
    {
        return $this->db->query(
            "DELETE FROM `refresh_tokens` WHERE `expires_at` < NOW() OR `revoked_at` IS NOT NULL"
        )->rowCount();
    }

    private function loadUserRoleSlugs(int $userId, ?int $organizationId): array
    {
        $sql = "SELECT `r`.`slug` FROM `user_roles` `ur` JOIN `roles` `r` ON `r`.`id` = `ur`.`role_id` WHERE `ur`.`user_id` = ?";
        $params = [$userId];

        if ($organizationId !== null) {
            $sql .= " AND `ur`.`organization_id` = ?";
            $params[] = $organizationId;
        }

        $roles = $this->db->fetchAll($sql, $params);
        return array_column($roles, 'slug');
    }
}
