<?php
$title = 'Create User';
$breadcrumbs = [['label' => 'Users', 'url' => ($baseUrl ?? '') . '/admin/users'], ['label' => 'Create']];

$formId = 'userForm';
$apiUrl = ($baseUrl ?? '') . '/api/users';
$method = 'POST';
$backUrl = ($baseUrl ?? '') . '/admin/users';
$fields = [
    ['name' => 'section_account', 'label' => 'Account Information', 'type' => 'section', 'help' => 'Core user identity, login access, and role assignment.'],
    ['name' => 'first_name', 'label' => 'First Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'last_name', 'label' => 'Last Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
    ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel'],
    ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'required' => true, 'help' => 'Minimum 8 characters'],
    ['name' => 'password_confirmation', 'label' => 'Confirm Password', 'type' => 'password', 'required' => true],
    ['name' => 'role_id', 'label' => 'Role', 'type' => 'select', 'options' => []],
    ['name' => 'facility_ids', 'label' => 'Assigned Facilities', 'type' => 'multiselect', 'help' => 'Select one or more facilities this user belongs to'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'options' => [
        'active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended',
    ]],

    ['name' => 'section_professional', 'label' => 'Professional Details', 'type' => 'section', 'help' => 'Sports club staff profile, credentials, and experience.'],
    ['name' => 'professional_title', 'label' => 'Professional Title', 'placeholder' => 'Coach, Manager, Front Desk, Referee'],
    ['name' => 'membership_id', 'label' => 'Membership ID', 'cols' => 'half'],
    ['name' => 'certification_level', 'label' => 'Certification Level', 'placeholder' => 'Enter certification level or license', 'cols' => 'half'],
    ['name' => 'years_experience', 'label' => 'Years of Experience', 'type' => 'number', 'min' => 0, 'max' => 60, 'cols' => 'half'],

    ['name' => 'emergency_contact_name', 'label' => 'Emergency Contact Name', 'cols' => 'half'],
    ['name' => 'emergency_contact_phone', 'label' => 'Emergency Contact Phone', 'type' => 'tel', 'cols' => 'half'],

    ['name' => 'bio', 'label' => 'Bio / Notes', 'type' => 'textarea', 'rows' => 4, 'placeholder' => 'Short background, responsibilities, certifications, or notes'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function userForm() {
    const token = localStorage.getItem('access_token');
    return {
        form: {
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            professional_title: '',
            membership_id: '',
            certification_level: '',
            years_experience: '',
            emergency_contact_name: '',
            emergency_contact_phone: '',
            bio: '',
            password: '',
            password_confirmation: '',
            role_id: '',
            facility_ids: [],
            status: 'active'
        },
        ms_facility_ids: [],
        errors: {},
        submitting: false,
        async init() {
            try {
                const [rolesRes, facilitiesRes] = await Promise.all([
                    authFetch(APP_BASE + '/api/roles', { headers: { 'Accept': 'application/json' } }),
                    authFetch(APP_BASE + '/api/facilities?per_page=200', { headers: { 'Accept': 'application/json' } })
                ]);
                const rolesJson = await rolesRes.json();
                if (rolesRes.ok && rolesJson.data) {
                    const select = this.$el.querySelector('select[data-field="role_id"]');
                    if (select) rolesJson.data.filter(r => r.slug !== 'super-admin').forEach(r => { const o = document.createElement('option'); o.value = r.id; o.textContent = r.name; select.appendChild(o); });
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: rolesJson.message || 'Unable to load roles', type: 'error' } }));
                }
                const facilitiesJson = await facilitiesRes.json();
                if (facilitiesRes.ok && facilitiesJson.data) {
                    this.ms_facility_ids = facilitiesJson.data.map(f => ({ value: f.id, label: f.name }));
                }
            } catch (e) { console.error(e); }
        },
        async submitForm() {
            this.submitting = true; this.errors = {};
            try {
                const res = await fetch('<?= $apiUrl ?>', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'User created', type: 'success' } }));
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
