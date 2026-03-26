<?php
$invoiceId   = (int) ($params['id'] ?? 0);
$title       = 'Edit Invoice';
$breadcrumbs = [
    ['label' => 'Invoices', 'url' => ($baseUrl ?? '') . '/admin/invoices'],
    ['label' => 'Edit Invoice'],
];
$apiBase = $baseUrl ?? '';

ob_start();
?>
<div x-data="invoiceEditForm()" x-init="init()" class="mx-auto max-w-5xl space-y-6">

    <div x-show="loading" class="flex justify-center py-16">
        <svg class="h-8 w-8 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </div>

    <div x-show="notFound" class="rounded-2xl border border-red-200 bg-red-50 p-8 text-center dark:border-red-500/20 dark:bg-red-500/5">
        <p class="font-semibold text-red-600 dark:text-red-400">Invoice not found or cannot be edited.</p>
        <a href="<?= htmlspecialchars($apiBase) ?>/admin/invoices" class="mt-3 inline-block text-sm text-primary-600 hover:underline">← Back to Invoices</a>
    </div>

    <form x-show="!loading && !notFound" @submit.prevent="submitForm()">

        <!-- ─── Customer ─── -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden mb-6">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="text-base font-bold text-surface-900 dark:text-white">Customer Information</h3>
            </div>
            <div class="px-6 py-6 space-y-5">
                <div class="flex gap-3">
                    <label class="flex cursor-pointer items-center gap-2">
                        <input type="radio" x-model="form.customer_type" value="individual" class="h-4 w-4 text-primary-600">
                        <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Individual</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2">
                        <input type="radio" x-model="form.customer_type" value="company" class="h-4 w-4 text-primary-600">
                        <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Company</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div x-show="form.customer_type === 'individual'">
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">First Name</label>
                        <input type="text" x-model="form.customer_first_name"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div x-show="form.customer_type === 'individual'">
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Last Name</label>
                        <input type="text" x-model="form.customer_last_name"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div x-show="form.customer_type === 'company'" class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Company Name</label>
                        <input type="text" x-model="form.customer_company"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Email</label>
                        <input type="email" x-model="form.customer_email"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Phone</label>
                        <input type="text" x-model="form.customer_phone"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Invoice Details ─── -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden mb-6">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="text-base font-bold text-surface-900 dark:text-white">Invoice Details</h3>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Facility</label>
                        <select x-model="form.facility_id" @change="loadSessionTypes()"
                                class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                            <option value="">All Facilities</option>
                            <template x-for="f in facilities" :key="f.id">
                                <option :value="f.id" x-text="f.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Issue Date</label>
                        <input type="date" x-model="form.issue_date"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Due Date</label>
                        <input type="date" x-model="form.due_date"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Payment Type</label>
                        <select x-model="form.payment_type"
                                class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                            <option value="full">Full Payment</option>
                            <option value="partial">Partial Payments Allowed</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Tax Rate (%)</label>
                        <input type="number" x-model="form.tax_rate" step="0.01" min="0" max="100"
                               @input="calcTotals()"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Discount ($)</label>
                        <input type="number" x-model="form.discount_amount" step="0.01" min="0"
                               @input="calcTotals()"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Line Items (same as create) ─── -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden mb-6">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30 flex items-center justify-between">
                <h3 class="text-base font-bold text-surface-900 dark:text-white">Line Items</h3>
                <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3 py-1.5 text-sm font-semibold text-primary-700 hover:bg-primary-100 dark:bg-primary-500/10 dark:text-primary-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-3">
                    <template x-for="(item, idx) in form.items" :key="idx">
                        <div class="rounded-xl border border-surface-200 bg-surface-50/50 dark:border-surface-700 dark:bg-surface-800/30 p-3">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-start">
                                <div class="md:col-span-2">
                                    <select x-model="item.item_type" @change="onItemTypeChange(item)"
                                            class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                        <option value="custom">Custom</option>
                                        <option value="session_type">Session Type</option>
                                        <option value="class">Class</option>
                                    </select>
                                </div>
                                <div class="md:col-span-5 space-y-1.5">
                                    <div x-show="item.item_type === 'custom'">
                                        <input type="text" x-model="item.description" placeholder="Description…"
                                               class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                    </div>
                                    <div x-show="item.item_type === 'session_type'">
                                        <select x-model="item.session_type_id" @change="onSessionTypeChange(item)"
                                                class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                            <option value="">Select session type…</option>
                                            <template x-for="st in sessionTypes" :key="st.id">
                                                <option :value="st.id" x-text="st.title"></option>
                                            </template>
                                        </select>
                                        <input type="text" x-model="item.description" placeholder="Description"
                                               class="mt-1.5 w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                    </div>
                                    <div x-show="item.item_type === 'class'" class="space-y-1.5">
                                        <select x-model="item.session_type_id" @change="loadClassesForItem(item)"
                                                class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                            <option value="">Select session type…</option>
                                            <template x-for="st in sessionTypes" :key="st.id">
                                                <option :value="st.id" x-text="st.title"></option>
                                            </template>
                                        </select>
                                        <select x-model="item.class_id" @change="onClassChange(item)"
                                                :disabled="!item.session_type_id"
                                                class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 disabled:opacity-50 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                            <option value="">Select class…</option>
                                            <template x-for="c in (item._classes || [])" :key="c.id">
                                                <option :value="c.id" x-text="formatClassDate(c.scheduled_at)"></option>
                                            </template>
                                        </select>
                                        <input type="text" x-model="item.description" placeholder="Class description"
                                               class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <input type="number" x-model="item.quantity" min="0.01" step="0.01"
                                           @input="calcTotals()"
                                           class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm text-right focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                </div>
                                <div class="md:col-span-2">
                                    <div class="relative">
                                        <span class="absolute left-2.5 top-2 text-sm text-surface-400">$</span>
                                        <input type="number" x-model="item.unit_price" min="0" step="0.01"
                                               @input="calcTotals()"
                                               class="w-full rounded-lg border border-surface-200 bg-white pl-6 pr-2.5 py-2 text-sm text-right focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                    </div>
                                </div>
                                <div class="md:col-span-1 flex items-center justify-between md:justify-end gap-2">
                                    <span class="text-sm font-semibold text-surface-900 dark:text-white"
                                          x-text="'$' + (parseFloat(item.quantity||1) * parseFloat(item.unit_price||0)).toFixed(2)"></span>
                                    <button type="button" @click="removeItem(idx)"
                                            class="rounded-lg p-1 text-red-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 flex justify-end">
                    <div class="w-full max-w-xs space-y-1.5 rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800/30">
                        <div class="flex justify-between text-sm text-surface-600 dark:text-surface-400">
                            <span>Subtotal</span>
                            <span x-text="'$' + subtotal.toFixed(2)"></span>
                        </div>
                        <div x-show="parseFloat(form.tax_rate) > 0" class="flex justify-between text-sm text-surface-600 dark:text-surface-400">
                            <span>Tax (<span x-text="form.tax_rate"></span>%)</span>
                            <span x-text="'$' + taxAmount.toFixed(2)"></span>
                        </div>
                        <div x-show="parseFloat(form.discount_amount) > 0" class="flex justify-between text-sm text-accent-600">
                            <span>Discount</span>
                            <span x-text="'-$' + parseFloat(form.discount_amount||0).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between border-t border-surface-200 pt-2 text-base font-bold text-surface-900 dark:border-surface-700 dark:text-white">
                            <span>Total</span>
                            <span x-text="'$' + totalAmount.toFixed(2)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Notes ─── -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden mb-6">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="text-base font-bold text-surface-900 dark:text-white">Notes</h3>
            </div>
            <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Customer Notes</label>
                    <textarea x-model="form.notes" rows="3"
                              class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Internal Notes</label>
                    <textarea x-model="form.internal_notes" rows="3"
                              class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white"></textarea>
                </div>
            </div>
        </div>

        <!-- ─── Actions ─── -->
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <a :href="'<?= htmlspecialchars($apiBase) ?>/admin/invoices/' + invoiceId"
               class="inline-flex items-center justify-center rounded-xl border border-surface-200 px-5 py-2.5 text-sm font-semibold text-surface-700 hover:bg-surface-50 dark:border-surface-700 dark:text-surface-300">
                Cancel
            </a>
            <button type="submit" :disabled="saving"
                    class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50">
                <span x-show="!saving">Save Changes</span>
                <span x-show="saving">Saving…</span>
            </button>
        </div>

        <template x-if="errorMsg">
            <div class="mt-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400" x-text="errorMsg"></div>
        </template>

    </form>
</div>

<script>
const EDIT_INVOICE_ID = <?= (int) $invoiceId ?>;
const EDIT_API_BASE   = <?= json_encode($apiBase) ?>;

function invoiceEditForm() {
    return {
        loading: true,
        notFound: false,
        saving: false,
        errorMsg: '',
        invoiceId: EDIT_INVOICE_ID,
        facilities: [],
        sessionTypes: [],
        subtotal: 0,
        taxAmount: 0,
        totalAmount: 0,

        form: {
            customer_type: 'individual',
            customer_first_name: '',
            customer_last_name: '',
            customer_company: '',
            customer_email: '',
            customer_phone: '',
            facility_id: '',
            issue_date: '',
            due_date: '',
            payment_type: 'full',
            tax_rate: 0,
            discount_amount: 0,
            notes: '',
            internal_notes: '',
            items: [],
        },

        async init() {
            await this.loadFacilities();
            await this.loadInvoice();
        },

        async loadFacilities() {
            try {
                const res = await fetch(EDIT_API_BASE + '/api/facilities?per_page=100', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                this.facilities = json.data ?? [];
            } catch {}
        },

        async loadInvoice() {
            try {
                const res = await fetch(EDIT_API_BASE + '/api/booking-invoices/' + EDIT_INVOICE_ID, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                if (json.success) {
                    const inv = json.data;
                    if (['paid','refunded'].includes(inv.status)) { this.notFound = true; return; }
                    this.form = {
                        customer_type:        inv.customer_type,
                        customer_first_name:  inv.customer_first_name ?? '',
                        customer_last_name:   inv.customer_last_name  ?? '',
                        customer_company:     inv.customer_company     ?? '',
                        customer_email:       inv.customer_email       ?? '',
                        customer_phone:       inv.customer_phone       ?? '',
                        facility_id:          inv.facility_id          ?? '',
                        issue_date:           inv.issue_date           ?? '',
                        due_date:             inv.due_date             ?? '',
                        payment_type:         inv.payment_type         ?? 'full',
                        tax_rate:             parseFloat(inv.tax_rate || 0) * 100,
                        discount_amount:      parseFloat(inv.discount_amount || 0),
                        notes:                inv.notes                ?? '',
                        internal_notes:       inv.internal_notes       ?? '',
                        items: (inv.items ?? []).map(item => ({
                            ...item,
                            _classes: [],
                        })),
                    };
                    await this.loadSessionTypes();
                    this.calcTotals();
                } else {
                    this.notFound = true;
                }
            } finally {
                this.loading = false;
            }
        },

        async loadSessionTypes() {
            try {
                const q = this.form.facility_id ? '?facility_id=' + this.form.facility_id : '';
                const res = await fetch(EDIT_API_BASE + '/api/booking-invoices/session-types' + q, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                this.sessionTypes = json.data ?? [];
            } catch {}
        },

        addItem() {
            this.form.items.push({ item_type:'custom', session_type_id:'', class_id:'', description:'', quantity:1, unit_price:0, _classes:[] });
        },

        removeItem(idx) { this.form.items.splice(idx, 1); this.calcTotals(); },

        onItemTypeChange(item) { item.session_type_id=''; item.class_id=''; item._classes=[]; },

        onSessionTypeChange(item) {
            const st = this.sessionTypes.find(s => s.id == item.session_type_id);
            if (st) { item.description = st.title; item.unit_price = parseFloat(st.standard_price ?? 0); this.calcTotals(); }
        },

        async loadClassesForItem(item) {
            item._classes = []; item.class_id = '';
            if (!item.session_type_id) return;
            try {
                const res = await fetch(EDIT_API_BASE + '/api/session-types/' + item.session_type_id + '/classes', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                item._classes = json.data ?? [];
                const st = this.sessionTypes.find(s => s.id == item.session_type_id);
                if (st) item.description = st.title;
            } catch {}
        },

        onClassChange(item) {
            const cls = (item._classes||[]).find(c => c.id == item.class_id);
            if (cls) {
                const st = this.sessionTypes.find(s => s.id == item.session_type_id);
                item.description = (st?.title ?? '') + ' — ' + this.formatClassDate(cls.scheduled_at);
            }
        },

        calcTotals() {
            let sub = 0;
            for (const item of this.form.items) sub += (parseFloat(item.quantity)||1) * (parseFloat(item.unit_price)||0);
            this.subtotal    = sub;
            this.taxAmount   = sub * (parseFloat(this.form.tax_rate)||0) / 100;
            this.totalAmount = Math.max(0, sub + this.taxAmount - (parseFloat(this.form.discount_amount)||0));
        },

        formatClassDate(dt) { try { return dt ? new Date(dt).toLocaleString(undefined, { dateStyle:'medium', timeStyle:'short' }) : '—'; } catch { return dt; } },

        async submitForm() {
            this.errorMsg = '';
            this.saving   = true;
            try {
                const payload = {
                    ...this.form,
                    tax_rate: parseFloat(this.form.tax_rate) || 0,
                    discount_amount: parseFloat(this.form.discount_amount) || 0,
                    items: this.form.items.map(({ _classes, ...item }) => ({
                        ...item,
                        quantity:  parseFloat(item.quantity)  || 1,
                        unit_price: parseFloat(item.unit_price) || 0,
                        session_type_id: item.session_type_id || null,
                        class_id: item.class_id || null,
                    })),
                };
                const res = await fetch(EDIT_API_BASE + '/api/booking-invoices/' + EDIT_INVOICE_ID, {
                    method: 'PUT',
                    headers: { 'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();
                if (res.ok && json.success) {
                    window.location.href = EDIT_API_BASE + '/admin/invoices/' + EDIT_INVOICE_ID;
                } else {
                    this.errorMsg = json.message || 'Failed to save changes.';
                }
            } catch (err) {
                this.errorMsg = 'Network error: ' + err.message;
            } finally {
                this.saving = false;
            }
        },
    };
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
