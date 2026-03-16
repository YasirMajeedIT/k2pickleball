<?php

declare(strict_types=1);

namespace App\Modules\Players;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class PlayerController extends Controller
{
    private PlayerRepository $repo;
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
        $this->repo = new PlayerRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));
        $status = $request->input('status');

        $result = $this->repo->findByOrganization($orgId, $search ?: null, $status, $page, $perPage);

        // Strip password hashes
        foreach ($result['data'] as &$player) {
            unset($player['password_hash']);
        }

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $player = $this->repo->findById($id);
        if (!$player) {
            throw new NotFoundException('Player not found');
        }
        unset($player['password_hash']);
        return $this->success($player);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'first_name'              => 'required|string|max:100',
            'last_name'               => 'required|string|max:100',
            'email'                   => 'nullable|email|max:255',
            'phone'                   => 'nullable|phone',
            'date_of_birth'           => 'nullable|date',
            'gender'                  => 'nullable|in:male,female,other,prefer_not_to_say',
            'skill_level'             => 'nullable|in:beginner,intermediate,advanced,pro',
            'rating'                  => 'nullable|numeric',
            'dupr_rating'             => 'nullable|numeric',
            'dupr_id'                 => 'nullable|string|max:50',
            'emergency_contact_name'  => 'nullable|string|max:200',
            'emergency_contact_phone' => 'nullable|phone',
            'medical_notes'           => 'nullable|string|max:5000',
            'notes'                   => 'nullable|string|max:5000',
            'address'                 => 'nullable|string|max:255',
            'city'                    => 'nullable|string|max:100',
            'state'                   => 'nullable|string|max:50',
            'zip_code'                => 'nullable|string|max:20',
            'is_waiver'               => 'nullable|boolean',
            'is_teen'                 => 'nullable|boolean',
            'is_email_marketing'      => 'nullable|boolean',
            'is_sms_marketing'        => 'nullable|boolean',
            'status'                  => 'nullable|in:active,inactive,suspended',
        ]);

        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);
        $orgId = $request->organizationId();

        if (!empty($data['email']) && $this->repo->emailExists($orgId, $data['email'])) {
            return $this->validationError(['email' => ['Email is already registered for another player']]);
        }

        $data['is_waiver'] = !empty($data['is_waiver']) ? 1 : 0;
        $data['is_teen'] = !empty($data['is_teen']) ? 1 : 0;
        $data['is_email_marketing'] = isset($data['is_email_marketing']) ? ($data['is_email_marketing'] ? 1 : 0) : 1;
        $data['is_sms_marketing'] = isset($data['is_sms_marketing']) ? ($data['is_sms_marketing'] ? 1 : 0) : 1;

        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $orgId;
        $data['status'] = $data['status'] ?? 'active';
        $data['date_joined'] = date('Y-m-d H:i:s');
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);
        $player = $this->repo->findById($id);
        unset($player['password_hash']);

        return $this->created($player, 'Player created');
    }

    public function update(Request $request, int $id): Response
    {
        $player = $this->repo->findById($id);
        if (!$player) {
            throw new NotFoundException('Player not found');
        }

        $data = Validator::validate($request->all(), [
            'first_name'              => 'required|string|max:100',
            'last_name'               => 'required|string|max:100',
            'email'                   => 'nullable|email|max:255',
            'phone'                   => 'nullable|phone',
            'date_of_birth'           => 'nullable|date',
            'gender'                  => 'nullable|in:male,female,other,prefer_not_to_say',
            'skill_level'             => 'nullable|in:beginner,intermediate,advanced,pro',
            'rating'                  => 'nullable|numeric',
            'dupr_rating'             => 'nullable|numeric',
            'dupr_id'                 => 'nullable|string|max:50',
            'emergency_contact_name'  => 'nullable|string|max:200',
            'emergency_contact_phone' => 'nullable|phone',
            'medical_notes'           => 'nullable|string|max:5000',
            'notes'                   => 'nullable|string|max:5000',
            'address'                 => 'nullable|string|max:255',
            'city'                    => 'nullable|string|max:100',
            'state'                   => 'nullable|string|max:50',
            'zip_code'                => 'nullable|string|max:20',
            'is_waiver'               => 'nullable|boolean',
            'is_teen'                 => 'nullable|boolean',
            'is_email_marketing'      => 'nullable|boolean',
            'is_sms_marketing'        => 'nullable|boolean',
            'status'                  => 'nullable|in:active,inactive,suspended',
        ]);

        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);
        $orgId = $request->organizationId();

        if (!empty($data['email']) && $this->repo->emailExists($orgId, $data['email'], $id)) {
            return $this->validationError(['email' => ['Email is already registered for another player']]);
        }

        $data['is_waiver'] = !empty($data['is_waiver']) ? 1 : 0;
        $data['is_teen'] = !empty($data['is_teen']) ? 1 : 0;
        $data['is_email_marketing'] = isset($data['is_email_marketing']) ? ($data['is_email_marketing'] ? 1 : 0) : 1;
        $data['is_sms_marketing'] = isset($data['is_sms_marketing']) ? ($data['is_sms_marketing'] ? 1 : 0) : 1;
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->repo->update($id, $data);
        $player = $this->repo->findById($id);
        unset($player['password_hash']);

        return $this->success($player, 'Player updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $player = $this->repo->findById($id);
        if (!$player) {
            throw new NotFoundException('Player not found');
        }

        // Delete avatar file if exists
        if (!empty($player['avatar_url'])) {
            $this->deleteAvatarFile($player['avatar_url']);
        }

        $this->repo->delete($id);
        return $this->success(null, 'Player deleted');
    }

    public function uploadAvatar(Request $request, int $id): Response
    {
        $player = $this->repo->findById($id);
        if (!$player) {
            throw new NotFoundException('Player not found');
        }

        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            return $this->validationError(['avatar' => ['No valid file uploaded']]);
        }

        $file = $_FILES['avatar'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mime, $allowed, true)) {
            return $this->validationError(['avatar' => ['File must be an image (JPEG, PNG, GIF, WebP)']]);
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return $this->validationError(['avatar' => ['File must be under 5MB']]);
        }

        // Delete old avatar
        if (!empty($player['avatar_url'])) {
            $this->deleteAvatarFile($player['avatar_url']);
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $orgId = $request->organizationId();
        $uuid = $this->generateUuid();
        $dir = dirname(__DIR__, 3) . '/storage/uploads/' . $orgId . '/avatars';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $uuid . '.' . $ext;
        $path = $dir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return $this->validationError(['avatar' => ['Failed to save file']]);
        }

        $avatarUrl = '/storage/uploads/' . $orgId . '/avatars/' . $filename;
        $this->repo->update($id, ['avatar_url' => $avatarUrl, 'updated_at' => date('Y-m-d H:i:s')]);

        $player = $this->repo->findById($id);
        unset($player['password_hash']);

        return $this->success($player, 'Avatar uploaded');
    }

    public function deleteAvatar(Request $request, int $id): Response
    {
        $player = $this->repo->findById($id);
        if (!$player) {
            throw new NotFoundException('Player not found');
        }

        if (!empty($player['avatar_url'])) {
            $this->deleteAvatarFile($player['avatar_url']);
            $this->repo->update($id, ['avatar_url' => null, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $player = $this->repo->findById($id);
        unset($player['password_hash']);

        return $this->success($player, 'Avatar removed');
    }

    private function deleteAvatarFile(string $avatarUrl): void
    {
        $fullPath = dirname(__DIR__, 3) . $avatarUrl;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
