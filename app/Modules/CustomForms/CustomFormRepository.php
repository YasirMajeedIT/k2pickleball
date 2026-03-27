<?php

declare(strict_types=1);

namespace App\Modules\CustomForms;

use App\Core\Database\Repository;

final class CustomFormRepository extends Repository
{
    protected string $table = 'custom_forms';

    /* ── List / Read ── */

    public function findAllForOrg(int $orgId): array
    {
        $forms = $this->db->fetchAll(
            "SELECT f.*, u.`name` AS created_by_name,
                    (SELECT COUNT(*) FROM `custom_form_submissions` s WHERE s.`form_id` = f.`id`) AS submission_count,
                    (SELECT COUNT(*) FROM `custom_form_submissions` s WHERE s.`form_id` = f.`id` AND s.`status` = 'new') AS new_submission_count
             FROM `{$this->table}` f
             LEFT JOIN `users` u ON u.`id` = f.`created_by`
             WHERE f.`organization_id` = ?
             ORDER BY f.`created_at` DESC",
            [$orgId]
        );
        return $forms;
    }

    public function findById(int $orgId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `id` = ? AND `organization_id` = ?",
            [$id, $orgId]
        );
    }

    public function findBySlug(int $orgId, string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `slug` = ? AND `organization_id` = ?",
            [$slug, $orgId]
        );
    }

    /* ── Create / Update / Delete ── */

    public function createForm(int $orgId, array $data): int
    {
        return $this->db->insert($this->table, [
            'organization_id' => $orgId,
            'title'           => $data['title'],
            'slug'            => $data['slug'],
            'description'     => $data['description'] ?? null,
            'status'          => $data['status'] ?? 'draft',
            'success_message' => $data['success_message'] ?? null,
            'redirect_url'    => $data['redirect_url'] ?? null,
            'requires_auth'   => (int) ($data['requires_auth'] ?? 0),
            'max_submissions' => $data['max_submissions'] ?? null,
            'closes_at'       => $data['closes_at'] ?? null,
            'show_in_nav'     => (int) ($data['show_in_nav'] ?? 0),
            'created_by'      => $data['created_by'] ?? null,
        ]);
    }

    public function updateForm(int $orgId, int $id, array $data): void
    {
        $fields = [];
        $params = [];

        $allowed = ['title', 'slug', 'description', 'status', 'success_message',
                     'redirect_url', 'requires_auth', 'max_submissions', 'closes_at', 'show_in_nav'];

        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $fields[] = "`{$key}` = ?";
                $val = $data[$key];
                if (in_array($key, ['requires_auth', 'show_in_nav'])) $val = (int) $val;
                $params[] = $val;
            }
        }

        if (empty($fields)) return;

        $params[] = $id;
        $params[] = $orgId;
        $this->db->query(
            "UPDATE `{$this->table}` SET " . implode(', ', $fields) . " WHERE `id` = ? AND `organization_id` = ?",
            $params
        );
    }

    public function deleteForm(int $orgId, int $id): void
    {
        $this->db->query(
            "DELETE FROM `{$this->table}` WHERE `id` = ? AND `organization_id` = ?",
            [$id, $orgId]
        );
    }

    /* ── Fields ── */

    public function getFields(int $formId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `custom_form_fields` WHERE `form_id` = ? ORDER BY `sort_order` ASC",
            [$formId]
        );
    }

    public function syncFields(int $formId, array $fields): void
    {
        // Delete old fields
        $this->db->query("DELETE FROM `custom_form_fields` WHERE `form_id` = ?", [$formId]);

        foreach ($fields as $i => $f) {
            $this->db->insert('custom_form_fields', [
                'form_id'     => $formId,
                'label'       => $f['label'],
                'name'        => $f['name'] ?? $this->slugify($f['label']),
                'type'        => $f['type'] ?? 'text',
                'placeholder' => $f['placeholder'] ?? null,
                'help_text'   => $f['help_text'] ?? null,
                'is_required' => (int) ($f['is_required'] ?? 0),
                'options'     => isset($f['options']) ? json_encode($f['options']) : null,
                'validation'  => isset($f['validation']) ? json_encode($f['validation']) : null,
                'sort_order'  => $f['sort_order'] ?? ($i * 10),
                'width'       => $f['width'] ?? 'full',
            ]);
        }
    }

    /* ── Submissions ── */

    public function getSubmissions(int $formId, ?string $status = null, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT s.*, p.`first_name`, p.`last_name`, p.`email` AS player_email
                FROM `custom_form_submissions` s
                LEFT JOIN `players` p ON p.`id` = s.`player_id`
                WHERE s.`form_id` = ?";
        $params = [$formId];

        if ($status) {
            $sql .= " AND s.`status` = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY s.`submitted_at` DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function getSubmissionData(int $submissionId): array
    {
        return $this->db->fetchAll(
            "SELECT d.*, ff.`label` AS field_label, ff.`type` AS field_type
             FROM `custom_form_submission_data` d
             JOIN `custom_form_fields` ff ON ff.`id` = d.`field_id`
             WHERE d.`submission_id` = ?
             ORDER BY ff.`sort_order` ASC",
            [$submissionId]
        );
    }

    public function countSubmissions(int $formId): int
    {
        $row = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `custom_form_submissions` WHERE `form_id` = ?",
            [$formId]
        );
        return (int) ($row['cnt'] ?? 0);
    }

    public function createSubmission(int $formId, int $orgId, ?int $playerId, ?string $ip, ?string $ua): int
    {
        return $this->db->insert('custom_form_submissions', [
            'form_id'         => $formId,
            'organization_id' => $orgId,
            'player_id'       => $playerId,
            'ip_address'      => $ip,
            'user_agent'      => $ua ? mb_substr($ua, 0, 500) : null,
        ]);
    }

    public function saveSubmissionData(int $submissionId, int $fieldId, string $fieldName, ?string $value): void
    {
        $this->db->insert('custom_form_submission_data', [
            'submission_id' => $submissionId,
            'field_id'      => $fieldId,
            'field_name'    => $fieldName,
            'value'         => $value,
        ]);
    }

    public function updateSubmissionStatus(int $id, string $status): void
    {
        $this->db->query(
            "UPDATE `custom_form_submissions` SET `status` = ? WHERE `id` = ?",
            [$status, $id]
        );
    }

    public function deleteSubmission(int $id): void
    {
        $this->db->query("DELETE FROM `custom_form_submissions` WHERE `id` = ?", [$id]);
    }

    /* ── Helpers ── */

    private function slugify(string $text): string
    {
        $text = preg_replace('/[^a-z0-9]+/i', '_', strtolower(trim($text)));
        return trim($text, '_');
    }
}
