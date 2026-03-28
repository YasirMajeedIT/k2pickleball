<?php

declare(strict_types=1);

namespace App\Modules\CustomForms;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;

final class CustomFormController extends Controller
{
    private CustomFormRepository $repo;
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
        $this->repo = new CustomFormRepository($db);
    }

    /* ═══ Admin CRUD ═══ */

    /** GET /api/custom-forms */
    public function index(Request $request): Response
    {
        try {
            $orgId = $request->organizationId();
            return $this->success($this->repo->findAllForOrg((int) ($orgId ?? 0)));
        } catch (\Throwable $e) {
            error_log('[CustomForm::index] ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $this->error(get_class($e) . ': ' . $e->getMessage(), 500);
        }
    }

    /** GET /api/custom-forms/{id} */
    public function show(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();
        $form = $this->repo->findByIdForOrg($orgId, $id);
        if (!$form) return $this->error('Form not found', 404);

        $form['fields'] = $this->repo->getFields($id);
        // Decode JSON columns
        foreach ($form['fields'] as &$f) {
            if (is_string($f['options'])) $f['options'] = json_decode($f['options'], true);
            if (is_string($f['validation'])) $f['validation'] = json_decode($f['validation'], true);
        }
        unset($f);

        return $this->success($form);
    }

    /** POST /api/custom-forms */
    public function store(Request $request): Response
    {
        try {
            $orgId = $request->organizationId();
            $input = $request->all();

            Validator::validate($input, [
                'title' => 'required|string|max:255',
            ]);

            $title = Sanitizer::string($input['title']);
            $slug = Sanitizer::slug($input['slug'] ?? $title);

            // Ensure unique slug
            $existing = $this->repo->findBySlug($orgId, $slug);
            if ($existing) {
                $slug .= '-' . time();
            }

            $closesAt = !empty($input['closes_at']) ? $input['closes_at'] : null;
            $maxSub   = isset($input['max_submissions']) && $input['max_submissions'] !== '' && $input['max_submissions'] !== null
                        ? (int) $input['max_submissions'] : null;

            $id = $this->repo->createForm((int) ($orgId ?? 0), [
                'title'           => $title,
                'slug'            => $slug,
                'description'     => Sanitizer::html($input['description'] ?? ''),
                'status'          => $input['status'] ?? 'draft',
                'success_message' => Sanitizer::string($input['success_message'] ?? ''),
                'redirect_url'    => $input['redirect_url'] ?? null,
                'requires_auth'   => $input['requires_auth'] ?? 0,
                'max_submissions' => $maxSub,
                'closes_at'       => $closesAt,
                'show_in_nav'     => $input['show_in_nav'] ?? 0,
                'created_by'      => $request->userId(),
            ]);

            // Sync fields if provided
            $fields = $input['fields'] ?? [];
            if (!empty($fields)) {
                $this->repo->syncFields($id, $this->sanitizeFields($fields));
            }

            $form = $this->repo->findByIdForOrg((int) ($orgId ?? 0), $id);
            $form['fields'] = $this->repo->getFields($id);
            return $this->success($form, 'Form created', 201);
        } catch (\Throwable $e) {
            // Temporary diagnostic — log and return the real error
            error_log('[CustomForm::store] ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $this->error(get_class($e) . ': ' . $e->getMessage(), 500);
        }
    }

    /** PUT /api/custom-forms/{id} */
    public function update(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();
        $form = $this->repo->findByIdForOrg($orgId, $id);
        if (!$form) return $this->error('Form not found', 404);

        $data = $request->all();

        // Sanitize
        if (isset($data['title'])) $data['title'] = Sanitizer::string($data['title']);
        if (isset($data['slug']))  $data['slug']  = Sanitizer::slug($data['slug']);
        if (isset($data['description'])) $data['description'] = Sanitizer::html($data['description']);
        if (isset($data['success_message'])) $data['success_message'] = Sanitizer::string($data['success_message']);

        // Normalize empty strings to null for typed DB columns
        if (array_key_exists('closes_at', $data) && ($data['closes_at'] === '' || $data['closes_at'] === null)) {
            $data['closes_at'] = null;
        }
        if (array_key_exists('max_submissions', $data) && ($data['max_submissions'] === '' || $data['max_submissions'] === null)) {
            $data['max_submissions'] = null;
        }

        // Unique slug check
        if (isset($data['slug']) && $data['slug'] !== $form['slug']) {
            $existing = $this->repo->findBySlug($orgId, $data['slug']);
            if ($existing && $existing['id'] != $id) {
                return $this->validationError(['slug' => ['This slug is already taken']]);
            }
        }

        $this->repo->updateForm($orgId, $id, $data);

        // Sync fields if provided
        if (isset($data['fields']) && is_array($data['fields'])) {
            $this->repo->syncFields($id, $this->sanitizeFields($data['fields']));
        }

        $updated = $this->repo->findByIdForOrg($orgId, $id);
        $updated['fields'] = $this->repo->getFields($id);
        return $this->success($updated, 'Form updated');
    }

    /** DELETE /api/custom-forms/{id} */
    public function destroy(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();
        $form = $this->repo->findByIdForOrg($orgId, $id);
        if (!$form) return $this->error('Form not found', 404);

        $this->repo->deleteForm($orgId, $id);
        return $this->success(null, 'Form deleted');
    }

    /* ═══ Submissions ═══ */

    /** GET /api/custom-forms/{id}/submissions */
    public function submissions(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();
        $form = $this->repo->findByIdForOrg($orgId, $id);
        if (!$form) return $this->error('Form not found', 404);

        $status = $request->input('status');
        $subs = $this->repo->getSubmissions($id, $status);

        // Attach field data for each
        foreach ($subs as &$sub) {
            $sub['data'] = $this->repo->getSubmissionData((int) $sub['id']);
        }
        unset($sub);

        return $this->success($subs);
    }

    /** GET /api/custom-forms/{id}/submissions/{subId} */
    public function submissionDetail(Request $request, int $id, int $subId): Response
    {
        $orgId = $request->organizationId();
        $form = $this->repo->findByIdForOrg($orgId, $id);
        if (!$form) return $this->error('Form not found', 404);

        $data = $this->repo->getSubmissionData($subId);
        return $this->success($data);
    }

    /** PATCH /api/custom-forms/{id}/submissions/{subId}/status */
    public function updateSubmissionStatus(Request $request, int $id, int $subId): Response
    {
        $status = $request->input('status');
        if (!in_array($status, ['new', 'reviewed', 'archived'])) {
            return $this->validationError(['status' => ['Invalid status']]);
        }
        $this->repo->updateSubmissionStatus($subId, $status);
        return $this->success(null, 'Status updated');
    }

    /** DELETE /api/custom-forms/{id}/submissions/{subId} */
    public function deleteSubmission(Request $request, int $id, int $subId): Response
    {
        $this->repo->deleteSubmission($subId);
        return $this->success(null, 'Submission deleted');
    }

    /* ═══ Public submission (tenant-facing) ═══ */

    /** POST /api/public/forms/{slug}/submit */
    public function submit(Request $request, string $slug): Response
    {
        $orgId = $request->organizationId();
        if (!$orgId) {
            // Try from the public request context
            $orgId = $request->input('organization_id');
        }

        $form = $this->repo->findBySlug((int) $orgId, $slug);
        if (!$form) return $this->error('Form not found', 404);
        if ($form['status'] !== 'active') return $this->error('This form is not currently accepting submissions', 403);

        // Check auth requirement
        if ($form['requires_auth'] && !$request->userId()) {
            return $this->error('You must be signed in to submit this form', 401);
        }

        // Check max submissions
        if ($form['max_submissions']) {
            $count = $this->repo->countSubmissions((int) $form['id']);
            if ($count >= (int) $form['max_submissions']) {
                return $this->error('This form has reached its maximum number of submissions', 403);
            }
        }

        // Check closes_at
        if ($form['closes_at'] && strtotime($form['closes_at']) < time()) {
            return $this->error('This form is no longer accepting submissions', 403);
        }

        $fields = $this->repo->getFields((int) $form['id']);
        $input = $request->all();

        // Validate required fields
        $errors = [];
        foreach ($fields as $field) {
            if (in_array($field['type'], ['heading', 'paragraph'])) continue;

            $val = $input[$field['name']] ?? null;
            if ($field['is_required'] && ($val === null || $val === '')) {
                $errors[$field['name']] = ["{$field['label']} is required"];
            }
            // Email validation
            if ($field['type'] === 'email' && $val && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                $errors[$field['name']] = ["{$field['label']} must be a valid email"];
            }
        }

        if (!empty($errors)) {
            return $this->validationError($errors);
        }

        // Rate-limit: max 5 submissions per IP per form per hour
        $ip = $request->ip();
        $recentCount = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `custom_form_submissions`
             WHERE `form_id` = ? AND `ip_address` = ? AND `submitted_at` > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [(int) $form['id'], $ip]
        );
        if (((int) ($recentCount['cnt'] ?? 0)) >= 5) {
            return $this->error('Too many submissions. Please try again later.', 429);
        }

        // Create submission
        $subId = $this->repo->createSubmission(
            (int) $form['id'],
            (int) $orgId,
            $request->userId() ? (int) $request->userId() : null,
            $ip,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // Save field data
        foreach ($fields as $field) {
            if (in_array($field['type'], ['heading', 'paragraph'])) continue;
            $val = $input[$field['name']] ?? '';
            if (is_array($val)) $val = json_encode($val);
            $this->repo->saveSubmissionData($subId, (int) $field['id'], $field['name'], Sanitizer::string((string) $val));
        }

        return $this->success([
            'submission_id'   => $subId,
            'success_message' => $form['success_message'] ?: 'Thank you! Your submission has been received.',
            'redirect_url'    => $form['redirect_url'] ?: null,
        ], 'Form submitted successfully', 201);
    }

    /** GET /api/public/forms/{slug} — public form definition */
    public function publicShow(Request $request, string $slug): Response
    {
        $orgId = $this->requireOrgFromRequest($request);
        if (!$orgId) return $this->error('Organization not found', 404);

        $form = $this->repo->findBySlug($orgId, $slug);
        if (!$form || $form['status'] !== 'active') return $this->error('Form not found', 404);

        $fields = $this->repo->getFields((int) $form['id']);
        foreach ($fields as &$f) {
            if (is_string($f['options'])) $f['options'] = json_decode($f['options'], true);
            // Strip internal validation rules from public response
            unset($f['id'], $f['form_id'], $f['created_at']);
        }
        unset($f);

        return $this->success([
            'title'         => $form['title'],
            'slug'          => $form['slug'],
            'description'   => $form['description'],
            'requires_auth' => (bool) $form['requires_auth'],
            'fields'        => $fields,
        ]);
    }

    /* ── Helpers ── */

    private function sanitizeFields(array $fields): array
    {
        $cleaned = [];
        foreach ($fields as $i => $f) {
            $cleaned[] = [
                'label'       => Sanitizer::string($f['label'] ?? 'Field'),
                'name'        => preg_replace('/[^a-z0-9_]/', '', strtolower($f['name'] ?? $f['label'] ?? 'field_' . $i)),
                'type'        => $f['type'] ?? 'text',
                'placeholder' => Sanitizer::string($f['placeholder'] ?? ''),
                'help_text'   => Sanitizer::string($f['help_text'] ?? ''),
                'is_required' => (int) ($f['is_required'] ?? 0),
                'options'     => $f['options'] ?? null,
                'validation'  => $f['validation'] ?? null,
                'sort_order'  => $f['sort_order'] ?? ($i * 10),
                'width'       => in_array($f['width'] ?? '', ['full', 'half']) ? $f['width'] : 'full',
            ];
        }
        return $cleaned;
    }

    private function requireOrgFromRequest(Request $request): ?int
    {
        $orgId = $request->organizationId();
        return $orgId ?: null;
    }
}
