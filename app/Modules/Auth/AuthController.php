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
use App\Core\Database\Connection;
use App\Modules\Payments\SquarePaymentService;
use App\Modules\Subscriptions\SubscriptionRepository;

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
            'organization_name' => 'nullable|string|max:255',
            'organization_slug' => 'nullable|slug|max:100',
        ]);

        $data['email'] = Sanitizer::email($data['email']);
        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);
        if (!empty($data['organization_name'])) {
            $data['organization_name'] = Sanitizer::string($data['organization_name']);
        }
        if (!empty($data['organization_slug'])) {
            $data['organization_slug'] = Sanitizer::slug($data['organization_slug']);
        }

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
     * POST /api/auth/register-with-payment
     * Registration + Square payment + subscription in one step.
     */
    public function registerWithPayment(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'email'             => 'required|email|max:255',
            'password'          => 'required|password|confirmed',
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'phone'             => 'required|phone',
            'organization_name' => 'required|string|max:255',
            'organization_slug' => 'required|slug|max:100',
            'plan_id'           => 'required|integer',
            'billing_cycle'     => 'required|in:monthly,yearly',
            'source_id'         => 'required|string',
        ]);

        $data['email'] = Sanitizer::email($data['email']);
        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);
        $data['organization_name'] = Sanitizer::string($data['organization_name']);
        $data['organization_slug'] = Sanitizer::slug($data['organization_slug']);

        $container = Container::getInstance();
        $db = $container->make(Connection::class);
        $subRepo = new SubscriptionRepository($db);

        // Validate plan exists and is active
        $plan = $subRepo->findPlanById((int) $data['plan_id']);
        if (!$plan || !$plan['is_active']) {
            return $this->error('Selected plan is not available', 422);
        }

        $price = $data['billing_cycle'] === 'yearly'
            ? (float) $plan['price_yearly']
            : (float) $plan['price_monthly'];
        $amountCents = (int) round($price * 100);

        // 1. Process Square payment FIRST (fail fast before creating any records)
        $square = new SquarePaymentService();
        $squareCustomer = $square->createCustomer(
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'] ?? null
        );

        $paymentResult = $square->createPayment(
            $amountCents,
            'USD',
            $data['source_id'],
            [
                'customer_id'  => $squareCustomer['id'],
                'reference_id' => 'REG-' . strtoupper(bin2hex(random_bytes(6))),
                'note'         => 'K2Pickleball ' . $plan['name'] . ' plan — ' . $data['billing_cycle'],
            ]
        );

        // 2. Register user + org (status 'active' since payment succeeded)
        $data['org_status'] = 'active';
        $user = $this->auth->register($data);

        $orgId = $user['organization_id'];

        // 3. Create subscription
        $startDate = date('Y-m-d');
        $endDate = $data['billing_cycle'] === 'yearly'
            ? date('Y-m-d', strtotime('+1 year'))
            : date('Y-m-d', strtotime('+1 month'));

        $subId = $subRepo->create([
            'organization_id'      => $orgId,
            'plan_id'              => $data['plan_id'],
            'status'               => 'active',
            'billing_cycle'        => $data['billing_cycle'],
            'current_period_start' => $startDate,
            'current_period_end'   => $endDate,
            'square_subscription_id' => $paymentResult['id'],
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        // 4. Create invoice
        $subRepo->createInvoice([
            'uuid'            => $this->generateUuid(),
            'organization_id' => $orgId,
            'subscription_id' => $subId,
            'invoice_number'  => 'INV-' . strtoupper(bin2hex(random_bytes(4))),
            'subtotal'        => $price,
            'tax'             => 0,
            'total'           => $price,
            'status'          => 'paid',
            'due_date'        => $startDate,
            'paid_at'         => date('Y-m-d H:i:s'),
            'notes'           => 'Registration payment — ' . $plan['name'] . ' (' . $data['billing_cycle'] . ')',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        // 5. Store payment record
        $payRepo = new \App\Modules\Payments\PaymentRepository($db);
        $payRepo->create([
            'uuid'               => $this->generateUuid(),
            'organization_id'    => $orgId,
            'user_id'            => $user['id'],
            'amount'             => $amountCents,
            'currency'           => 'USD',
            'status'             => strtolower($paymentResult['status']),
            'description'        => 'Registration — ' . $plan['name'] . ' plan',
            'square_payment_id'  => $paymentResult['id'],
            'square_receipt_url' => $paymentResult['receipt_url'] ?? null,
            'idempotency_key'    => bin2hex(random_bytes(16)),
            'metadata'           => json_encode($paymentResult),
            'processed_at'       => date('Y-m-d H:i:s'),
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        return $this->created([
            'user'    => $user,
            'plan'    => $plan['name'],
            'payment' => [
                'status'      => $paymentResult['status'],
                'receipt_url' => $paymentResult['receipt_url'] ?? null,
            ],
        ], 'Registration and payment successful');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
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

        $orgId = $request->organizationId() ?? ($user['organization_id'] ?? null);
        $organization = null;
        if ($orgId) {
            $org = $db->fetch("SELECT `id`, `name`, `slug`, `status`, `trial_ends_at` FROM `organizations` WHERE `id` = ? LIMIT 1", [(int) $orgId]);
            if ($org) {
                $organization = [
                    'id'            => (int) $org['id'],
                    'name'          => $org['name'] ?? '',
                    'slug'          => $org['slug'] ?? '',
                    'status'        => $org['status'] ?? '',
                    'trial_ends_at' => $org['trial_ends_at'] ?? null,
                ];
            }
        }

        return $this->success([
            'user_id'         => $request->userId(),
            'organization_id' => $orgId,
            'roles'           => $request->userRoles(),
            'permissions'     => $request->getAttributes()['user_permissions'] ?? [],
            'first_name'      => $user['first_name'] ?? '',
            'last_name'       => $user['last_name'] ?? '',
            'email'           => $user['email'] ?? '',
            'phone'           => $user['phone'] ?? '',
            'avatar_url'      => $user['avatar_url'] ?? '',
            'organization'    => $organization,
        ]);
    }
}
