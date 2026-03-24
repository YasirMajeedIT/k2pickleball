<?php
$title = 'Organization Details';
$breadcrumbs = [
    ['label' => 'Organizations', 'url' => ($baseUrl ?? '') . '/platform/organizations'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/platform/organizations';
$editUrl = ($baseUrl ?? '') . '/platform/organizations/' . ($id ?? '') . '/edit';

ob_start();
?>
<div x-data="orgShow()" x-init="init()">
    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="inline-flex items-center gap-2 text-sm font-medium text-surface-500 hover:text-primary-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Organizations
        </a>
        <div class="flex items-center gap-2">
            <template x-if="org && org.status !== 'active'">
                <button @click="changeStatus('active')" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Activate
                </button>
            </template>
            <template x-if="org && org.status === 'active'">
                <button @click="changeStatus('suspended')" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Suspend
                </button>
            </template>
            <template x-if="org && org.status !== 'cancelled' && org.status !== 'inactive'">
                <button @click="changeStatus('inactive')" class="inline-flex items-center gap-1.5 rounded-xl border border-red-300 dark:border-red-500/30 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Disable
                </button>
            </template>
            <a :href="'<?= htmlspecialchars($editUrl) ?>'" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-primary-500/20 hover:from-primary-700 hover:to-primary-800 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                Edit
            </a>
            <template x-if="org && (org.status === 'active' || org.status === 'trial')">
                <a :href="clientSiteUrl()" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-semibold text-surface-700 dark:text-surface-200 hover:border-primary-400 hover:text-primary-600 dark:hover:text-primary-400 shadow-soft transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253M3.157 7.582A8.959 8.959 0 003 12c0 .778.099 1.533.284 2.253"/></svg>
                    View Client Site
                    <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            </template>
            <template x-if="org && (org.status === 'active' || org.status === 'trial')">
                <button @click="viewAdminDashboard()" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-purple-500/20 hover:from-purple-700 hover:to-purple-800 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    View Admin Dashboard
                </button>
            </template>
        </div>
    </div>

    <!-- Loading -->
    <template x-if="loading">
        <div class="flex items-center justify-center py-20">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-primary-200 border-t-primary-600"></div>
        </div>
    </template>

    <!-- Content -->
    <template x-if="!loading && org">
        <div class="space-y-6">
            <!-- Stats Row -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft p-4">
                    <p class="text-xs font-medium text-surface-400">Users</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1" x-text="org.user_count || 0"></p>
                </div>
                <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft p-4">
                    <p class="text-xs font-medium text-surface-400">Facilities</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1" x-text="org.facility_count || 0"></p>
                </div>
                <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft p-4">
                    <p class="text-xs font-medium text-surface-400">Extensions</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1" x-text="org.extensions ? org.extensions.length : 0"></p>
                </div>
                <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft p-4">
                    <p class="text-xs font-medium text-surface-400">Status</p>
                    <span :class="statusColors[org.status] || 'bg-surface-100 text-surface-600'" class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold" x-text="org.status"></span>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-surface-900 dark:text-white" x-text="org.name"></h3>
                            <p class="text-xs text-surface-500 font-mono" x-text="org.slug"></p>
                        </div>
                    </div>
                    <dl class="divide-y divide-surface-100 dark:divide-surface-800">
                        <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">ID</dt><dd class="text-sm font-semibold text-surface-900 dark:text-white" x-text="org.id"></dd></div>
                        <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Email</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="org.email || '-'"></dd></div>
                        <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Phone</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="org.phone || '-'"></dd></div>
                        <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Website</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="org.website || '-'"></dd></div>
                        <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Timezone</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="org.timezone || '-'"></dd></div>
                    </dl>
                </div>

                <!-- Side Panels -->
                <div class="space-y-6">
                    <!-- Subscription Card -->
                    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                            <h4 class="text-sm font-bold text-surface-900 dark:text-white">Subscription</h4>
                        </div>
                        <div class="px-6 py-4">
                            <template x-if="org.subscription">
                                <div class="space-y-3">
                                    <div class="flex justify-between"><span class="text-xs text-surface-500">Plan</span><span class="text-xs font-semibold text-surface-900 dark:text-white" x-text="org.subscription.plan_name || 'Unknown'"></span></div>
                                    <div class="flex justify-between"><span class="text-xs text-surface-500">Status</span><span :class="org.subscription.status === 'active' ? 'text-emerald-600' : 'text-red-500'" class="text-xs font-semibold" x-text="org.subscription.status"></span></div>
                                    <div class="flex justify-between"><span class="text-xs text-surface-500">Cycle</span><span class="text-xs text-surface-700 dark:text-surface-300 capitalize" x-text="org.subscription.billing_cycle"></span></div>
                                    <div class="flex justify-between"><span class="text-xs text-surface-500">Renews</span><span class="text-xs text-surface-700 dark:text-surface-300" x-text="org.subscription.current_period_end ? new Date(org.subscription.current_period_end).toLocaleDateString() : '-'"></span></div>
                                    <a :href="APP_BASE + '/platform/subscriptions/' + org.subscription.id" class="block text-center text-xs font-medium text-primary-600 hover:text-primary-700 pt-2">View Subscription &rarr;</a>
                                </div>
                            </template>
                            <template x-if="!org.subscription">
                                <div class="text-center py-3">
                                    <p class="text-sm text-surface-400 mb-2">No subscription</p>
                                    <button @click="showSubscribeModal = true" class="text-xs font-medium text-primary-600 hover:text-primary-700">+ Create Subscription</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Address Card -->
                    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                            <h4 class="text-sm font-bold text-surface-900 dark:text-white">Address</h4>
                        </div>
                        <div class="px-6 py-4 space-y-1 text-sm text-surface-600 dark:text-surface-400">
                            <p x-text="org.address_line1 || 'Not provided'"></p>
                            <p x-show="org.address_line2" x-text="org.address_line2"></p>
                            <p><span x-text="org.city || ''"></span><span x-show="org.state" x-text="', ' + org.state"></span><span x-show="org.zip" x-text="' ' + org.zip"></span></p>
                            <p x-show="org.country" x-text="org.country"></p>
                        </div>
                    </div>

                    <!-- Meta Card -->
                    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                            <h4 class="text-sm font-bold text-surface-900 dark:text-white">Details</h4>
                        </div>
                        <dl class="divide-y divide-surface-100 dark:divide-surface-800">
                            <div class="flex justify-between px-6 py-3"><dt class="text-xs font-medium text-surface-500">UUID</dt><dd class="text-xs text-surface-600 dark:text-surface-400 font-mono truncate max-w-[160px]" x-text="org.uuid"></dd></div>
                            <div class="flex justify-between px-6 py-3"><dt class="text-xs font-medium text-surface-500">Created</dt><dd class="text-xs text-surface-600 dark:text-surface-400" x-text="org.created_at ? new Date(org.created_at).toLocaleString() : '-'"></dd></div>
                            <div class="flex justify-between px-6 py-3"><dt class="text-xs font-medium text-surface-500">Updated</dt><dd class="text-xs text-surface-600 dark:text-surface-400" x-text="org.updated_at ? new Date(org.updated_at).toLocaleString() : '-'"></dd></div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Extensions -->
            <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30 flex items-center justify-between">
                    <h4 class="text-sm font-bold text-surface-900 dark:text-white">Installed Extensions</h4>
                    <button @click="showExtModal = true" class="text-xs font-medium text-primary-600 hover:text-primary-700">+ Install Extension</button>
                </div>
                <div class="px-6 py-4">
                    <template x-if="org.extensions && org.extensions.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <template x-for="ext in org.extensions" :key="ext.extension_id || ext.id">
                                <div class="flex items-center justify-between rounded-xl border border-surface-200 dark:border-surface-700 p-3">
                                    <div><p class="text-sm font-semibold text-surface-900 dark:text-white" x-text="ext.name"></p><p class="text-xs text-surface-400" x-text="'v' + (ext.version || '1.0.0')"></p></div>
                                    <button @click="uninstallExt(ext.extension_id || ext.id)" class="text-xs text-red-500 hover:text-red-700 font-medium">Remove</button>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!org.extensions || org.extensions.length === 0">
                        <p class="text-sm text-surface-400 text-center py-4">No extensions installed</p>
                    </template>
                </div>
            </div>

            <!-- Domains -->
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

            <!-- Danger Zone -->
            <div class="rounded-2xl border border-red-200 dark:border-red-500/20 bg-red-50/50 dark:bg-red-500/5 shadow-soft overflow-hidden">
                <div class="border-b border-red-200 dark:border-red-500/20 px-6 py-4">
                    <h4 class="text-sm font-bold text-red-700 dark:text-red-400">Danger Zone</h4>
                </div>
                <div class="px-6 py-4 flex items-center justify-between">
                    <div><p class="text-sm font-medium text-surface-700 dark:text-surface-300">Delete Organization</p><p class="text-xs text-surface-500 mt-0.5">Permanently delete this organization and all its data.</p></div>
                    <button @click="deleteOrg()" class="rounded-xl border border-red-300 dark:border-red-500/30 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-100 dark:hover:bg-red-500/10 transition-all">Delete</button>
                </div>
            </div>
        </div>
    </template>

    <!-- Install Extension Modal -->
    <template x-if="showExtModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-surface-950/50 backdrop-blur-sm" @click.self="showExtModal = false">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-700 w-full max-w-lg mx-4">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                    <h3 class="font-bold text-surface-900 dark:text-white">Install Extension</h3>
                    <button @click="showExtModal = false" class="text-surface-400 hover:text-surface-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-4 max-h-80 overflow-y-auto space-y-2">
                    <template x-if="availableExts.length === 0"><p class="text-sm text-surface-400 text-center py-4">All extensions already installed</p></template>
                    <template x-for="ext in availableExts" :key="ext.id">
                        <div class="flex items-center justify-between rounded-xl border border-surface-200 dark:border-surface-700 p-3">
                            <div><p class="text-sm font-semibold text-surface-900 dark:text-white" x-text="ext.name"></p><p class="text-xs text-primary-500 mt-0.5" x-text="ext.price_monthly > 0 ? '$' + parseFloat(ext.price_monthly).toFixed(2) + '/mo' : 'Free'"></p></div>
                            <button @click="installExt(ext.id)" class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700">Install</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <!-- Create Subscription Modal -->
    <template x-if="showSubscribeModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-surface-950/50 backdrop-blur-sm" @click.self="showSubscribeModal = false">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-700 w-full max-w-md mx-4">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                    <h3 class="font-bold text-surface-900 dark:text-white">Create Subscription</h3>
                    <button @click="showSubscribeModal = false" class="text-surface-400 hover:text-surface-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Plan</label>
                        <select x-model="subForm.plan_id" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm">
                            <option value="">Select Plan</option>
                            <template x-for="p in plans" :key="p.id"><option :value="p.id" x-text="p.name + ' ($' + parseFloat(p.price_monthly).toFixed(2) + '/mo)'"></option></template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Billing Cycle</label>
                        <select x-model="subForm.billing_cycle" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm">
                            <option value="monthly">Monthly</option><option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <button @click="createSubscription()" :disabled="!subForm.plan_id" class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50">Create Subscription</button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function orgShow() {
    const orgId = <?= (int) ($id ?? 0) ?>;
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    return {
        org: null, loading: true, showExtModal: false, showSubscribeModal: false,
        availableExts: [], plans: [], subForm: { plan_id: '', billing_cycle: 'monthly' },
        clientSiteUrl() {
            if (!this.org || !this.org.slug) return '#';
            const hostname = window.location.hostname; // e.g. platform.k2pickleball.local
            const baseDomain = hostname.replace(/^platform\./, '');
            return window.location.protocol + '//' + this.org.slug + '.' + baseDomain;
        },
        statusColors: {
            active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
            suspended: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
            inactive: 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400',
            trial: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
            cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
        },
        async init() {
            try {
                const [orgRes, extRes, plansRes] = await Promise.all([
                    fetch(APP_BASE + '/api/organizations/' + orgId + '/details', { headers }),
                    fetch(APP_BASE + '/api/platform/extensions?per_page=100', { headers }),
                    fetch(APP_BASE + '/api/plans?per_page=100', { headers }),
                ]);
                if (orgRes.status === 401) { window.location.href = APP_BASE + '/platform/login'; return; }
                const orgJson = await orgRes.json(); this.org = orgJson.data;
                const extJson = await extRes.json();
                const installed = (this.org.extensions || []).map(x => x.extension_id || x.id);
                this.availableExts = (extJson.data || []).filter(e => !installed.includes(e.id));
                const plansJson = await plansRes.json();
                this.plans = (plansJson.data || []).filter(p => p.is_active);
            } catch (e) { console.error(e); }
            this.loading = false;
        },
        async changeStatus(status) {
            const label = { active: 'activate', suspended: 'suspend', inactive: 'disable', cancelled: 'cancel' };
            if (!confirm('Are you sure you want to ' + (label[status] || status) + ' this organization?')) return;
            try {
                const res = await fetch(APP_BASE + '/api/organizations/' + orgId + '/status', { method: 'PATCH', headers: { ...headers, 'Content-Type': 'application/json' }, body: JSON.stringify({ status }) });
                const json = await res.json();
                if (res.ok) { this.org.status = status; window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Status updated', type: 'success' } })); }
                else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        },
        async viewAdminDashboard() {
            if (!confirm('View this organization\'s admin dashboard? Your current session will be preserved.')) return;
            try {
                const res = await fetch(APP_BASE + '/api/platform/organizations/' + orgId + '/impersonate', { method: 'POST', headers: { ...headers, 'Content-Type': 'application/json' } });
                const json = await res.json();
                if (res.ok) {
                    localStorage.setItem('original_token', localStorage.getItem('access_token'));
                    localStorage.setItem('access_token', json.data.access_token);
                    localStorage.setItem('impersonating', JSON.stringify(json.data.user));
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Entering admin dashboard as ' + json.data.user.email, type: 'success' } }));
                    setTimeout(() => window.location.href = APP_BASE + '/admin', 500);
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed to impersonate', type: 'error' } }));
                }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        },
        async deleteOrg() {
            if (!confirm('WARNING: This will permanently delete the organization. Continue?')) return;
            try {
                const res = await fetch(APP_BASE + '/api/organizations/' + orgId, { method: 'DELETE', headers });
                const json = await res.json();
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Organization deleted', type: 'success' } })); setTimeout(() => window.location.href = APP_BASE + '/platform/organizations', 500); }
                else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed to delete', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        },
        async installExt(extId) {
            try {
                const res = await fetch(APP_BASE + '/api/platform/organizations/' + orgId + '/extensions', { method: 'POST', headers: { ...headers, 'Content-Type': 'application/json' }, body: JSON.stringify({ extension_id: extId }) });
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Extension installed', type: 'success' } })); this.showExtModal = false; this.init(); }
                else { const json = await res.json(); window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        },
        async uninstallExt(extId) {
            if (!confirm('Uninstall this extension?')) return;
            try {
                const res = await fetch(APP_BASE + '/api/platform/organizations/' + orgId + '/extensions/' + extId, { method: 'DELETE', headers });
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Extension removed', type: 'success' } })); this.init(); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        },
        async createSubscription() {
            try {
                const res = await fetch(APP_BASE + '/api/subscriptions/create-for-org', { method: 'POST', headers: { ...headers, 'Content-Type': 'application/json' }, body: JSON.stringify({ organization_id: orgId, plan_id: parseInt(this.subForm.plan_id), billing_cycle: this.subForm.billing_cycle }) });
                const json = await res.json();
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Subscription created', type: 'success' } })); this.showSubscribeModal = false; this.init(); }
                else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
