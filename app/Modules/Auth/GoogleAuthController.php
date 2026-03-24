<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use App\Core\Auth\AuthService;
use App\Core\Auth\JwtService;
use App\Core\Database\Connection;
use App\Core\Exceptions\AuthenticationException;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\Config;
use App\Core\Services\Container;

/**
 * Google OAuth 2.0 authentication via Google Identity Services (One-Tap / Sign-In button).
 *
 * Flow:
 *   1. Frontend uses Google Identity Services JS library to get an ID token (credential)
 *   2. Frontend POSTs the credential to POST /api/auth/google
 *   3. We verify the ID token with Google's tokeninfo endpoint
 *   4. We find or create the user, then issue our own JWT pair
 */
final class GoogleAuthController extends Controller
{
    private Connection $db;
    private JwtService $jwt;
    private AuthService $auth;

    public function __construct()
    {
        $container = Container::getInstance();
        $this->db  = $container->make(Connection::class);
        $this->jwt = $container->make(JwtService::class);
        $this->auth = $container->make(AuthService::class);
    }

    /**
     * POST /api/auth/google
     * Body: { credential: "<google_id_token>" }
     */
    public function handleGoogleToken(Request $request): Response
    {
        $credential = trim($request->input('credential', ''));
        if (empty($credential)) {
            return $this->error('Google credential is required.', 422);
        }

        // Organization context — from request body (tenant pages) or from tenant middleware
        $requestOrgId = (int) $request->input('organization_id', 0);
        if ($requestOrgId <= 0) {
            $requestOrgId = (int) ($request->getAttribute('organization_id') ?? 0);
        }

        // Verify the ID token with Google
        $googleUser = $this->verifyGoogleIdToken($credential);
        if ($googleUser === null) {
            return $this->error('Invalid or expired Google credential. Please try again.', 401);
        }

        $email     = $googleUser['email'];
        $firstName = $googleUser['given_name'] ?? '';
        $lastName  = $googleUser['family_name'] ?? '';
        $googleId  = $googleUser['sub'];
        $avatar    = $googleUser['picture'] ?? null;

        // 1. Look up by google_id first
        $user = $this->db->fetch("SELECT * FROM `users` WHERE `google_id` = ? LIMIT 1", [$googleId]);

        // 2. Look up by email
        if ($user === null) {
            $user = $this->db->fetch("SELECT * FROM `users` WHERE `email` = ? LIMIT 1", [$email]);
        }

        if ($user !== null) {
            // Existing user — ensure google_id is linked and email_verified_at set
            $updates = [];
            if (empty($user['google_id'])) {
                $updates['google_id'] = $googleId;
            }
            if (empty($user['email_verified_at'])) {
                $updates['email_verified_at'] = date('Y-m-d H:i:s');
            }
            if ($avatar && empty($user['avatar_url'])) {
                $updates['avatar_url'] = $avatar;
            }
            if (!empty($updates)) {
                $updates['updated_at'] = date('Y-m-d H:i:s');
                $this->db->update('users', $updates, ['id' => $user['id']]);
                // Reload
                $user = $this->db->fetch("SELECT * FROM `users` WHERE `id` = ? LIMIT 1", [$user['id']]);
            }

            if ($user['status'] !== 'active') {
                return $this->error('Account is not active. Please contact support.', 403);
            }
        } else {
            // New user — auto-create, auto-verified via Google
            $uuidBytes = random_bytes(16);
            $uuidBytes[6] = chr(ord($uuidBytes[6]) & 0x0f | 0x40);
            $uuidBytes[8] = chr(ord($uuidBytes[8]) & 0x3f | 0x80);
            $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($uuidBytes), 4));

            $userId = $this->db->insert('users', [
                'uuid'              => $uuid,
                'organization_id'   => $requestOrgId > 0 ? $requestOrgId : null,
                'email'             => $email,
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'google_id'         => $googleId,
                'avatar_url'        => $avatar,
                'password_hash'     => '',                           // No password for OAuth users
                'status'            => 'active',
                'email_verified_at' => date('Y-m-d H:i:s'),         // Auto-verified via Google
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            $user = $this->db->fetch("SELECT * FROM `users` WHERE `id` = ? LIMIT 1", [$userId]);

            // Auto-assign 'player' role when signing up via a tenant page
            if ($requestOrgId > 0) {
                $playerRole = $this->db->fetch(
                    "SELECT `id` FROM `roles` WHERE `slug` = 'player' AND (`organization_id` = ? OR `organization_id` IS NULL) LIMIT 1",
                    [$requestOrgId]
                );
                if ($playerRole) {
                    $this->db->insert('user_roles', [
                        'user_id'         => $userId,
                        'role_id'         => $playerRole['id'],
                        'organization_id' => $requestOrgId,
                    ]);
                }
            }
        }

        // Issue our JWT pair
        $orgId     = $user['organization_id'] ? (int) $user['organization_id'] : null;
        $roles     = $this->loadUserRoles((int) $user['id'], $orgId);
        $roleSlugs = array_column($roles, 'slug');

        $accessToken  = $this->jwt->generateAccessToken((int) $user['id'], $orgId, $roleSlugs);
        $refreshToken = $this->jwt->generateRefreshToken((int) $user['id'], $request->ip(), $request->header('User-Agent'));

        // Update last login
        $this->db->update('users', [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $request->ip(),
        ], ['id' => $user['id']]);

        return $this->success([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type'    => 'Bearer',
            'expires_in'    => Config::get('auth.jwt.access_ttl', 1800),
            'user' => [
                'id'              => $user['id'],
                'uuid'            => $user['uuid'],
                'email'           => $user['email'],
                'first_name'      => $user['first_name'],
                'last_name'       => $user['last_name'],
                'organization_id' => $user['organization_id'],
                'avatar_url'      => $user['avatar_url'] ?? null,
                'roles'           => $roleSlugs,
            ],
        ], 'Google sign-in successful');
    }

    // ---- Private ----

    /**
     * Verify Google ID token using Google's tokeninfo endpoint.
     * Returns the payload array on success, null on failure.
     */
    private function verifyGoogleIdToken(string $idToken): ?array
    {
        $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';

        // Use Google's tokeninfo endpoint for lightweight verification
        $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);

        $context = stream_context_create([
            'http' => [
                'timeout' => 8,
                'method'  => 'GET',
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            error_log('[GoogleAuth] Failed to reach Google tokeninfo endpoint');
            return null;
        }

        $payload = json_decode($response, true);
        if (empty($payload) || isset($payload['error'])) {
            error_log('[GoogleAuth] Invalid token: ' . ($payload['error_description'] ?? $payload['error'] ?? 'unknown'));
            return null;
        }

        // Validate audience (client_id)
        if (!empty($clientId) && ($payload['aud'] ?? '') !== $clientId) {
            error_log('[GoogleAuth] Token audience mismatch: ' . ($payload['aud'] ?? ''));
            return null;
        }

        // Validate issuer
        if (!in_array($payload['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)) {
            error_log('[GoogleAuth] Invalid issuer: ' . ($payload['iss'] ?? ''));
            return null;
        }

        // Validate expiry
        if ((int) ($payload['exp'] ?? 0) < time()) {
            error_log('[GoogleAuth] Token expired');
            return null;
        }

        // Require verified email
        if (($payload['email_verified'] ?? 'false') !== 'true') {
            error_log('[GoogleAuth] Email not verified by Google');
            return null;
        }

        return $payload;
    }

    private function loadUserRoles(int $userId, ?int $organizationId): array
    {
        $sql    = "SELECT `r`.`id`, `r`.`slug`, `r`.`name` FROM `user_roles` `ur` JOIN `roles` `r` ON `r`.`id` = `ur`.`role_id` WHERE `ur`.`user_id` = ?";
        $params = [$userId];
        if ($organizationId !== null) {
            $sql    .= " AND `ur`.`organization_id` = ?";
            $params[] = $organizationId;
        }
        return $this->db->fetchAll($sql, $params);
    }
}
