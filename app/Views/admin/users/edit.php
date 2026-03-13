<?php
$title = 'Edit User';
$breadcrumbs = [['label' => 'Users', 'url' => ($baseUrl ?? '') . '/admin/users'], ['label' => 'Edit']];

$formId = 'userEditForm';
$apiUrl = ($baseUrl ?? '') . '/api/users';
$method = 'PUT';
$backUrl = ($baseUrl ?? '') . '/admin/users';
$fields = [
    ['name' => 'first_name', 'label' => 'First Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'last_name', 'label' => 'Last Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
    ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel'],
    ['name' => 'password', 'label' => 'New Password', 'type' => 'password', 'help' => 'Leave blank to keep current password'],
    ['name' => 'role_id', 'label' => 'Role', 'type' => 'select', 'options' => []],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'options' => [
        'active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended', 'pending' => 'Pending',
    ]],
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
        form: { first_name: '', last_name: '', email: '', phone: '', password: '', role_id: '', status: '' },
        errors: {},
        submitting: false,
        async init() {
            try {
                const [rolesRes, userRes] = await Promise.all([
                    fetch(APP_BASE + '/api/roles', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } }),
                    fetch(APP_BASE + '/api/users/' + id, { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } })
                ]);
                const rolesJson = await rolesRes.json();
                if (rolesJson.data) {
                    const select = this.$el.querySelector('select[x-model="form.role_id"]');
                    if (select) rolesJson.data.forEach(r => { const o = document.createElement('option'); o.value = r.id; o.textContent = r.name; select.appendChild(o); });
                }
                const userJson = await userRes.json();
                if (userJson.data) {
                    const d = userJson.data;
                    this.form = { first_name: d.first_name || '', last_name: d.last_name || '', email: d.email || '', phone: d.phone || '', password: '', role_id: d.roles?.[0]?.id || '', status: d.status || '' };
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
