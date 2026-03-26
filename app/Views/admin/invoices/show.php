<?php
$invoiceId = (int) ($params['id'] ?? 0);
$apiBase   = $baseUrl ?? '';

// Get Square config for Web Payments SDK
$squareCfg    = \App\Core\Services\Config::get('payments.square', []);
$squareEnv    = $squareCfg['environment'] ?? 'sandbox';
$squareAppId  = $squareCfg['application_id'] ?? '';
$squareLocId  = $squareCfg['location_id'] ?? '';
$squareJsUrl  = $squareEnv === 'production'
    ? 'https://web.squarecdn.com/v1/square.js'
    : 'https://sandbox.web.squarecdn.com/v1/square.js';

$title       = 'Invoice';
$breadcrumbs = [['label' => 'Invoices', 'url' => $apiBase . '/admin/invoices'], ['label' => 'View Invoice']];

ob_start();
?>
<!-- Square Web Payments SDK -->
<script src="<?= htmlspecialchars($squareJsUrl) ?>"></script>

<div x-data="invoiceShow()" x-init="init()" class="mx-auto max-w-5xl space-y-6">

    <!-- Loading -->
    <div x-show="loading" class="flex justify-center py-16">
        <svg class="h-8 w-8 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </div>

    <template x-if="!loading && invoice">

        <div class="space-y-6">

            <!-- Header row -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold tracking-tight text-surface-900 dark:text-white" x-text="invoice.invoice_number"></h2>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                              :class="statusClass(invoice.status)" x-text="statusLabel(invoice.status)"></span>
                    </div>
                    <p class="mt-1 text-sm text-surface-500">
                        Issued: <span x-text="fmtDate(invoice.issue_date)"></span>
                        <template x-if="invoice.due_date">
                            &nbsp;·&nbsp; Due: <span :class="isPastDue ? 'text-red-500 font-semibold' : ''" x-text="fmtDate(invoice.due_date)"></span>
                        </template>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button @click="sendInvoice()" x-show="invoice.customer_email"
                            :disabled="actionLoading"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100 disabled:opacity-50 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span x-text="invoice.status === 'draft' ? 'Send Invoice' : 'Resend'"></span>
                    </button>
                    <a x-show="!['paid','refunded'].includes(invoice.status)"
                       :href="'<?= htmlspecialchars($apiBase) ?>/admin/invoices/' + invoice.id + '/edit'"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <a href="<?= htmlspecialchars($apiBase) ?>/admin/invoices"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-surface-200 px-3 py-2 text-sm font-semibold text-surface-600 hover:bg-surface-50 dark:border-surface-700 dark:text-surface-400">
                        ← Back
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left: Invoice card -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Customer + Invoice meta -->
                    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                        <div class="grid grid-cols-2 gap-0 divide-x divide-surface-100 dark:divide-surface-800">
                            <div class="p-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-surface-400 mb-2">Bill To</p>
                                <template x-if="invoice.customer_type === 'company'">
                                    <p class="font-bold text-surface-900 dark:text-white" x-text="invoice.customer_company"></p>
                                </template>
                                <template x-if="invoice.customer_type !== 'company'">
                                    <p class="font-bold text-surface-900 dark:text-white"
                                       x-text="(invoice.customer_first_name ?? '') + ' ' + (invoice.customer_last_name ?? '')"></p>
                                </template>
                                <p class="text-sm text-surface-500 mt-0.5" x-text="invoice.customer_email"></p>
                                <p class="text-sm text-surface-500" x-text="invoice.customer_phone"></p>
                            </div>
                            <div class="p-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-surface-400 mb-2">Invoice Info</p>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-surface-500">Number</span>
                                        <span class="font-mono font-semibold text-surface-900 dark:text-white" x-text="invoice.invoice_number"></span>
                                    </div>
                                    <div class="flex justify-between" x-show="invoice.facility_name">
                                        <span class="text-surface-500">Facility</span>
                                        <span class="text-surface-700 dark:text-surface-300" x-text="invoice.facility_name"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-surface-500">Payment</span>
                                        <span class="text-surface-700 dark:text-surface-300" x-text="invoice.payment_type === 'partial' ? 'Partial allowed' : 'Full'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                            <h3 class="text-base font-bold text-surface-900 dark:text-white">Line Items</h3>
                        </div>
                        <table class="w-full text-sm">
                            <thead class="border-b border-surface-100 dark:border-surface-800">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-400">Description</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-surface-400">Qty</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-surface-400">Unit Price</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-surface-400">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                                <template x-for="item in invoice.items" :key="item.id">
                                    <tr>
                                        <td class="px-5 py-3">
                                            <div class="font-medium text-surface-900 dark:text-white" x-text="item.description"></div>
                                            <div class="text-xs text-surface-400 mt-0.5"
                                                 x-show="item.item_type !== 'custom'"
                                                 x-text="item.item_type === 'session_type' ? 'Session Type' : 'Class'"></div>
                                        </td>
                                        <td class="px-5 py-3 text-center text-surface-600" x-text="item.quantity"></td>
                                        <td class="px-5 py-3 text-right text-surface-600" x-text="'$' + parseFloat(item.unit_price).toFixed(2)"></td>
                                        <td class="px-5 py-3 text-right font-semibold text-surface-900 dark:text-white" x-text="'$' + parseFloat(item.total).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-surface-50/50 dark:bg-surface-800/10">
                                <tr x-show="parseFloat(invoice.tax_amount) > 0">
                                    <td colspan="3" class="px-5 py-2 text-right text-sm text-surface-500">Tax (<span x-text="parseFloat(invoice.tax_rate*100).toFixed(1)"></span>%)</td>
                                    <td class="px-5 py-2 text-right text-sm" x-text="'$' + parseFloat(invoice.tax_amount).toFixed(2)"></td>
                                </tr>
                                <tr x-show="parseFloat(invoice.discount_amount) > 0">
                                    <td colspan="3" class="px-5 py-2 text-right text-sm text-surface-500">Discount</td>
                                    <td class="px-5 py-2 text-right text-sm text-accent-600" x-text="'-$' + parseFloat(invoice.discount_amount).toFixed(2)"></td>
                                </tr>
                                <tr class="border-t border-surface-200 dark:border-surface-700">
                                    <td colspan="3" class="px-5 py-3 text-right font-bold text-surface-900 dark:text-white">Total</td>
                                    <td class="px-5 py-3 text-right font-bold text-lg text-primary-600" x-text="'$' + parseFloat(invoice.total).toFixed(2)"></td>
                                </tr>
                                <tr x-show="parseFloat(invoice.amount_paid) > 0">
                                    <td colspan="3" class="px-5 py-1.5 text-right text-sm text-surface-500">Paid</td>
                                    <td class="px-5 py-1.5 text-right text-sm text-accent-600 font-semibold" x-text="'-$' + parseFloat(invoice.amount_paid).toFixed(2)"></td>
                                </tr>
                                <tr x-show="parseFloat(invoice.amount_due) > 0" class="bg-amber-50/50 dark:bg-amber-500/5">
                                    <td colspan="3" class="px-5 py-3 text-right font-bold text-amber-700 dark:text-amber-400">Balance Due</td>
                                    <td class="px-5 py-3 text-right font-bold text-lg text-amber-600 dark:text-amber-400" x-text="'$' + parseFloat(invoice.amount_due).toFixed(2)"></td>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- Notes -->
                        <div x-show="invoice.notes" class="border-t border-surface-100 dark:border-surface-800 px-5 py-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-surface-400 mb-1">Notes</p>
                            <p class="text-sm text-surface-600 dark:text-surface-400" x-text="invoice.notes"></p>
                        </div>
                    </div>

                    <!-- Payment History -->
                    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                            <h3 class="text-base font-bold text-surface-900 dark:text-white">Payment History</h3>
                        </div>
                        <div x-show="invoice.payments.length === 0" class="px-6 py-8 text-center text-sm text-surface-400">
                            No payments recorded yet.
                        </div>
                        <table x-show="invoice.payments.length > 0" class="w-full text-sm">
                            <thead class="border-b border-surface-100 dark:border-surface-800">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-400">Date</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-400">Method</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-surface-400">Amount</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-400">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-400 hidden sm:table-cell">Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                                <template x-for="pmt in invoice.payments" :key="pmt.id">
                                    <tr class="hover:bg-surface-50/50 dark:hover:bg-surface-800/20">
                                        <td class="px-5 py-3 text-surface-600 dark:text-surface-400" x-text="fmtDateTime(pmt.processed_at || pmt.created_at)"></td>
                                        <td class="px-5 py-3">
                                            <span class="capitalize font-medium text-surface-700 dark:text-surface-300" x-text="pmt.payment_method?.replace('_',' ')"></span>
                                        </td>
                                        <td class="px-5 py-3 text-right font-semibold text-accent-600 dark:text-accent-400"
                                            x-text="'$' + parseFloat(pmt.amount).toFixed(2)"></td>
                                        <td class="px-5 py-3">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold"
                                                  :class="pmt.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                                  x-text="pmt.status"></span>
                                        </td>
                                        <td class="px-5 py-3 hidden sm:table-cell">
                                            <a x-show="pmt.square_receipt_url" :href="pmt.square_receipt_url" target="_blank"
                                               class="text-xs text-primary-600 hover:underline">View Receipt</a>
                                            <span x-show="!pmt.square_receipt_url" class="text-xs text-surface-400">—</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right: Payment panel -->
                <div class="space-y-4">

                    <!-- Balance summary -->
                    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft p-5">
                        <p class="text-sm font-semibold text-surface-500 uppercase tracking-wide mb-3">Balance</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-surface-500">Invoice Total</span>
                                <span class="font-semibold text-surface-900 dark:text-white" x-text="'$' + parseFloat(invoice.total||0).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between" x-show="parseFloat(invoice.amount_paid) > 0">
                                <span class="text-surface-500">Paid</span>
                                <span class="font-semibold text-accent-600" x-text="'$' + parseFloat(invoice.amount_paid||0).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between border-t border-surface-100 pt-2 dark:border-surface-800">
                                <span class="font-bold text-surface-900 dark:text-white">Amount Due</span>
                                <span class="font-bold text-lg text-amber-600 dark:text-amber-400" x-text="'$' + parseFloat(invoice.amount_due||0).toFixed(2)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Record Payment panel (hidden when fully paid or cancelled) -->
                    <div x-show="!['paid','cancelled','refunded'].includes(invoice.status)"
                         class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                        <div class="border-b border-surface-100 dark:border-surface-800 px-5 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                            <h3 class="text-base font-bold text-surface-900 dark:text-white">Record Payment</h3>
                        </div>
                        <div class="p-5 space-y-4">

                            <!-- Payment method tabs -->
                            <div class="flex rounded-xl border border-surface-200 overflow-hidden dark:border-surface-700">
                                <button type="button" @click="paymentTab = 'card'"
                                        :class="paymentTab === 'card' ? 'bg-primary-600 text-white' : 'bg-white text-surface-600 hover:bg-surface-50 dark:bg-surface-900 dark:text-surface-400'"
                                        class="flex-1 py-2 text-xs font-semibold transition-colors">Card</button>
                                <button type="button" @click="paymentTab = 'manual'"
                                        :class="paymentTab === 'manual' ? 'bg-primary-600 text-white' : 'bg-white text-surface-600 hover:bg-surface-50 dark:bg-surface-900 dark:text-surface-400'"
                                        class="flex-1 py-2 text-xs font-semibold transition-colors border-l border-surface-200 dark:border-surface-700">Manual</button>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-surface-600 dark:text-surface-400">Amount <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-sm text-surface-400">$</span>
                                    <input type="number" x-model="payAmount" step="0.01" min="0.01"
                                           :max="parseFloat(invoice.amount_due)"
                                           :placeholder="parseFloat(invoice.amount_due).toFixed(2)"
                                           class="w-full rounded-xl border border-surface-200 bg-white pl-7 pr-3 py-2.5 text-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                </div>
                                <template x-if="invoice.payment_type === 'partial'">
                                    <p class="mt-1 text-xs text-surface-400">Partial payments allowed for this invoice.</p>
                                </template>
                            </div>

                            <!-- CARD: Square Web Payments form -->
                            <div x-show="paymentTab === 'card'" class="space-y-3">
                                <p class="text-xs text-surface-400">Enter card details securely via Square.</p>
                                <div id="card-container" class="rounded-xl border border-surface-200 p-3 bg-surface-50 dark:border-surface-700 dark:bg-surface-800 min-h-[60px]"></div>
                                <div x-show="squareError" class="text-xs text-red-500" x-text="squareError"></div>
                                <button type="button" @click="payWithCard()"
                                        :disabled="payLoading || !squareCard || !payAmount"
                                        class="w-full rounded-xl bg-primary-600 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50">
                                    <span x-show="!payLoading">Charge Card</span>
                                    <span x-show="payLoading">Processing…</span>
                                </button>
                            </div>

                            <!-- MANUAL: Cash / Check / etc -->
                            <div x-show="paymentTab === 'manual'" class="space-y-3">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-surface-600 dark:text-surface-400">Payment Method</label>
                                    <select x-model="manualMethod"
                                            class="w-full rounded-xl border border-surface-200 bg-white px-3 py-2.5 text-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-surface-600 dark:text-surface-400">Notes</label>
                                    <input type="text" x-model="payNotes" placeholder="Reference #, notes…"
                                           class="w-full rounded-xl border border-surface-200 bg-white px-3 py-2.5 text-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                </div>
                                <button type="button" @click="recordManualPayment()"
                                        :disabled="payLoading || !payAmount"
                                        class="w-full rounded-xl bg-accent-600 py-2.5 text-sm font-semibold text-white hover:bg-accent-700 disabled:opacity-50">
                                    <span x-show="!payLoading">Record Payment</span>
                                    <span x-show="payLoading">Saving…</span>
                                </button>
                            </div>

                            <div x-show="paySuccess" class="rounded-xl bg-green-50 border border-green-200 px-3 py-2 text-sm text-green-700 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400">
                                Payment recorded successfully!
                            </div>
                            <div x-show="payError" class="rounded-xl bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400" x-text="payError"></div>
                        </div>
                    </div>

                    <!-- Internal Notes -->
                    <div x-show="invoice.internal_notes"
                         class="rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-500/20 dark:bg-amber-500/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400 mb-1">Internal Notes</p>
                        <p class="text-sm text-amber-800 dark:text-amber-300" x-text="invoice.internal_notes"></p>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>

<script>
const SQUARE_APP_ID  = <?= json_encode($squareAppId) ?>;
const SQUARE_LOC_ID  = <?= json_encode($squareLocId) ?>;
const INVOICE_ID     = <?= (int) $invoiceId ?>;
const API_BASE       = <?= json_encode($apiBase) ?>;

function invoiceShow() {
    return {
        loading: true,
        invoice: null,
        actionLoading: false,
        paymentTab: 'card',
        payAmount: '',
        manualMethod: 'cash',
        payNotes: '',
        payLoading: false,
        payError: '',
        paySuccess: false,
        squareCard: null,
        squareError: '',

        get isPastDue() {
            return this.invoice?.due_date
                && !['paid','cancelled','refunded'].includes(this.invoice.status)
                && new Date(this.invoice.due_date) < new Date();
        },

        async init() {
            await this.loadInvoice();
            this.$nextTick(() => this.initSquare());
        },

        async loadInvoice() {
            this.loading = true;
            try {
                const res = await fetch(API_BASE + '/api/booking-invoices/' + INVOICE_ID, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                if (json.success) {
                    this.invoice  = json.data;
                    this.payAmount = parseFloat(json.data.amount_due || 0).toFixed(2);
                } else {
                    alert('Invoice not found.');
                }
            } finally {
                this.loading = false;
            }
        },

        async initSquare() {
            if (!window.Square || !SQUARE_APP_ID || !SQUARE_LOC_ID) return;
            try {
                const payments = Square.payments(SQUARE_APP_ID, SQUARE_LOC_ID);
                this.squareCard = await payments.card();
                await this.squareCard.attach('#card-container');
            } catch (e) {
                this.squareError = 'Square initialization failed: ' + e.message;
            }
        },

        async payWithCard() {
            this.payError   = '';
            this.paySuccess = false;
            this.payLoading = true;
            try {
                if (!this.squareCard) { this.payError = 'Card form not ready.'; return; }
                const result = await this.squareCard.tokenize();
                if (result.status !== 'OK') {
                    this.payError = (result.errors ?? []).map(e => e.message).join(', ') || 'Card tokenization failed.';
                    return;
                }
                await this.submitPayment({ payment_method: 'card', source_id: result.token });
            } finally {
                this.payLoading = false;
            }
        },

        async recordManualPayment() {
            this.payError   = '';
            this.paySuccess = false;
            this.payLoading = true;
            try {
                await this.submitPayment({ payment_method: this.manualMethod, notes: this.payNotes });
            } finally {
                this.payLoading = false;
            }
        },

        async submitPayment(extra) {
            const res = await fetch(API_BASE + '/api/booking-invoices/' + INVOICE_ID + '/pay', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ amount: parseFloat(this.payAmount), ...extra }),
            });
            const json = await res.json();
            if (res.ok && json.success) {
                this.invoice    = json.data;
                this.payAmount  = parseFloat(json.data.amount_due || 0).toFixed(2);
                this.paySuccess = true;
                this.payNotes   = '';
                // Re-attach card form for next payment if still due
                if (parseFloat(json.data.amount_due) > 0 && window.Square) {
                    this.$nextTick(() => this.initSquare());
                }
            } else {
                this.payError = json.message || 'Payment failed.';
            }
        },

        async sendInvoice() {
            if (!confirm('Send invoice by email to ' + this.invoice.customer_email + '?')) return;
            this.actionLoading = true;
            try {
                const res = await fetch(API_BASE + '/api/booking-invoices/' + INVOICE_ID + '/send', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();
                if (json.success) {
                    if (this.invoice.status === 'draft') this.invoice.status = 'sent';
                    alert('Invoice sent successfully!');
                } else {
                    alert(json.message || 'Failed to send invoice.');
                }
            } finally {
                this.actionLoading = false;
            }
        },

        fmtDate(d) { try { return d ? new Date(d).toLocaleDateString(undefined, { year:'numeric', month:'short', day:'numeric' }) : '—'; } catch { return d; } },
        fmtDateTime(d) { try { return d ? new Date(d).toLocaleString(undefined, { dateStyle:'medium', timeStyle:'short' }) : '—'; } catch { return d; } },

        statusLabel(s) {
            return { draft:'Draft', sent:'Sent', partially_paid:'Partial', paid:'Paid', overdue:'Overdue', cancelled:'Cancelled', refunded:'Refunded' }[s] ?? s;
        },
        statusClass(s) {
            return {
                draft:          'bg-surface-100 text-surface-600',
                sent:           'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                partially_paid: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                paid:           'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                overdue:        'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                cancelled:      'bg-surface-100 text-surface-500',
                refunded:       'bg-purple-100 text-purple-700',
            }[s] ?? 'bg-surface-100 text-surface-600';
        },
    };
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
