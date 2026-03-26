<?php
$title = 'Audit Log Detail';
$breadcrumbs = [
    ['label' => 'Audit Logs', 'url' => ($baseUrl ?? '') . '/admin/audit-logs'],
    ['label' => 'Detail'],
];
$backUrl = ($baseUrl ?? '') . '/admin/audit-logs';
$apiUrl  = ($baseUrl ?? '') . '/api/audit-logs/' . ($id ?? '');

ob_start();
?>
<div x-data="auditLogShow()" x-init="init()">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-surface-900 dark:text-white">Audit Log Entry</h2>
        <a href="<?= htmlspecialchars($backUrl) ?>"
           class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-soft">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    <div x-show="loading" class="flex items-center justify-center py-20">
        <div class="h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
    </div>

    <div x-show="!loading" class="space-y-6">
        <!-- Summary -->
        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800 bg-surface-50 dark:bg-surface-800/50">
                <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-200">Summary</h3>
            </div>
            <div class="divide-y divide-surface-100 dark:divide-surface-800">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-0">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">ID</dt>
                        <dd class="mt-1 text-sm font-mono" x-text="log.id || '—'"></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Action</dt>
                        <dd class="mt-1">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium"
                                  :class="actionColor" x-text="log.action || '—'"></span>
                        </dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Entity Type</dt>
                        <dd class="mt-1 text-sm capitalize" x-text="log.entity_type || '—'"></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Entity ID</dt>
                        <dd class="mt-1 text-sm font-mono" x-text="log.entity_id || '—'"></dd>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-0">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">User</dt>
                        <dd class="mt-1 text-sm" x-text="userName"></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">IP Address</dt>
                        <dd class="mt-1 text-sm font-mono" x-text="log.ip_address || '—'"></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Date</dt>
                        <dd class="mt-1 text-sm" x-text="log.created_at ? new Date(log.created_at).toLocaleString() : '—'"></dd>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">User Agent</dt>
                    <dd class="mt-1 text-xs text-surface-500 break-all" x-text="log.user_agent || '—'"></dd>
                </div>
            </div>
        </div>

        <!-- Old Values -->
        <div x-show="hasOld" class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800 bg-red-50 dark:bg-red-900/10">
                <h3 class="text-sm font-semibold text-red-700 dark:text-red-400">Old Values</h3>
            </div>
            <div class="px-6 py-4">
                <pre class="text-xs font-mono text-surface-600 dark:text-surface-300 bg-surface-50 dark:bg-surface-800 p-4 rounded-lg overflow-x-auto whitespace-pre-wrap" x-text="oldFormatted"></pre>
            </div>
        </div>

        <!-- New Values -->
        <div x-show="hasNew" class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800 bg-green-50 dark:bg-green-900/10">
                <h3 class="text-sm font-semibold text-green-700 dark:text-green-400">New Values</h3>
            </div>
            <div class="px-6 py-4">
                <pre class="text-xs font-mono text-surface-600 dark:text-surface-300 bg-surface-50 dark:bg-surface-800 p-4 rounded-lg overflow-x-auto whitespace-pre-wrap" x-text="newFormatted"></pre>
            </div>
        </div>
    </div>
</div>

<script>
function auditLogShow() {
    return {
        log: {},
        loading: true,
        get actionColor() {
            const colors = {
                created: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                updated: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                deleted: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                login:   'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
                logout:  'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-300',
            };
            return colors[this.log.action] || 'bg-surface-100 text-surface-600';
        },
        get userName() {
            if (this.log.first_name || this.log.last_name) {
                return ((this.log.first_name || '') + ' ' + (this.log.last_name || '')).trim() + (this.log.email ? ' (' + this.log.email + ')' : '');
            }
            return this.log.email || 'System';
        },
        get hasOld() {
            return this.log.old_values && this.log.old_values !== 'null';
        },
        get hasNew() {
            return this.log.new_values && this.log.new_values !== 'null';
        },
        get oldFormatted() {
            try { return JSON.stringify(JSON.parse(this.log.old_values), null, 2); } catch(_) { return this.log.old_values || ''; }
        },
        get newFormatted() {
            try { return JSON.stringify(JSON.parse(this.log.new_values), null, 2); } catch(_) { return this.log.new_values || ''; }
        },
        async init() {
            try {
                const res = await authFetch('<?= htmlspecialchars($apiUrl) ?>');
                if (res.status === 401) { window.location.href = APP_BASE + '/admin/login'; return; }
                const json = await res.json();
                if (json.data) this.log = json.data;
            } catch (e) { console.error(e); }
            finally { this.loading = false; }
        },
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
