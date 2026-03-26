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
use App\Modules\AuditLogs\AuditLogRepository;

final class FacilityController extends Controller
{
    private FacilityRepository $repo;
    private AuditLogRepository $auditLog;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new FacilityRepository($db);
        $this->auditLog = new AuditLogRepository($db);
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
        // Parse settings JSON for the client
        if (isset($facility['settings']) && is_string($facility['settings'])) {
            $facility['settings'] = json_decode($facility['settings'], true) ?: [];
        }
        return $this->success($facility);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name'            => 'required|string|max:255',
            'tagline'         => 'nullable|string|max:255',
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
            'tax_rate'        => 'nullable|numeric',
            'image_url'       => 'nullable|string|max:500',
            'instagram_url'   => 'nullable|url|max:500',
            'facebook_url'    => 'nullable|url|max:500',
            'youtube_url'     => 'nullable|url|max:500',
            'twilio_sid'      => 'nullable|string|max:255',
            'twilio_auth_token' => 'nullable|string|max:255',
            'twilio_from_number' => 'nullable|string|max:20',
            'twilio_enabled'  => 'nullable',
            'settings'        => 'nullable',
            'status'          => 'nullable|in:active,inactive,maintenance',
        ]);

        $data['name']            = Sanitizer::string($data['name']);
        $data['slug']            = Sanitizer::string($data['slug']);
        if (isset($data['tagline'])) $data['tagline'] = Sanitizer::string($data['tagline']);
        $data['zip']             = $data['zip_code'] ?? null;
        unset($data['zip_code']);
        $data['tax_rate']        = isset($data['tax_rate']) ? round((float) $data['tax_rate'], 2) : 0.00;
        $data['twilio_enabled']  = !empty($data['twilio_enabled']) ? 1 : 0;

        // Ensure settings is stored as JSON string
        if (isset($data['settings'])) {
            if (is_string($data['settings'])) {
                $decoded = json_decode($data['settings'], true);
                $data['settings'] = json_encode(is_array($decoded) ? $decoded : []);
            } elseif (is_array($data['settings'])) {
                $data['settings'] = json_encode($data['settings']);
            }
        }

        $data['uuid']            = $this->generateUuid();
        $data['organization_id'] = $request->organizationId();
        $data['status']          = $data['status'] ?? 'active';
        $data['created_at']      = date('Y-m-d H:i:s');
        $data['updated_at']      = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);
        $facility = $this->repo->findById($id);

        $this->auditLog->log(
            $request->organizationId(), $request->userId(), 'created',
            'facility', $id, null, ['name' => $data['name']],
            $request->ip(), $request->header('User-Agent')
        );

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
            'tagline'         => 'nullable|string|max:255',
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
            'tax_rate'        => 'nullable|numeric',
            'image_url'       => 'nullable|string|max:500',
            'instagram_url'   => 'nullable|url|max:500',
            'facebook_url'    => 'nullable|url|max:500',
            'youtube_url'     => 'nullable|url|max:500',
            'twilio_sid'      => 'nullable|string|max:255',
            'twilio_auth_token' => 'nullable|string|max:255',
            'twilio_from_number' => 'nullable|string|max:20',
            'twilio_enabled'  => 'nullable',
            'settings'        => 'nullable',
            'status'          => 'nullable|in:active,inactive,maintenance',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['slug'] = Sanitizer::string($data['slug']);
        if (isset($data['tagline'])) $data['tagline'] = Sanitizer::string($data['tagline']);
        $data['zip']  = $data['zip_code'] ?? null;
        unset($data['zip_code']);
        $data['tax_rate'] = isset($data['tax_rate']) ? round((float) $data['tax_rate'], 2) : 0.00;
        $data['twilio_enabled'] = !empty($data['twilio_enabled']) ? 1 : 0;

        // Ensure settings is stored as JSON string
        if (isset($data['settings'])) {
            if (is_string($data['settings'])) {
                $decoded = json_decode($data['settings'], true);
                $data['settings'] = json_encode(is_array($decoded) ? $decoded : []);
            } elseif (is_array($data['settings'])) {
                $data['settings'] = json_encode($data['settings']);
            }
        }

        $this->repo->update($id, $data);
        $facility = $this->repo->findById($id);

        $this->auditLog->log(
            $request->organizationId(), $request->userId(), 'updated',
            'facility', $id, null, ['name' => $data['name']],
            $request->ip(), $request->header('User-Agent')
        );

        return $this->success($facility, 'Facility updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $facility = $this->repo->findById($id);
        if (!$facility) {
            throw new NotFoundException('Facility not found');
        }

        $this->auditLog->log(
            $request->organizationId(), $request->userId(), 'deleted',
            'facility', $id, ['name' => $facility['name']], null,
            $request->ip(), $request->header('User-Agent')
        );

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
