<?php
$title = 'Pages';
$breadcrumbs = [['label' => 'Content', 'url' => '#'], ['label' => 'Pages']];
ob_start();
?>
<div x-data="pagesIndex()" x-init="load()">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Pages</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Create static pages for your public website — info pages, policies, hiring, etc.</p>
        </div>
        <a href="/admin/pages/create"
           class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Page
        </a>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-20 text-center">
        <svg class="animate-spin w-6 h-6 text-primary-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <!-- Empty state -->
    <div x-show="!loading && pages.length === 0" class="rounded-2xl border-2 border-dashed border-surface-200 dark:border-surface-700 px-6 py-16 text-center">
        <svg class="mx-auto w-12 h-12 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        <h3 class="mt-4 text-lg font-semibold text-surface-700 dark:text-surface-300">No pages yet</h3>
        <p class="mt-1 text-sm text-surface-500">Create your first page to add custom content to your website.</p>
        <a href="/admin/pages/create" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create Page
        </a>
    </div>

    <!-- Pages table -->
    <div x-show="!loading && pages.length > 0" class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-surface-50 dark:bg-surface-800/50">
                <tr class="border-b border-surface-200 dark:border-surface-700/50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Title</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider hidden md:table-cell">Slug</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-surface-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-surface-500 uppercase tracking-wider hidden lg:table-cell">Nav</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-surface-500 uppercase tracking-wider hidden lg:table-cell">Footer</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-surface-500 uppercase tracking-wider w-28">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="page in pages" :key="page.id">
                    <tr class="border-b border-surface-100 dark:border-surface-800/50 hover:bg-surface-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-3">
                            <a :href="'/admin/pages/' + page.id" class="font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition-colors" x-text="page.title"></a>
                            <p class="text-xs text-surface-400 mt-0.5" x-text="page.created_by_name ? 'by ' + page.created_by_name : ''"></p>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell">
                            <code class="text-xs bg-surface-100 dark:bg-surface-800 px-2 py-1 rounded-md text-surface-600 dark:text-surface-400" x-text="'/p/' + page.slug"></code>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                  :class="page.status === 'published' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400' : page.status === 'draft' ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-surface-100 text-surface-600 dark:bg-surface-800 dark:text-surface-400'"
                                  x-text="page.status"></span>
                        </td>
                        <td class="px-5 py-3 text-center hidden lg:table-cell">
                            <span x-show="page.show_in_nav == 1" class="text-emerald-500">✓</span>
                            <span x-show="page.show_in_nav != 1" class="text-surface-300">—</span>
                        </td>
                        <td class="px-5 py-3 text-center hidden lg:table-cell">
                            <span x-show="page.show_in_footer == 1" class="text-emerald-500">✓</span>
                            <span x-show="page.show_in_footer != 1" class="text-surface-300">—</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a :href="'/admin/pages/' + page.id + '/edit'" title="Edit"
                                   class="p-1.5 rounded-lg text-surface-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                </a>
                                <button @click="deletePage(page)" title="Delete"
                                        class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Toast -->
    <div x-show="toast.show" x-cloak x-transition class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border px-4 py-3 shadow-lg text-sm font-medium"
         :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/30 dark:text-emerald-400' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-400'">
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
function pagesIndex() {
    return {
        pages: [], loading: true,
        toast: { show: false, type: 'success', message: '' },
        async load() {
            this.loading = true;
            try {
                const res = await fetch('/api/custom-pages', { headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('admin_token') || '') }});
                const json = await res.json();
                this.pages = json.data || [];
            } catch(e) { this.pages = []; }
            this.loading = false;
        },
        async deletePage(page) {
            if (!confirm('Delete "' + page.title + '"? This cannot be undone.')) return;
            try {
                await fetch('/api/custom-pages/' + page.id, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('admin_token') || '') }
                });
                this.pages = this.pages.filter(p => p.id !== page.id);
                this.showToast('Page deleted');
            } catch(e) { this.showToast('Delete failed', 'error'); }
        },
        showToast(msg, type='success') { this.toast = {show:true,type,message:msg}; setTimeout(()=>this.toast.show=false, 3000); }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
