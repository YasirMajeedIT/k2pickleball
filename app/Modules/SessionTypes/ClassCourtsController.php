<?php

declare(strict_types=1);

namespace App\Modules\SessionTypes;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class ClassCourtsController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * List courts assigned to a class.
     */
    public function index(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);

        $courts = $this->db->fetchAll(
            "SELECT cc.id AS assignment_id, cc.court_id, c.name AS court_name, c.court_number
             FROM st_class_courts cc
             JOIN courts c ON c.id = cc.court_id
             WHERE cc.class_id = ?
             ORDER BY c.court_number ASC",
            [$classId]
        );

        return $this->success($courts);
    }

    /**
     * Sync (replace) court assignments for a class.
     * Expects { "court_ids": [1,2,3] }
     */
    public function sync(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);

        $courtIds = $request->input('court_ids', []);
        if (!is_array($courtIds)) {
            $courtIds = [];
        }

        // Remove existing assignments
        $this->db->delete('st_class_courts', ['class_id' => $classId]);

        // Insert new ones
        foreach ($courtIds as $courtId) {
            $courtId = (int) $courtId;
            if ($courtId > 0) {
                $this->db->insert('st_class_courts', [
                    'class_id'   => $classId,
                    'court_id'   => $courtId,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return $this->index($request, $id, $classId);
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
