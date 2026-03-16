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

final class FormFieldController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * List all form fields for a session type, ordered by sort_order.
     */
    public function index(Request $request, int $id): Response
    {
        $this->ensureSessionTypeExists($id);

        $fields = $this->db->fetchAll(
            "SELECT * FROM `session_form_fields` WHERE `session_type_id` = ? ORDER BY `sort_order` ASC, `id` ASC",
            [$id]
        );

        foreach ($fields as &$field) {
            $field['field_options'] = $field['field_options'] ? json_decode($field['field_options'], true) : [];
            $field['is_required'] = (bool) $field['is_required'];
        }

        return $this->success($fields);
    }

    /**
     * Create a new form field for a session type.
     */
    public function store(Request $request, int $id): Response
    {
        $this->ensureSessionTypeExists($id);

        $data = Validator::validate($request->all(), [
            'field_label'   => 'required|string|max:255',
            'field_name'    => 'required|string|max:255',
            'field_type'    => 'required|string',
            'field_options' => 'nullable|array',
            'placeholder'   => 'nullable|string|max:255',
            'is_required'   => 'nullable|boolean',
            'sort_order'    => 'nullable|integer',
        ]);

        $data['field_label'] = Sanitizer::string($data['field_label']);
        $data['field_name'] = preg_replace('/[^a-z0-9_]/', '', strtolower(Sanitizer::string($data['field_name'])));

        $validTypes = ['text', 'number', 'email', 'phone', 'date', 'textarea', 'select', 'checkbox', 'radio', 'toggle'];
        if (!in_array($data['field_type'], $validTypes)) {
            $data['field_type'] = 'text';
        }

        // Check uniqueness of field_name within this session type
        $existing = $this->db->fetch(
            "SELECT `id` FROM `session_form_fields` WHERE `session_type_id` = ? AND `field_name` = ?",
            [$id, $data['field_name']]
        );
        if ($existing) {
            return $this->error('A field with this name already exists for this session type.', 422);
        }

        $insertData = [
            'session_type_id' => $id,
            'field_label'     => $data['field_label'],
            'field_name'      => $data['field_name'],
            'field_type'      => $data['field_type'],
            'field_options'   => !empty($data['field_options']) ? json_encode($data['field_options']) : null,
            'placeholder'     => $data['placeholder'] ?? null,
            'is_required'     => !empty($data['is_required']) ? 1 : 0,
            'sort_order'      => (int) ($data['sort_order'] ?? 0),
        ];

        $fieldId = $this->db->insert('session_form_fields', $insertData);
        $record = $this->db->fetch("SELECT * FROM `session_form_fields` WHERE `id` = ?", [$fieldId]);
        $record['field_options'] = $record['field_options'] ? json_decode($record['field_options'], true) : [];
        $record['is_required'] = (bool) $record['is_required'];

        return $this->created($record, 'Form field created');
    }

    /**
     * Update an existing form field.
     */
    public function update(Request $request, int $id, int $fieldId): Response
    {
        $this->ensureSessionTypeExists($id);

        $field = $this->db->fetch(
            "SELECT * FROM `session_form_fields` WHERE `id` = ? AND `session_type_id` = ?",
            [$fieldId, $id]
        );
        if (!$field) {
            throw new NotFoundException('Form field not found');
        }

        $data = Validator::validate($request->all(), [
            'field_label'   => 'required|string|max:255',
            'field_name'    => 'required|string|max:255',
            'field_type'    => 'required|string',
            'field_options' => 'nullable|array',
            'placeholder'   => 'nullable|string|max:255',
            'is_required'   => 'nullable|boolean',
            'sort_order'    => 'nullable|integer',
        ]);

        $data['field_label'] = Sanitizer::string($data['field_label']);
        $data['field_name'] = preg_replace('/[^a-z0-9_]/', '', strtolower(Sanitizer::string($data['field_name'])));

        $validTypes = ['text', 'number', 'email', 'phone', 'date', 'textarea', 'select', 'checkbox', 'radio', 'toggle'];
        if (!in_array($data['field_type'], $validTypes)) {
            $data['field_type'] = 'text';
        }

        // Check uniqueness (excluding self)
        $existing = $this->db->fetch(
            "SELECT `id` FROM `session_form_fields` WHERE `session_type_id` = ? AND `field_name` = ? AND `id` != ?",
            [$id, $data['field_name'], $fieldId]
        );
        if ($existing) {
            return $this->error('A field with this name already exists for this session type.', 422);
        }

        $this->db->update('session_form_fields', [
            'field_label'   => $data['field_label'],
            'field_name'    => $data['field_name'],
            'field_type'    => $data['field_type'],
            'field_options' => !empty($data['field_options']) ? json_encode($data['field_options']) : null,
            'placeholder'   => $data['placeholder'] ?? null,
            'is_required'   => !empty($data['is_required']) ? 1 : 0,
            'sort_order'    => (int) ($data['sort_order'] ?? 0),
        ], ['id' => $fieldId]);

        $record = $this->db->fetch("SELECT * FROM `session_form_fields` WHERE `id` = ?", [$fieldId]);
        $record['field_options'] = $record['field_options'] ? json_decode($record['field_options'], true) : [];
        $record['is_required'] = (bool) $record['is_required'];

        return $this->success($record, 'Form field updated');
    }

    /**
     * Delete a form field.
     */
    public function destroy(Request $request, int $id, int $fieldId): Response
    {
        $this->ensureSessionTypeExists($id);

        $field = $this->db->fetch(
            "SELECT * FROM `session_form_fields` WHERE `id` = ? AND `session_type_id` = ?",
            [$fieldId, $id]
        );
        if (!$field) {
            throw new NotFoundException('Form field not found');
        }

        $this->db->delete('session_form_fields', ['id' => $fieldId]);

        return $this->success(null, 'Form field deleted');
    }

    /**
     * Bulk sync form fields: replaces all fields for a session type.
     */
    public function sync(Request $request, int $id): Response
    {
        $this->ensureSessionTypeExists($id);

        $data = Validator::validate($request->all(), [
            'fields' => 'required|array',
        ]);

        $validTypes = ['text', 'number', 'email', 'phone', 'date', 'textarea', 'select', 'checkbox', 'radio', 'toggle'];

        $this->db->transaction(function () use ($id, $data, $validTypes) {
            $this->db->query("DELETE FROM `session_form_fields` WHERE `session_type_id` = ?", [$id]);

            foreach ($data['fields'] as $i => $field) {
                $label = Sanitizer::string(trim((string) ($field['field_label'] ?? '')));
                $name = preg_replace('/[^a-z0-9_]/', '', strtolower(trim((string) ($field['field_name'] ?? ''))));
                $type = in_array($field['field_type'] ?? '', $validTypes) ? $field['field_type'] : 'text';

                if (!$label || !$name) continue;

                $this->db->insert('session_form_fields', [
                    'session_type_id' => $id,
                    'field_label'     => $label,
                    'field_name'      => $name,
                    'field_type'      => $type,
                    'field_options'   => !empty($field['field_options']) ? json_encode($field['field_options']) : null,
                    'placeholder'     => isset($field['placeholder']) ? Sanitizer::string($field['placeholder']) : null,
                    'is_required'     => !empty($field['is_required']) ? 1 : 0,
                    'sort_order'      => $i,
                ]);
            }
        });

        // Return the fresh list
        $fields = $this->db->fetchAll(
            "SELECT * FROM `session_form_fields` WHERE `session_type_id` = ? ORDER BY `sort_order` ASC, `id` ASC",
            [$id]
        );
        foreach ($fields as &$f) {
            $f['field_options'] = $f['field_options'] ? json_decode($f['field_options'], true) : [];
            $f['is_required'] = (bool) $f['is_required'];
        }

        return $this->success($fields, 'Form fields saved');
    }

    private function ensureSessionTypeExists(int $id): void
    {
        $exists = $this->db->fetch("SELECT `id` FROM `session_types` WHERE `id` = ?", [$id]);
        if (!$exists) {
            throw new NotFoundException('Session type not found');
        }
    }
}
