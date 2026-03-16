<?php
$title = 'Edit Resource';
$breadcrumbs = [['label' => 'Resources', 'url' => ($baseUrl ?? '') . '/admin/resources'], ['label' => 'Edit']];

$formId = 'resourceForm';
$apiUrl = ($baseUrl ?? '') . '/api/resources/' . ($id ?? '');
$method = 'PUT';
$backUrl = ($baseUrl ?? '') . '/admin/resources';
$fields = [
    ['name' => 'name', 'label' => 'Resource Name', 'required' => true],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
    ['name' => 'field_type', 'label' => 'Field Type', 'type' => 'select', 'options' => [
        ['value' => 'checkbox', 'label' => 'Checkbox Group'],
        ['value' => 'selectbox', 'label' => 'Select Box'],
        ['value' => 'radio', 'label' => 'Radio Buttons'],
        ['value' => 'input', 'label' => 'Input Field'],
    ], 'help' => 'How this resource appears in session type forms'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function resourceForm() {
    return {
        form: { name: '', description: '', field_type: 'checkbox' },
        errors: {},
        submitting: false,
        async init() {
            try {
                const res = await authFetch('<?= $apiUrl ?>');
                const json = await res.json();
                if (json.data) {
                    this.form.name = json.data.name || '';
                    this.form.description = json.data.description || '';
                    this.form.field_type = json.data.field_type || 'checkbox';
                }
            } catch (e) { console.error(e); }
        },
        async submitForm() {
            this.submitting = true; this.errors = {};
            try {
                const res = await authFetch('<?= $apiUrl ?>', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Resource updated', type: 'success' } }));
                    setTimeout(() => window.location.href = '<?= $backUrl ?>', 500);
                } else { this.errors = json.errors || {}; window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Validation failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            this.submitting = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
