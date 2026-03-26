<?php
$title       = 'Invoices';
$breadcrumbs = [['label' => 'Invoices']];
$apiBase     = $baseUrl ?? '';

ob_start();
?>
<div x-data="invoiceIndex()" x-init="init()" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-surface-900 dark:text-white">Invoices</h2>
            <p class="mt-1 text-sm text-surface-500">Manage and track all customer invoices</p>
        </div>
        <a href="<?= htmlspecialchars($apiBase) ?>/admin/invoices/create"
           class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-primary-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Invoice
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <template x-for="stat in stats" :key="stat.label">
            <div class="rounded-2xl border border-surface-200 bg-white p-4 shadow-soft dark:border-surface-800 dark:bg-surface-900">
                <p class="text-xs font-semibold uppercase tracking-wide text-surface-500" x-text="stat.label"></p>
                <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-white" x-text="stat.value"></p>
            </div>
        </template>
    </div>

    <!-- Filters -->
    <div class="rounded-2xl border border-surface-200 bg-white p-4 shadow-soft dark:border-surface-800 dark:bg-surface-900">
        <div class="flex flex-wrap gap-3">
            <input x-model="filters.search" @input.debounce.400ms="load()"
                   type="text" placeholder="Search invoice #, name, email…"
                   class="flex-1 min-w-48 rounded-xl border border-surface-200 bg-surface-50 px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            <select x-model="filters.status" @change="load()"
                    class="rounded-xl border border-surface-200 bg-surface-50 px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="sent">Sent</option>
                <option value="partially_paid">Partially Paid</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <input x-model="filters.from_date" @change="load()" type="date"
                   class="rounded-xl border border-surface-200 bg-surface-50 px-3 py-2 text-sm focus:border-primary-400 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            <input x-model="filters.to_date" @change="load()" type="date"
                   class="rounded-xl border border-surface-200 bg-surface-50 px-3 py-2 text-sm focus:border-primary-400 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white">
            <button @click="resetFilters()"
                    class="rounded-xl border border-surface-200 px-3 py-2 text-sm text-surface-600 hover:bg-surface-50 dark:border-surface-700 dark:text-surface-400">
                Reset
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-surface-200 bg-white shadow-soft overflow-hidden dark:border-surface-800 dark:bg-surface-900">
        <!-- Empty / Loading -->
        <div x-show="loading" class="flex items-center justify-center py-16">
            <svg class="h-6 w-6 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>

        <div x-show="!loading && invoices.length === 0" class="flex flex-col items-center py-16 text-center">
            <div class="mb-4 rounded-full bg-surface-100 p-4 dark:bg-surface-800">
                <svg class="h-8 w-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
            </div>
            <p class="font-semibold text-surface-600 dark:text-surface-400">No invoices found</p>
            <p class="mt-1 text-sm text-surface-400">Create your first invoice to get started</p>
            <a href="<?= htmlspecialchars($apiBase) ?>/admin/invoices/create"
               class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Invoice
            </a>
        </div>

        <table x-show="!loading && invoices.length > 0" class="w-full text-sm">
            <thead class="border-b border-surface-100 bg-surface-50/80 dark:border-surface-800 dark:bg-surface-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-500">Invoice #</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-500">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-500 hidden md:table-cell">Facility</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-500 hidden sm:table-cell">Issue Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-500 hidden sm:table-cell">Due Date</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-surface-500">Total</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-surface-500 hidden sm:table-cell">Paid</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-surface-500">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-surface-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                <template x-for="inv in invoices" :key="inv.id">
                    <tr class="hover:bg-surface-50/50 dark:hover:bg-surface-800/30 transition-colors">
                        <td class="px-4 py-3">
                            <a :href="'<?= htmlspecialchars($apiBase) ?>/admin/invoices/' + inv.id"
                               class="font-mono font-semibold text-primary-600 hover:text-primary-800 dark:text-primary-400"
                               x-text="inv.invoice_number"></a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-surface-900 dark:text-white"
                                 x-text="inv.customer_type === 'company' ? inv.customer_company : inv.customer_full_name"></div>
                            <div class="text-xs text-surface-400" x-text="inv.customer_email"></div>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-surface-600 dark:text-surface-400" x-text="inv.facility_name || '—'"></td>
                        <td class="px-4 py-3 hidden sm:table-cell text-surface-600 dark:text-surface-400" x-text="inv.issue_date ? new Date(inv.issue_date).toLocaleDateString() : '—'"></td>
                        <td class="px-4 py-3 hidden sm:table-cell" :class="isDue(inv) ? 'text-red-500 font-semibold' : 'text-surface-600 dark:text-surface-400'"
                            x-text="inv.due_date ? new Date(inv.due_date).toLocaleDateString() : '—'"></td>
                        <td class="px-4 py-3 text-right font-semibold text-surface-900 dark:text-white"
                            x-text="'$' + parseFloat(inv.total || 0).toFixed(2)"></td>
                        <td class="px-4 py-3 text-right hidden sm:table-cell text-accent-600 dark:text-accent-400"
                            x-text="'$' + parseFloat(inv.amount_paid || 0).toFixed(2)"></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold"
                                  :class="statusClass(inv.status)"
                                  x-text="statusLabel(inv.status)"></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a :href="'<?= htmlspecialchars($apiBase) ?>/admin/invoices/' + inv.id"
                                   class="rounded-lg p-1.5 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-500/10"
                                   title="View">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a :href="'<?= htmlspecialchars($apiBase) ?>/admin/invoices/' + inv.id + '/edit'"
                                   x-show="!['paid','refunded'].includes(inv.status)"
                                   class="rounded-lg p-1.5 text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10"
                                   title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button @click="deleteInvoice(inv)"
                                        x-show="['draft','cancelled'].includes(inv.status)"
                                        class="rounded-lg p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10"
                                        title="Delete">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Pagination -->
        <div x-show="!loading && totalPages > 1" class="flex items-center justify-between border-t border-surface-100 px-4 py-3 dark:border-surface-800">
            <p class="text-sm text-surface-500">
                Showing <span class="font-medium" x-text="(currentPage-1)*perPage+1"></span>–<span class="font-medium" x-text="Math.min(currentPage*perPage, total)"></span>
                of <span class="font-medium" x-text="total"></span>
            </p>
            <div class="flex gap-1">
                <button @click="goto(currentPage-1)" :disabled="currentPage===1"
                        class="rounded-lg px-3 py-1.5 text-sm text-surface-600 hover:bg-surface-100 disabled:opacity-40 dark:text-surface-400 dark:hover:bg-surface-800">Prev</button>
                <template x-for="p in pageRange()" :key="p">
                    <button @click="goto(p)"
                            class="rounded-lg px-3 py-1.5 text-sm transition-colors"
                            :class="p===currentPage ? 'bg-primary-600 text-white' : 'text-surface-600 hover:bg-surface-100 dark:text-surface-400 dark:hover:bg-surface-800'"
                            x-text="p"></button>
                </template>
                <button @click="goto(currentPage+1)" :disabled="currentPage===totalPages"
                        class="rounded-lg px-3 py-1.5 text-sm text-surface-600 hover:bg-surface-100 disabled:opacity-40 dark:text-surface-400 dark:hover:bg-surface-800">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
function invoiceIndex() {
    return {
        invoices: [],
        loading: true,
        total: 0,
        currentPage: 1,
        perPage: 20,
        totalPages: 1,
        stats: [
            { label: 'Total', value: '—' },
            { label: 'Outstanding', value: '—' },
            { label: 'Paid', value: '—' },
            { label: 'Overdue', value: '—' },
        ],
        filters: { search: '', status: '', from_date: '', to_date: '' },
        apiBase: '<?= htmlspecialchars($apiBase) ?>',

        init() { this.load(); },

        async load() {
            this.loading = true;
            try {
                const q = new URLSearchParams({
                    page: this.currentPage,
                    per_page: this.perPage,
                    search: this.filters.search,
                    status: this.filters.status,
                    from_date: this.filters.from_date,
                    to_date: this.filters.to_date,
                });
                const res = await fetch(this.apiBase + '/api/booking-invoices?' + q, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                if (json.success) {
                    this.invoices   = json.data;
                    this.total      = json.meta?.total ?? 0;
                    this.totalPages = Math.ceil(this.total / this.perPage) || 1;
                    this.buildStats();
                }
            } finally {
                this.loading = false;
            }
        },

        buildStats() {
            const all     = this.invoices;
            const totalAmt= all.reduce((s,i) => s + parseFloat(i.total||0), 0);
            const paidAmt = all.reduce((s,i) => s + parseFloat(i.amount_paid||0), 0);
            const dueAmt  = all.reduce((s,i) => s + parseFloat(i.amount_due||0), 0);
            const overdue = all.filter(i => i.status === 'overdue').length;
            this.stats = [
                { label: 'Total Invoices', value: this.total },
                { label: 'Outstanding',    value: '$' + dueAmt.toFixed(2) },
                { label: 'Collected',      value: '$' + paidAmt.toFixed(2) },
                { label: 'Overdue',        value: overdue },
            ];
        },

        goto(p) {
            if (p < 1 || p > this.totalPages) return;
            this.currentPage = p;
            this.load();
        },

        pageRange() {
            const r = [], half = 2;
            for (let p = Math.max(1, this.currentPage - half); p <= Math.min(this.totalPages, this.currentPage + half); p++) r.push(p);
            return r;
        },

        resetFilters() {
            this.filters = { search: '', status: '', from_date: '', to_date: '' };
            this.currentPage = 1;
            this.load();
        },

        isDue(inv) {
            return inv.due_date && !['paid','cancelled','refunded'].includes(inv.status) && new Date(inv.due_date) < new Date();
        },

        statusLabel(s) {
            return { draft:'Draft', sent:'Sent', partially_paid:'Partial', paid:'Paid', overdue:'Overdue', cancelled:'Cancelled', refunded:'Refunded' }[s] ?? s;
        },

        statusClass(s) {
            return {
                draft:          'bg-surface-100 text-surface-600 dark:bg-surface-800 dark:text-surface-400',
                sent:           'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                partially_paid: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                paid:           'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                overdue:        'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                cancelled:      'bg-surface-100 text-surface-500',
                refunded:       'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
            }[s] ?? 'bg-surface-100 text-surface-600';
        },

        async deleteInvoice(inv) {
            if (!confirm('Delete invoice ' + inv.invoice_number + '?')) return;
            const res = await fetch(this.apiBase + '/api/booking-invoices/' + inv.id, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.status === 204 || res.ok) {
                this.invoices = this.invoices.filter(i => i.id !== inv.id);
            } else {
                const j = await res.json().catch(() => ({}));
                alert(j.message || 'Delete failed');
            }
        },
    };
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
