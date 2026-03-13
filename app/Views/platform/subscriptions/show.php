<?php
$title = 'Subscription Details';
$breadcrumbs = [
    ['label' => 'Subscriptions', 'url' => ($baseUrl ?? '') . '/platform/subscriptions'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/platform/subscriptions';
$apiUrl = ($baseUrl ?? '') . '/api/subscriptions/' . ($id ?? '');

ob_start();
?>
<div x-data="subShow()" x-init="init()">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="inline-flex items-center gap-2 text-sm font-medium text-surface-500 hover:text-primary-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Subscriptions
        </a>
    </div>

    <!-- Loading -->
    <template x-if="loading">
        <div class="flex items-center justify-center py-20">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-primary-200 border-t-primary-600"></div>
        </div>
    </template>

    <!-- Content -->
    <template x-if="!loading && sub">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-surface-900 dark:text-white">Subscription #<span x-text="sub.id"></span></h3>
                        <p class="text-sm text-surface-500" x-text="sub.organization_name || 'Org #' + sub.organization_id"></p>
                    </div>
                </div>
                <dl class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Status</dt>
                        <dd>
                            <span :class="{
                                'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400': sub.status === 'active',
                                'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400': sub.status === 'cancelled',
                                'bg-yellow-100 text-yellow-700': sub.status === 'past_due',
                                'bg-blue-100 text-blue-700': sub.status === 'trialing'
                            }" class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold" x-text="sub.status"></span>
                        </dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Plan</dt>
                        <dd class="text-sm font-semibold text-surface-900 dark:text-white" x-text="sub.plan ? sub.plan.name : 'Plan #' + sub.plan_id"></dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Billing Cycle</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300 capitalize" x-text="sub.billing_cycle || '-'"></dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Period Start</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300" x-text="sub.current_period_start ? new Date(sub.current_period_start).toLocaleDateString() : '-'"></dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Period End</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300" x-text="sub.current_period_end ? new Date(sub.current_period_end).toLocaleDateString() : '-'"></dd>
                    </div>
                    <div x-show="sub.cancelled_at" class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Cancelled At</dt>
                        <dd class="text-sm text-red-600" x-text="sub.cancelled_at ? new Date(sub.cancelled_at).toLocaleString() : '-'"></dd>
                    </div>
                    <div class="flex justify-between px-6 py-4">
                        <dt class="text-sm font-medium text-surface-500">Created</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300" x-text="sub.created_at ? new Date(sub.created_at).toLocaleString() : '-'"></dd>
                    </div>
                </dl>
            </div>

            <!-- Plan Details -->
            <div class="space-y-6">
                <template x-if="sub.plan">
                    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                            <h4 class="text-sm font-bold text-surface-900 dark:text-white">Plan Details</h4>
                        </div>
                        <dl class="divide-y divide-surface-100 dark:divide-surface-800">
                            <div class="flex justify-between px-6 py-3">
                                <dt class="text-xs font-medium text-surface-500">Name</dt>
                                <dd class="text-xs font-semibold text-surface-900 dark:text-white" x-text="sub.plan.name"></dd>
                            </div>
                            <div class="flex justify-between px-6 py-3">
                                <dt class="text-xs font-medium text-surface-500">Monthly</dt>
                                <dd class="text-xs text-surface-600 dark:text-surface-400" x-text="'$' + parseFloat(sub.plan.price_monthly || 0).toFixed(2)"></dd>
                            </div>
                            <div class="flex justify-between px-6 py-3">
                                <dt class="text-xs font-medium text-surface-500">Yearly</dt>
                                <dd class="text-xs text-surface-600 dark:text-surface-400" x-text="'$' + parseFloat(sub.plan.price_yearly || 0).toFixed(2)"></dd>
                            </div>
                            <div class="flex justify-between px-6 py-3">
                                <dt class="text-xs font-medium text-surface-500">Max Users</dt>
                                <dd class="text-xs text-surface-600 dark:text-surface-400" x-text="sub.plan.max_users || '∞'"></dd>
                            </div>
                        </dl>
                    </div>
                </template>

                <!-- Actions -->
                <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white">Actions</h4>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <a :href="APP_BASE + '/platform/organizations/' + sub.organization_id" class="flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21"/></svg>
                            View Organization
                        </a>
                        <button x-show="sub.status === 'active'" @click="cancelSub()" :disabled="cancelling"
                                class="flex items-center gap-2 text-sm text-red-600 hover:text-red-700 font-medium disabled:opacity-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancel Subscription
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function subShow() {
    const apiUrl = '<?= htmlspecialchars($apiUrl, ENT_QUOTES) ?>';
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    return {
        sub: null,
        loading: true,
        cancelling: false,
        async init() {
            try {
                const res = await fetch(apiUrl, { headers });
                if (res.status === 401) { window.location.href = APP_BASE + '/admin/login'; return; }
                const json = await res.json();
                this.sub = json.data;
            } catch (e) { console.error(e); }
            this.loading = false;
        },
        async cancelSub() {
            if (!confirm('Are you sure you want to cancel this subscription?')) return;
            this.cancelling = true;
            try {
                const res = await fetch(apiUrl + '/cancel', {
                    method: 'POST',
                    headers: { ...headers, 'Content-Type': 'application/json' }
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Subscription cancelled', type: 'success' } }));
                    this.sub.status = 'cancelled';
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed to cancel', type: 'error' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
            this.cancelling = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
