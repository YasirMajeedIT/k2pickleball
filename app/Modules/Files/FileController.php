<?php

declare(strict_types=1);

namespace App\Modules\Files;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;
use App\Core\Services\Config;

final class FileController extends Controller
{
    private FileRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new FileRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $type = $request->input('context');

        $result = $this->repo->findByOrganization($orgId, $type, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $file = $this->repo->findById($id);
        if (!$file) {
            throw new NotFoundException('File not found');
        }
        return $this->success($file);
    }

    /**
     * POST /api/files — Upload a file
     */
    public function upload(Request $request): Response
    {
        // Validate file presence
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return $this->validationError(['file' => ['A valid file is required']]);
        }

        $file = $_FILES['file'];
        $context = Sanitizer::slug($request->input('context', 'general'));

        // Validate file type via finfo (MIME detection)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowedMimes = Config::get('app.upload.allowed_mimes', [
            'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp',
            'application/pdf',
            'text/csv',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);

        if (!in_array($mimeType, $allowedMimes, true)) {
            return $this->validationError(['file' => ['File type not allowed: ' . $mimeType]]);
        }

        // Validate file size
        $maxSize = Config::get('app.upload.max_size', 10 * 1024 * 1024); // 10MB
        if ($file['size'] > $maxSize) {
            return $this->validationError(['file' => ['File exceeds maximum size of ' . ($maxSize / 1024 / 1024) . 'MB']]);
        }

        $orgId = $request->organizationId();
        $uuid = $this->generateUuid();
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName = Sanitizer::filename($file['name']);
        $storedName = $uuid . ($ext ? '.' . strtolower($ext) : '');

        // Build storage path: storage/uploads/{orgId}/{context}/
        $storagePath = Config::get('app.storage_path', dirname(__DIR__, 3) . '/storage');
        $uploadDir = $storagePath . '/uploads/' . $orgId . '/' . $context;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . '/' . $storedName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return $this->error('Failed to store file', 500);
        }

        $fileId = $this->repo->create([
            'uuid' => $uuid,
            'organization_id' => $orgId,
            'user_id' => $request->userId(),
            'name' => $storedName,
            'original_name' => $safeName,
            'mime_type' => $mimeType,
            'size' => $file['size'],
            'path' => 'uploads/' . $orgId . '/' . $context . '/' . $storedName,
            'disk' => 'local',
            'context' => $context,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $record = $this->repo->findById($fileId);
        return $this->created($record, 'File uploaded');
    }

    /**
     * DELETE /api/files/{id}
     */
    public function destroy(Request $request, int $id): Response
    {
        $file = $this->repo->findById($id);
        if (!$file) {
            throw new NotFoundException('File not found');
        }

        // Soft delete the record
        $this->repo->delete($id);

        // Optionally remove physical file
        $storagePath = Config::get('app.storage_path', dirname(__DIR__, 3) . '/storage');
        $filePath = $storagePath . '/' . $file['path'];
        if (is_file($filePath)) {
            unlink($filePath);
        }

        return $this->success(null, 'File deleted');
    }

    /**
     * GET /api/files/usage — storage usage for the org
     */
    public function usage(Request $request): Response
    {
        $orgId = $request->organizationId();
        $totalBytes = $this->repo->totalSizeByOrganization($orgId);

        return $this->success([
            'total_bytes' => $totalBytes,
            'total_mb' => round($totalBytes / (1024 * 1024), 2),
        ]);
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
