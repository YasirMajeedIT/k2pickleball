<?php

declare(strict_types=1);

namespace App\Modules\SessionTypes;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class ClassAttendeeController extends Controller
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

        $attendees = $this->db->fetchAll(
            "SELECT a.*,
                    p.first_name AS player_first_name, p.last_name AS player_last_name,
                    pa.first_name AS partner_first_name, pa.last_name AS partner_last_name
             FROM st_class_attendees a
             LEFT JOIN players p ON p.id = a.player_id
             LEFT JOIN st_class_attendees pa ON pa.id = a.partner_id
             WHERE a.class_id = ?
             ORDER BY a.created_at ASC",
            [$classId]
        );

        // Attach labels
        foreach ($attendees as &$att) {
            $att['labels'] = $this->db->fetchAll(
                "SELECT l.id, l.name, l.color
                 FROM st_attendee_labels al
                 JOIN labels l ON l.id = al.label_id
                 WHERE al.attendee_id = ?",
                [$att['id']]
            );
        }

        return $this->success($attendees);
    }

    public function store(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);

        $data = Validator::validate($request->all(), [
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:30',
            'player_id'    => 'nullable|integer',
            'status'       => 'nullable|string',
            'amount_paid'  => 'nullable|numeric',
            'quote_amount' => 'nullable|numeric',
            'notes'        => 'nullable|string',
        ]);

        $uuid = $this->generateUuid();
        $status = in_array($data['status'] ?? '', ['registered', 'waitlisted', 'cancelled', 'no_show'])
            ? $data['status'] : 'registered';

        $attId = $this->db->insert('st_class_attendees', [
            'uuid'         => $uuid,
            'class_id'     => $classId,
            'player_id'    => $data['player_id'] ?? null,
            'first_name'   => Sanitizer::string($data['first_name']),
            'last_name'    => Sanitizer::string($data['last_name'] ?? ''),
            'email'        => $data['email'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'status'       => $status,
            'amount_paid'  => (float) ($data['amount_paid'] ?? 0),
            'quote_amount' => isset($data['quote_amount']) ? (float) $data['quote_amount'] : null,
            'notes'        => $data['notes'] ?? null,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        // Decrement available slots
        $this->db->query(
            "UPDATE st_classes SET slots_available = GREATEST(0, slots_available - 1), updated_at = NOW() WHERE id = ?",
            [$classId]
        );

        $created = $this->db->fetch("SELECT * FROM st_class_attendees WHERE id = ?", [$attId]);
        return $this->success($created, 'Attendee added');
    }

    public function update(Request $request, int $id, int $classId, int $attendeeId): Response
    {
        $this->ensureClassExists($id, $classId);

        $att = $this->db->fetch(
            "SELECT * FROM st_class_attendees WHERE id = ? AND class_id = ?",
            [$attendeeId, $classId]
        );
        if (!$att) {
            throw new NotFoundException('Attendee not found');
        }

        $data = $request->all();
        $update = ['updated_at' => date('Y-m-d H:i:s')];

        $allowedFields = ['first_name', 'last_name', 'email', 'phone', 'status', 'amount_paid', 'quote_amount', 'notes', 'partner_id', 'checked_in'];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $update[$field] = $data[$field];
            }
        }

        $this->db->update('st_class_attendees', $update, ['id' => $attendeeId]);

        // Handle labels if provided
        if (array_key_exists('label_ids', $data) && is_array($data['label_ids'])) {
            $this->db->query("DELETE FROM st_attendee_labels WHERE attendee_id = ?", [$attendeeId]);
            foreach ($data['label_ids'] as $labelId) {
                $labelId = (int) $labelId;
                if ($labelId > 0) {
                    $this->db->insert('st_attendee_labels', [
                        'attendee_id' => $attendeeId,
                        'label_id'    => $labelId,
                    ]);
                }
            }
        }

        $updated = $this->db->fetch("SELECT * FROM st_class_attendees WHERE id = ?", [$attendeeId]);
        return $this->success($updated, 'Attendee updated');
    }

    public function destroy(Request $request, int $id, int $classId, int $attendeeId): Response
    {
        $this->ensureClassExists($id, $classId);

        $att = $this->db->fetch(
            "SELECT * FROM st_class_attendees WHERE id = ? AND class_id = ?",
            [$attendeeId, $classId]
        );
        if (!$att) {
            throw new NotFoundException('Attendee not found');
        }

        $this->db->delete('st_class_attendees', ['id' => $attendeeId]);

        // Increment available slots if they were registered
        if ($att['status'] === 'registered') {
            $this->db->query(
                "UPDATE st_classes SET slots_available = slots_available + 1, updated_at = NOW() WHERE id = ?",
                [$classId]
            );
        }

        return $this->success(null, 'Attendee removed');
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
