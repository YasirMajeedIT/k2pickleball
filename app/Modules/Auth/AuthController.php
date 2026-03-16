<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use App\Core\Auth\AuthService;
use App\Core\Auth\JwtService;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Services\Container;

final class AuthController extends Controller
{
    private AuthService $auth;
    private JwtService $jwt;

    public function __construct()
    {
        $container = Container::getInstance();
        $this->auth = $container->make(AuthService::class);
        $this->jwt = $container->make(JwtService::class);
    }

    /**
     * POST /api/auth/login
     */
    public function login(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:1',
        ]);

        $result = $this->auth->login(
            Sanitizer::email($data['email']),
            $data['password'],
            $request->ip(),
            $request->header('User-Agent')
        );

        return $this->success($result, 'Login successful');
    }

    /**
     * POST /api/auth/register
     */
    public function register(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|password|confirmed',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|phone',
            'organization_id' => 'nullable|integer',
            'role_slug' => 'nullable|string|max:50',
        ]);

        $data['email'] = Sanitizer::email($data['email']);
        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);

        $user = $this->auth->register($data);

        return $this->created($user, 'Registration successful');
    }

    /**
     * POST /api/auth/refresh
     */
    public function refresh(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        $result = $this->jwt->refreshTokenPair(
            $data['refresh_token'],
            $request->ip(),
            $request->header('User-Agent')
        );

        return $this->success($result, 'Token refreshed');
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        $this->auth->logout($data['refresh_token']);

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * POST /api/auth/forgot-password
     */
    public function forgotPassword(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        $token = $this->auth->forgotPassword(Sanitizer::email($data['email']));

        // In production, send email with $token. For now, return it in dev/sandbox.
        $response = ['message' => 'If the email is registered, a reset link will be sent.'];

        if (getenv('APP_ENV') !== 'production' && $token !== null) {
            $response['reset_token'] = $token;
        }

        return $this->success($response);
    }

    /**
     * POST /api/auth/reset-password
     */
    public function resetPassword(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'token' => 'required|string',
            'password' => 'required|password|confirmed',
        ]);

        $this->auth->resetPassword($data['token'], $data['password']);

        return $this->success(null, 'Password reset successful');
    }

    /**
     * GET /api/auth/verify-email?token=xxx
     */
    public function verifyEmail(Request $request): Response
    {
        $token = $request->query('token', '');
        if (empty($token)) {
            return $this->error('Verification token is required.', 422);
        }

        $user = $this->auth->verifyEmail($token);

        return $this->success([
            'email' => $user['email'],
        ], 'Email verified successfully. You can now sign in.');
    }

    /**
     * POST /api/auth/resend-verification
     */
    public function resendVerification(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        $this->auth->resendVerification(Sanitizer::email($data['email']));

        return $this->success(null, 'If the email is registered and unverified, a new verification link has been sent.');
    }

    /**
     * POST /api/auth/change-password (authenticated)
     */
    public function changePassword(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|password|confirmed',
        ]);

        $this->auth->changePassword(
            $request->userId(),
            $data['current_password'],
            $data['password']
        );

        return $this->success(null, 'Password changed successfully');
    }

    /**
     * PUT /api/auth/profile (authenticated)
     */
    public function updateProfile(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|phone',
        ]);

        $userId = $request->userId();
        $container = Container::getInstance();
        $db = $container->make(\App\Core\Database\Connection::class);

        $db->execute(
            "UPDATE `users` SET `first_name` = ?, `last_name` = ?, `phone` = ?, `updated_at` = NOW() WHERE `id` = ?",
            [
                Sanitizer::string($data['first_name']),
                Sanitizer::string($data['last_name']),
                isset($data['phone']) ? Sanitizer::string($data['phone']) : null,
                $userId,
            ]
        );

        $user = $db->fetch(
            "SELECT `id`, `email`, `first_name`, `last_name`, `phone`, `avatar_url` FROM `users` WHERE `id` = ? LIMIT 1",
            [$userId]
        );

        return $this->success($user, 'Profile updated successfully');
    }

    /**
     * GET /api/auth/me (authenticated)
     */
    public function me(Request $request): Response
    {
        $userId = $request->userId();
        $container = Container::getInstance();
        $db = $container->make(\App\Core\Database\Connection::class);
        $user = $db->fetch("SELECT `id`, `uuid`, `email`, `first_name`, `last_name`, `phone`, `avatar_url`, `status`, `organization_id`, `created_at` FROM `users` WHERE `id` = ? LIMIT 1", [$userId]);

        return $this->success([
            'user_id'         => $request->userId(),
            'organization_id' => $request->organizationId(),
            'roles'           => $request->userRoles(),
            'permissions'     => $request->getAttributes()['user_permissions'] ?? [],
            'first_name'      => $user['first_name'] ?? '',
            'last_name'       => $user['last_name'] ?? '',
            'email'           => $user['email'] ?? '',
            'phone'           => $user['phone'] ?? '',
            'avatar_url'      => $user['avatar_url'] ?? '',
        ]);
    }
}
