<?php
$title = 'Add Gift Certificate';
$breadcrumbs = [['label' => 'Gift Certificates', 'url' => ($baseUrl ?? '') . '/admin/gift-certificates'], ['label' => 'Create']];

$formId = 'giftCertForm';
$apiUrl = ($baseUrl ?? '') . '/api/gift-certificates';
$method = 'POST';
$backUrl = ($baseUrl ?? '') . '/admin/gift-certificates';
$fields = [
    ['name' => 'facility_id', 'label' => 'Facility', 'type' => 'select', 'required' => true, 'options' => [], 'cols' => 'half'],
    ['name' => 'certificate_name', 'label' => 'Certificate Name', 'cols' => 'half'],
    ['name' => 'code', 'label' => 'Code', 'placeholder' => 'Leave blank to auto-generate', 'cols' => 'half'],
    ['name' => 'value', 'label' => 'Value ($)', 'type' => 'number', 'required' => true, 'cols' => 'half'],
    ['name' => 'paid_amount', 'label' => 'Paid Amount ($)', 'type' => 'number', 'cols' => 'half'],
    ['name' => 'currency', 'label' => 'Currency', 'type' => 'select', 'cols' => 'half', 'options' => [
        'USD' => 'USD', 'CAD' => 'CAD', 'EUR' => 'EUR', 'GBP' => 'GBP',
    ]],
    ['name' => 'buyer_first_name', 'label' => 'Buyer First Name', 'cols' => 'half'],
    ['name' => 'buyer_last_name', 'label' => 'Buyer Last Name', 'cols' => 'half'],
    ['name' => 'buyer_email', 'label' => 'Buyer Email', 'type' => 'email', 'cols' => 'half'],
    ['name' => 'buyer_phone', 'label' => 'Buyer Phone', 'type' => 'tel', 'cols' => 'half'],
    ['name' => 'recipient_first_name', 'label' => 'Recipient First Name', 'cols' => 'half'],
    ['name' => 'recipient_last_name', 'label' => 'Recipient Last Name', 'cols' => 'half'],
    ['name' => 'recipient_email', 'label' => 'Recipient Email', 'type' => 'email', 'cols' => 'half'],
    ['name' => 'recipient_phone', 'label' => 'Recipient Phone', 'type' => 'tel', 'cols' => 'half'],
    ['name' => 'gift_message', 'label' => 'Gift Message', 'type' => 'textarea'],
    ['name' => 'start_using_after', 'label' => 'Valid From', 'type' => 'flatpickr', 'cols' => 'half'],
    ['name' => 'expired_at', 'label' => 'Expires At', 'type' => 'flatpickr', 'cols' => 'half'],
    ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function giftCertForm() {
    return {
        form: {
            facility_id: '', certificate_name: '', code: '', value: '', paid_amount: '', currency: 'USD',
            buyer_first_name: '', buyer_last_name: '', buyer_email: '', buyer_phone: '',
            recipient_first_name: '', recipient_last_name: '', recipient_email: '', recipient_phone: '',
            gift_message: '', start_using_after: '', expired_at: '', notes: '',
        },
        errors: {},
        submitting: false,
        async init() {
            await this.loadFacilities();
            this.$nextTick(() => this.initDatePickers());
        },
        initDatePickers() {
            const self = this;
            ['start_using_after', 'expired_at'].forEach(refName => {
                const el = self.$refs[refName];
                if (!el) return;
                if (el._flatpickr) el._flatpickr.destroy();
                flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    allowInput: false,
                    onChange(_, dateStr) {
                        self.form[refName] = dateStr;
                    },
                });
            });
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
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gift certificate created', type: 'success' } }));
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
