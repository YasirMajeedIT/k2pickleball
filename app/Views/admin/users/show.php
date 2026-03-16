<?php
$title = 'User Details';
$breadcrumbs = [
    ['label' => 'Users', 'url' => ($baseUrl ?? '') . '/admin/users'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/admin/users';
$editUrl = ($baseUrl ?? '') . '/admin/users/' . ($id ?? '') . '/edit';
$apiUrl = ($baseUrl ?? '') . '/api/users/' . ($id ?? '');

ob_start();
?>
<div x-data="userShow()" x-init="init()">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <template x-if="user.avatar_url">
                <img :src="user.avatar_url" alt="Avatar" class="h-16 w-16 rounded-2xl object-cover border-2 border-surface-200 dark:border-surface-700 shadow-soft">
            </template>
            <template x-if="!user.avatar_url">
                <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-lg shadow-soft"
                     x-text="initials()"></div>
            </template>
            <div>
                <h2 class="text-xl font-bold text-surface-900 dark:text-white" x-text="fullName()"></h2>
                <p class="mt-1 text-sm text-surface-500" x-text="user.professional_title || ('User ID ' + (user.id || ''))"></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($backUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-soft">
                Back
            </a>
            <a href="<?= htmlspecialchars($editUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all">
                Edit
            </a>
        </div>
    </div>

    <div x-show="loading" class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
    </div>

    <div x-show="!loading" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Basic Information</h3>
            </div>
            <div class="divide-y divide-surface-100 dark:divide-surface-800">
                <div class="grid grid-cols-2 gap-0">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Full Name</dt>
                        <dd class="mt-1 text-sm font-medium text-surface-800 dark:text-surface-100" x-text="(user.first_name || '') + ' ' + (user.last_name || '')"></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Status</dt>
                        <dd class="mt-1"><span x-html="statusBadge(user.status)"></span></dd>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-0">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Email</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.email || '-' "></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Phone</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.phone || '-' "></dd>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-0">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">User ID</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.id || '-' "></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Organization ID</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.organization_id || '-' "></dd>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-0">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Email Verified</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="formatDate(user.email_verified_at)"></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Google Account</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.google_id ? 'Connected' : 'Not connected'"></dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Professional Profile</h3>
            </div>
            <div class="divide-y divide-surface-100 dark:divide-surface-800">
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Professional Title</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.professional_title || '-' "></dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Certification Level</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.certification_level || '-' "></dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Years of Experience</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.years_experience ?? '-' "></dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Assigned Roles</dt>
                    <dd class="mt-1 flex flex-wrap gap-2">
                        <template x-if="(user.roles || []).length === 0">
                            <span class="text-sm text-surface-500">-</span>
                        </template>
                        <template x-for="role in (user.roles || [])" :key="role.id + '-' + (role.organization_id || 0)">
                            <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400" x-text="role.name"></span>
                        </template>
                    </dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Assigned Facilities</dt>
                    <dd class="mt-1 flex flex-wrap gap-2">
                        <template x-if="(user.facilities || []).length === 0">
                            <span class="text-sm text-surface-500">-</span>
                        </template>
                        <template x-for="facility in (user.facilities || [])" :key="facility.id">
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-text="facility.name"></span>
                            </span>
                        </template>
                    </dd>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Membership Details</h3>
            </div>
            <div class="grid grid-cols-2 gap-0 divide-y divide-surface-100 dark:divide-surface-800">
                <div class="px-6 py-4 col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Membership ID</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.membership_id || '-' "></dd>
                </div>
                <div class="px-6 py-4 col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Bio / Notes</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300 whitespace-pre-line" x-text="user.bio || '-' "></dd>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Emergency Contact</h3>
            </div>
            <div class="divide-y divide-surface-100 dark:divide-surface-800">
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Contact Name</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.emergency_contact_name || '-' "></dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Contact Phone</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.emergency_contact_phone || '-' "></dd>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Audit</h3>
            </div>
            <div class="divide-y divide-surface-100 dark:divide-surface-800">
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Created</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="formatDate(user.created_at)"></dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Updated</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="formatDate(user.updated_at)"></dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Last Login</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="formatDate(user.last_login_at)"></dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Last Login IP</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="user.last_login_ip || '-' "></dd>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function userShow() {
    const apiUrl = '<?= htmlspecialchars($apiUrl) ?>';
    return {
        user: {},
        loading: true,
        async init() {
            try {
                const res = await authFetch(apiUrl);
                const json = await res.json();
                if (json.data) this.user = json.data;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        formatDate(value) {
            if (!value) return '-';
            const d = new Date(value);
            return isNaN(d.getTime()) ? value : d.toLocaleString();
        },
        fullName() {
            const first = this.user.first_name || '';
            const last = this.user.last_name || '';
            const value = (first + ' ' + last).trim();
            return value || 'User Profile';
        },
        initials() {
            const first = (this.user.first_name || '').charAt(0);
            const last = (this.user.last_name || '').charAt(0);
            return (first + last || 'U').toUpperCase();
        },
        statusBadge(status) {
            const map = {
                active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                inactive: 'bg-surface-100 text-surface-600',
                suspended: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400'
            };
            return '<span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize ' + (map[status] || 'bg-surface-100 text-surface-600') + '">' + (status || '-') + '</span>';
        }
    }
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
