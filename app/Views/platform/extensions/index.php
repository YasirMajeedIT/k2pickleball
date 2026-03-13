<?php
$title = 'Extensions';
$breadcrumbs = [['label' => 'Extensions']];

ob_start();
?>
<div x-data="extensionsPage()" x-init="init()">
    <!-- Header -->
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-80 group">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" x-on:input.debounce.300ms="fetchData()" placeholder="Search extensions..."
                   class="w-full rounded-xl border border-surface-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft placeholder:text-surface-400">
        </div>
        <button @click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Extension
        </button>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="relative"><div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div><div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div></div>
        <p class="text-sm text-surface-400">Loading extensions...</p>
    </div>

    <!-- Grid -->
    <div x-show="!loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="ext in rows" :key="ext.id">
            <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden hover:shadow-medium transition-shadow">
                <div class="px-5 py-4 border-b border-surface-100 dark:border-surface-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white" x-text="ext.name"></h4>
                        <p class="text-xs text-surface-400 font-mono" x-text="ext.slug"></p>
                    </div>
                    <span :class="ext.is_active ? 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' : 'bg-surface-100 text-surface-600'" class="rounded-full px-2 py-0.5 text-xs font-medium" x-text="ext.is_active ? 'Active' : 'Inactive'"></span>
                </div>
                <div class="px-5 py-3 space-y-2">
                    <p class="text-xs text-surface-500 line-clamp-2" x-text="ext.description || 'No description'"></p>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-surface-400">v<span x-text="ext.version || '1.0.0'"></span></span>
                        <span class="text-surface-400"><span x-text="ext.category || 'General'"></span></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-primary-600" x-text="ext.price_monthly > 0 ? '$' + parseFloat(ext.price_monthly).toFixed(2) + '/mo' : 'Free'"></span>
                        <span class="text-surface-400"><span x-text="ext.active_installs || 0"></span> installs</span>
                    </div>
                </div>
                <div class="px-5 py-3 border-t border-surface-100 dark:border-surface-800 flex items-center justify-end gap-2">
                    <button @click="openEdit(ext)" class="text-xs font-medium text-amber-500 hover:text-amber-700">Edit</button>
                    <button @click="deleteExt(ext.id)" class="text-xs font-medium text-red-500 hover:text-red-700">Delete</button>
                </div>
            </div>
        </template>
        <template x-if="rows.length === 0 && !loading">
            <div class="col-span-full text-center py-12"><p class="text-sm text-surface-400">No extensions found</p></div>
        </template>
    </div>

    <!-- Create/Edit Modal -->
    <template x-if="showModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-surface-950/50 backdrop-blur-sm" @click.self="showModal = false">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-700 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between sticky top-0 bg-white dark:bg-surface-800 z-10">
                    <h3 class="font-bold text-surface-900 dark:text-white" x-text="editId ? 'Edit Extension' : 'New Extension'"></h3>
                    <button @click="showModal = false" class="text-surface-400 hover:text-surface-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Name *</label><input x-model="form.name" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                        <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Slug *</label><input x-model="form.slug" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                    </div>
                    <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Description</label><textarea x-model="form.description" rows="3" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></textarea></div>
                    <div class="grid grid-cols-3 gap-4">
                        <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Version</label><input x-model="form.version" placeholder="1.0.0" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                        <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Category</label><input x-model="form.category" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                        <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Sort Order</label><input x-model="form.sort_order" type="number" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Monthly Price ($)</label><input x-model="form.price_monthly" type="number" step="0.01" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                        <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Yearly Price ($)</label><input x-model="form.price_yearly" type="number" step="0.01" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input x-model="form.is_active" type="checkbox" class="rounded border-surface-300 text-primary-600" :true-value="1" :false-value="0">
                        <label class="text-sm text-surface-700 dark:text-surface-300">Active</label>
                    </div>
                    <template x-if="errors && Object.keys(errors).length > 0">
                        <div class="rounded-xl border border-red-200 bg-red-50 dark:bg-red-500/5 p-3">
                            <template x-for="(msgs, field) in errors" :key="field">
                                <p class="text-xs text-red-600" x-text="field + ': ' + (Array.isArray(msgs) ? msgs.join(', ') : msgs)"></p>
                            </template>
                        </div>
                    </template>
                    <button @click="saveExt()" :disabled="submitting" class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50" x-text="submitting ? 'Saving...' : (editId ? 'Update Extension' : 'Create Extension')"></button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function extensionsPage() {
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    return {
        rows: [], loading: true, search: '', showModal: false, editId: null, submitting: false, errors: {},
        form: { name: '', slug: '', description: '', version: '1.0.0', category: '', price_monthly: 0, price_yearly: 0, is_active: 1, sort_order: 0 },
        async init() { await this.fetchData(); },
        async fetchData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({ per_page: 100 }); if (this.search) params.set('search', this.search);
                const res = await fetch(APP_BASE + '/api/platform/extensions?' + params.toString(), { headers });
                const json = await res.json(); this.rows = json.data || [];
            } catch (e) { console.error(e); }
            this.loading = false;
        },
        openCreate() {
            this.editId = null; this.errors = {};
            this.form = { name: '', slug: '', description: '', version: '1.0.0', category: '', price_monthly: 0, price_yearly: 0, is_active: 1, sort_order: 0 };
            this.showModal = true;
        },
        openEdit(ext) {
            this.editId = ext.id; this.errors = {};
            this.form = { name: ext.name, slug: ext.slug, description: ext.description || '', version: ext.version || '1.0.0', category: ext.category || '', price_monthly: ext.price_monthly || 0, price_yearly: ext.price_yearly || 0, is_active: ext.is_active ? 1 : 0, sort_order: ext.sort_order || 0 };
            this.showModal = true;
        },
        async saveExt() {
            this.submitting = true; this.errors = {};
            const url = this.editId ? APP_BASE + '/api/platform/extensions/' + this.editId : APP_BASE + '/api/platform/extensions';
            const method = this.editId ? 'PUT' : 'POST';
            try {
                const res = await fetch(url, { method, headers: { ...headers, 'Content-Type': 'application/json' }, body: JSON.stringify(this.form) });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.editId ? 'Extension updated' : 'Extension created', type: 'success' } }));
                    this.showModal = false; this.fetchData();
                } else {
                    this.errors = json.errors || {}; if (json.message) window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message, type: 'error' } }));
                }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            this.submitting = false;
        },
        async deleteExt(id) {
            if (!confirm('Delete this extension?')) return;
            try {
                const res = await fetch(APP_BASE + '/api/platform/extensions/' + id, { method: 'DELETE', headers });
                const json = await res.json();
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Extension deleted', type: 'success' } })); this.fetchData(); }
                else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
