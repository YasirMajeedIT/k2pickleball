<?php
$title = 'Edit Credit Code';
$breadcrumbs = [['label' => 'Credit Codes', 'url' => ($baseUrl ?? '') . '/admin/credit-codes'], ['label' => 'Edit']];

$formId = 'creditCodeForm';
$apiUrl = ($baseUrl ?? '') . '/api/credit-codes/' . ($id ?? '');
$method = 'PUT';
$backUrl = ($baseUrl ?? '') . '/admin/credit-codes';
$fields = [
    ['name' => 'name', 'label' => 'Name', 'required' => true],
    ['name' => 'reason', 'label' => 'Reason', 'cols' => 'half'],
    ['name' => 'expires_after_days', 'label' => 'Expires After (days)', 'type' => 'number', 'cols' => 'half'],
    ['name' => 'active', 'label' => 'Active', 'type' => 'checkbox'],
    ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function creditCodeForm() {
    return {
        form: {
            name: '', reason: '', expires_after_days: '',
            active: true, notes: '',
        },
        errors: {},
        submitting: false,
        async init() {
            try {
                const res = await authFetch('<?= $apiUrl ?>');
                const json = await res.json();
                if (json.data) {
                    const d = json.data;
                    this.form.name = d.name || '';
                    this.form.reason = d.reason || '';
                    this.form.expires_after_days = d.expires_after_days || '';
                    this.form.active = d.active == 1;
                    this.form.notes = d.notes || '';
                }
            } catch (e) { console.error('Failed to load credit code', e); }
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
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Credit code updated', type: 'success' } }));
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
