<?php

declare(strict_types=1);

namespace App\Modules\Courts;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class CourtController extends Controller
{
    private CourtRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new CourtRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);

        $facilityId = Sanitizer::integer($request->input('facility_id'));

        if ($facilityId) {
            $search = Sanitizer::string($request->input('search', ''));
            $result = $this->repo->findByFacility($orgId, $facilityId, $search ?: null, $page, $perPage);
        } else {
            $result = $this->repo->findByOrganization($orgId, $page, $perPage);
        }

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $court = $this->repo->findById($id);
        if (!$court) {
            throw new NotFoundException('Court not found');
        }
        return $this->success($court);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'facility_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sport_type' => 'required|in:pickleball,tennis,badminton,basketball,volleyball,multi',
            'surface_type' => 'nullable|in:concrete,asphalt,wood,synthetic,clay,grass',
            'is_indoor' => 'nullable|boolean',
            'is_lighted' => 'nullable|boolean',
            'court_number' => 'nullable|integer|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'max_players' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive,maintenance,reserved',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $request->organizationId();
        $data['status'] = $data['status'] ?? 'active';
        $data['is_indoor'] = !empty($data['is_indoor']) ? 1 : 0;
        $data['is_lighted'] = !empty($data['is_lighted']) ? 1 : 0;
        if (isset($data['court_number'])) $data['court_number'] = (int) $data['court_number'];
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);
        $court = $this->repo->findById($id);

        return $this->created($court, 'Court created');
    }

    public function update(Request $request, int $id): Response
    {
        $court = $this->repo->findById($id);
        if (!$court) {
            throw new NotFoundException('Court not found');
        }

        $data = Validator::validate($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sport_type' => 'required|in:pickleball,tennis,badminton,basketball,volleyball,multi',
            'surface_type' => 'nullable|in:concrete,asphalt,wood,synthetic,clay,grass',
            'is_indoor' => 'nullable|boolean',
            'is_lighted' => 'nullable|boolean',
            'court_number' => 'nullable|integer|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'max_players' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive,maintenance,reserved',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        if (isset($data['court_number'])) $data['court_number'] = (int) $data['court_number'];
        if (isset($data['is_indoor'])) {
            $data['is_indoor'] = !empty($data['is_indoor']) ? 1 : 0;
        }
        if (isset($data['is_lighted'])) {
            $data['is_lighted'] = !empty($data['is_lighted']) ? 1 : 0;
        }
        $this->repo->update($id, $data);
        $court = $this->repo->findById($id);

        return $this->success($court, 'Court updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $court = $this->repo->findById($id);
        if (!$court) {
            throw new NotFoundException('Court not found');
        }

        $this->repo->delete($id);
        return $this->success(null, 'Court deleted');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
