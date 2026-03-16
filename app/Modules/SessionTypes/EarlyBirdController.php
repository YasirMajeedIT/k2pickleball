<?php

declare(strict_types=1);

namespace App\Modules\SessionTypes;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class EarlyBirdController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    public function show(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);

        $eb = $this->db->fetch(
            "SELECT * FROM st_early_birds WHERE class_id = ?",
            [$classId]
        );

        return $this->success($eb);
    }

    public function upsert(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);

        $data = Validator::validate($request->all(), [
            'discount_price' => 'required|numeric',
            'cutoff_hours'   => 'nullable|integer',
            'is_active'      => 'nullable|boolean',
        ]);

        $existing = $this->db->fetch("SELECT id FROM st_early_birds WHERE class_id = ?", [$classId]);

        if ($existing) {
            $this->db->update('st_early_birds', [
                'discount_price' => (float) $data['discount_price'],
                'cutoff_hours'   => (int) ($data['cutoff_hours'] ?? 24),
                'is_active'      => isset($data['is_active']) ? ($data['is_active'] ? 1 : 0) : 1,
                'updated_at'     => date('Y-m-d H:i:s'),
            ], ['id' => $existing['id']]);
        } else {
            $this->db->insert('st_early_birds', [
                'class_id'       => $classId,
                'discount_price' => (float) $data['discount_price'],
                'cutoff_hours'   => (int) ($data['cutoff_hours'] ?? 24),
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        $eb = $this->db->fetch("SELECT * FROM st_early_birds WHERE class_id = ?", [$classId]);
        return $this->success($eb, 'Early bird saved');
    }

    public function destroy(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);
        $this->db->delete('st_early_birds', ['class_id' => $classId]);
        return $this->success(null, 'Early bird removed');
    }

    private function ensureClassExists(int $sessionTypeId, int $classId): void
    {
        $class = $this->db->fetch(
            "SELECT id FROM st_classes WHERE id = ? AND session_type_id = ?",
            [$classId, $sessionTypeId]
        );
        if (!$class) {
            throw new NotFoundException('Class not found');
        }
    }
}
