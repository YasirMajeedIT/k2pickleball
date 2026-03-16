<?php

declare(strict_types=1);

namespace App\Modules\SessionDetails;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;
use App\Core\Services\Config;

final class SessionDetailController extends Controller
{
    private SessionDetailRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new SessionDetailRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));
        $categoryId = $request->input('category_id') ? (int) $request->input('category_id') : null;

        $result = $this->repo->findByOrganization($orgId, $search ?: null, $categoryId, $page, $perPage);

        // Attach facility_ids to each session detail
        $sessionIds = array_column($result['data'], 'id');
        $facilityMap = $this->repo->getFacilityIdsForSessions($sessionIds);
        foreach ($result['data'] as &$row) {
            $row['facility_ids'] = $facilityMap[(int) $row['id']] ?? [];
        }
        unset($row);

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    /**
     * Get sessions for a specific category (used in session type form).
     */
    public function byCategory(Request $request): Response
    {
        $orgId = $request->organizationId();
        $categoryId = $request->input('category_id') ? (int) $request->input('category_id') : null;

        if (!$categoryId) {
            return $this->success([]);
        }

        $sessions = $this->repo->findByCategory($orgId, $categoryId);
        return $this->success($sessions);
    }

    public function show(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Session detail not found');
        }
        $record['facility_ids'] = $this->repo->getFacilityIds($id);
        return $this->success($record);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'category_id'      => 'required|integer',
            'session_name'     => 'required|string|max:255',
            'session_tagline'  => 'nullable|string|max:500',
            'description'      => 'nullable|string|max:50000',
            'picture'          => 'nullable|string|max:500',
            'is_active'        => 'nullable|boolean',
        ]);

        $data['session_name'] = Sanitizer::string($data['session_name']);
        $orgId = $request->organizationId();

        // Check duplicate name in same category
        if ($this->repo->nameExistsInCategory($data['session_name'], $orgId, (int) $data['category_id'])) {
            return $this->error('A session with this name already exists in this category', 422);
        }

        if (isset($data['is_active'])) {
            $data['is_active'] = $data['is_active'] ? 1 : 0;
        }

        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $orgId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);

        // Sync facility associations
        $facilityIds = $request->input('facility_ids');
        if (is_array($facilityIds)) {
            $this->repo->syncFacilities($id, array_map('intval', $facilityIds));
        }

        $record = $this->repo->findById($id);
        $record['facility_ids'] = $this->repo->getFacilityIds($id);

        return $this->created($record, 'Session created');
    }

    /**
     * Upload a featured image for a session detail.
     */
    public function uploadPicture(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Session detail not found');
        }

        if (!isset($_FILES['picture']) || $_FILES['picture']['error'] !== UPLOAD_ERR_OK) {
            return $this->validationError(['picture' => ['A valid image file is required']]);
        }

        $file = $_FILES['picture'];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowed, true)) {
            return $this->validationError(['picture' => ['Only JPEG, PNG, GIF, and WebP images are allowed']]);
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return $this->validationError(['picture' => ['Image must be under 5MB']]);
        }

        $orgId = $request->organizationId();
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = $this->generateUuid() . '.' . $ext;

        $storagePath = Config::get('app.storage_path', dirname(__DIR__, 3) . '/storage');
        $dir = $storagePath . '/uploads/' . $orgId . '/session-details';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Delete old picture if exists
        if (!empty($record['picture'])) {
            $oldPath = $storagePath . '/' . ltrim($record['picture'], '/');
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        $destination = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return $this->error('Failed to store image', 500);
        }

        $pictureUrl = '/storage/uploads/' . $orgId . '/session-details/' . $filename;
        $this->repo->update($id, ['picture' => $pictureUrl, 'updated_at' => date('Y-m-d H:i:s')]);

        $updated = $this->repo->findById($id);
        return $this->success($updated, 'Image uploaded');
    }

    /**
     * Remove the featured image for a session detail.
     */
    public function removePicture(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Session detail not found');
        }

        if (!empty($record['picture'])) {
            $storagePath = Config::get('app.storage_path', dirname(__DIR__, 3) . '/storage');
            $oldPath = $storagePath . '/' . ltrim($record['picture'], '/');
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        $this->repo->update($id, ['picture' => null, 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->success(null, 'Image removed');
    }

    public function update(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Session detail not found');
        }

        $data = Validator::validate($request->all(), [
            'category_id'      => 'required|integer',
            'session_name'     => 'required|string|max:255',
            'session_tagline'  => 'nullable|string|max:500',
            'description'      => 'nullable|string|max:50000',
            'picture'          => 'nullable|string|max:500',
            'is_active'        => 'nullable|boolean',
        ]);

        $data['session_name'] = Sanitizer::string($data['session_name']);
        $orgId = $request->organizationId();

        if ($this->repo->nameExistsInCategory($data['session_name'], $orgId, (int) $data['category_id'], $id)) {
            return $this->error('A session with this name already exists in this category', 422);
        }

        if (isset($data['is_active'])) {
            $data['is_active'] = $data['is_active'] ? 1 : 0;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->repo->update($id, $data);

        // Sync facility associations
        $facilityIds = $request->input('facility_ids');
        if (is_array($facilityIds)) {
            $this->repo->syncFacilities($id, array_map('intval', $facilityIds));
        }

        $record = $this->repo->findById($id);
        $record['facility_ids'] = $this->repo->getFacilityIds($id);
        return $this->success($record, 'Session updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Session detail not found');
        }

        $this->repo->delete($id);
        return $this->success(null, 'Session deleted');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
