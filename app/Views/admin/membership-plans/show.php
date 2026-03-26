<?php
$title = 'Membership Plan Details';
$breadcrumbs = [
    ['label' => 'Membership Plans', 'url' => ($baseUrl ?? '') . '/admin/membership-plans'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/admin/membership-plans';
$editUrl = ($baseUrl ?? '') . '/admin/membership-plans/' . ($id ?? '') . '/edit';
$apiUrl  = ($baseUrl ?? '') . '/api/membership-plans/' . ($id ?? '');

ob_start();
?>
<div x-data="membershipPlanShow()" x-init="init()">

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-4 h-4 rounded-full flex-shrink-0" :style="'background:' + (plan.color || '#6366f1')"></span>
            <div>
                <h2 class="text-xl font-bold text-surface-900 dark:text-white" x-text="plan.name || 'Membership Plan'"></h2>
                <p class="mt-0.5 text-sm text-surface-500" x-text="plan.facility_name ? 'Facility: ' + plan.facility_name : ''"></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($backUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-soft">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <button @click="toggleStatus()" class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-medium transition-colors shadow-soft"
                :class="plan.is_active == 1 ? 'border-amber-300 text-amber-700 hover:bg-amber-50 dark:border-amber-700 dark:text-amber-400 dark:hover:bg-amber-900/20' : 'border-green-300 text-green-700 hover:bg-green-50 dark:border-green-700 dark:text-green-400 dark:hover:bg-green-900/20'">
                <span x-text="plan.is_active == 1 ? 'Deactivate' : 'Activate'"></span>
            </button>
            <a href="<?= htmlspecialchars($editUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-20 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
    </div>

    <!-- Content -->
    <div x-show="!loading" class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Left: Details + Benefits -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Plan Info -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Plan Information</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Plan Name</dt>
                            <dd class="mt-1 text-sm font-medium text-surface-800 dark:text-surface-100" x-text="plan.name || '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Status</dt>
                            <dd class="mt-1">
                                <span x-show="plan.is_active == 1" class="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400">Active</span>
                                <span x-show="plan.is_active != 1" class="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-400">Inactive</span>
                            </dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Price</dt>
                            <dd class="mt-1 text-sm font-bold text-surface-800 dark:text-surface-100" x-text="'$' + parseFloat(plan.price || 0).toFixed(2)"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Setup Fee</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="'$' + parseFloat(plan.setup_fee || 0).toFixed(2)"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Taxable</dt>
                            <dd class="mt-1 text-sm" x-text="plan.is_taxable == 1 ? 'Yes' : 'No'"></dd>
                        </div>
                    </div>
                    <div x-show="plan.renewal_type && plan.renewal_type !== 'none'" class="grid grid-cols-3 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Renewal Price</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="plan.renewal_price ? '$' + parseFloat(plan.renewal_price).toFixed(2) : 'Same as plan price'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800 col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Renewal Policy</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="plan.renewal_price_policy === 'locked_price' ? 'Locked — Existing members keep original signup price' : 'Current — Members charged latest plan price on renewal'"></dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Duration</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="durationLabel()"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Renewal</dt>
                            <dd class="mt-1 text-sm capitalize text-surface-700 dark:text-surface-300" x-text="plan.renewal_type || 'auto'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Max Members</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="plan.max_members || 'Unlimited'"></dd>
                        </div>
                    </div>
                    <div x-show="plan.description" class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Description</dt>
                        <dd class="mt-1 text-sm text-surface-600 dark:text-surface-400" x-text="plan.description"></dd>
                    </div>
                </div>
            </div>

            <!-- Category Benefits -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-green-500 to-green-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Category Benefits</h3>
                </div>
                <div class="p-6">
                    <template x-if="!plan.included_categories?.length && !plan.discounted_categories?.length">
                        <p class="text-sm text-surface-400 italic">No category benefits configured</p>
                    </template>
                    <div class="space-y-2">
                        <template x-for="c in plan.included_categories || []" :key="'inc-cat-'+c.category_id">
                            <div class="rounded-lg bg-green-50 dark:bg-green-500/5 px-4 py-2.5 border border-green-100 dark:border-green-800/30">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-green-800 dark:text-green-300" x-text="c.category_name || 'Category #' + c.category_id"></span>
                                    <span class="text-xs font-semibold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-500/10 rounded-full px-2 py-0.5">Included</span>
                                </div>
                                <div class="mt-1.5 flex flex-wrap gap-3 text-xs text-green-700 dark:text-green-400">
                                    <template x-if="c.price">
                                        <span class="bg-green-100 dark:bg-green-500/10 rounded px-1.5 py-0.5" x-text="'Price: $' + parseFloat(c.price).toFixed(2)"></span>
                                    </template>
                                    <template x-if="c.usage_limit">
                                        <span class="bg-green-100 dark:bg-green-500/10 rounded px-1.5 py-0.5" x-text="'Limit: ' + c.usage_limit + (c.usage_period && c.usage_period !== 'unlimited' ? ' / ' + c.usage_period : '')"></span>
                                    </template>
                                    <template x-if="!c.price && !c.usage_limit">
                                        <span class="text-green-600/60 dark:text-green-500/40">No restrictions</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-for="c in plan.discounted_categories || []" :key="'disc-cat-'+c.category_id">
                            <div class="flex items-center justify-between rounded-lg bg-amber-50 dark:bg-amber-500/5 px-4 py-2.5 border border-amber-100 dark:border-amber-800/30">
                                <span class="text-sm font-medium text-amber-800 dark:text-amber-300" x-text="c.category_name || 'Category #' + c.category_id"></span>
                                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/10 rounded-full px-2 py-0.5" x-text="(c.discount_percentage || 0) + '% off'"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Session Type Benefits -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Session Type Benefits</h3>
                </div>
                <div class="p-6">
                    <template x-if="!plan.included_session_types?.length && !plan.discounted_session_types?.length">
                        <p class="text-sm text-surface-400 italic">No session type benefits configured</p>
                    </template>
                    <div class="space-y-2">
                        <template x-for="s in plan.included_session_types || []" :key="'inc-st-'+s.session_type_id">
                            <div class="rounded-lg bg-green-50 dark:bg-green-500/5 px-4 py-2.5 border border-green-100 dark:border-green-800/30">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-green-800 dark:text-green-300" x-text="s.session_type_name || 'Session Type #' + s.session_type_id"></span>
                                    <span class="text-xs font-semibold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-500/10 rounded-full px-2 py-0.5">Included</span>
                                </div>
                                <div class="mt-1.5 flex flex-wrap gap-3 text-xs text-green-700 dark:text-green-400">
                                    <template x-if="s.price">
                                        <span class="bg-green-100 dark:bg-green-500/10 rounded px-1.5 py-0.5" x-text="'Member Price: $' + parseFloat(s.price).toFixed(2)"></span>
                                    </template>
                                    <template x-if="s.standard_price && !s.price">
                                        <span class="bg-green-100 dark:bg-green-500/10 rounded px-1.5 py-0.5" x-text="'Standard: $' + parseFloat(s.standard_price).toFixed(2)"></span>
                                    </template>
                                    <template x-if="s.usage_limit">
                                        <span class="bg-green-100 dark:bg-green-500/10 rounded px-1.5 py-0.5" x-text="'Limit: ' + s.usage_limit + (s.usage_period && s.usage_period !== 'unlimited' ? ' / ' + s.usage_period : '')"></span>
                                    </template>
                                    <template x-if="!s.price && !s.usage_limit">
                                        <span class="text-green-600/60 dark:text-green-500/40">No restrictions</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-for="s in plan.discounted_session_types || []" :key="'disc-st-'+s.session_type_id">
                            <div class="flex items-center justify-between rounded-lg bg-amber-50 dark:bg-amber-500/5 px-4 py-2.5 border border-amber-100 dark:border-amber-800/30">
                                <span class="text-sm font-medium text-amber-800 dark:text-amber-300" x-text="s.session_type_name || 'Session Type #' + s.session_type_id"></span>
                                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/10 rounded-full px-2 py-0.5" x-text="(s.discount_percentage || 0) + '% off'"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Summary Card + Active Members -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Quick Stats</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-surface-500">Active Members</span>
                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400" x-text="plan.active_members || '0'"></span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-surface-500">Revenue/mo</span>
                        <span class="text-sm font-semibold text-surface-800 dark:text-surface-100" x-text="'$' + ((parseFloat(plan.price || 0) * parseInt(plan.active_members || 0)) / (plan.duration_value || 1)).toFixed(2)"></span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-surface-500">Category Benefits</span>
                        <span class="text-sm font-medium" x-text="((plan.included_categories?.length || 0) + (plan.discounted_categories?.length || 0))"></span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-surface-500">Session Benefits</span>
                        <span class="text-sm font-medium" x-text="((plan.included_session_types?.length || 0) + (plan.discounted_session_types?.length || 0))"></span>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Timestamps</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-3">
                        <dt class="text-xs text-surface-400">Created</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300" x-text="plan.created_at ? new Date(plan.created_at).toLocaleString() : '—'"></dd>
                    </div>
                    <div class="px-6 py-3">
                        <dt class="text-xs text-surface-400">Updated</dt>
                        <dd class="text-sm text-surface-700 dark:text-surface-300" x-text="plan.updated_at ? new Date(plan.updated_at).toLocaleString() : '—'"></dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function membershipPlanShow() {
    return {
        plan: {},
        loading: true,
        async init() {
            try {
                const res = await authFetch(APP_BASE + '/api/membership-plans/<?= $id ?? '' ?>');
                const json = await res.json();
                if (json.data) this.plan = json.data;
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Failed to load plan', type: 'error' } }));
            }
            this.loading = false;
        },
        durationLabel() {
            const labels = { monthly: '1 Month', '3months': '3 Months', '6months': '6 Months', '12months': '12 Months', custom: (this.plan.duration_value || 1) + ' Months' };
            return labels[this.plan.duration_type] || this.plan.duration_type;
        },
        async toggleStatus() {
            try {
                const res = await authFetch(APP_BASE + '/api/membership-plans/<?= $id ?? '' ?>/toggle-status', { method: 'PATCH' });
                const json = await res.json();
                if (res.ok) {
                    this.plan.is_active = json.data.is_active ? 1 : 0;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Status updated', type: 'success' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Failed to update status', type: 'error' } }));
            }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
