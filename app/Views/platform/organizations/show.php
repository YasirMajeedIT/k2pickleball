<?php
$title = 'Organization Details';
$breadcrumbs = [
    ['label' => 'Organizations', 'url' => ($baseUrl ?? '') . '/platform/organizations'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/platform/organizations';
$editUrl = ($baseUrl ?? '') . '/platform/organizations/' . ($id ?? '') . '/edit';
$apiUrl = ($baseUrl ?? '') . '/api/organizations/' . ($id ?? '');

ob_start();
?>
<div x-data="orgShow()" x-init="init()">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="inline-flex items-center gap-2 text-sm font-medium text-surface-500 hover:text-primary-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Organizations
        </a>
        <a :href="'<?= htmlspecialchars($editUrl) ?>'" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary-500/20 hover:from-primary-700 hover:to-primary-800 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
            Edit
        </a>
    </div>

    <!-- Loading -->
    <template x-if="loading">
        <div class="flex items-center justify-center py-20">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-primary-200 border-t-primary-600"></div>
        </div>
    </template>

    <!-- Content -->
    <template x-if="!loading && org">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white" x-text="org.name"></h3>
                </div>
                <dl class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">ID</dt>
                        <dd class="text-sm font-semibold text-surface-900 dark:text-white" x-text="org.id"></dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Slug</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300 font-mono" x-text="org.slug"></dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Email</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300" x-text="org.email || '-'"></dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Phone</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300" x-text="org.phone || '-'"></dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Website</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300" x-text="org.website || '-'"></dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Status</dt>
                        <dd>
                            <span :class="{
                                'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400': org.status === 'active',
                                'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400': org.status === 'suspended',
                                'bg-surface-100 text-surface-600': org.status === 'inactive',
                                'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400': org.status === 'trial'
                            }" class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold" x-text="org.status"></span>
                        </dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Timezone</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300" x-text="org.timezone || '-'"></dd>
                    </div>
                </dl>
            </div>

            <!-- Side Info -->
            <div class="space-y-6">
                <!-- Address Card -->
                <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white">Address</h4>
                    </div>
                    <div class="px-6 py-4 space-y-2 text-sm text-surface-600 dark:text-surface-400">
                        <p x-text="org.address_line1 || 'Not provided'"></p>
                        <p x-show="org.address_line2" x-text="org.address_line2"></p>
                        <p>
                            <span x-text="org.city || ''"></span>
                            <span x-show="org.state" x-text="', ' + org.state"></span>
                            <span x-show="org.zip" x-text="' ' + org.zip"></span>
                        </p>
                        <p x-show="org.country" x-text="org.country"></p>
                    </div>
                </div>

                <!-- Meta Card -->
                <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white">Details</h4>
                    </div>
                    <dl class="divide-y divide-surface-100 dark:divide-surface-800">
                        <div class="flex justify-between px-6 py-3">
                            <dt class="text-xs font-medium text-surface-500">UUID</dt>
                            <dd class="text-xs text-surface-600 dark:text-surface-400 font-mono truncate max-w-[160px]" x-text="org.uuid"></dd>
                        </div>
                        <div class="flex justify-between px-6 py-3">
                            <dt class="text-xs font-medium text-surface-500">Created</dt>
                            <dd class="text-xs text-surface-600 dark:text-surface-400" x-text="org.created_at ? new Date(org.created_at).toLocaleString() : '-'"></dd>
                        </div>
                        <div class="flex justify-between px-6 py-3">
                            <dt class="text-xs font-medium text-surface-500">Updated</dt>
                            <dd class="text-xs text-surface-600 dark:text-surface-400" x-text="org.updated_at ? new Date(org.updated_at).toLocaleString() : '-'"></dd>
                        </div>
                    </dl>
                </div>

                <!-- Domains Card -->
                <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white">Verified Domains</h4>
                    </div>
                    <div class="px-6 py-4">
                        <template x-if="org.domains && org.domains.length > 0">
                            <ul class="space-y-2">
                                <template x-for="d in org.domains" :key="d.id">
                                    <li class="text-sm text-surface-600 dark:text-surface-400 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span x-text="d.domain"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                        <template x-if="!org.domains || org.domains.length === 0">
                            <p class="text-sm text-surface-400">No verified domains</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function orgShow() {
    const apiUrl = '<?= htmlspecialchars($apiUrl, ENT_QUOTES) ?>';
    const token = localStorage.getItem('access_token');

    return {
        org: null,
        loading: true,
        async init() {
            try {
                const res = await fetch(apiUrl, {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (res.status === 401) { window.location.href = APP_BASE + '/admin/login'; return; }
                const json = await res.json();
                this.org = json.data;
            } catch (e) { console.error(e); }
            this.loading = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
