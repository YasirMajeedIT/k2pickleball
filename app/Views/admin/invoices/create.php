<?php
$title       = 'Create Invoice';
$breadcrumbs = [['label' => 'Invoices', 'url' => ($baseUrl ?? '') . '/admin/invoices'], ['label' => 'Create']];
$apiBase     = $baseUrl ?? '';

ob_start();
?>
<div x-data="invoiceForm()" x-init="init()" class="mx-auto max-w-5xl space-y-6">
<form @submit.prevent="submitForm()">

    <!-- ─── Customer ─── -->
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
            <h3 class="text-base font-bold text-surface-900 dark:text-white">Customer Information</h3>
        </div>
        <div class="px-6 py-6 space-y-5">
            <!-- Type toggle -->
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
                    <input type="text" x-model="form.customer_first_name" placeholder="First name"
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                </div>
                <div x-show="form.customer_type === 'individual'">
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Last Name</label>
                    <input type="text" x-model="form.customer_last_name" placeholder="Last name"
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                </div>
                <div x-show="form.customer_type === 'company'" class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Company Name</label>
                    <input type="text" x-model="form.customer_company" placeholder="Company name"
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Email</label>
                    <input type="email" x-model="form.customer_email" placeholder="email@example.com"
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Phone</label>
                    <input type="text" x-model="form.customer_phone" placeholder="(555) 000-0000"
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                </div>
            </div>
        </div>
    </div>

    <!-- ─── Invoice Details ─── -->
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
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
                    <input type="number" x-model="form.tax_rate" step="0.01" min="0" max="100" placeholder="0.00"
                           @input="calcTotals()"
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Discount ($)</label>
                    <input type="number" x-model="form.discount_amount" step="0.01" min="0" placeholder="0.00"
                           @input="calcTotals()"
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                </div>
            </div>
        </div>
    </div>

    <!-- ─── Line Items ─── -->
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30 flex items-center justify-between">
            <h3 class="text-base font-bold text-surface-900 dark:text-white">Line Items</h3>
            <button type="button" @click="addItem()"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3 py-1.5 text-sm font-semibold text-primary-700 hover:bg-primary-100 dark:bg-primary-500/10 dark:text-primary-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Item
            </button>
        </div>
        <div class="px-6 py-4">
            <!-- Column headers (desktop) -->
            <div class="mb-2 hidden grid-cols-12 gap-2 text-xs font-semibold uppercase tracking-wide text-surface-400 md:grid">
                <div class="col-span-2">Type</div>
                <div class="col-span-5">Description</div>
                <div class="col-span-2 text-right">Qty</div>
                <div class="col-span-2 text-right">Unit Price</div>
                <div class="col-span-1 text-right">Total</div>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in form.items" :key="idx">
                    <div class="rounded-xl border border-surface-200 bg-surface-50/50 dark:border-surface-700 dark:bg-surface-800/30 p-3">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-start">
                            <!-- Item Type -->
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-surface-500 md:hidden">Type</label>
                                <select x-model="item.item_type" @change="onItemTypeChange(item)"
                                        class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                    <option value="custom">Custom</option>
                                    <option value="session_type">Session Type</option>
                                    <option value="class">Class</option>
                                </select>
                            </div>

                            <!-- Description / Session Type / Class picker -->
                            <div class="md:col-span-5 space-y-1.5">
                                <!-- Custom: free text description -->
                                <div x-show="item.item_type === 'custom'">
                                    <label class="mb-1 block text-xs font-semibold text-surface-500 md:hidden">Description</label>
                                    <input type="text" x-model="item.description" placeholder="Description…"
                                           class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                </div>

                                <!-- Session Type: dropdown + auto-fill description -->
                                <div x-show="item.item_type === 'session_type'">
                                    <label class="mb-1 block text-xs font-semibold text-surface-500 md:hidden">Session Type</label>
                                    <select x-model="item.session_type_id" @change="onSessionTypeChange(item)"
                                            class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                        <option value="">Select session type…</option>
                                        <template x-for="st in sessionTypes" :key="st.id">
                                            <option :value="st.id" x-text="st.title"></option>
                                        </template>
                                    </select>
                                    <input type="text" x-model="item.description" placeholder="Description (auto-filled)"
                                           class="mt-1.5 w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                </div>

                                <!-- Class: session type then class picker -->
                                <div x-show="item.item_type === 'class'" class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-surface-500 md:hidden">Session Type</label>
                                    <select x-model="item.session_type_id" @change="loadClassesForItem(item)"
                                            class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                        <option value="">Select session type…</option>
                                        <template x-for="st in sessionTypes" :key="st.id">
                                            <option :value="st.id" x-text="st.title"></option>
                                        </template>
                                    </select>
                                    <select x-model="item.class_id" @change="onClassChange(item)"
                                            :disabled="!item.session_type_id || !item._classes?.length"
                                            class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 disabled:opacity-50 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                        <option value="">Select class…</option>
                                        <template x-for="c in (item._classes || [])" :key="c.id">
                                            <option :value="c.id" x-text="formatClassDate(c.scheduled_at)"></option>
                                        </template>
                                    </select>
                                    <input type="text" x-model="item.description" placeholder="Class description…"
                                           class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                </div>
                            </div>

                            <!-- Qty -->
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-surface-500 md:hidden">Qty</label>
                                <input type="number" x-model="item.quantity" min="0.01" step="0.01" placeholder="1"
                                       @input="calcItemTotal(item); calcTotals()"
                                       class="w-full rounded-lg border border-surface-200 bg-white px-2.5 py-2 text-sm text-right focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                            </div>

                            <!-- Unit Price -->
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-surface-500 md:hidden">Unit Price</label>
                                <div class="relative">
                                    <span class="absolute left-2.5 top-2 text-sm text-surface-400">$</span>
                                    <input type="number" x-model="item.unit_price" min="0" step="0.01" placeholder="0.00"
                                           @input="calcItemTotal(item); calcTotals()"
                                           class="w-full rounded-lg border border-surface-200 bg-white pl-6 pr-2.5 py-2 text-sm text-right focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                                </div>
                            </div>

                            <!-- Total + Remove -->
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

                <div x-show="form.items.length === 0" class="rounded-xl border-2 border-dashed border-surface-200 py-8 text-center dark:border-surface-700">
                    <p class="text-sm text-surface-400">No items yet. Click "Add Item" to begin.</p>
                </div>
            </div>

            <!-- Totals -->
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
                    <div x-show="parseFloat(form.discount_amount) > 0" class="flex justify-between text-sm text-accent-600 dark:text-accent-400">
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
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
            <h3 class="text-base font-bold text-surface-900 dark:text-white">Notes</h3>
        </div>
        <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Customer Notes <span class="text-surface-400 font-normal">(visible on invoice)</span></label>
                <textarea x-model="form.notes" rows="3" placeholder="Payment instructions, thank you note…"
                          class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white"></textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Internal Notes <span class="text-surface-400 font-normal">(staff only)</span></label>
                <textarea x-model="form.internal_notes" rows="3" placeholder="Internal reference notes…"
                          class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white"></textarea>
            </div>
        </div>
    </div>

    <!-- ─── Submit ─── -->
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
        <a href="<?= htmlspecialchars($apiBase) ?>/admin/invoices"
           class="inline-flex items-center justify-center rounded-xl border border-surface-200 px-5 py-2.5 text-sm font-semibold text-surface-700 hover:bg-surface-50 dark:border-surface-700 dark:text-surface-300">
            Cancel
        </a>
        <button type="submit" @click="form.send_immediately = false" :disabled="saving"
                class="inline-flex items-center justify-center rounded-xl border border-primary-500 px-5 py-2.5 text-sm font-semibold text-primary-600 hover:bg-primary-50 disabled:opacity-50 dark:border-primary-500 dark:text-primary-400">
            <span x-show="!saving">Save as Draft</span>
            <span x-show="saving">Saving…</span>
        </button>
        <button type="submit" @click="form.send_immediately = true" :disabled="saving"
                class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50">
            <span x-show="!saving">Send Invoice</span>
            <span x-show="saving">Sending…</span>
        </button>
    </div>

    <template x-if="errorMsg">
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400" x-text="errorMsg"></div>
    </template>

</form>
</div>

<script>
function invoiceForm() {
    return {
        saving: false,
        errorMsg: '',
        facilities: [],
        sessionTypes: [],
        subtotal: 0,
        taxAmount: 0,
        totalAmount: 0,
        apiBase: '<?= htmlspecialchars($apiBase) ?>',

        form: {
            customer_type: 'individual',
            customer_first_name: '',
            customer_last_name: '',
            customer_company: '',
            customer_email: '',
            customer_phone: '',
            facility_id: '',
            issue_date: new Date().toISOString().split('T')[0],
            due_date: '',
            payment_type: 'full',
            tax_rate: 0,
            discount_amount: 0,
            notes: '',
            internal_notes: '',
            items: [],
            send_immediately: false,
        },

        async init() {
            await this.loadFacilities();
            await this.loadSessionTypes();
        },

        async loadFacilities() {
            try {
                const res = await fetch(this.apiBase + '/api/facilities?per_page=100', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                this.facilities = json.data ?? [];
            } catch {}
        },

        async loadSessionTypes() {
            try {
                const q = this.form.facility_id ? '?facility_id=' + this.form.facility_id : '';
                const res = await fetch(this.apiBase + '/api/booking-invoices/session-types' + q, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                this.sessionTypes = json.data ?? [];
            } catch {}
        },

        addItem() {
            this.form.items.push({
                item_type: 'custom',
                session_type_id: '',
                class_id: '',
                description: '',
                quantity: 1,
                unit_price: 0,
                _classes: [],
            });
        },

        removeItem(idx) {
            this.form.items.splice(idx, 1);
            this.calcTotals();
        },

        onItemTypeChange(item) {
            item.session_type_id = '';
            item.class_id = '';
            item._classes = [];
            if (item.item_type !== 'custom') item.description = '';
        },

        onSessionTypeChange(item) {
            const st = this.sessionTypes.find(s => s.id == item.session_type_id);
            if (st) {
                item.description = st.title;
                if (!item.unit_price || item.unit_price == 0) {
                    item.unit_price = parseFloat(st.standard_price ?? 0);
                }
                this.calcItemTotal(item);
                this.calcTotals();
            }
        },

        async loadClassesForItem(item) {
            item._classes = [];
            item.class_id = '';
            if (!item.session_type_id) return;
            try {
                const res = await fetch(this.apiBase + '/api/booking-invoices/' + item.session_type_id + '/session-type/0/classes', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                // Actually use the classes endpoint on session_types
                const res2 = await fetch(this.apiBase + '/api/session-types/' + item.session_type_id + '/classes', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res2.json();
                item._classes = json.data ?? [];
                // Auto-fill description from session type
                const st = this.sessionTypes.find(s => s.id == item.session_type_id);
                if (st) item.description = st.title;
            } catch {}
        },

        onClassChange(item) {
            const cls = (item._classes || []).find(c => c.id == item.class_id);
            if (cls) {
                const st = this.sessionTypes.find(s => s.id == item.session_type_id);
                item.description = (st?.title ?? '') + ' — ' + this.formatClassDate(cls.scheduled_at);
            }
        },

        calcItemTotal(item) {
            item.unit_price = parseFloat(item.unit_price) || 0;
            item.quantity   = parseFloat(item.quantity)   || 1;
        },

        calcTotals() {
            let sub = 0;
            for (const item of this.form.items) {
                sub += (parseFloat(item.quantity) || 1) * (parseFloat(item.unit_price) || 0);
            }
            this.subtotal    = sub;
            this.taxAmount   = sub * (parseFloat(this.form.tax_rate) || 0) / 100;
            const disc       = parseFloat(this.form.discount_amount) || 0;
            this.totalAmount = Math.max(0, sub + this.taxAmount - disc);
        },

        formatClassDate(dt) {
            if (!dt) return '—';
            try { return new Date(dt).toLocaleString(undefined, { dateStyle:'medium', timeStyle:'short' }); }
            catch { return dt; }
        },

        async submitForm() {
            this.errorMsg = '';
            if (this.form.items.length === 0) { this.errorMsg = 'Add at least one line item.'; return; }
            this.saving = true;
            try {
                const payload = {
                    ...this.form,
                    tax_rate: parseFloat(this.form.tax_rate) || 0,
                    discount_amount: parseFloat(this.form.discount_amount) || 0,
                    items: this.form.items.map(({ _classes, ...item }) => ({
                        ...item,
                        quantity: parseFloat(item.quantity) || 1,
                        unit_price: parseFloat(item.unit_price) || 0,
                        session_type_id: item.session_type_id || null,
                        class_id: item.class_id || null,
                    })),
                };
                const res = await fetch(this.apiBase + '/api/booking-invoices', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();
                if (res.ok && json.success) {
                    window.location.href = this.apiBase + '/admin/invoices/' + json.data.id;
                } else {
                    this.errorMsg = json.message || 'Failed to save invoice.';
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
