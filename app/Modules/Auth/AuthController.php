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
            'phone' => 'nullable|phone',
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
     * GET /api/auth/me (authenticated)
     */
    public function me(Request $request): Response
    {
        return $this->success([
            'user_id' => $request->userId(),
            'organization_id' => $request->organizationId(),
            'roles' => $request->userRoles(),
            'permissions' => $request->getAttributes()['user_permissions'] ?? [],
        ]);
    }
}
