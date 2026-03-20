<?php
$title = 'User Details';
$breadcrumbs = [
    ['label' => 'System Users', 'url' => ($baseUrl ?? '') . '/platform/system-users'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/platform/system-users';

ob_start();
?>
<div x-data="userShow()" x-init="init()">
    <div class="mb-6 flex items-center justify-between">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="inline-flex items-center gap-2 text-sm font-medium text-surface-500 hover:text-primary-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Users
        </a>
    </div>

    <template x-if="loading">
        <div class="flex items-center justify-center py-20">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-primary-200 border-t-primary-600"></div>
        </div>
    </template>

    <template x-if="!loading && user">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white font-bold text-sm" x-text="(user.first_name||'?')[0] + (user.last_name||'?')[0]"></div>
                    <div>
                        <h3 class="text-lg font-bold text-surface-900 dark:text-white" x-text="(user.first_name || '') + ' ' + (user.last_name || '')"></h3>
                        <p class="text-sm text-surface-500" x-text="user.email"></p>
                    </div>
                    <span :class="statusColors[user.status]" class="ml-auto inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold" x-text="user.status"></span>
                </div>
                <dl class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">ID</dt><dd class="text-sm font-semibold text-surface-900 dark:text-white" x-text="user.id"></dd></div>
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Email</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="user.email"></dd></div>
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Phone</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="user.phone || '-'"></dd></div>
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Organization</dt><dd><a x-show="user.organization_id" :href="APP_BASE + '/platform/organizations/' + user.organization_id" class="text-sm text-primary-600 hover:text-primary-700 font-medium" x-text="user.organization_name || '-'"></a><span x-show="!user.organization_id" class="text-sm text-surface-400">None (Platform)</span></dd></div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Roles</dt>
                        <dd class="flex flex-wrap gap-1">
                            <template x-for="role in (user.roles || [])" :key="role.slug">
                                <span class="inline-block rounded-full bg-primary-100 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400 px-2 py-0.5 text-xs font-medium" x-text="role.name"></span>
                            </template>
                            <span x-show="!user.roles || user.roles.length === 0" class="text-xs text-surface-400">No roles</span>
                        </dd>
                    </div>
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Created</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="user.created_at ? new Date(user.created_at).toLocaleString() : '-'"></dd></div>
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Last Login</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="user.last_login_at ? new Date(user.last_login_at).toLocaleString() : 'Never'"></dd></div>
                </dl>
            </div>

            <!-- Actions -->
            <div class="space-y-6">
                <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white">Actions</h4>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <template x-if="user.status !== 'active'">
                            <button @click="changeStatus('active')" class="flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium w-full text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Activate User
                            </button>
                        </template>
                        <template x-if="user.status === 'active'">
                            <button @click="changeStatus('suspended')" class="flex items-center gap-2 text-sm text-amber-600 hover:text-amber-700 font-medium w-full text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Suspend User
                            </button>
                        </template>
                        <template x-if="user.status !== 'inactive'">
                            <button @click="changeStatus('inactive')" class="flex items-center gap-2 text-sm text-red-600 hover:text-red-700 font-medium w-full text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                Disable User
                            </button>
                        </template>
                        <hr class="border-surface-100 dark:border-surface-800">
                        <button @click="impersonateUser()" class="flex items-center gap-2 text-sm text-purple-600 hover:text-purple-700 font-medium w-full text-left">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Login as User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function userShow() {
    const userId = <?= (int) ($id ?? 0) ?>;
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    return {
        user: null, loading: true,
        statusColors: {
            active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
            suspended: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
            inactive: 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400',
            pending: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
        },
        async init() {
            try {
                const res = await fetch(APP_BASE + '/api/platform/users/' + userId, { headers });
                if (res.status === 401) { window.location.href = APP_BASE + '/platform/login'; return; }
                const json = await res.json(); this.user = json.data;
            } catch (e) { console.error(e); }
            this.loading = false;
        },
        async changeStatus(status) {
            if (!confirm('Change user status to ' + status + '?')) return;
            try {
                const res = await fetch(APP_BASE + '/api/platform/users/' + userId + '/status', { method: 'PATCH', headers: { ...headers, 'Content-Type': 'application/json' }, body: JSON.stringify({ status }) });
                const json = await res.json();
                if (res.ok) { this.user.status = status; window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Status updated', type: 'success' } })); }
                else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        },
        async impersonateUser() {
            if (!confirm('Login as this user? Your current session will be preserved.')) return;
            try {
                const res = await fetch(APP_BASE + '/api/platform/impersonate/' + userId, { method: 'POST', headers: { ...headers, 'Content-Type': 'application/json' } });
                const json = await res.json();
                if (res.ok) {
                    localStorage.setItem('original_token', localStorage.getItem('access_token'));
                    localStorage.setItem('access_token', json.data.access_token);
                    localStorage.setItem('impersonating', JSON.stringify(json.data.user));
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Now logged in as ' + json.data.user.email, type: 'success' } }));
                    setTimeout(() => window.location.href = APP_BASE + '/admin', 500);
                } else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
