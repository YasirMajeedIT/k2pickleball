<?php
$title = 'Edit Gift Certificate';
$breadcrumbs = [['label' => 'Gift Certificates', 'url' => ($baseUrl ?? '') . '/admin/gift-certificates'], ['label' => 'Edit']];

$formId = 'giftCertForm';
$apiUrl = ($baseUrl ?? '') . '/api/gift-certificates/' . ($id ?? '');
$method = 'PUT';
$backUrl = ($baseUrl ?? '') . '/admin/gift-certificates';
$fields = [
    ['name' => 'certificate_name', 'label' => 'Certificate Name'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => [
        'active' => 'Active', 'redeemed' => 'Redeemed', 'expired' => 'Expired',
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
            certificate_name: '', status: 'active',
            buyer_first_name: '', buyer_last_name: '', buyer_email: '', buyer_phone: '',
            recipient_first_name: '', recipient_last_name: '', recipient_email: '', recipient_phone: '',
            gift_message: '', start_using_after: '', expired_at: '', notes: '',
        },
        errors: {},
        submitting: false,
        async init() {
            try {
                const res = await authFetch('<?= $apiUrl ?>');
                const json = await res.json();
                if (json.data) {
                    const d = json.data;
                    Object.keys(this.form).forEach(k => {
                        if (d[k] !== undefined && d[k] !== null) {
                            this.form[k] = d[k];
                        }
                    });
                    // Format dates for flatpickr
                    if (d.start_using_after) this.form.start_using_after = d.start_using_after.substring(0, 10);
                    if (d.expired_at) this.form.expired_at = d.expired_at.substring(0, 10);
                }
            } catch (e) { console.error('Failed to load gift certificate', e); }
            this.$nextTick(() => this.initDatePickers());
        },
        initDatePickers() {
            const self = this;
            ['start_using_after', 'expired_at'].forEach(refName => {
                const el = self.$refs[refName];
                if (!el) return;
                if (el._flatpickr) el._flatpickr.destroy();
                const fp = flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    allowInput: false,
                    onChange(_, dateStr) {
                        self.form[refName] = dateStr;
                    },
                });
                if (self.form[refName]) fp.setDate(self.form[refName], false);
            });
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
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gift certificate updated', type: 'success' } }));
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
