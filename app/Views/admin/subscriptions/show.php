<?php
$title = 'Subscription Details';
$breadcrumbs = [
    ['label' => 'Subscriptions', 'url' => ($baseUrl ?? '') . '/admin/subscriptions'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/admin/subscriptions';
$apiUrl  = ($baseUrl ?? '') . '/api/subscriptions/' . ($id ?? '');

ob_start();
?>
<div x-data="subscriptionShow()" x-init="init()">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-surface-900 dark:text-white">Subscription Details</h2>
            <p class="mt-0.5 text-sm text-surface-500" x-text="sub.plan ? sub.plan.name + ' — ' + sub.billing_cycle : ''"></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($backUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-soft">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <button x-show="sub.status === 'active'" @click="cancelSub()"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-4 py-2 text-sm font-semibold text-white hover:from-red-700 hover:to-red-800 shadow-soft transition-all">
                Cancel Subscription
            </button>
            <button x-show="sub.status === 'cancelled'" @click="reactivateSub()"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-green-600 to-green-700 px-4 py-2 text-sm font-semibold text-white hover:from-green-700 hover:to-green-800 shadow-soft transition-all">
                Reactivate
            </button>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-20 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
        <p class="text-sm text-surface-400">Loading subscription...</p>
    </div>

    <!-- Content -->
    <div x-show="!loading" class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Subscription Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Subscription</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Status</dt>
                            <dd class="mt-1"><span x-html="statusBadge(sub.status)"></span></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Billing Cycle</dt>
                            <dd class="mt-1 text-sm font-medium text-surface-700 dark:text-surface-300 capitalize" x-text="sub.billing_cycle || '—'"></dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Period Start</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="sub.current_period_start ? new Date(sub.current_period_start).toLocaleDateString() : '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Period End / Renews</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="sub.current_period_end ? new Date(sub.current_period_end).toLocaleDateString() : '—'"></dd>
                        </div>
                    </div>
                    <div x-show="sub.cancelled_at" class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Cancelled At</dt>
                            <dd class="mt-1 text-sm text-red-600" x-text="sub.cancelled_at ? new Date(sub.cancelled_at).toLocaleString() : '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Cancel Reason</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="sub.cancel_reason || '—'"></dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Created</dt>
                            <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="sub.created_at ? new Date(sub.created_at).toLocaleDateString() : '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Subscription ID</dt>
                            <dd class="mt-1 text-sm font-mono text-surface-500" x-text="sub.id || '—'"></dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan Info -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Plan Details</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Plan Name</dt>
                        <dd class="mt-1 text-lg font-bold text-surface-800 dark:text-surface-100" x-text="sub.plan ? sub.plan.name : '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Price</dt>
                        <dd class="mt-1 text-sm font-semibold text-primary-600 dark:text-primary-400">
                            <span x-text="sub.plan ? '$' + parseFloat(sub.billing_cycle === 'yearly' ? sub.plan.price_yearly : sub.plan.price_monthly).toFixed(2) : '—'"></span>
                            <span class="text-surface-400 font-normal" x-text="'/ ' + (sub.billing_cycle || 'month')"></span>
                        </dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Description</dt>
                        <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="sub.plan ? (sub.plan.description || '—') : '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Limits</dt>
                        <dd class="mt-2 space-y-1.5 text-sm">
                            <div class="flex justify-between" x-show="sub.plan && sub.plan.max_facilities">
                                <span class="text-surface-500">Facilities</span>
                                <span class="font-medium text-surface-700 dark:text-surface-300" x-text="sub.plan ? sub.plan.max_facilities : ''"></span>
                            </div>
                            <div class="flex justify-between" x-show="sub.plan && sub.plan.max_courts">
                                <span class="text-surface-500">Courts</span>
                                <span class="font-medium text-surface-700 dark:text-surface-300" x-text="sub.plan ? sub.plan.max_courts : ''"></span>
                            </div>
                            <div class="flex justify-between" x-show="sub.plan && sub.plan.max_users">
                                <span class="text-surface-500">Users</span>
                                <span class="font-medium text-surface-700 dark:text-surface-300" x-text="sub.plan ? sub.plan.max_users : ''"></span>
                            </div>
                            <div class="flex justify-between" x-show="sub.plan && sub.plan.max_players">
                                <span class="text-surface-500">Players</span>
                                <span class="font-medium text-surface-700 dark:text-surface-300" x-text="sub.plan ? sub.plan.max_players : ''"></span>
                            </div>
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function subscriptionShow() {
    return {
        sub: {},
        loading: true,
        async init() {
            try {
                const res = await authFetch('<?= htmlspecialchars($apiUrl) ?>');
                if (res.status === 401) { window.location.href = APP_BASE + '/admin/login'; return; }
                const json = await res.json();
                if (json.data) this.sub = json.data;
            } catch (e) { console.error(e); }
            finally { this.loading = false; }
        },
        statusBadge(status) {
            const map = {
                active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                past_due: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
                trialing: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                paused: 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400',
                expired: 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400',
            };
            const cls = map[status] || 'bg-surface-100 text-surface-600';
            return '<span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize ' + cls + '">' + (status || '—') + '</span>';
        },
        async cancelSub() {
            if (!confirm('Cancel this subscription? The organization will lose access at the end of the billing period.')) return;
            try {
                const res = await authFetch('<?= htmlspecialchars($apiUrl) ?>/cancel', { method: 'POST' });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Subscription cancelled', type: 'success' } }));
                    this.init();
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } }));
                }
            } catch(e) { console.error(e); }
        },
        async reactivateSub() {
            try {
                const res = await authFetch('<?= htmlspecialchars($apiUrl) ?>/reactivate', { method: 'POST' });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Subscription reactivated', type: 'success' } }));
                    this.init();
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } }));
                }
            } catch(e) { console.error(e); }
        },
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
