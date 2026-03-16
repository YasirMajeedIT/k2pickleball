<?php
$title = 'Edit User';
$breadcrumbs = [['label' => 'Users', 'url' => ($baseUrl ?? '') . '/admin/users'], ['label' => 'Edit']];

$formId = 'userEditForm';
$apiUrl = ($baseUrl ?? '') . '/api/users';
$method = 'PUT';
$backUrl = ($baseUrl ?? '') . '/admin/users';
$fields = [
    ['name' => 'section_account', 'label' => 'Account Information', 'type' => 'section', 'help' => 'Core user identity, access state, and role assignment.'],
    ['name' => 'first_name', 'label' => 'First Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'last_name', 'label' => 'Last Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
    ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel'],
    ['name' => 'password', 'label' => 'New Password', 'type' => 'password', 'help' => 'Leave blank to keep current password'],
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
function userEditForm() {
    const token = localStorage.getItem('access_token');
    const pathParts = window.location.pathname.split('/').filter(Boolean);
    const id = pathParts[pathParts.indexOf('users') + 1];

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
            role_id: '',
            facility_ids: [],
            status: ''
        },
        ms_facility_ids: [],
        errors: {},
        submitting: false,
        async init() {
            try {
                const [rolesRes, userRes, facilitiesRes] = await Promise.all([
                    authFetch(APP_BASE + '/api/roles', { headers: { 'Accept': 'application/json' } }),
                    authFetch(APP_BASE + '/api/users/' + id, { headers: { 'Accept': 'application/json' } }),
                    authFetch(APP_BASE + '/api/facilities?per_page=200', { headers: { 'Accept': 'application/json' } })
                ]);
                const rolesJson = await rolesRes.json();
                if (rolesRes.ok && rolesJson.data) {
                    const select = this.$el.querySelector('select[data-field="role_id"]');
                    if (select) rolesJson.data.forEach(r => { const o = document.createElement('option'); o.value = r.id; o.textContent = r.name; select.appendChild(o); });
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: rolesJson.message || 'Unable to load roles', type: 'error' } }));
                }
                const facilitiesJson = await facilitiesRes.json();
                if (facilitiesRes.ok && facilitiesJson.data) {
                    this.ms_facility_ids = facilitiesJson.data.map(f => ({ value: f.id, label: f.name }));
                }
                const userJson = await userRes.json();
                if (userJson.data) {
                    const d = userJson.data;
                    this.form = {
                        first_name: d.first_name || '',
                        last_name: d.last_name || '',
                        email: d.email || '',
                        phone: d.phone || '',
                        professional_title: d.professional_title || '',
                        membership_id: d.membership_id || '',
                        certification_level: d.certification_level || '',
                        years_experience: d.years_experience || '',
                        emergency_contact_name: d.emergency_contact_name || '',
                        emergency_contact_phone: d.emergency_contact_phone || '',
                        bio: d.bio || '',
                        password: '',
                        role_id: d.roles?.[0]?.id || '',
                        facility_ids: (d.facilities || []).map(f => f.id),
                        status: d.status === 'pending' ? 'inactive' : (d.status || 'active')
                    };
                }
            } catch (e) { console.error(e); }
        },
        async submitForm() {
            this.submitting = true; this.errors = {};
            try {
                const body = { ...this.form };
                if (!body.password) delete body.password;
                const res = await fetch(APP_BASE + '/api/users/' + id, {
                    method: 'PUT',
                    headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'User updated', type: 'success' } }));
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
