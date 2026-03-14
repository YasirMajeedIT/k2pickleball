<?php $pageTitle = 'Invoices — K2 Portal'; ?>

<div x-data="invoicesPage()" x-init="init()">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Invoices</h1>
        <p class="mt-1 text-surface-400">View and download your billing history.</p>
    </div>

    <!-- Summary cards -->
    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="p-5 rounded-2xl border border-surface-800/60 bg-surface-900/30">
            <div class="text-sm text-surface-400 mb-1">Total Paid</div>
            <div class="text-2xl font-bold text-white" x-text="'$' + totalPaid.toFixed(2)">$0.00</div>
        </div>
        <div class="p-5 rounded-2xl border border-surface-800/60 bg-surface-900/30">
            <div class="text-sm text-surface-400 mb-1">Outstanding</div>
            <div class="text-2xl font-bold text-amber-400" x-text="'$' + totalOutstanding.toFixed(2)">$0.00</div>
        </div>
        <div class="p-5 rounded-2xl border border-surface-800/60 bg-surface-900/30">
            <div class="text-sm text-surface-400 mb-1">Total Invoices</div>
            <div class="text-2xl font-bold text-white" x-text="invoices.length">0</div>
        </div>
    </div>

    <!-- Invoices table -->
    <div class="rounded-2xl border border-surface-800/60 bg-surface-900/30 overflow-hidden">
        <!-- Loading -->
        <div x-show="loading" class="p-8">
            <div class="animate-pulse space-y-4">
                <div class="h-4 bg-surface-800 rounded w-full"></div>
                <div class="h-4 bg-surface-800 rounded w-5/6"></div>
                <div class="h-4 bg-surface-800 rounded w-4/6"></div>
            </div>
        </div>

        <!-- Table -->
        <div x-show="!loading && invoices.length > 0">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-surface-800/40">
                        <th class="text-left py-3 px-6 text-surface-400 font-medium">Invoice #</th>
                        <th class="text-left py-3 px-6 text-surface-400 font-medium">Date</th>
                        <th class="text-left py-3 px-6 text-surface-400 font-medium">Description</th>
                        <th class="text-right py-3 px-6 text-surface-400 font-medium">Amount</th>
                        <th class="text-center py-3 px-6 text-surface-400 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-800/30">
                    <template x-for="inv in invoices" :key="inv.id">
                        <tr class="hover:bg-surface-800/20 transition-colors">
                            <td class="py-3 px-6">
                                <span class="text-white font-medium" x-text="'#' + (inv.invoice_number || inv.id)"></span>
                            </td>
                            <td class="py-3 px-6 text-surface-400" x-text="new Date(inv.created_at || inv.issue_date).toLocaleDateString()"></td>
                            <td class="py-3 px-6 text-surface-300" x-text="inv.description || 'Subscription payment'"></td>
                            <td class="py-3 px-6 text-right text-white font-medium" x-text="'$' + parseFloat(inv.amount || 0).toFixed(2)"></td>
                            <td class="py-3 px-6 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="{
                                    'bg-brand-500/10 text-brand-400': inv.status === 'paid',
                                    'bg-amber-500/10 text-amber-400': inv.status === 'pending' || inv.status === 'sent',
                                    'bg-red-500/10 text-red-400': inv.status === 'overdue',
                                    'bg-surface-500/10 text-surface-400': inv.status === 'draft'
                                }" x-text="inv.status"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty state -->
        <div x-show="!loading && invoices.length === 0" class="text-center py-12">
            <svg class="w-12 h-12 mx-auto mb-3 text-surface-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            <p class="text-sm text-surface-500">No invoices yet.</p>
            <p class="text-xs text-surface-600 mt-1">Invoices will appear here after your first billing cycle.</p>
        </div>
    </div>
</div>

<script>
function invoicesPage() {
    return {
        invoices: [],
        loading: true,
        get totalPaid() {
            return this.invoices.filter(i => i.status === 'paid').reduce((s, i) => s + parseFloat(i.amount || 0), 0);
        },
        get totalOutstanding() {
            return this.invoices.filter(i => i.status !== 'paid' && i.status !== 'draft').reduce((s, i) => s + parseFloat(i.amount || 0), 0);
        },
        init() {
            const token = localStorage.getItem('access_token');
            if (!token) return;
            fetch(APP_BASE + '/api/invoices', {
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                this.invoices = data.data || data || [];
                if (!Array.isArray(this.invoices)) this.invoices = [];
                this.loading = false;
            })
            .catch(() => { this.invoices = []; this.loading = false; });
        }
    }
}
</script>
