<?php
$title = 'Court Details';
$breadcrumbs = [
    ['label' => 'Courts', 'url' => ($baseUrl ?? '') . '/admin/courts'],
    ['label' => 'View'],
];
$backUrl  = ($baseUrl ?? '') . '/admin/courts';
$editUrl  = ($baseUrl ?? '') . '/admin/courts/' . ($id ?? '') . '/edit';
$apiUrl   = ($baseUrl ?? '') . '/api/courts/' . ($id ?? '');

ob_start();
?>
<div x-data="courtShow()" x-init="init()">

    <!-- Header bar -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-surface-900 dark:text-white" x-text="court.name || 'Court Details'"></h2>
            <p class="mt-0.5 text-sm text-surface-500" x-text="court.description || ''"></p>
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
                Edit Court
            </a>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-20 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
        <p class="text-sm text-surface-400">Loading court...</p>
    </div>

    <!-- Content -->
    <div x-show="!loading" class="space-y-6">

        <!-- Basic Information -->
        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Basic Information</h3>
            </div>
            <div class="divide-y divide-surface-100 dark:divide-surface-800">
                <div class="grid grid-cols-2 gap-0">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Court Name</dt>
                        <dd class="mt-1 text-sm font-medium text-surface-800 dark:text-surface-100" x-text="court.name || '—'"></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Court Number</dt>
                        <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="court.court_number || '—'"></dd>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-0">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Surface Type</dt>
                        <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300 capitalize" x-text="court.surface_type || '—'"></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Status</dt>
                        <dd class="mt-1">
                            <span :class="{
                                'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400': court.status === 'active',
                                'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-300': court.status === 'inactive',
                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400': court.status === 'maintenance',
                                'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400': court.status === 'reserved'
                            }" class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium" x-text="court.status || '—'"></span>
                        </dd>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-0">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Max Players</dt>
                        <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="court.max_players || '—'"></dd>
                    </div>
                    <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Created</dt>
                        <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="court.created_at ? new Date(court.created_at).toLocaleDateString() : '—'"></dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Features</h3>
            </div>
            <div class="px-6 py-4">
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-2 rounded-lg border border-surface-200 dark:border-surface-700 px-3 py-2">
                        <span class="w-2 h-2 rounded-full" :class="court.is_indoor == 1 ? 'bg-green-500' : 'bg-surface-300'"></span>
                        <span class="text-sm text-surface-600 dark:text-surface-300">Indoor</span>
                    </div>
                    <div class="flex items-center gap-2 rounded-lg border border-surface-200 dark:border-surface-700 px-3 py-2">
                        <span class="w-2 h-2 rounded-full" :class="court.is_lighted == 1 ? 'bg-green-500' : 'bg-surface-300'"></span>
                        <span class="text-sm text-surface-600 dark:text-surface-300">Lighted</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div x-show="court.description" class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </div>
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Description</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-surface-600 dark:text-surface-300 leading-relaxed" x-text="court.description"></p>
            </div>
        </div>
    </div>
</div>

<script>
function courtShow() {
    const apiUrl = '<?= htmlspecialchars($apiUrl) ?>';

    return {
        court: {},
        loading: true,

        async init() {
            try {
                const res = await authFetch(apiUrl);
                const json = await res.json();
                if (json.data) {
                    this.court = json.data;
                }
            } catch (e) {
                console.error('Court fetch failed', e);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Failed to load court', type: 'error' } }));
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
