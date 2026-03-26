<?php
$title = 'Notification';
$breadcrumbs = [
    ['label' => 'Notifications', 'url' => ($baseUrl ?? '') . '/admin/notifications'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/admin/notifications';
$apiUrl  = ($baseUrl ?? '') . '/api/notifications/' . ($id ?? '');

ob_start();
?>
<div x-data="notificationShow()" x-init="init()">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-surface-900 dark:text-white" x-text="n.title || 'Notification'"></h2>
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($backUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-soft">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <button x-show="!n.read_at" @click="markRead()"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all">
                Mark as Read
            </button>
        </div>
    </div>

    <div x-show="loading" class="flex items-center justify-center py-20">
        <div class="h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
    </div>

    <div x-show="!loading" class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
        <div class="divide-y divide-surface-100 dark:divide-surface-800">
            <div class="grid grid-cols-3 gap-0">
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Type</dt>
                    <dd class="mt-1 text-sm capitalize" x-text="n.type || '—'"></dd>
                </div>
                <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Channel</dt>
                    <dd class="mt-1 text-sm capitalize" x-text="n.channel || '—'"></dd>
                </div>
                <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Status</dt>
                    <dd class="mt-1">
                        <span x-show="n.read_at" class="inline-block rounded-full bg-surface-100 dark:bg-surface-700 px-2.5 py-0.5 text-xs font-medium text-surface-500">Read</span>
                        <span x-show="!n.read_at" class="inline-block rounded-full bg-blue-100 dark:bg-blue-500/10 px-2.5 py-0.5 text-xs font-medium text-blue-600 dark:text-blue-400">Unread</span>
                    </dd>
                </div>
            </div>
            <div class="px-6 py-4">
                <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Title</dt>
                <dd class="mt-1 text-sm font-semibold text-surface-900 dark:text-white" x-text="n.title || '—'"></dd>
            </div>
            <div class="px-6 py-4">
                <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Message</dt>
                <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300 leading-relaxed whitespace-pre-wrap" x-text="n.message || '—'"></dd>
            </div>
            <div class="grid grid-cols-2 gap-0">
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Created</dt>
                    <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="n.created_at ? new Date(n.created_at).toLocaleString() : '—'"></dd>
                </div>
                <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Read At</dt>
                    <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="n.read_at ? new Date(n.read_at).toLocaleString() : '—'"></dd>
                </div>
            </div>
            <div x-show="n.data && Object.keys(n.data || {}).length" class="px-6 py-4">
                <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Data</dt>
                <dd class="mt-1 text-xs font-mono text-surface-500 bg-surface-50 dark:bg-surface-800 p-3 rounded-lg overflow-x-auto" x-text="JSON.stringify(n.data, null, 2)"></dd>
            </div>
        </div>
    </div>
</div>

<script>
function notificationShow() {
    return {
        n: {},
        loading: true,
        async init() {
            try {
                const res = await authFetch('<?= htmlspecialchars($apiUrl) ?>');
                if (res.status === 401) { window.location.href = APP_BASE + '/admin/login'; return; }
                const json = await res.json();
                if (json.data) {
                    this.n = json.data;
                    if (typeof this.n.data === 'string') {
                        try { this.n.data = JSON.parse(this.n.data); } catch(_) {}
                    }
                }
            } catch (e) { console.error(e); }
            finally { this.loading = false; }
        },
        async markRead() {
            try {
                const res = await authFetch('<?= htmlspecialchars($apiUrl) ?>/read', { method: 'POST' });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Marked as read', type: 'success' } }));
                    this.init();
                }
            } catch(e) { console.error(e); }
        },
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
