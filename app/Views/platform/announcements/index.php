<?php
$title = 'Announcements';
$breadcrumbs = [['label' => 'Announcements']];

ob_start();
?>
<div x-data="announcementsPage()" x-init="init()">
    <!-- Header -->
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-80 group">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" x-on:input.debounce.300ms="fetchData()" placeholder="Search announcements..."
                   class="w-full rounded-xl border border-surface-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft placeholder:text-surface-400">
        </div>
        <button @click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Announcement
        </button>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="relative"><div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div><div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div></div>
    </div>

    <!-- List -->
    <div x-show="!loading" class="space-y-3">
        <template x-for="ann in rows" :key="ann.id">
            <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="px-6 py-4 flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span :class="{
                                'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400': ann.type === 'info',
                                'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400': ann.type === 'warning',
                                'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400': ann.type === 'critical',
                                'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400': ann.type === 'maintenance'
                            }" class="inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize" x-text="ann.type"></span>
                            <span :class="ann.is_active ? 'text-green-600' : 'text-surface-400'" class="text-xs font-medium" x-text="ann.is_active ? 'Active' : 'Inactive'"></span>
                            <span class="text-xs text-surface-400 capitalize" x-text="'Target: ' + ann.target"></span>
                        </div>
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white" x-text="ann.title"></h4>
                        <p class="text-xs text-surface-500 mt-1 line-clamp-2" x-text="ann.message"></p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-surface-400">
                            <span x-show="ann.starts_at" x-text="'From: ' + new Date(ann.starts_at).toLocaleDateString()"></span>
                            <span x-show="ann.ends_at" x-text="'To: ' + new Date(ann.ends_at).toLocaleDateString()"></span>
                            <span x-text="'Created: ' + new Date(ann.created_at).toLocaleDateString()"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button @click="openEdit(ann)" class="text-xs font-medium text-amber-500 hover:text-amber-700">Edit</button>
                        <button @click="deleteAnn(ann.id)" class="text-xs font-medium text-red-500 hover:text-red-700">Delete</button>
                    </div>
                </div>
            </div>
        </template>
        <template x-if="rows.length === 0 && !loading">
            <div class="text-center py-12"><p class="text-sm text-surface-400">No announcements</p></div>
        </template>
    </div>

    <!-- Modal -->
    <template x-if="showModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-surface-950/50 backdrop-blur-sm" @click.self="showModal = false">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-700 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between sticky top-0 bg-white dark:bg-surface-800 z-10">
                    <h3 class="font-bold text-surface-900 dark:text-white" x-text="editId ? 'Edit Announcement' : 'New Announcement'"></h3>
                    <button @click="showModal = false" class="text-surface-400 hover:text-surface-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Title *</label><input x-model="form.title" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                    <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Message *</label><textarea x-model="form.message" rows="4" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></textarea></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Type</label>
                            <select x-model="form.type" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm">
                                <option value="info">Info</option><option value="warning">Warning</option><option value="critical">Critical</option><option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Target</label>
                            <select x-model="form.target" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm">
                                <option value="all">All Organizations</option><option value="specific">Specific</option>
                            </select>
                        </div>
                    </div>
                    <div x-show="form.target === 'specific'">
                        <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Organization IDs (comma-separated)</label>
                        <input x-model="form.target_org_ids" placeholder="1,2,3" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Starts At</label><input x-model="form.starts_at" type="datetime-local" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                        <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Ends At</label><input x-model="form.ends_at" type="datetime-local" class="w-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-3 py-2 text-sm"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input x-model="form.is_active" type="checkbox" class="rounded border-surface-300 text-primary-600" :true-value="1" :false-value="0">
                        <label class="text-sm text-surface-700 dark:text-surface-300">Active</label>
                    </div>
                    <button @click="saveAnn()" :disabled="submitting" class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50" x-text="submitting ? 'Saving...' : (editId ? 'Update' : 'Create')"></button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function announcementsPage() {
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    return {
        rows: [], loading: true, search: '', showModal: false, editId: null, submitting: false,
        form: { title: '', message: '', type: 'info', target: 'all', target_org_ids: '', starts_at: '', ends_at: '', is_active: 1 },
        async init() { await this.fetchData(); },
        async fetchData() {
            this.loading = true;
            try {
                const res = await fetch(APP_BASE + '/api/platform/announcements?per_page=100', { headers });
                const json = await res.json(); this.rows = json.data || [];
            } catch (e) { console.error(e); }
            this.loading = false;
        },
        openCreate() {
            this.editId = null;
            this.form = { title: '', message: '', type: 'info', target: 'all', target_org_ids: '', starts_at: '', ends_at: '', is_active: 1 };
            this.showModal = true;
        },
        openEdit(ann) {
            this.editId = ann.id;
            this.form = { title: ann.title, message: ann.message, type: ann.type || 'info', target: ann.target || 'all', target_org_ids: ann.target_org_ids || '', starts_at: ann.starts_at ? ann.starts_at.replace(' ', 'T').substring(0, 16) : '', ends_at: ann.ends_at ? ann.ends_at.replace(' ', 'T').substring(0, 16) : '', is_active: ann.is_active ? 1 : 0 };
            this.showModal = true;
        },
        async saveAnn() {
            this.submitting = true;
            const url = this.editId ? APP_BASE + '/api/platform/announcements/' + this.editId : APP_BASE + '/api/platform/announcements';
            const method = this.editId ? 'PUT' : 'POST';
            try {
                const res = await fetch(url, { method, headers: { ...headers, 'Content-Type': 'application/json' }, body: JSON.stringify(this.form) });
                const json = await res.json();
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.editId ? 'Updated' : 'Created', type: 'success' } })); this.showModal = false; this.fetchData(); }
                else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            this.submitting = false;
        },
        async deleteAnn(id) {
            if (!confirm('Delete this announcement?')) return;
            try {
                const res = await fetch(APP_BASE + '/api/platform/announcements/' + id, { method: 'DELETE', headers });
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Deleted', type: 'success' } })); this.fetchData(); }
                else { const json = await res.json(); window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
