<?php
$title = 'Add Credit Code';
$breadcrumbs = [['label' => 'Credit Codes', 'url' => ($baseUrl ?? '') . '/admin/credit-codes'], ['label' => 'Create']];

$formId = 'creditCodeForm';
$apiUrl = ($baseUrl ?? '') . '/api/credit-codes';
$method = 'POST';
$backUrl = ($baseUrl ?? '') . '/admin/credit-codes';
$fields = [
    ['name' => 'facility_id', 'label' => 'Facility', 'type' => 'select', 'required' => true, 'options' => [], 'cols' => 'half'],
    ['name' => 'name', 'label' => 'Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'code', 'label' => 'Code', 'placeholder' => 'Leave blank to auto-generate', 'cols' => 'half'],
    ['name' => 'amount', 'label' => 'Amount ($)', 'type' => 'number', 'required' => true, 'cols' => 'half'],
    ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'cols' => 'half', 'options' => [
        'credit' => 'Credit', 'infacility' => 'In-Facility',
    ]],
    ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'cols' => 'half', 'options' => [
        'admin' => 'Admin', 'system' => 'System',
    ]],
    ['name' => 'reason', 'label' => 'Reason', 'cols' => 'half'],
    ['name' => 'expires_after_days', 'label' => 'Expires After (days)', 'type' => 'number', 'cols' => 'half'],
    ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function creditCodeForm() {
    return {
        form: {
            facility_id: '', name: '', code: '', amount: '',
            type: 'credit', category: 'admin', reason: '',
            expires_after_days: '', notes: '',
        },
        errors: {},
        submitting: false,
        async init() {
            await this.loadFacilities();
        },
        async loadFacilities() {
            try {
                const res = await authFetch('<?= ($baseUrl ?? '') ?>/api/facilities');
                const json = await res.json();
                const sel = this.$refs?.facility_id || document.querySelector('[x-model="form.facility_id"]');
                if (sel && json.data) {
                    sel.innerHTML = '<option value="">Select Facility...</option>';
                    json.data.forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.id;
                        opt.textContent = f.name;
                        sel.appendChild(opt);
                    });
                }
            } catch (e) { console.error('Failed to load facilities', e); }
        },
        async submitForm() {
            this.submitting = true; this.errors = {};
            try {
                const res = await authFetch('<?= $apiUrl ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Credit code created', type: 'success' } }));
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
