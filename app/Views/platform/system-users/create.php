<?php
$title = 'Create User';
$breadcrumbs = [
    ['label' => 'System Users', 'url' => ($baseUrl ?? '') . '/platform/system-users'],
    ['label' => 'Create'],
];
$backUrl = ($baseUrl ?? '') . '/platform/system-users';

ob_start();
?>
<div x-data="createUserForm()" x-init="init()">
    <div class="mb-6 flex items-center justify-between">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="inline-flex items-center gap-2 text-sm font-medium text-surface-500 hover:text-primary-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Users
        </a>
    </div>

    <div class="max-w-2xl">
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white">Create New User</h3>
                <p class="text-sm text-surface-500 mt-1">Create a super-admin or organization user</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                <!-- Name -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">First Name *</label>
                        <input x-model="form.first_name" type="text" placeholder="John" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                        <p x-show="errors.first_name" class="text-xs text-red-500 mt-1" x-text="errors.first_name?.[0]"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Last Name *</label>
                        <input x-model="form.last_name" type="text" placeholder="Doe" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                        <p x-show="errors.last_name" class="text-xs text-red-500 mt-1" x-text="errors.last_name?.[0]"></p>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Email *</label>
                    <input x-model="form.email" type="email" placeholder="john@example.com" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                    <p x-show="errors.email" class="text-xs text-red-500 mt-1" x-text="errors.email?.[0]"></p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Password *</label>
                    <input x-model="form.password" type="password" placeholder="Minimum 8 characters" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                    <p x-show="errors.password" class="text-xs text-red-500 mt-1" x-text="errors.password?.[0]"></p>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Role *</label>
                    <select x-model="form.role_id" @change="onRoleChange()" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                        <option value="">Select a role...</option>
                        <template x-for="role in roles" :key="role.id">
                            <option :value="role.id" x-text="role.name"></option>
                        </template>
                    </select>
                    <p x-show="errors.role_id" class="text-xs text-red-500 mt-1" x-text="errors.role_id?.[0]"></p>
                </div>

                <!-- Organization (hidden for super-admin) -->
                <div x-show="!isSuperAdmin">
                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Organization *</label>
                    <select x-model="form.organization_id" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                        <option value="">Select an organization...</option>
                        <template x-for="org in organizations" :key="org.id">
                            <option :value="org.id" x-text="org.name"></option>
                        </template>
                    </select>
                    <p x-show="errors.organization_id" class="text-xs text-red-500 mt-1" x-text="errors.organization_id?.[0]"></p>
                </div>
                <template x-if="isSuperAdmin">
                    <p class="text-xs text-surface-400 italic">Super-admin users are not tied to any organization.</p>
                </template>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Status</label>
                    <select x-model="form.status" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <!-- Errors -->
                <template x-if="serverError">
                    <div class="rounded-xl border border-red-200 bg-red-50 dark:bg-red-500/5 dark:border-red-500/20 p-3">
                        <p class="text-xs text-red-600 dark:text-red-400" x-text="serverError"></p>
                    </div>
                </template>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-2">
                    <button @click="submitForm()" :disabled="submitting" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary-500/20 hover:from-primary-700 hover:to-primary-800 transition-all disabled:opacity-50">
                        <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="submitting ? 'Creating...' : 'Create User'"></span>
                    </button>
                    <a href="<?= htmlspecialchars($backUrl) ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-surface-600 hover:text-surface-800 dark:text-surface-400 dark:hover:text-white transition-colors">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createUserForm() {
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    return {
        form: { first_name: '', last_name: '', email: '', password: '', role_id: '', organization_id: '', status: 'active' },
        roles: [],
        organizations: [],
        isSuperAdmin: false,
        errors: {},
        serverError: '',
        submitting: false,
        async init() {
            try {
                const [rolesRes, orgsRes] = await Promise.all([
                    fetch(APP_BASE + '/api/roles', { headers }),
                    fetch(APP_BASE + '/api/organizations?per_page=200', { headers })
                ]);
                if (rolesRes.ok) {
                    const rJson = await rolesRes.json();
                    // Show ALL roles including super-admin on platform
                    this.roles = rJson.data || [];
                }
                if (orgsRes.ok) {
                    const oJson = await orgsRes.json();
                    this.organizations = oJson.data || [];
                }
            } catch (e) { console.error(e); }
        },
        onRoleChange() {
            const selectedRole = this.roles.find(r => r.id == this.form.role_id);
            this.isSuperAdmin = selectedRole && selectedRole.slug === 'super-admin';
            if (this.isSuperAdmin) {
                this.form.organization_id = '';
            }
        },
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            this.serverError = '';
            try {
                const body = {
                    first_name: this.form.first_name,
                    last_name: this.form.last_name,
                    email: this.form.email,
                    password: this.form.password,
                    password_confirmation: this.form.password,
                    role_id: parseInt(this.form.role_id) || null,
                    status: this.form.status,
                };
                if (this.form.organization_id) {
                    body.organization_id = parseInt(this.form.organization_id);
                }

                // Use the existing /api/users endpoint (same as admin panel)
                const res = await fetch(APP_BASE + '/api/users', {
                    method: 'POST',
                    headers: { ...headers, 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'User created', type: 'success' } }));
                    setTimeout(() => window.location.href = APP_BASE + '/platform/system-users', 500);
                } else {
                    this.errors = json.errors || {};
                    this.serverError = json.message || '';
                    if (!this.serverError && Object.keys(this.errors).length === 0) {
                        this.serverError = 'Failed to create user';
                    }
                }
            } catch (e) {
                this.serverError = 'Network error';
            }
            this.submitting = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
