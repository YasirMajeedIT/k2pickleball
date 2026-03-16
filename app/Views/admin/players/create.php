<?php
$title = 'Add Player';
$breadcrumbs = [['label' => 'Players', 'url' => ($baseUrl ?? '') . '/admin/players'], ['label' => 'Create']];

$formId = 'playerForm';
$apiUrl = ($baseUrl ?? '') . '/api/players';
$method = 'POST';
$backUrl = ($baseUrl ?? '') . '/admin/players';
$fields = [
    ['name' => 'first_name', 'label' => 'First Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'last_name', 'label' => 'Last Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'cols' => 'half'],
    ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'cols' => 'half'],
    ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date', 'cols' => 'half'],
    ['name' => 'gender', 'label' => 'Gender', 'type' => 'select', 'cols' => 'half', 'options' => [
        '' => 'Select...', 'male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say',
    ]],
    ['name' => 'skill_level', 'label' => 'Skill Level', 'type' => 'select', 'cols' => 'half', 'options' => [
        'beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'pro' => 'Pro',
    ]],
    ['name' => 'rating', 'label' => 'Rating', 'type' => 'number', 'cols' => 'half'],
    ['name' => 'dupr_rating', 'label' => 'DUPR Rating', 'type' => 'number', 'cols' => 'half'],
    ['name' => 'dupr_id', 'label' => 'DUPR ID', 'cols' => 'half'],
    ['name' => 'address', 'label' => 'Address'],
    ['name' => 'city', 'label' => 'City', 'cols' => 'half'],
    ['name' => 'state', 'label' => 'State', 'cols' => 'half'],
    ['name' => 'zip_code', 'label' => 'ZIP Code', 'cols' => 'half'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'cols' => 'half', 'options' => [
        'active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended',
    ]],
    ['name' => 'emergency_contact_name', 'label' => 'Emergency Contact Name', 'cols' => 'half'],
    ['name' => 'emergency_contact_phone', 'label' => 'Emergency Contact Phone', 'type' => 'tel', 'cols' => 'half'],
    ['name' => 'medical_notes', 'label' => 'Medical Notes', 'type' => 'textarea'],
    ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
    ['name' => 'is_waiver', 'label' => 'Waiver Signed', 'type' => 'checkbox'],
    ['name' => 'is_teen', 'label' => 'Teen Player', 'type' => 'checkbox'],
    ['name' => 'is_email_marketing', 'label' => 'Email Marketing', 'type' => 'checkbox'],
    ['name' => 'is_sms_marketing', 'label' => 'SMS Marketing', 'type' => 'checkbox'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function playerForm() {
    return {
        form: {
            first_name: '', last_name: '', email: '', phone: '',
            date_of_birth: '', gender: '', skill_level: 'beginner',
            rating: '', dupr_rating: '', dupr_id: '',
            address: '', city: '', state: '', zip_code: '',
            status: 'active',
            emergency_contact_name: '', emergency_contact_phone: '',
            medical_notes: '', notes: '',
            is_waiver: false, is_teen: false, is_email_marketing: true, is_sms_marketing: true,
        },
        errors: {},
        submitting: false,
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
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Player created', type: 'success' } }));
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
