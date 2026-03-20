<?php
$title = 'Consultations';
$breadcrumbs = [['label' => 'Consultations']];

ob_start();
?>

<div x-data="consultationsPage()" x-init="loadStats(); loadConsultations()">

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <template x-for="card in statsCards" :key="card.label">
            <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 p-5 shadow-soft">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-surface-400" x-text="card.label"></span>
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg" :class="card.bgClass">
                        <span x-html="card.icon"></span>
                    </span>
                </div>
                <div class="text-2xl font-bold text-surface-900 dark:text-white" x-text="card.count"></div>
            </div>
        </template>
    </div>

    <!-- Filters -->
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <div class="relative w-full sm:w-72 group">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" @input.debounce.350ms="currentPage = 1; loadConsultations()" placeholder="Search by name, email..."
                       class="w-full rounded-xl border border-surface-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft placeholder:text-surface-400">
            </div>
            <select x-model="filterStatus" @change="currentPage = 1; loadConsultations()"
                    class="rounded-xl border border-surface-200 bg-white py-2.5 px-4 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft">
                <option value="">All Statuses</option>
                <option value="new">New</option>
                <option value="contacted">Contacted</option>
                <option value="in_progress">In Progress</option>
                <option value="closed">Closed</option>
            </select>
            <select x-model="filterType" @change="currentPage = 1; loadConsultations()"
                    class="rounded-xl border border-surface-200 bg-white py-2.5 px-4 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft">
                <option value="">All Types</option>
                <option value="partnership">Partnership</option>
                <option value="software_integration">Software Integration</option>
                <option value="general">General</option>
            </select>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
        <p class="text-sm text-surface-400">Loading consultations...</p>
    </div>

    <!-- Table -->
    <div x-show="!loading" class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 overflow-hidden shadow-soft">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-50 dark:bg-surface-800/50">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Name</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Email</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Type</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Stage</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Status</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Date</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                    <template x-for="row in rows" :key="row.id">
                        <tr class="hover:bg-primary-50/30 dark:hover:bg-primary-500/[0.03] transition-colors">
                            <td class="px-5 py-4 text-surface-700 dark:text-surface-300 whitespace-nowrap font-medium" x-text="row.first_name + ' ' + row.last_name"></td>
                            <td class="px-5 py-4 text-surface-700 dark:text-surface-300 whitespace-nowrap" x-text="row.email"></td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                      :class="typeClass(row.consultation_type)" x-text="formatType(row.consultation_type)"></span>
                            </td>
                            <td class="px-5 py-4 text-surface-500 dark:text-surface-400 whitespace-nowrap text-xs" x-text="formatStage(row.facility_stage)"></td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                      :class="statusClass(row.status)" x-text="formatStatus(row.status)"></span>
                            </td>
                            <td class="px-5 py-4 text-surface-500 dark:text-surface-400 whitespace-nowrap text-xs" x-text="formatDate(row.created_at)"></td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <button @click="viewDetail(row)" class="text-primary-500 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-500/10 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors">View</button>
                                    <button @click="openStatusModal(row)" class="text-amber-500 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-500/10 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors">Status</button>
                                    <button @click="deleteConsultation(row)" class="text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-500/10 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="rows.length === 0">
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-12 h-12 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p class="text-sm font-medium text-surface-400">No consultations found</p>
                                    <p class="text-xs text-surface-300 dark:text-surface-500">Consultation requests will appear here when clients submit the form</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div x-show="totalPages > 0" class="flex items-center justify-between border-t border-surface-100 dark:border-surface-800 px-5 py-3.5 bg-surface-50/50 dark:bg-surface-800/30">
            <div class="text-xs text-surface-400 font-medium">
                Showing <span class="text-surface-600 dark:text-surface-300" x-text="rows.length > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></span>-<span class="text-surface-600 dark:text-surface-300" x-text="Math.min(currentPage * perPage, totalRecords)"></span>
                of <span class="text-surface-600 dark:text-surface-300" x-text="totalRecords"></span> results
            </div>
            <div class="flex items-center gap-1.5">
                <button @click="currentPage--; loadConsultations()" :disabled="currentPage <= 1"
                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 disabled:opacity-40 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Prev
                </button>
                <span class="inline-flex items-center rounded-lg bg-primary-50 dark:bg-primary-500/10 px-3 py-1.5 text-xs font-semibold text-primary-600 dark:text-primary-400">
                    <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                </span>
                <button @click="currentPage++; loadConsultations()" :disabled="currentPage >= totalPages"
                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 disabled:opacity-40 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                    Next
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="detailModal = false">
        <div class="fixed inset-0 bg-black/50" @click="detailModal = false"></div>
        <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-xl max-w-lg w-full max-h-[85vh] overflow-y-auto border border-surface-200 dark:border-surface-800">
            <div class="sticky top-0 bg-white dark:bg-surface-900 px-6 py-4 border-b border-surface-200 dark:border-surface-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white">Consultation Details</h3>
                <button @click="detailModal = false" class="text-surface-400 hover:text-surface-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4" x-show="detailRow">
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="block text-xs text-surface-400 mb-1">Name</span><span class="text-sm font-medium text-surface-900 dark:text-white" x-text="detailRow?.first_name + ' ' + detailRow?.last_name"></span></div>
                    <div><span class="block text-xs text-surface-400 mb-1">Email</span><span class="text-sm text-surface-700 dark:text-surface-300" x-text="detailRow?.email"></span></div>
                    <div><span class="block text-xs text-surface-400 mb-1">Phone</span><span class="text-sm text-surface-700 dark:text-surface-300" x-text="detailRow?.phone || '—'"></span></div>
                    <div><span class="block text-xs text-surface-400 mb-1">Type</span><span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" :class="typeClass(detailRow?.consultation_type)" x-text="formatType(detailRow?.consultation_type)"></span></div>
                    <div><span class="block text-xs text-surface-400 mb-1">Stage</span><span class="text-sm text-surface-700 dark:text-surface-300" x-text="formatStage(detailRow?.facility_stage)"></span></div>
                    <div><span class="block text-xs text-surface-400 mb-1">Location</span><span class="text-sm text-surface-700 dark:text-surface-300" x-text="detailRow?.planned_location || '—'"></span></div>
                    <div><span class="block text-xs text-surface-400 mb-1">Courts</span><span class="text-sm text-surface-700 dark:text-surface-300" x-text="detailRow?.number_of_courts || '—'"></span></div>
                    <div x-show="detailRow?.software_interest"><span class="block text-xs text-surface-400 mb-1">Software Interest</span><span class="text-sm text-surface-700 dark:text-surface-300" x-text="detailRow?.software_interest?.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())"></span></div>
                    <div><span class="block text-xs text-surface-400 mb-1">Status</span><span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(detailRow?.status)" x-text="formatStatus(detailRow?.status)"></span></div>
                    <div><span class="block text-xs text-surface-400 mb-1">Submitted</span><span class="text-sm text-surface-700 dark:text-surface-300" x-text="formatDate(detailRow?.created_at)"></span></div>
                </div>
                <div x-show="detailRow?.message">
                    <span class="block text-xs text-surface-400 mb-1">Message</span>
                    <p class="text-sm text-surface-700 dark:text-surface-300 bg-surface-50 dark:bg-surface-800 rounded-xl p-3 whitespace-pre-wrap" x-text="detailRow?.message"></p>
                </div>
                <div x-show="detailRow?.notes">
                    <span class="block text-xs text-surface-400 mb-1">Internal Notes</span>
                    <p class="text-sm text-surface-700 dark:text-surface-300 bg-amber-50 dark:bg-amber-500/10 rounded-xl p-3 whitespace-pre-wrap" x-text="detailRow?.notes"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div x-show="statusModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="statusModal = false">
        <div class="fixed inset-0 bg-black/50" @click="statusModal = false"></div>
        <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-xl max-w-md w-full border border-surface-200 dark:border-surface-800">
            <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-800">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white">Update Status</h3>
                <p class="text-sm text-surface-400 mt-1" x-text="statusRow?.first_name + ' ' + statusRow?.last_name"></p>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Status</label>
                    <select x-model="statusForm.status" class="w-full rounded-xl border border-surface-200 bg-white py-2.5 px-4 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft">
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="in_progress">In Progress</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Notes (optional)</label>
                    <textarea x-model="statusForm.notes" rows="3" class="w-full rounded-xl border border-surface-200 bg-white py-2.5 px-4 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft resize-none" placeholder="Add internal notes..."></textarea>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button @click="statusModal = false" class="rounded-xl px-4 py-2.5 text-sm font-medium text-surface-600 hover:bg-surface-100 dark:text-surface-400 dark:hover:bg-surface-800 transition-colors">Cancel</button>
                    <button @click="saveStatus()" :disabled="statusSaving" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 shadow-soft disabled:opacity-60 transition-all">
                        <span x-show="!statusSaving">Save</span>
                        <span x-show="statusSaving">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function consultationsPage() {
    const BASE = '<?= $baseUrl ?? '' ?>';
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'Content-Type': 'application/json' };

    return {
        rows: [], loading: true,
        search: '', filterStatus: '', filterType: '',
        currentPage: 1, perPage: 20, totalRecords: 0, totalPages: 0,
        stats: { new: 0, contacted: 0, in_progress: 0, closed: 0 },
        detailModal: false, detailRow: null,
        statusModal: false, statusRow: null, statusSaving: false,
        statusForm: { status: 'new', notes: '' },

        get statsCards() {
            return [
                { label: 'New', count: this.stats.new, bgClass: 'bg-blue-100 dark:bg-blue-500/10', icon: '<svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>' },
                { label: 'Contacted', count: this.stats.contacted, bgClass: 'bg-amber-100 dark:bg-amber-500/10', icon: '<svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>' },
                { label: 'In Progress', count: this.stats.in_progress, bgClass: 'bg-purple-100 dark:bg-purple-500/10', icon: '<svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>' },
                { label: 'Closed', count: this.stats.closed, bgClass: 'bg-emerald-100 dark:bg-emerald-500/10', icon: '<svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' },
            ];
        },

        async loadStats() {
            try {
                const res = await fetch(BASE + '/api/platform/consultations/stats', { headers });
                const json = await res.json();
                if (json.status === 'success' && json.data) this.stats = { ...this.stats, ...json.data };
            } catch (e) { console.error('Stats load failed:', e); }
        },

        async loadConsultations() {
            this.loading = true;
            const params = new URLSearchParams({ page: this.currentPage, per_page: this.perPage });
            if (this.search) params.set('search', this.search);
            if (this.filterStatus) params.set('status', this.filterStatus);
            if (this.filterType) params.set('type', this.filterType);
            try {
                const res = await fetch(BASE + '/api/platform/consultations?' + params.toString(), { headers });
                if (res.status === 401) {
                    const rt = localStorage.getItem('refresh_token');
                    if (rt) {
                        const rr = await fetch(BASE + '/api/auth/refresh', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ refresh_token: rt }) });
                        const rd = await rr.json();
                        if (rd.data?.access_token) { localStorage.setItem('access_token', rd.data.access_token); headers['Authorization'] = 'Bearer ' + rd.data.access_token; return this.loadConsultations(); }
                    }
                }
                const json = await res.json();
                if (json.status === 'success') {
                    const d = json.data;
                    this.rows = d.data || d;
                    this.totalRecords = d.total || this.rows.length;
                    this.totalPages = d.last_page || Math.ceil(this.totalRecords / this.perPage);
                    this.currentPage = d.current_page || this.currentPage;
                }
            } catch (e) { console.error('Load failed:', e); }
            this.loading = false;
        },

        viewDetail(row) { this.detailRow = row; this.detailModal = true; },

        openStatusModal(row) {
            this.statusRow = row;
            this.statusForm = { status: row.status, notes: row.notes || '' };
            this.statusModal = true;
        },

        async saveStatus() {
            this.statusSaving = true;
            try {
                const res = await fetch(BASE + '/api/platform/consultations/' + this.statusRow.id + '/status', {
                    method: 'PATCH', headers, body: JSON.stringify(this.statusForm)
                });
                if (res.ok) { this.statusModal = false; this.loadStats(); this.loadConsultations(); }
            } catch (e) { console.error('Status update failed:', e); }
            this.statusSaving = false;
        },

        async deleteConsultation(row) {
            if (!confirm('Delete consultation from ' + row.first_name + ' ' + row.last_name + '?')) return;
            try {
                const res = await fetch(BASE + '/api/platform/consultations/' + row.id, { method: 'DELETE', headers });
                if (res.ok) { this.loadStats(); this.loadConsultations(); }
            } catch (e) { console.error('Delete failed:', e); }
        },

        formatType(t) {
            const map = { partnership: 'Partnership', software_integration: 'Software Integration', general: 'General' };
            return map[t] || t || '—';
        },
        typeClass(t) {
            const map = { partnership: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400', software_integration: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-400', general: 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-300' };
            return map[t] || '';
        },
        formatStatus(s) {
            const map = { new: 'New', contacted: 'Contacted', in_progress: 'In Progress', closed: 'Closed' };
            return map[s] || s || '—';
        },
        statusClass(s) {
            const map = { new: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', contacted: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400', in_progress: 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400', closed: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' };
            return map[s] || '';
        },
        formatStage(s) {
            const map = { exploring: 'Exploring', planning: 'Planning', location: 'Location Found', building: 'Building', operating: 'Operating', expanding: 'Expanding' };
            return map[s] || s || '—';
        },
        formatDate(d) {
            if (!d) return '—';
            return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
    };
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
