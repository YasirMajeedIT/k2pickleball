<?php
$title = 'Edit Page';
$breadcrumbs = [['label' => 'Content', 'url' => '#'], ['label' => 'Pages', 'url' => '/admin/pages'], ['label' => 'Edit']];
ob_start();
?>
<div x-data="pageEdit()" x-init="load()">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Edit Page</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400" x-text="form.title ? 'Editing: ' + form.title : 'Loading...'"></p>
        </div>
        <a href="/admin/pages" class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back
        </a>
    </div>

    <div x-show="loading" class="py-20 text-center">
        <svg class="animate-spin w-6 h-6 text-primary-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <form x-show="!loading" @submit.prevent="save()" class="space-y-6">
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.title" required
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Slug</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-surface-400">/p/</span>
                        <input type="text" x-model="form.slug"
                               class="flex-1 rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Content <span class="text-red-500">*</span></label>
                    <textarea x-model="form.content" rows="16" required
                              class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all font-mono"
                              placeholder="HTML content"></textarea>
                    <p class="mt-1 text-xs text-surface-400">You can use HTML. Scripts and event handlers are stripped for security.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Meta Description</label>
                    <input type="text" x-model="form.meta_description" maxlength="160"
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                <h3 class="text-sm font-semibold text-surface-900 dark:text-white">Settings</h3>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Status</label>
                    <select x-model="form.status"
                            class="rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" x-model="form.show_in_nav" :true-value="1" :false-value="0"
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Show in Navigation</span>
                            <p class="text-xs text-surface-400">Page will appear in the top nav bar</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" x-model="form.show_in_footer" :true-value="1" :false-value="0"
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Show in Footer</span>
                            <p class="text-xs text-surface-400">Page link will appear in the footer</p>
                        </div>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Sort Order</label>
                    <input type="number" x-model="form.sort_order" min="0"
                           class="w-24 rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" :disabled="saving"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 disabled:opacity-50 transition-all">
                <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Save Changes
            </button>
            <a href="/admin/pages" class="rounded-xl border border-surface-200 dark:border-surface-700 px-6 py-2.5 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-all">Cancel</a>
        </div>
    </form>

    <div x-show="toast.show" x-cloak x-transition class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border px-4 py-3 shadow-lg text-sm font-medium"
         :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/30 dark:text-emerald-400' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-400'">
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
function pageEdit() {
    return {
        form: { title:'', slug:'', content:'', meta_description:'', status:'draft', show_in_nav:0, show_in_footer:0, sort_order:0 },
        saving:false, loading:true,
        toast: { show:false, type:'success', message:'' },
        async load() {
            const id = window.location.pathname.split('/').filter(Boolean).find((_, i, a) => a[i-1] === 'pages');
            try {
                const res = await fetch('/api/custom-pages/' + id, { headers: { 'Authorization':'Bearer '+(localStorage.getItem('admin_token')||'') }});
                const json = await res.json();
                if (json.data) {
                    const d = json.data;
                    this.form = { title:d.title, slug:d.slug, content:d.content||'', meta_description:d.meta_description||'', status:d.status, show_in_nav:parseInt(d.show_in_nav)||0, show_in_footer:parseInt(d.show_in_footer)||0, sort_order:parseInt(d.sort_order)||0 };
                }
            } catch(e) { this.showToast('Failed to load page', 'error'); }
            this.loading = false;
        },
        async save() {
            const id = window.location.pathname.split('/').filter(Boolean).find((_, i, a) => a[i-1] === 'pages');
            this.saving = true;
            try {
                const res = await fetch('/api/custom-pages/' + id, {
                    method: 'PUT',
                    headers: { 'Content-Type':'application/json', 'Authorization':'Bearer '+(localStorage.getItem('admin_token')||'') },
                    body: JSON.stringify(this.form)
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.error || 'Save failed');
                this.showToast('Page saved');
            } catch(e) { this.showToast(e.message, 'error'); }
            this.saving = false;
        },
        showToast(msg, type='success') { this.toast = {show:true,type,message:msg}; setTimeout(()=>this.toast.show=false, 4000); }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
