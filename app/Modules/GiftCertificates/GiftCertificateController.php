<?php

declare(strict_types=1);

namespace App\Modules\GiftCertificates;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class GiftCertificateController extends Controller
{
    private GiftCertificateRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new GiftCertificateRepository($db);
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
            throw new NotFoundException('Gift certificate not found');
        }
        return $this->success($record);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'facility_id'        => 'required|integer',
            'certificate_name'   => 'nullable|string|max:200',
            'code'               => 'nullable|string|max:50',
            'value'              => 'required|numeric',
            'paid_amount'        => 'nullable|numeric',
            'currency'           => 'nullable|string|max:3',
            'buyer_first_name'   => 'nullable|string|max:100',
            'buyer_last_name'    => 'nullable|string|max:100',
            'buyer_email'        => 'nullable|email|max:255',
            'buyer_phone'        => 'nullable|string|max:30',
            'recipient_first_name' => 'nullable|string|max:100',
            'recipient_last_name'  => 'nullable|string|max:100',
            'recipient_email'    => 'nullable|email|max:255',
            'recipient_phone'    => 'nullable|string|max:30',
            'gift_message'       => 'nullable|string|max:5000',
            'start_using_after'  => 'nullable|date',
            'expired_at'         => 'nullable|date',
            'notes'              => 'nullable|string|max:5000',
        ]);

        $facilityId = (int) $data['facility_id'];

        if (empty($data['code'])) {
            $data['code'] = $this->repo->generateUniqueCode($facilityId);
        } else {
            $data['code'] = strtoupper(Sanitizer::string($data['code']));
            if ($this->repo->codeExistsForFacility($facilityId, $data['code'])) {
                return $this->validationError(['code' => ['This code already exists for this facility']]);
            }
        }

        $value = round((float) $data['value'], 2);
        $data['value'] = $value;
        $data['original_value'] = $value;
        $data['paid_amount'] = isset($data['paid_amount']) ? round((float) $data['paid_amount'], 2) : $value;
        $data['currency'] = $data['currency'] ?? 'USD';
        $data['status'] = 'active';
        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $request->organizationId();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Sanitize name fields
        foreach (['certificate_name', 'buyer_first_name', 'buyer_last_name', 'recipient_first_name', 'recipient_last_name'] as $field) {
            if (!empty($data[$field])) {
                $data[$field] = Sanitizer::string($data[$field]);
            }
        }

        $id = $this->repo->create($data);
        $record = $this->repo->findById($id);

        return $this->created($record, 'Gift certificate created');
    }

    public function update(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Gift certificate not found');
        }

        $data = Validator::validate($request->all(), [
            'certificate_name'   => 'nullable|string|max:200',
            'status'             => 'nullable|in:active,redeemed,expired',
            'buyer_first_name'   => 'nullable|string|max:100',
            'buyer_last_name'    => 'nullable|string|max:100',
            'buyer_email'        => 'nullable|email|max:255',
            'buyer_phone'        => 'nullable|string|max:30',
            'recipient_first_name' => 'nullable|string|max:100',
            'recipient_last_name'  => 'nullable|string|max:100',
            'recipient_email'    => 'nullable|email|max:255',
            'recipient_phone'    => 'nullable|string|max:30',
            'gift_message'       => 'nullable|string|max:5000',
            'start_using_after'  => 'nullable|date',
            'expired_at'         => 'nullable|date',
            'notes'              => 'nullable|string|max:5000',
        ]);

        foreach (['certificate_name', 'buyer_first_name', 'buyer_last_name', 'recipient_first_name', 'recipient_last_name'] as $field) {
            if (!empty($data[$field])) {
                $data[$field] = Sanitizer::string($data[$field]);
            }
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->repo->update($id, $data);
        $record = $this->repo->findById($id);

        return $this->success($record, 'Gift certificate updated');
    }

    public function recordUsage(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Gift certificate not found');
        }

        $data = Validator::validate($request->all(), [
            'amount_used'    => 'required|numeric',
            'reference_id'   => 'nullable|string|max:100',
            'reference_type' => 'nullable|string|max:50',
            'used_by'        => 'nullable|string|max:255',
            'notes'          => 'nullable|string|max:2000',
        ]);

        $amountUsed = round((float) $data['amount_used'], 2);
        $currentValue = (float) $record['value'];

        if ($amountUsed > $currentValue) {
            return $this->validationError(['amount_used' => ['Amount exceeds remaining value ($' . number_format($currentValue, 2) . ')']]);
        }

        $newBalance = max(0, round($currentValue - $amountUsed, 2));

        $usageData = [
            'uuid'                => $this->generateUuid(),
            'gift_certificate_id' => $id,
            'usage_date'          => date('Y-m-d H:i:s'),
            'amount_used'         => $amountUsed,
            'remaining_balance'   => $newBalance,
            'reference_id'        => $data['reference_id'] ?? null,
            'reference_type'      => $data['reference_type'] ?? null,
            'notes'               => $data['notes'] ?? null,
            'used_by'             => $data['used_by'] ?? null,
            'created_at'          => date('Y-m-d H:i:s'),
        ];

        $this->repo->addUsage($usageData);

        $updateData = ['value' => $newBalance, 'updated_at' => date('Y-m-d H:i:s')];
        if ($newBalance <= 0) {
            $updateData['status'] = 'redeemed';
        }
        $this->repo->update($id, $updateData);

        $record = $this->repo->findWithUsages($id);
        return $this->success($record, 'Usage recorded');
    }

    public function destroy(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Gift certificate not found');
        }

        $this->repo->delete($id);
        return $this->success(null, 'Gift certificate deleted');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
