<?php

declare(strict_types=1);

namespace App\Modules\SessionTypes;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class FeedbackController extends Controller
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

        $fb = $this->db->fetch(
            "SELECT * FROM st_feedback_requests WHERE class_id = ?",
            [$classId]
        );

        if ($fb) {
            $fb['responses'] = $this->db->fetchAll(
                "SELECT r.*, a.first_name, a.last_name
                 FROM st_feedback_responses r
                 JOIN st_class_attendees a ON a.id = r.attendee_id
                 WHERE r.feedback_request_id = ?
                 ORDER BY r.created_at DESC",
                [$fb['id']]
            );
        }

        return $this->success($fb);
    }

    public function upsert(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);

        $data = Validator::validate($request->all(), [
            'message'   => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $existing = $this->db->fetch("SELECT id FROM st_feedback_requests WHERE class_id = ?", [$classId]);

        if ($existing) {
            $update = [];
            if (array_key_exists('message', $data)) {
                $update['message'] = $data['message'];
            }
            if (isset($data['is_active'])) {
                $update['is_active'] = $data['is_active'] ? 1 : 0;
            }
            if ($update) {
                $this->db->update('st_feedback_requests', $update, ['id' => $existing['id']]);
            }
        } else {
            $this->db->insert('st_feedback_requests', [
                'class_id'   => $classId,
                'message'    => $data['message'] ?? null,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->show($request, $id, $classId);
    }

    public function send(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);

        $fb = $this->db->fetch("SELECT * FROM st_feedback_requests WHERE class_id = ?", [$classId]);
        if (!$fb) {
            return Response::json(['error' => 'No feedback request configured'], 404);
        }

        $this->db->update('st_feedback_requests', [
            'sent_at' => date('Y-m-d H:i:s'),
        ], ['id' => $fb['id']]);

        return $this->success(null, 'Feedback request sent');
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
