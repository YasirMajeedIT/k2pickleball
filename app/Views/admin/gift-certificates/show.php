<?php
$title = 'Gift Certificate Details';
$breadcrumbs = [
    ['label' => 'Gift Certificates', 'url' => ($baseUrl ?? '') . '/admin/gift-certificates'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/admin/gift-certificates';
$editUrl = ($baseUrl ?? '') . '/admin/gift-certificates/' . ($id ?? '') . '/edit';
$apiUrl  = ($baseUrl ?? '') . '/api/gift-certificates/' . ($id ?? '');

ob_start();
?>
<div x-data="giftCertShow()" x-init="init()">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-surface-900 dark:text-white" x-text="item.certificate_name || 'Gift Certificate'"></h2>
            <p class="mt-0.5 text-sm text-surface-500 font-mono" x-text="item.code || ''"></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($backUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-soft">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <a href="<?= htmlspecialchars($editUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all">
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

        <!-- Left: Details -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Certificate Info -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Certificate Information</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Name</dt>
                            <dd class="mt-1 text-sm font-medium text-surface-800 dark:text-surface-100" x-text="item.certificate_name || '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Code</dt>
                            <dd class="mt-1 text-sm font-mono text-surface-800 dark:text-surface-100" x-text="item.code || '—'"></dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Original Value</dt>
                            <dd class="mt-1 text-sm font-semibold text-surface-800 dark:text-surface-100" x-text="'$' + parseFloat(item.original_value || 0).toFixed(2)"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Remaining</dt>
                            <dd class="mt-1 text-sm font-bold" :class="parseFloat(item.value||0) > 0 ? 'text-green-600 dark:text-green-400' : 'text-surface-400'" x-text="'$' + parseFloat(item.value || 0).toFixed(2)"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Paid</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="'$' + parseFloat(item.paid_amount || 0).toFixed(2)"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                      :class="{
                                          'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400': item.status === 'active',
                                          'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400': item.status === 'redeemed',
                                          'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400': item.status === 'expired',
                                      }"
                                      x-text="item.status || '—'"></span>
                            </dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Valid From</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="item.start_using_after ? new Date(item.start_using_after).toLocaleDateString() : 'Immediately'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Expires</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="item.expired_at ? new Date(item.expired_at).toLocaleString() : 'Never'"></dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buyer & Recipient -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Buyer -->
                <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3 class="font-semibold text-surface-800 dark:text-surface-100">Buyer</h3>
                    </div>
                    <div class="divide-y divide-surface-100 dark:divide-surface-800">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Name</dt>
                            <dd class="mt-1 text-sm text-surface-800 dark:text-surface-100" x-text="((item.buyer_first_name || '') + ' ' + (item.buyer_last_name || '')).trim() || '—'"></dd>
                        </div>
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Email</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="item.buyer_email || '—'"></dd>
                        </div>
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Phone</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="item.buyer_phone || '—'"></dd>
                        </div>
                    </div>
                </div>
                <!-- Recipient -->
                <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        </div>
                        <h3 class="font-semibold text-surface-800 dark:text-surface-100">Recipient</h3>
                    </div>
                    <div class="divide-y divide-surface-100 dark:divide-surface-800">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Name</dt>
                            <dd class="mt-1 text-sm text-surface-800 dark:text-surface-100" x-text="((item.recipient_first_name || '') + ' ' + (item.recipient_last_name || '')).trim() || '—'"></dd>
                        </div>
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Email</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="item.recipient_email || '—'"></dd>
                        </div>
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Phone</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="item.recipient_phone || '—'"></dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gift Message -->
            <template x-if="item.gift_message">
                <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-pink-500 to-pink-600 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <h3 class="font-semibold text-surface-800 dark:text-surface-100">Gift Message</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-surface-700 dark:text-surface-300 whitespace-pre-line italic" x-text="item.gift_message"></p>
                    </div>
                </div>
            </template>

            <!-- Usage History -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center justify-between border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h3 class="font-semibold text-surface-800 dark:text-surface-100">Usage History</h3>
                    </div>
                    <span class="text-xs font-medium text-surface-400" x-text="(usages.length || 0) + ' records'"></span>
                </div>
                <div x-show="usages.length === 0" class="px-6 py-8 text-center text-sm text-surface-400">
                    No usage records yet.
                </div>
                <div x-show="usages.length > 0" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-50/50 dark:bg-surface-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-400">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-400">Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-400">Remaining</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-400">Used By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-400">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                            <template x-for="u in usages" :key="u.id">
                                <tr class="hover:bg-surface-50/50 dark:hover:bg-surface-800/30">
                                    <td class="px-6 py-3 text-surface-700 dark:text-surface-300" x-text="u.usage_date ? new Date(u.usage_date).toLocaleString() : '—'"></td>
                                    <td class="px-6 py-3 text-right font-semibold text-red-600" x-text="'-$' + parseFloat(u.amount_used || 0).toFixed(2)"></td>
                                    <td class="px-6 py-3 text-right text-surface-700 dark:text-surface-300" x-text="'$' + parseFloat(u.remaining_balance || 0).toFixed(2)"></td>
                                    <td class="px-6 py-3 text-surface-700 dark:text-surface-300" x-text="u.used_by || '—'"></td>
                                    <td class="px-6 py-3 text-surface-500 text-xs" x-text="u.notes || ''"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="space-y-6">

            <!-- Record Usage -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Record Usage</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Amount ($)</label>
                        <input type="number" step="0.01" x-model="usageForm.amount_used"
                               class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3.5 py-2 text-sm text-surface-800 dark:text-surface-100 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Used By</label>
                        <input type="text" x-model="usageForm.used_by"
                               class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3.5 py-2 text-sm text-surface-800 dark:text-surface-100 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Notes</label>
                        <textarea x-model="usageForm.notes" rows="2"
                                  class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3.5 py-2 text-sm text-surface-800 dark:text-surface-100 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all"></textarea>
                    </div>
                    <button @click="recordUsage()" :disabled="usageSubmitting || !usageForm.amount_used"
                            class="w-full rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-2.5 text-sm font-semibold text-white hover:from-emerald-700 hover:to-emerald-800 shadow-soft transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!usageSubmitting">Record Usage</span>
                        <span x-show="usageSubmitting">Recording...</span>
                    </button>
                </div>
            </div>

            <!-- Summary -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Summary</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-surface-500">Used</span>
                        <span class="text-sm font-semibold text-surface-800 dark:text-surface-100" x-text="'$' + (parseFloat(item.original_value||0) - parseFloat(item.value||0)).toFixed(2)"></span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-surface-500">Remaining</span>
                        <span class="text-sm font-bold" :class="parseFloat(item.value||0) > 0 ? 'text-green-600 dark:text-green-400' : 'text-surface-400'" x-text="'$' + parseFloat(item.value||0).toFixed(2)"></span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-surface-500">Total Transactions</span>
                        <span class="text-sm font-semibold text-surface-800 dark:text-surface-100" x-text="usages.length"></span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-surface-500">Currency</span>
                        <span class="text-sm font-medium text-surface-800 dark:text-surface-100" x-text="item.currency || 'USD'"></span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <template x-if="item.notes">
                <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-surface-500 to-surface-600 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 class="font-semibold text-surface-800 dark:text-surface-100">Notes</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-surface-700 dark:text-surface-300 whitespace-pre-line" x-text="item.notes"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function giftCertShow() {
    return {
        item: {},
        usages: [],
        loading: true,
        usageForm: { amount_used: '', used_by: '', notes: '' },
        usageSubmitting: false,
        async init() {
            try {
                const res = await authFetch('<?= $apiUrl ?>');
                const json = await res.json();
                if (json.data) {
                    this.item = json.data;
                    this.usages = json.data.usages || [];
                }
            } catch (e) { console.error('Failed to load', e); }
            this.loading = false;
        },
        async recordUsage() {
            this.usageSubmitting = true;
            try {
                const res = await authFetch('<?= $apiUrl ?>/usages', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.usageForm)
                });
                const json = await res.json();
                if (res.ok) {
                    this.item = json.data;
                    this.usages = json.data.usages || [];
                    this.usageForm = { amount_used: '', used_by: '', notes: '' };
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Usage recorded', type: 'success' } }));
                } else {
                    const msg = json.errors?.amount_used?.[0] || json.message || 'Failed';
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: msg, type: 'error' } }));
                }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            this.usageSubmitting = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
