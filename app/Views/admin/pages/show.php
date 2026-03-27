<?php
$title = 'Page Details';
$breadcrumbs = [['label' => 'Content', 'url' => '#'], ['label' => 'Pages', 'url' => '/admin/pages'], ['label' => 'Details']];
ob_start();
?>
<div x-data="pageShow()" x-init="load()">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white" x-text="page.title || 'Loading...'"></h1>
            <p class="mt-1 text-sm text-surface-400" x-show="page.slug">/p/<span x-text="page.slug"></span></p>
        </div>
        <div class="flex items-center gap-2">
            <a :href="'/admin/pages/' + pageId + '/edit'"
               class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                Edit
            </a>
            <a href="/admin/pages" class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-all">
                Back
            </a>
        </div>
    </div>

    <div x-show="loading" class="py-20 text-center">
        <svg class="animate-spin w-6 h-6 text-primary-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <div x-show="!loading" class="space-y-6">
        <!-- Info bar -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="grid grid-cols-2 md:grid-cols-5 divide-x divide-surface-100 dark:divide-surface-800">
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">Status</p>
                    <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                          :class="page.status === 'published' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400' : page.status === 'draft' ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-surface-100 text-surface-600 dark:bg-surface-800 dark:text-surface-400'"
                          x-text="page.status"></span>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">In Nav</p>
                    <p class="mt-1 text-sm font-medium text-surface-900 dark:text-white" x-text="page.show_in_nav == 1 ? 'Yes' : 'No'"></p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">In Footer</p>
                    <p class="mt-1 text-sm font-medium text-surface-900 dark:text-white" x-text="page.show_in_footer == 1 ? 'Yes' : 'No'"></p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">Sort Order</p>
                    <p class="mt-1 text-sm font-medium text-surface-900 dark:text-white" x-text="page.sort_order"></p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">Created</p>
                    <p class="mt-1 text-sm font-medium text-surface-900 dark:text-white" x-text="page.created_at ? new Date(page.created_at).toLocaleDateString() : '—'"></p>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-surface-900 dark:text-white">Content Preview</h3>
                <a :href="'/p/' + page.slug" target="_blank" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">View live ↗</a>
            </div>
            <div class="p-6 prose prose-sm dark:prose-invert max-w-none" x-html="page.content"></div>
        </div>

        <!-- Meta -->
        <div x-show="page.meta_description" class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                <h3 class="text-sm font-semibold text-surface-900 dark:text-white">SEO</h3>
            </div>
            <div class="p-6">
                <p class="text-xs text-surface-400 font-medium uppercase tracking-wider mb-1">Meta Description</p>
                <p class="text-sm text-surface-700 dark:text-surface-300" x-text="page.meta_description"></p>
            </div>
        </div>
    </div>
</div>

<script>
function pageShow() {
    const parts = window.location.pathname.split('/').filter(Boolean);
    const id = parts[parts.indexOf('pages') + 1];
    return {
        page: {}, pageId: id, loading: true,
        async load() {
            try {
                const res = await fetch('/api/custom-pages/' + this.pageId, { headers: { 'Authorization':'Bearer '+(localStorage.getItem('admin_token')||'') }});
                const json = await res.json();
                this.page = json.data || {};
            } catch(e) {}
            this.loading = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
