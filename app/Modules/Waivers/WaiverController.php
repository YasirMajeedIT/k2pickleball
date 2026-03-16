<?php

declare(strict_types=1);

namespace App\Modules\Waivers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\NotFoundException;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;

final class WaiverController extends Controller
{
    private WaiverRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new WaiverRepository($db);
    }

    /**
     * GET /api/waivers
     */
    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));

        $result = $this->repo->findByOrganization($orgId, $search ?: null, $page, $perPage);

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    /**
     * GET /api/waivers/active
     */
    public function active(Request $request): Response
    {
        $orgId = $request->organizationId();
        $waiver = $this->repo->findActive($orgId);

        if (!$waiver) {
            return $this->success(null, 'No active waiver');
        }

        return $this->success($waiver);
    }

    /**
     * GET /api/waivers/{id}
     */
    public function show(Request $request, int $id): Response
    {
        $waiver = $this->repo->findById($id);
        if (!$waiver) {
            throw new NotFoundException('Waiver not found');
        }

        return $this->success($waiver);
    }

    /**
     * POST /api/waivers
     */
    public function store(Request $request): Response
    {
        $orgId = $request->organizationId();

        $data = Validator::validate($request->all(), [
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'version'        => 'required|string|max:50',
            'effective_date' => 'nullable|string',
            'expiry_date'    => 'nullable|string',
            'is_active'      => 'nullable',
        ]);

        $isActive = !empty($data['is_active']) ? 1 : 0;

        // If activating, deactivate others first
        if ($isActive) {
            $this->repo->deactivateAll($orgId);
        }

        $insertData = [
            'uuid'            => $this->generateUuid(),
            'organization_id' => $orgId,
            'title'           => Sanitizer::string($data['title']),
            'content'         => $data['content'],
            'version'         => Sanitizer::string($data['version']),
            'is_active'       => $isActive,
            'effective_date'  => $data['effective_date'] ?: null,
            'expiry_date'     => $data['expiry_date'] ?: null,
            'created_by'      => $request->userId(),
        ];

        $id = $this->repo->create($insertData);
        $waiver = $this->repo->findById($id);

        return $this->created($waiver, 'Waiver created successfully');
    }

    /**
     * PUT /api/waivers/{id}
     */
    public function update(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();

        $waiver = $this->repo->findById($id);
        if (!$waiver) {
            throw new NotFoundException('Waiver not found');
        }

        $data = Validator::validate($request->all(), [
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'version'        => 'required|string|max:50',
            'effective_date' => 'nullable|string',
            'expiry_date'    => 'nullable|string',
            'is_active'      => 'nullable',
        ]);

        $isActive = !empty($data['is_active']) ? 1 : 0;

        // If activating, deactivate all others first
        if ($isActive) {
            $this->repo->deactivateAll($orgId);
        }

        $this->repo->update($id, [
            'title'          => Sanitizer::string($data['title']),
            'content'        => $data['content'],
            'version'        => Sanitizer::string($data['version']),
            'is_active'      => $isActive,
            'effective_date' => $data['effective_date'] ?: null,
            'expiry_date'    => $data['expiry_date'] ?: null,
        ]);

        $waiver = $this->repo->findById($id);

        return $this->success($waiver, 'Waiver updated successfully');
    }

    /**
     * POST /api/waivers/{id}/activate — Activate this waiver, deactivate all others.
     */
    public function activate(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();

        $waiver = $this->repo->findById($id);
        if (!$waiver) {
            throw new NotFoundException('Waiver not found');
        }

        $this->repo->activate($id, $orgId);

        $waiver = $this->repo->findById($id);

        return $this->success($waiver, 'Waiver activated');
    }

    /**
     * DELETE /api/waivers/{id}
     */
    public function destroy(Request $request, int $id): Response
    {
        $waiver = $this->repo->findById($id);
        if (!$waiver) {
            throw new NotFoundException('Waiver not found');
        }

        $this->repo->delete($id);

        return $this->success(null, 'Waiver deleted successfully');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
