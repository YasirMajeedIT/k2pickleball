<?php
$title = 'Create User';
$breadcrumbs = [['label' => 'Users', 'url' => ($baseUrl ?? '') . '/admin/users'], ['label' => 'Create']];

$formId = 'userForm';
$apiUrl = ($baseUrl ?? '') . '/api/users';
$method = 'POST';
$backUrl = ($baseUrl ?? '') . '/admin/users';
$fields = [
    ['name' => 'first_name', 'label' => 'First Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'last_name', 'label' => 'Last Name', 'required' => true, 'cols' => 'half'],
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
    ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel'],
    ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'required' => true, 'help' => 'Minimum 8 characters'],
    ['name' => 'password_confirmation', 'label' => 'Confirm Password', 'type' => 'password', 'required' => true],
    ['name' => 'role_id', 'label' => 'Role', 'type' => 'select', 'options' => []],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'options' => [
        'active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended', 'pending' => 'Pending',
    ]],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function userForm() {
    const token = localStorage.getItem('access_token');
    return {
        form: { first_name: '', last_name: '', email: '', phone: '', password: '', password_confirmation: '', role_id: '', status: 'active' },
        errors: {},
        submitting: false,
        async init() {
            try {
                const res = await fetch(APP_BASE + '/api/roles', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
                const json = await res.json();
                if (json.data) {
                    const select = this.$el.querySelector('select[x-model="form.role_id"]');
                    if (select) json.data.forEach(r => { const o = document.createElement('option'); o.value = r.id; o.textContent = r.name; select.appendChild(o); });
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
