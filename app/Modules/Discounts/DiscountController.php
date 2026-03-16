<?php

declare(strict_types=1);

namespace App\Modules\Discounts;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\NotFoundException;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;

class DiscountController extends Controller
{
    private DiscountRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new DiscountRepository($db);
    }

    /**
     * GET /api/discounts — List discount rules (optionally filtered by facility_id).
     */
    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));
        $facilityId = $request->input('facility_id') ? (int) $request->input('facility_id') : null;

        $result = $this->repo->findByOrganization($orgId, $facilityId, $search ?: null, $page, $perPage);

        // Attach session type ids to each discount
        foreach ($result['data'] as &$discount) {
            $stRows = $this->repo->getSessionTypes((int) $discount['id']);
            $discount['session_type_ids'] = array_map(fn($r) => (int) $r['session_type_id'], $stRows);
        }

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    /**
     * GET /api/discounts/{id} — Show single discount.
     */
    public function show(Request $request, int $id): Response
    {
        $discount = $this->repo->findById($id);
        if (!$discount) {
            throw new NotFoundException('Discount rule not found');
        }
        $stRows = $this->repo->getSessionTypes($id);
        $discount['session_type_ids'] = array_map(fn($r) => (int) $r['session_type_id'], $stRows);
        return $this->success($discount);
    }

    /**
     * POST /api/discounts — Create a discount rule.
     */
    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'facility_id'       => 'nullable|integer',
            'name'              => 'required|string|max:255',
            'discount_category' => 'nullable|string|max:100',
            'coupon_code'       => 'nullable|string|max:50',
            'discount_type'     => 'required|in:fixed,percent',
            'discount_value'    => 'required|numeric',
            'valid_from'        => 'nullable|string',
            'valid_to'          => 'nullable|string',
            'usage_limit'       => 'nullable|integer',
            'is_active'         => 'nullable',
            'session_type_ids'  => 'nullable',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        if (isset($data['coupon_code'])) {
            $data['coupon_code'] = strtoupper(trim(Sanitizer::string($data['coupon_code'])));
        }
        $data['discount_value'] = round((float) $data['discount_value'], 2);
        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $request->organizationId();
        $data['created_by'] = $request->userId();
        $data['is_active'] = !empty($data['is_active']) ? 1 : 0;
        $data['used_count'] = 0;

        $sessionTypeIds = $data['session_type_ids'] ?? [];
        unset($data['session_type_ids']);

        $id = $this->repo->create($data);

        if (is_array($sessionTypeIds) && count($sessionTypeIds) > 0) {
            $this->repo->syncSessionTypes($id, $sessionTypeIds);
        }

        $discount = $this->repo->findById($id);
        $stRows = $this->repo->getSessionTypes($id);
        $discount['session_type_ids'] = array_map(fn($r) => (int) $r['session_type_id'], $stRows);

        return $this->created($discount, 'Discount rule created');
    }

    /**
     * PUT /api/discounts/{id} — Update a discount rule.
     */
    public function update(Request $request, int $id): Response
    {
        $discount = $this->repo->findById($id);
        if (!$discount) {
            throw new NotFoundException('Discount rule not found');
        }

        $data = Validator::validate($request->all(), [
            'facility_id'       => 'nullable|integer',
            'name'              => 'required|string|max:255',
            'discount_category' => 'nullable|string|max:100',
            'coupon_code'       => 'nullable|string|max:50',
            'discount_type'     => 'required|in:fixed,percent',
            'discount_value'    => 'required|numeric',
            'valid_from'        => 'nullable|string',
            'valid_to'          => 'nullable|string',
            'usage_limit'       => 'nullable|integer',
            'is_active'         => 'nullable',
            'session_type_ids'  => 'nullable',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        if (isset($data['coupon_code'])) {
            $data['coupon_code'] = strtoupper(trim(Sanitizer::string($data['coupon_code'])));
        }
        $data['discount_value'] = round((float) $data['discount_value'], 2);
        $data['is_active'] = !empty($data['is_active']) ? 1 : 0;

        $sessionTypeIds = $data['session_type_ids'] ?? [];
        unset($data['session_type_ids']);

        $this->repo->update($id, $data);

        if (is_array($sessionTypeIds)) {
            $this->repo->syncSessionTypes($id, $sessionTypeIds);
        }

        $discount = $this->repo->findById($id);
        $stRows = $this->repo->getSessionTypes($id);
        $discount['session_type_ids'] = array_map(fn($r) => (int) $r['session_type_id'], $stRows);

        return $this->success($discount, 'Discount rule updated');
    }

    /**
     * DELETE /api/discounts/{id} — Delete a discount rule.
     */
    public function destroy(Request $request, int $id): Response
    {
        $discount = $this->repo->findById($id);
        if (!$discount) {
            throw new NotFoundException('Discount rule not found');
        }

        // Remove session type assignments first
        $this->repo->syncSessionTypes($id, []);
        $this->repo->delete($id);

        return $this->success(null, 'Discount rule deleted');
    }

    /**
     * POST /api/discounts/validate-coupon — Validate a coupon code.
     */
    public function validateCoupon(Request $request): Response
    {
        $code = Sanitizer::string($request->input('coupon_code', ''));
        if (empty($code)) {
            return $this->validationError(['coupon_code' => ['Coupon code is required']]);
        }

        $orgId = $request->organizationId();
        $discount = $this->repo->findByCouponCode(strtoupper($code), $orgId);

        if (!$discount) {
            return $this->error('Invalid coupon code', 404);
        }

        // Check active
        if (!$discount['is_active']) {
            return $this->error('This coupon is no longer active', 422);
        }

        // Check date validity
        $now = date('Y-m-d');
        if ($discount['valid_from'] && $now < $discount['valid_from']) {
            return $this->error('This coupon is not yet valid', 422);
        }
        if ($discount['valid_to'] && $now > $discount['valid_to']) {
            return $this->error('This coupon has expired', 422);
        }

        // Check usage limit
        if ($discount['usage_limit'] && $discount['used_count'] >= $discount['usage_limit']) {
            return $this->error('This coupon has reached its usage limit', 422);
        }

        return $this->success([
            'discount_type' => $discount['discount_type'],
            'discount_value' => $discount['discount_value'],
            'name' => $discount['name'],
        ], 'Coupon is valid');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
