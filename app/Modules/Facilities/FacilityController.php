<?php

declare(strict_types=1);

namespace App\Modules\Facilities;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class FacilityController extends Controller
{
    private FacilityRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new FacilityRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));

        $result = $this->repo->findByOrganization($orgId, $search ?: null, $page, $perPage);

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $facility = $this->repo->findWithCourts($id);
        if (!$facility) {
            throw new NotFoundException('Facility not found');
        }
        return $this->success($facility);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name'            => 'required|string|max:255',
            'slug'            => 'required|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'address_line1'   => 'nullable|string|max:255',
            'address_line2'   => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'zip_code'        => 'nullable|string|max:20',
            'country'         => 'nullable|string|max:2',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'phone'           => 'nullable|phone',
            'email'           => 'nullable|email|max:255',
            'timezone'        => 'nullable|string|max:100',
            'operating_hours' => 'nullable|json',
            'amenities'       => 'nullable|json',
            'status'          => 'nullable|in:active,inactive,maintenance',
        ]);

        $data['name']            = Sanitizer::string($data['name']);
        $data['slug']            = Sanitizer::string($data['slug']);
        $data['zip']             = $data['zip_code'] ?? null;
        unset($data['zip_code']);

        // Pack operating_hours and amenities into settings JSON
        $settings = [];
        if (isset($data['operating_hours'])) {
            $settings['operating_hours'] = is_string($data['operating_hours']) ? json_decode($data['operating_hours'], true) : $data['operating_hours'];
            unset($data['operating_hours']);
        }
        if (isset($data['amenities'])) {
            $settings['amenities'] = is_string($data['amenities']) ? json_decode($data['amenities'], true) : $data['amenities'];
            unset($data['amenities']);
        }
        if (!empty($settings)) {
            $data['settings'] = json_encode($settings);
        }

        $data['uuid']            = $this->generateUuid();
        $data['organization_id'] = $request->organizationId();
        $data['status']          = $data['status'] ?? 'active';
        $data['created_at']      = date('Y-m-d H:i:s');
        $data['updated_at']      = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);
        $facility = $this->repo->findById($id);

        return $this->created($facility, 'Facility created');
    }

    public function update(Request $request, int $id): Response
    {
        $facility = $this->repo->findById($id);
        if (!$facility) {
            throw new NotFoundException('Facility not found');
        }

        $data = Validator::validate($request->all(), [
            'name'            => 'required|string|max:255',
            'slug'            => 'required|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'address_line1'   => 'nullable|string|max:255',
            'address_line2'   => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'zip_code'        => 'nullable|string|max:20',
            'country'         => 'nullable|string|max:2',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'phone'           => 'nullable|phone',
            'email'           => 'nullable|email|max:255',
            'timezone'        => 'nullable|string|max:100',
            'operating_hours' => 'nullable|json',
            'amenities'       => 'nullable|json',
            'status'          => 'nullable|in:active,inactive,maintenance',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['slug'] = Sanitizer::string($data['slug']);
        $data['zip']  = $data['zip_code'] ?? null;
        unset($data['zip_code']);

        // Pack operating_hours and amenities into settings JSON
        $settings = [];
        if (isset($data['operating_hours'])) {
            $settings['operating_hours'] = is_string($data['operating_hours']) ? json_decode($data['operating_hours'], true) : $data['operating_hours'];
            unset($data['operating_hours']);
        }
        if (isset($data['amenities'])) {
            $settings['amenities'] = is_string($data['amenities']) ? json_decode($data['amenities'], true) : $data['amenities'];
            unset($data['amenities']);
        }
        if (!empty($settings)) {
            $data['settings'] = json_encode($settings);
        }

        $this->repo->update($id, $data);
        $facility = $this->repo->findById($id);

        return $this->success($facility, 'Facility updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $facility = $this->repo->findById($id);
        if (!$facility) {
            throw new NotFoundException('Facility not found');
        }

        $this->repo->delete($id);
        return $this->success(null, 'Facility deleted');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
