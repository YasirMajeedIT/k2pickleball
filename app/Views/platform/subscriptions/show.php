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
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Plan</dt><dd class="text-sm font-semibold text-surface-900 dark:text-white" x-text="sub.plan ? sub.plan.name : 'Plan #' + sub.plan_id"></dd></div>
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Billing Cycle</dt><dd class="text-sm text-surface-700 dark:text-surface-300 capitalize" x-text="sub.billing_cycle || '-'"></dd></div>
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Period Start</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="sub.current_period_start ? new Date(sub.current_period_start).toLocaleDateString() : '-'"></dd></div>
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Period End</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="sub.current_period_end ? new Date(sub.current_period_end).toLocaleDateString() : '-'"></dd></div>
                    <div x-show="sub.cancelled_at" class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Cancelled At</dt><dd class="text-sm text-red-600" x-text="sub.cancelled_at ? new Date(sub.cancelled_at).toLocaleString() : '-'"></dd></div>
                    <div class="flex justify-between px-6 py-4"><dt class="text-sm font-medium text-surface-500">Created</dt><dd class="text-sm text-surface-700 dark:text-surface-300" x-text="sub.created_at ? new Date(sub.created_at).toLocaleString() : '-'"></dd></div>
                </dl>
            </div>

            <!-- Side -->
            <div class="space-y-6">
                <!-- Plan Details -->
                <template x-if="sub.plan">
                    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                            <h4 class="text-sm font-bold text-surface-900 dark:text-white">Plan Details</h4>
                        </div>
                        <dl class="divide-y divide-surface-100 dark:divide-surface-800">
                            <div class="flex justify-between px-6 py-3"><dt class="text-xs font-medium text-surface-500">Name</dt><dd class="text-xs font-semibold text-surface-900 dark:text-white" x-text="sub.plan.name"></dd></div>
                            <div class="flex justify-between px-6 py-3"><dt class="text-xs font-medium text-surface-500">Monthly</dt><dd class="text-xs text-surface-600 dark:text-surface-400" x-text="'$' + parseFloat(sub.plan.price_monthly || 0).toFixed(2)"></dd></div>
                            <div class="flex justify-between px-6 py-3"><dt class="text-xs font-medium text-surface-500">Yearly</dt><dd class="text-xs text-surface-600 dark:text-surface-400" x-text="'$' + parseFloat(sub.plan.price_yearly || 0).toFixed(2)"></dd></div>
                            <div class="flex justify-between px-6 py-3"><dt class="text-xs font-medium text-surface-500">Max Users</dt><dd class="text-xs text-surface-600 dark:text-surface-400" x-text="sub.plan.max_users || '∞'"></dd></div>
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
                        <button x-show="sub.status === 'active' || sub.status === 'trialing'" @click="showChangePlan = true" class="flex items-center gap-2 text-sm text-amber-600 hover:text-amber-700 font-medium w-full text-left">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                            Change Plan
                        </button>
                        <button x-show="sub.status === 'cancelled'" @click="reactivate()" :disabled="processing" class="flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium disabled:opacity-50 w-full text-left">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9"/></svg>
                            Reactivate
                        </button>
                        <button x-show="sub.status === 'active'" @click="cancelSub()" :disabled="processing"
                                class="flex items-center gap-2 text-sm text-red-600 hover:text-red-700 font-medium disabled:opacity-50 w-full text-left">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancel Subscription
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Change Plan Modal -->
    <template x-if="showChangePlan">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-surface-950/50 backdrop-blur-sm" @click.self="showChangePlan = false">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-700 w-full max-w-md mx-4">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                    <h3 class="font-bold text-surface-900 dark:text-white">Change Plan</h3>
                    <button @click="showChangePlan = false" class="text-surface-400 hover:text-surface-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <p class="text-sm text-surface-500">Current plan: <span class="font-semibold text-surface-900 dark:text-white" x-text="sub.plan ? sub.plan.name : 'Unknown'"></span></p>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">New Plan</label>
                        <select x-model="newPlanId" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm">
                            <option value="">Select Plan</option>
                            <template x-for="p in plans" :key="p.id"><option :value="p.id" x-text="p.name + ' ($' + parseFloat(p.price_monthly).toFixed(2) + '/mo)'"></option></template>
                        </select>
                    </div>
                    <button @click="changePlan()" :disabled="!newPlanId || processing" class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50">Change Plan</button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function subShow() {
    const subId = <?= (int) ($id ?? 0) ?>;
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    return {
        sub: null, loading: true, processing: false, showChangePlan: false, plans: [], newPlanId: '',
        async init() {
            try {
                const [subRes, plansRes] = await Promise.all([
                    fetch(APP_BASE + '/api/subscriptions/' + subId, { headers }),
                    fetch(APP_BASE + '/api/plans?per_page=100', { headers }),
                ]);
                if (subRes.status === 401) { window.location.href = APP_BASE + '/platform/login'; return; }
                const subJson = await subRes.json(); this.sub = subJson.data;
                const plansJson = await plansRes.json(); this.plans = (plansJson.data || []).filter(p => p.is_active);
            } catch (e) { console.error(e); }
            this.loading = false;
        },
        async cancelSub() {
            if (!confirm('Are you sure you want to cancel this subscription?')) return;
            this.processing = true;
            try {
                const res = await fetch(APP_BASE + '/api/subscriptions/' + subId + '/cancel', { method: 'POST', headers: { ...headers, 'Content-Type': 'application/json' } });
                const json = await res.json();
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Subscription cancelled', type: 'success' } })); this.sub.status = 'cancelled'; this.sub.cancelled_at = new Date().toISOString(); }
                else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            this.processing = false;
        },
        async reactivate() {
            if (!confirm('Reactivate this subscription?')) return;
            this.processing = true;
            try {
                const res = await fetch(APP_BASE + '/api/subscriptions/' + subId + '/reactivate', { method: 'POST', headers: { ...headers, 'Content-Type': 'application/json' } });
                const json = await res.json();
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Subscription reactivated', type: 'success' } })); this.init(); }
                else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            this.processing = false;
        },
        async changePlan() {
            this.processing = true;
            try {
                const res = await fetch(APP_BASE + '/api/subscriptions/' + subId + '/change-plan', { method: 'PUT', headers: { ...headers, 'Content-Type': 'application/json' }, body: JSON.stringify({ plan_id: parseInt(this.newPlanId) }) });
                const json = await res.json();
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Plan changed successfully', type: 'success' } })); this.showChangePlan = false; this.init(); }
                else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            this.processing = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
