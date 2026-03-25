<?php
$title = 'Database Migrations';
$breadcrumbs = [['label' => 'Migrations']];

ob_start();
?>
<div x-data="migrationsPage()" x-init="init()">
    <!-- Header -->
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-surface-500 dark:text-surface-400">
                Manage and run database migrations. Pending migrations are shown with a <span class="font-semibold text-amber-500">Run</span> button.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="markAllExecuted()" :disabled="pending === 0 || busy"
                    class="inline-flex items-center gap-2 rounded-xl border border-surface-300 dark:border-surface-600 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Mark All as Run
            </button>
            <button @click="runAllPending()" :disabled="pending === 0 || busy"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                Run All Pending (<span x-text="pending"></span>)
            </button>
        </div>
    </div>

    <!-- Stats bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-900 p-4 text-center shadow-soft">
            <p class="text-2xl font-bold text-surface-900 dark:text-white" x-text="total">0</p>
            <p class="text-xs text-surface-400 mt-1">Total Migrations</p>
        </div>
        <div class="rounded-xl border border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-500/10 p-4 text-center shadow-soft">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="success">0</p>
            <p class="text-xs text-green-600/70 dark:text-green-400/70 mt-1">Executed</p>
        </div>
        <div class="rounded-xl border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-500/10 p-4 text-center shadow-soft">
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400" x-text="pending">0</p>
            <p class="text-xs text-amber-600/70 dark:text-amber-400/70 mt-1">Pending</p>
        </div>
        <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-500/10 p-4 text-center shadow-soft">
            <p class="text-2xl font-bold text-red-600 dark:text-red-400" x-text="failed">0</p>
            <p class="text-xs text-red-600/70 dark:text-red-400/70 mt-1">Failed</p>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="relative"><div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div><div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div></div>
        <p class="text-sm text-surface-400">Loading migrations...</p>
    </div>

    <!-- Busy overlay -->
    <div x-show="busy && !loading" class="mb-4 rounded-xl border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-500/10 p-4 flex items-center gap-3">
        <div class="relative"><div class="h-5 w-5 rounded-full border-2 border-surface-200 dark:border-surface-700"></div><div class="absolute top-0 left-0 h-5 w-5 rounded-full border-2 border-transparent border-t-primary-500 animate-spin"></div></div>
        <p class="text-sm font-medium text-primary-700 dark:text-primary-300" x-text="busyMsg">Running migrations...</p>
    </div>

    <!-- Table -->
    <div x-show="!loading" class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Migration File</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Batch</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Executed</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-surface-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                    <template x-for="(m, index) in rows" :key="m.filename">
                        <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/40 transition-colors">
                            <td class="px-4 py-3 text-surface-500 font-mono text-xs" x-text="m.number ?? '—'"></td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs text-surface-900 dark:text-white" x-text="m.filename"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span x-show="m.status === 'success'" class="inline-flex items-center gap-1 rounded-full bg-green-100 dark:bg-green-500/10 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Executed
                                </span>
                                <span x-show="m.status === 'pending'" class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-500/10 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:text-amber-400">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                    Pending
                                </span>
                                <span x-show="m.status === 'failed'" class="inline-flex items-center gap-1 rounded-full bg-red-100 dark:bg-red-500/10 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-400" :title="m.error_message">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    Failed
                                </span>
                            </td>
                            <td class="px-4 py-3 text-surface-500 text-xs" x-text="m.batch ?? '—'"></td>
                            <td class="px-4 py-3 text-surface-500 text-xs" x-text="m.executed_at ? new Date(m.executed_at).toLocaleString() : '—'"></td>
                            <td class="px-4 py-3 text-right">
                                <template x-if="m.status === 'pending' || m.status === 'failed'">
                                    <button @click="runOne(m.filename)" :disabled="busy"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary-500 hover:bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                                        Run
                                    </button>
                                </template>
                                <template x-if="m.status === 'success'">
                                    <span class="text-xs text-surface-400">—</span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <template x-if="rows.length === 0 && !loading">
            <div class="text-center py-12"><p class="text-sm text-surface-400">No migration files found</p></div>
        </template>
    </div>

    <!-- Error details modal -->
    <template x-if="rows.some(m => m.status === 'failed' && m.error_message)">
        <div class="mt-5 rounded-2xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-500/5 p-5">
            <h3 class="text-sm font-bold text-red-700 dark:text-red-400 mb-3">Failed Migration Details</h3>
            <template x-for="m in rows.filter(r => r.status === 'failed' && r.error_message)" :key="m.filename + '-err'">
                <div class="mb-2 last:mb-0">
                    <p class="text-xs font-semibold text-red-600 dark:text-red-400" x-text="m.filename"></p>
                    <p class="text-xs text-red-500 dark:text-red-300 font-mono mt-0.5" x-text="m.error_message"></p>
                </div>
            </template>
        </div>
    </template>
</div>

<script>
function migrationsPage() {
    return {
        rows: [],
        loading: true,
        busy: false,
        busyMsg: '',
        total: 0,
        success: 0,
        pending: 0,
        failed: 0,

        async init() {
            await this.fetchData();
        },

        async fetchData() {
            this.loading = true;
            try {
                const res = await fetch((window.APP_BASE || '') + '/api/platform/migrations', {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('access_token') }
                });
                const json = await res.json();
                if (json.status === 'success') {
                    this.rows = json.data.data || [];
                    this.total = this.rows.length;
                    this.success = this.rows.filter(m => m.status === 'success').length;
                    this.pending = this.rows.filter(m => m.status === 'pending').length;
                    this.failed = this.rows.filter(m => m.status === 'failed').length;
                }
            } catch (e) {
                this.toast('Failed to load migrations', 'error');
            }
            this.loading = false;
        },

        async runOne(filename) {
            if (!confirm('Run migration: ' + filename + '?')) return;
            this.busy = true;
            this.busyMsg = 'Running ' + filename + '...';
            try {
                const res = await fetch((window.APP_BASE || '') + '/api/platform/migrations/run', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ migrations: [filename] })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    const r = (json.data.results || [])[0];
                    this.toast(r ? r.migration + ': ' + r.status : 'Done', r && r.status === 'success' ? 'success' : 'error');
                } else {
                    this.toast(json.message || 'Failed', 'error');
                }
            } catch (e) {
                this.toast('Request failed', 'error');
            }
            this.busy = false;
            this.busyMsg = '';
            await this.fetchData();
        },

        async runAllPending() {
            if (!confirm('Run all ' + this.pending + ' pending migrations?')) return;
            this.busy = true;
            this.busyMsg = 'Running all pending migrations...';
            try {
                const res = await fetch((window.APP_BASE || '') + '/api/platform/migrations/run', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ run_all: true })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    this.toast(json.data.message || 'Done', 'success');
                } else {
                    this.toast(json.message || 'Failed', 'error');
                }
            } catch (e) {
                this.toast('Request failed', 'error');
            }
            this.busy = false;
            this.busyMsg = '';
            await this.fetchData();
        },

        async markAllExecuted() {
            if (!confirm('This will mark ALL pending migrations as already executed (without running them). Use this only if your database is already up to date. Continue?')) return;
            this.busy = true;
            this.busyMsg = 'Marking all as executed...';
            try {
                const res = await fetch((window.APP_BASE || '') + '/api/platform/migrations/mark-executed', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ mark_all: true })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    this.toast(json.message || 'Done', 'success');
                } else {
                    this.toast(json.message || 'Failed', 'error');
                }
            } catch (e) {
                this.toast('Request failed', 'error');
            }
            this.busy = false;
            this.busyMsg = '';
            await this.fetchData();
        },

        toast(msg, type = 'success') {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: msg, type: type } }));
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
