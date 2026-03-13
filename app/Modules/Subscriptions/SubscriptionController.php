<?php

declare(strict_types=1);

namespace App\Modules\Subscriptions;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class SubscriptionController extends Controller
{
    private SubscriptionRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new SubscriptionRepository($db);
    }

    // -- Plans --

    public function plans(Request $request): Response
    {
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));

        $result = $this->repo->findAllPlansPaginated($search ?: null, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function showPlan(Request $request, int $id): Response
    {
        $plan = $this->repo->findPlanById($id);
        if (!$plan) {
            throw new NotFoundException('Plan not found');
        }
        return $this->success($plan);
    }

    // -- Subscriptions --

    public function index(Request $request): Response
    {
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));

        if ($request->isSuperAdmin()) {
            $result = $this->repo->findAllSubscriptions($search ?: null, $page, $perPage);
        } else {
            $orgId = $request->organizationId();
            $result = $this->repo->findByOrganization($orgId, $page, $perPage);
        }

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $sub = $this->repo->findWithPlan($id);
        if (!$sub) {
            throw new NotFoundException('Subscription not found');
        }
        return $this->success($sub);
    }

    public function current(Request $request): Response
    {
        $orgId = $request->organizationId();
        $sub = $this->repo->findActiveByOrganization($orgId);

        if (!$sub) {
            return $this->success(null, 'No active subscription');
        }

        $sub['plan'] = $this->repo->findPlanById((int) $sub['plan_id']);
        return $this->success($sub);
    }

    public function subscribe(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'plan_id' => 'required|integer',
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method_id' => 'nullable|integer',
        ]);

        $orgId = $request->organizationId();
        $plan = $this->repo->findPlanById((int) $data['plan_id']);
        if (!$plan) {
            throw new NotFoundException('Plan not found');
        }

        // Check for existing active subscription
        $existing = $this->repo->findActiveByOrganization($orgId);
        if ($existing) {
            return $this->error('Organization already has an active subscription. Cancel or upgrade instead.', 409);
        }

        $price = $data['billing_cycle'] === 'yearly'
            ? (float) $plan['price_yearly']
            : (float) $plan['price_monthly'];

        $startDate = date('Y-m-d');
        $endDate = $data['billing_cycle'] === 'yearly'
            ? date('Y-m-d', strtotime('+1 year'))
            : date('Y-m-d', strtotime('+1 month'));

        $subId = $this->repo->create([
            'organization_id' => $orgId,
            'plan_id' => $data['plan_id'],
            'status' => 'active',
            'billing_cycle' => $data['billing_cycle'],
            'current_period_start' => $startDate,
            'current_period_end' => $endDate,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create invoice
        $this->repo->createInvoice([
            'uuid' => $this->generateUuid(),
            'organization_id' => $orgId,
            'subscription_id' => $subId,
            'invoice_number' => 'INV-' . strtoupper(bin2hex(random_bytes(4))),
            'subtotal' => $price,
            'tax' => 0,
            'total' => $price,
            'status' => 'paid',
            'due_date' => $startDate,
            'paid_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $sub = $this->repo->findWithPlan($subId);
        return $this->created($sub, 'Subscription created');
    }

    public function cancel(Request $request, int $id): Response
    {
        $sub = $this->repo->findById($id);
        if (!$sub) {
            throw new NotFoundException('Subscription not found');
        }

        $this->repo->update($id, [
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s'),
        ]);

        $sub = $this->repo->findById($id);
        return $this->success($sub, 'Subscription canceled');
    }

    // -- Plan CRUD (Platform Admin) --

    public function storePlan(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|slug|max:100',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric',
            'price_yearly' => 'required|numeric',
            'max_users' => 'nullable|integer',
            'max_facilities' => 'nullable|integer',
            'max_courts' => 'nullable|integer',
            'features' => 'nullable|json',
            'is_active' => 'nullable|integer',
            'sort_order' => 'nullable|integer',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['slug'] = Sanitizer::slug($data['slug']);

        if ($this->repo->planSlugExists($data['slug'])) {
            return $this->validationError(['slug' => ['Slug is already in use']]);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->createPlan($data);
        $plan = $this->repo->findPlanById($id);

        return $this->created($plan, 'Plan created');
    }

    public function updatePlan(Request $request, int $id): Response
    {
        $plan = $this->repo->findPlanById($id);
        if (!$plan) {
            throw new NotFoundException('Plan not found');
        }

        $data = Validator::validate($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|slug|max:100',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric',
            'price_yearly' => 'required|numeric',
            'max_users' => 'nullable|integer',
            'max_facilities' => 'nullable|integer',
            'max_courts' => 'nullable|integer',
            'features' => 'nullable|json',
            'is_active' => 'nullable|integer',
            'sort_order' => 'nullable|integer',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['slug'] = Sanitizer::slug($data['slug']);

        if ($this->repo->planSlugExists($data['slug'], $id)) {
            return $this->validationError(['slug' => ['Slug is already in use']]);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->repo->updatePlan($id, $data);
        $plan = $this->repo->findPlanById($id);

        return $this->success($plan, 'Plan updated');
    }

    public function destroyPlan(Request $request, int $id): Response
    {
        $plan = $this->repo->findPlanById($id);
        if (!$plan) {
            throw new NotFoundException('Plan not found');
        }

        $this->repo->deletePlan($id);
        return $this->success(null, 'Plan deleted');
    }

    // -- Invoices --

    public function invoices(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);

        $result = $this->repo->findInvoices($orgId, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
