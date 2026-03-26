<?php

declare(strict_types=1);

namespace App\Modules\Memberships;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;
use App\Modules\AuditLogs\AuditLogRepository;

final class MembershipPlanController extends Controller
{
    private MembershipPlanRepository $repo;
    private AuditLogRepository $auditLog;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new MembershipPlanRepository($db);
        $this->auditLog = new AuditLogRepository($db);
    }

    // ── Plans ──────────────────────────────────────────

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));
        $facilityId = $request->input('facility_id') ? (int) $request->input('facility_id') : null;

        $result = $this->repo->findByOrganization($orgId, $search ?: null, $facilityId, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $plan = $this->repo->findWithBenefits($id);
        if (!$plan) {
            throw new NotFoundException('Membership plan not found');
        }
        return $this->success($plan);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'facility_id'    => 'required|integer',
            'name'           => 'required|string|max:200',
            'description'    => 'nullable|string|max:5000',
            'duration_type'  => 'required|in:monthly,3months,6months,12months,custom',
            'duration_value' => 'nullable|integer',
            'price'          => 'required|numeric',
            'setup_fee'      => 'nullable|numeric',
            'renewal_type'   => 'nullable|in:auto,manual,none',
            'is_taxable'     => 'nullable|boolean',
            'color'          => 'nullable|string|max:7',
            'max_members'    => 'nullable|integer',
        ]);

        $durationMap = ['monthly' => 1, '3months' => 3, '6months' => 6, '12months' => 12];

        $planData = [
            'uuid'            => $this->generateUuid(),
            'organization_id' => $request->organizationId(),
            'facility_id'     => (int) $data['facility_id'],
            'name'            => Sanitizer::string($data['name']),
            'description'     => $data['description'] ?? null,
            'duration_type'   => $data['duration_type'],
            'duration_value'  => (int) ($data['duration_value'] ?? $durationMap[$data['duration_type']] ?? 1),
            'price'           => round((float) $data['price'], 2),
            'setup_fee'       => round((float) ($data['setup_fee'] ?? 0), 2),
            'renewal_type'    => $data['renewal_type'] ?? 'auto',
            'is_taxable'      => !empty($data['is_taxable']) ? 1 : 0,
            'color'           => $data['color'] ?? '#6366f1',
            'max_members'     => $data['max_members'] ?? null,
            'is_active'       => 1,
            'created_by'      => $request->userId(),
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $id = $this->repo->create($planData);

        // Sync benefits
        $this->syncBenefitsFromRequest($request, $id);

        $plan = $this->repo->findWithBenefits($id);

        $this->auditLog->log(
            $request->organizationId(), $request->userId(),
            'created', 'membership_plan', $id,
            null, $plan,
            $request->ip(), $request->header('User-Agent')
        );

        return $this->created($plan, 'Membership plan created');
    }

    public function update(Request $request, int $id): Response
    {
        $plan = $this->repo->findById($id);
        if (!$plan) {
            throw new NotFoundException('Membership plan not found');
        }

        $data = Validator::validate($request->all(), [
            'name'           => 'required|string|max:200',
            'description'    => 'nullable|string|max:5000',
            'duration_type'  => 'required|in:monthly,3months,6months,12months,custom',
            'duration_value' => 'nullable|integer',
            'price'          => 'required|numeric',
            'setup_fee'      => 'nullable|numeric',
            'renewal_type'   => 'nullable|in:auto,manual,none',
            'is_taxable'     => 'nullable|boolean',
            'color'          => 'nullable|string|max:7',
            'max_members'    => 'nullable|integer',
            'is_active'      => 'nullable|boolean',
        ]);

        $durationMap = ['monthly' => 1, '3months' => 3, '6months' => 6, '12months' => 12];

        $updateData = [
            'name'            => Sanitizer::string($data['name']),
            'description'     => $data['description'] ?? null,
            'duration_type'   => $data['duration_type'],
            'duration_value'  => (int) ($data['duration_value'] ?? $durationMap[$data['duration_type']] ?? 1),
            'price'           => round((float) $data['price'], 2),
            'setup_fee'       => round((float) ($data['setup_fee'] ?? 0), 2),
            'renewal_type'    => $data['renewal_type'] ?? 'auto',
            'is_taxable'      => !empty($data['is_taxable']) ? 1 : 0,
            'color'           => $data['color'] ?? '#6366f1',
            'max_members'     => $data['max_members'] ?? null,
            'is_active'       => isset($data['is_active']) ? ($data['is_active'] ? 1 : 0) : (int) $plan['is_active'],
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->repo->update($id, $updateData);
        $this->syncBenefitsFromRequest($request, $id);

        $updated = $this->repo->findWithBenefits($id);

        $this->auditLog->log(
            $request->organizationId(), $request->userId(),
            'updated', 'membership_plan', $id,
            $plan, $updated,
            $request->ip(), $request->header('User-Agent')
        );

        return $this->success($updated, 'Membership plan updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $plan = $this->repo->findById($id);
        if (!$plan) {
            throw new NotFoundException('Membership plan not found');
        }

        $this->auditLog->log(
            $request->organizationId(), $request->userId(),
            'deleted', 'membership_plan', $id,
            $plan, null,
            $request->ip(), $request->header('User-Agent')
        );

        $this->repo->delete($id);
        return $this->success(null, 'Membership plan deleted');
    }

    public function toggleStatus(Request $request, int $id): Response
    {
        $plan = $this->repo->findById($id);
        if (!$plan) {
            throw new NotFoundException('Membership plan not found');
        }

        $newStatus = !(bool) $plan['is_active'];
        $this->repo->togglePlanStatus($id, $newStatus);

        return $this->success(['is_active' => $newStatus], 'Status updated');
    }

    public function reorder(Request $request): Response
    {
        $order = $request->input('order', []);
        if (!is_array($order) || empty($order)) {
            return $this->validationError(['order' => ['Order array is required']]);
        }

        $this->repo->reorderPlans($order);
        return $this->success(null, 'Order updated');
    }

    // ── Player Memberships ─────────────────────────────

    public function memberships(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));
        $status = $request->input('status') ? Sanitizer::string($request->input('status')) : null;

        $result = $this->repo->findMembershipsByOrganization($orgId, $search ?: null, $status, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function showMembership(Request $request, int $id): Response
    {
        $membership = $this->repo->findMembershipById($id);
        if (!$membership) {
            throw new NotFoundException('Membership not found');
        }
        return $this->success($membership);
    }

    public function assignMembership(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'membership_plan_id' => 'required|integer',
            'player_id'          => 'required|integer',
            'facility_id'        => 'required|integer',
            'start_date'         => 'required|string',
            'amount_paid'        => 'nullable|numeric',
            'payment_reference'  => 'nullable|string|max:255',
            'notes'              => 'nullable|string|max:5000',
        ]);

        $plan = $this->repo->findById((int) $data['membership_plan_id']);
        if (!$plan) {
            throw new NotFoundException('Membership plan not found');
        }

        $startDate = $data['start_date'];
        $endDate = date('Y-m-d', strtotime($startDate . " +{$plan['duration_value']} months"));

        $membershipData = [
            'uuid'               => $this->generateUuid(),
            'organization_id'    => $request->organizationId(),
            'membership_plan_id' => (int) $data['membership_plan_id'],
            'player_id'          => (int) $data['player_id'],
            'facility_id'        => (int) $data['facility_id'],
            'status'             => 'active',
            'start_date'         => $startDate,
            'end_date'           => $endDate,
            'amount_paid'        => round((float) ($data['amount_paid'] ?? $plan['price']), 2),
            'payment_reference'  => $data['payment_reference'] ?? null,
            'notes'              => $data['notes'] ?? null,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        $id = $this->repo->createMembership($membershipData);
        $membership = $this->repo->findMembershipById($id);

        $this->auditLog->log(
            $request->organizationId(), $request->userId(),
            'created', 'player_membership', $id,
            null, $membership,
            $request->ip(), $request->header('User-Agent')
        );

        return $this->created($membership, 'Membership assigned');
    }

    public function cancelMembership(Request $request, int $id): Response
    {
        $membership = $this->repo->findMembershipById($id);
        if (!$membership) {
            throw new NotFoundException('Membership not found');
        }

        $reason = Sanitizer::string($request->input('reason', ''));

        $this->repo->updateMembership($id, [
            'status'       => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s'),
            'cancel_reason'=> $reason ?: null,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return $this->success(null, 'Membership cancelled');
    }

    // ── Helpers ─────────────────────────────────────────

    private function syncBenefitsFromRequest(Request $request, int $planId): void
    {
        $includedCategories = json_decode($request->input('included_categories', '[]'), true) ?? [];
        $discountedCategories = json_decode($request->input('discounted_categories', '[]'), true) ?? [];
        $this->repo->syncCategoryBenefits($planId, $includedCategories, $discountedCategories);

        $includedSessionTypes = json_decode($request->input('included_session_types', '[]'), true) ?? [];
        $discountedSessionTypes = json_decode($request->input('discounted_session_types', '[]'), true) ?? [];
        $this->repo->syncSessionTypeBenefits($planId, $includedSessionTypes, $discountedSessionTypes);
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
