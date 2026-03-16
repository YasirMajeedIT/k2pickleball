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

final class ClassController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * List classes for a session type.
     */
    public function index(Request $request, int $id): Response
    {
        $sessionType = $this->db->fetch(
            "SELECT * FROM `session_types` WHERE `id` = ?",
            [$id]
        );
        if (!$sessionType) {
            throw new NotFoundException('Session type not found');
        }

        $classes = $this->db->fetchAll(
            "SELECT c.*, u.first_name AS coach_first_name, u.last_name AS coach_last_name
             FROM `st_classes` c
             LEFT JOIN `users` u ON u.id = c.coach_id
             WHERE c.session_type_id = ?
             ORDER BY c.scheduled_at ASC",
            [$id]
        );

        return $this->success($classes);
    }

    /**
     * Create one or more classes (schedule sessions).
     */
    public function store(Request $request, int $id): Response
    {
        $sessionType = $this->db->fetch(
            "SELECT * FROM `session_types` WHERE `id` = ?",
            [$id]
        );
        if (!$sessionType) {
            throw new NotFoundException('Session type not found');
        }

        $input = $request->all();

        // Support both single and batch creation
        // Batch: { "classes": [{ "date": "2026-03-20", "time": "09:00" }, ...] }
        // Single: { "date": "2026-03-20", "time": "09:00" }
        $entries = [];
        if (!empty($input['classes']) && is_array($input['classes'])) {
            $entries = $input['classes'];
        } else {
            $entries[] = $input;
        }

        $capacity = (int) ($sessionType['capacity'] ?? 0);
        $facilityId = $sessionType['facility_id'] ?? null;

        $created = [];
        $errors = [];

        foreach ($entries as $i => $entry) {
            $date = trim(Sanitizer::string($entry['date'] ?? ''));
            $time = trim(Sanitizer::string($entry['time'] ?? ''));

            if (!$date || !$time) {
                $errors[] = "Entry {$i}: date and time are required";
                continue;
            }

            // Parse date + time into datetime
            $scheduledAt = $this->parseDateTime($date, $time);
            if (!$scheduledAt) {
                $errors[] = "Entry {$i}: invalid date/time format";
                continue;
            }

            // Duplicate check
            $existing = $this->db->fetch(
                "SELECT `id` FROM `st_classes` WHERE `session_type_id` = ? AND `scheduled_at` = ?",
                [$id, $scheduledAt]
            );
            if ($existing) {
                $errors[] = "Entry {$i}: a class already exists at {$scheduledAt}";
                continue;
            }

            $uuid = $this->generateUuid();

            // For series only: first class is open for booking (status=1), rest are hidden (status=0)
            // class and series_rolling always default to booking_status=1
            $bookingStatus = ($sessionType['session_type'] === 'series' && $i > 0) ? 0 : 1;

            $classId = $this->db->insert('st_classes', [
                'uuid'                  => $uuid,
                'session_type_id'       => $id,
                'facility_id'           => $facilityId,
                'scheduled_at'          => $scheduledAt,
                'slots'                 => $capacity,
                'slots_available'       => $capacity,
                'booking_status'        => $bookingStatus,
                'is_active'             => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);

            $created[] = $this->db->fetch("SELECT * FROM `st_classes` WHERE `id` = ?", [$classId]);
        }

        return $this->success([
            'created' => $created,
            'errors'  => $errors,
        ], count($created) . ' class(es) scheduled');
    }

    /**
     * Update a class (coach, courts, status, etc.).
     */
    public function update(Request $request, int $id, int $classId): Response
    {
        $class = $this->db->fetch(
            "SELECT * FROM `st_classes` WHERE `id` = ? AND `session_type_id` = ?",
            [$classId, $id]
        );
        if (!$class) {
            throw new NotFoundException('Class not found');
        }

        $data = Validator::validate($request->all(), [
            'scheduled_at'          => 'nullable|string',
            'coach_id'              => 'nullable|integer',
            'slots'                 => 'nullable|integer',
            'booking_status'        => 'nullable|boolean',
            'is_active'             => 'nullable|boolean',
        ]);

        // Only update provided fields
        $update = ['updated_at' => date('Y-m-d H:i:s')];
        if (isset($data['scheduled_at']) && $data['scheduled_at'] !== null) {
            $update['scheduled_at'] = $data['scheduled_at'];
        }
        if (array_key_exists('coach_id', $data)) {
            $update['coach_id'] = $data['coach_id'];
        }
        if (isset($data['slots'])) {
            $diff = (int) $data['slots'] - (int) $class['slots'];
            $update['slots'] = (int) $data['slots'];
            $update['slots_available'] = max(0, (int) $class['slots_available'] + $diff);
        }
        if (isset($data['booking_status'])) {
            $update['booking_status'] = $data['booking_status'] ? 1 : 0;
        }
        if (isset($data['is_active'])) {
            $update['is_active'] = $data['is_active'] ? 1 : 0;
        }

        $this->db->update('st_classes', $update, ['id' => $classId]);

        $updated = $this->db->fetch("SELECT * FROM `st_classes` WHERE `id` = ?", [$classId]);
        return $this->success($updated, 'Class updated');
    }

    /**
     * Delete a class.
     */
    public function destroy(Request $request, int $id, int $classId): Response
    {
        $class = $this->db->fetch(
            "SELECT * FROM `st_classes` WHERE `id` = ? AND `session_type_id` = ?",
            [$classId, $id]
        );
        if (!$class) {
            throw new NotFoundException('Class not found');
        }

        $this->db->delete('st_classes', ['id' => $classId]);
        return $this->success(null, 'Class deleted');
    }

    /**
     * Bulk delete classes.
     */
    public function bulkDestroy(Request $request, int $id): Response
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return $this->success(null, 'No classes to delete');
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_map('intval', $ids);
        $params[] = $id;

        $this->db->query(
            "DELETE FROM `st_classes` WHERE `id` IN ({$placeholders}) AND `session_type_id` = ?",
            $params
        );

        return $this->success(null, count($ids) . ' class(es) deleted');
    }

    private function parseDateTime(string $date, string $time): ?string
    {
        // Normalize time: "9:00am" -> "09:00", "2:30pm" -> "14:30"
        $time = strtolower(trim($time));

        // Try standard H:i format first
        $dt = \DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        if ($dt) {
            return $dt->format('Y-m-d H:i:s');
        }

        // Try with am/pm (e.g., "9:00am", "2:30pm")
        $time = str_replace([' am', ' pm'], ['am', 'pm'], $time);
        $dt = \DateTime::createFromFormat('Y-m-d g:ia', $date . ' ' . $time);
        if ($dt) {
            return $dt->format('Y-m-d H:i:s');
        }

        $dt = \DateTime::createFromFormat('Y-m-d g:i a', $date . ' ' . $time);
        if ($dt) {
            return $dt->format('Y-m-d H:i:s');
        }

        return null;
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
