<?php

declare(strict_types=1);

namespace App\Modules\SessionTypes;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class ClassNotesController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    public function index(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);

        $notes = $this->db->fetchAll(
            "SELECT n.*, u.first_name, u.last_name
             FROM st_class_notes n
             LEFT JOIN users u ON u.id = n.user_id
             WHERE n.class_id = ?
             ORDER BY n.created_at DESC",
            [$classId]
        );

        return $this->success($notes);
    }

    public function store(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);

        $note = trim(Sanitizer::string($request->input('note', '')));
        if ($note === '') {
            return Response::json(['error' => 'Note content is required'], 422);
        }

        $userId = $request->input('user_id');

        $noteId = $this->db->insert('st_class_notes', [
            'class_id'   => $classId,
            'user_id'    => $userId ?: null,
            'note'       => $note,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $created = $this->db->fetch("SELECT * FROM st_class_notes WHERE id = ?", [$noteId]);
        return $this->success($created, 'Note added');
    }

    public function destroy(Request $request, int $id, int $classId, int $noteId): Response
    {
        $this->ensureClassExists($id, $classId);

        $note = $this->db->fetch(
            "SELECT * FROM st_class_notes WHERE id = ? AND class_id = ?",
            [$noteId, $classId]
        );
        if (!$note) {
            throw new NotFoundException('Note not found');
        }

        $this->db->delete('st_class_notes', ['id' => $noteId]);
        return $this->success(null, 'Note deleted');
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
