<?php

declare(strict_types=1);

namespace App\Modules\CreditCodes;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class CreditCodeController extends Controller
{
    private CreditCodeRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new CreditCodeRepository($db);
    }

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
        $record = $this->repo->findWithUsages($id);
        if (!$record) {
            throw new NotFoundException('Credit code not found');
        }
        return $this->success($record);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'facility_id'       => 'required|integer',
            'name'              => 'required|string|max:150',
            'code'              => 'nullable|string|max:50',
            'type'              => 'nullable|in:credit,infacility',
            'category'          => 'nullable|in:system,admin',
            'reason'            => 'nullable|string|max:100',
            'amount'            => 'required|numeric',
            'issued_to'         => 'nullable|integer',
            'expires_after_days'=> 'nullable|integer',
            'notes'             => 'nullable|string|max:5000',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $facilityId = (int) $data['facility_id'];

        // Auto-generate code if not provided
        if (empty($data['code'])) {
            $data['code'] = $this->repo->generateUniqueCode($facilityId);
        } else {
            $data['code'] = strtoupper(Sanitizer::string($data['code']));
            if ($this->repo->codeExistsForFacility($facilityId, $data['code'])) {
                return $this->validationError(['code' => ['This code already exists for this facility']]);
            }
        }

        $data['balance'] = round((float) $data['amount'], 2);
        $data['amount'] = round((float) $data['amount'], 2);
        $data['type'] = $data['type'] ?? 'credit';
        $data['category'] = $data['category'] ?? 'admin';
        $data['active'] = 1;
        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $request->organizationId();
        $data['issued_at'] = date('Y-m-d H:i:s');
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);
        $record = $this->repo->findById($id);

        return $this->created($record, 'Credit code created');
    }

    public function update(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Credit code not found');
        }

        $data = Validator::validate($request->all(), [
            'name'              => 'required|string|max:150',
            'reason'            => 'nullable|string|max:100',
            'issued_to'         => 'nullable|integer',
            'expires_after_days'=> 'nullable|integer',
            'active'            => 'nullable|boolean',
            'notes'             => 'nullable|string|max:5000',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['active'] = isset($data['active']) ? ($data['active'] ? 1 : 0) : (int) $record['active'];
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->repo->update($id, $data);
        $record = $this->repo->findById($id);

        return $this->success($record, 'Credit code updated');
    }

    public function recordUsage(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Credit code not found');
        }

        $data = Validator::validate($request->all(), [
            'amount_used' => 'required|numeric',
            'usage_type'  => 'nullable|in:SESSION,MANUAL_PURCHASE,ADMIN_ADJUSTMENT,REFUND',
            'player_id'   => 'nullable|integer',
            'notes'       => 'nullable|string|max:2000',
        ]);

        $amountUsed = round((float) $data['amount_used'], 2);
        $currentBalance = (float) $record['balance'];

        if ($amountUsed > $currentBalance && ($data['usage_type'] ?? 'MANUAL_PURCHASE') !== 'REFUND') {
            return $this->validationError(['amount_used' => ['Amount exceeds available balance ($' . number_format($currentBalance, 2) . ')']]);
        }

        $usageData = [
            'uuid'           => $this->generateUuid(),
            'credit_code_id' => $id,
            'player_id'      => $data['player_id'] ?? null,
            'amount_used'    => $amountUsed,
            'usage_type'     => $data['usage_type'] ?? 'MANUAL_PURCHASE',
            'notes'          => $data['notes'] ?? null,
            'used_at'        => date('Y-m-d H:i:s'),
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $this->repo->addUsage($usageData);

        // Update balance
        $newBalance = ($data['usage_type'] ?? 'MANUAL_PURCHASE') === 'REFUND'
            ? $currentBalance + $amountUsed
            : $currentBalance - $amountUsed;

        $this->repo->update($id, ['balance' => max(0, round($newBalance, 2)), 'updated_at' => date('Y-m-d H:i:s')]);

        $record = $this->repo->findWithUsages($id);
        return $this->success($record, 'Usage recorded');
    }

    public function destroy(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Credit code not found');
        }

        $this->repo->delete($id);
        return $this->success(null, 'Credit code deleted');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
