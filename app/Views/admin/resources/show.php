<?php
$title = 'Resource Details';
$breadcrumbs = [
    ['label' => 'Resources', 'url' => ($baseUrl ?? '') . '/admin/resources'],
    ['label' => 'View'],
];
$backUrl  = ($baseUrl ?? '') . '/admin/resources';
$editUrl  = ($baseUrl ?? '') . '/admin/resources/' . ($id ?? '') . '/edit';
$apiUrl   = ($baseUrl ?? '') . '/api/resources/' . ($id ?? '');

ob_start();
?>
<div x-data="resourceShow()" x-init="init()">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-surface-900 dark:text-white" x-text="resource.name || 'Resource Details'"></h2>
            <p class="mt-0.5 text-sm text-surface-500" x-text="resource.description || ''"></p>
            <span class="mt-1 inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-500/10 px-2.5 py-0.5 text-xs font-semibold text-primary-600 dark:text-primary-400"
                  x-text="({checkbox:'Checkbox Group',selectbox:'Select Box',radio:'Radio Buttons',input:'Input Field'})[resource.field_type] || 'Checkbox Group'"></span>
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
                Edit Resource
            </a>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-20 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
        <p class="text-sm text-surface-400">Loading resource...</p>
    </div>

    <!-- Values Section -->
    <div x-show="!loading" class="space-y-6">

        <!-- Add Value Form -->
        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Values</h3>
                <span class="ml-auto inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-500/10 px-2.5 py-0.5 text-xs font-semibold text-primary-600 dark:text-primary-400"
                      x-text="values.length"></span>
            </div>

            <!-- Add new value row -->
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800 bg-surface-50/30 dark:bg-surface-800/20">
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-surface-500 mb-1">Name</label>
                        <input type="text" x-model="newValue.name" placeholder="Value name..."
                               class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-surface-500 mb-1">Description</label>
                        <input type="text" x-model="newValue.description" placeholder="Optional description..."
                               class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-semibold text-surface-500 mb-1">Order</label>
                        <input type="number" x-model="newValue.sort_order" placeholder="#"
                               class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                    </div>
                    <button x-on:click="addValue()" :disabled="!newValue.name || addingValue"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add
                    </button>
                </div>
            </div>

            <!-- Values list -->
            <div class="divide-y divide-surface-100 dark:divide-surface-800">
                <template x-for="val in values" :key="val.id">
                    <div class="flex items-center gap-4 px-6 py-3 hover:bg-primary-50/20 dark:hover:bg-surface-800/40 transition-colors">
                        <div class="flex-shrink-0 w-10 text-center">
                            <span class="inline-flex items-center justify-center h-7 w-7 rounded-lg bg-surface-100 dark:bg-surface-800 text-xs font-bold text-surface-500" x-text="val.sort_order"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <!-- View mode -->
                            <template x-if="editingId !== val.id">
                                <div>
                                    <p class="text-sm font-medium text-surface-800 dark:text-surface-100" x-text="val.name"></p>
                                    <p class="text-xs text-surface-400 truncate" x-text="val.description || ''"></p>
                                </div>
                            </template>
                            <!-- Edit mode -->
                            <template x-if="editingId === val.id">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="editForm.name" class="flex-1 rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 px-2 py-1 text-sm dark:text-white">
                                    <input type="text" x-model="editForm.description" placeholder="Description" class="flex-1 rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 px-2 py-1 text-sm dark:text-white">
                                    <input type="number" x-model="editForm.sort_order" class="w-16 rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 px-2 py-1 text-sm dark:text-white">
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center gap-1">
                            <template x-if="editingId !== val.id">
                                <div class="flex gap-1">
                                    <button x-on:click="startEdit(val)" class="rounded-lg p-1.5 text-surface-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button x-on:click="deleteValue(val.id)" class="rounded-lg p-1.5 text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="editingId === val.id">
                                <div class="flex gap-1">
                                    <button x-on:click="saveEdit(val.id)" class="rounded-lg p-1.5 text-green-500 hover:bg-green-50 dark:hover:bg-green-500/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button x-on:click="editingId = null" class="rounded-lg p-1.5 text-surface-400 hover:text-surface-600 hover:bg-surface-100 dark:hover:bg-surface-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="values.length === 0">
                    <div class="px-6 py-8 text-center">
                        <p class="text-sm text-surface-400">No values defined yet. Add one above.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function resourceShow() {
    const apiUrl = '<?= htmlspecialchars($apiUrl) ?>';

    return {
        resource: {},
        values: [],
        loading: true,
        newValue: { name: '', description: '', sort_order: '' },
        addingValue: false,
        editingId: null,
        editForm: { name: '', description: '', sort_order: '' },

        async init() {
            await this.loadResource();
        },

        async loadResource() {
            try {
                const res = await authFetch(apiUrl);
                const json = await res.json();
                if (json.data) {
                    this.resource = json.data;
                    this.values = json.data.values || [];
                }
            } catch (e) {
                console.error('Resource fetch failed', e);
            } finally {
                this.loading = false;
            }
        },

        async addValue() {
            if (!this.newValue.name) return;
            this.addingValue = true;
            try {
                const res = await authFetch(apiUrl + '/values', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.newValue)
                });
                const json = await res.json();
                if (res.ok) {
                    this.values = json.data.values || [];
                    this.newValue = { name: '', description: '', sort_order: '' };
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Value added', type: 'success' } }));
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
            this.addingValue = false;
        },

        startEdit(val) {
            this.editingId = val.id;
            this.editForm = { name: val.name, description: val.description || '', sort_order: val.sort_order };
        },

        async saveEdit(valId) {
            try {
                const res = await authFetch(apiUrl + '/values/' + valId, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.editForm)
                });
                const json = await res.json();
                if (res.ok) {
                    this.values = json.data.values || [];
                    this.editingId = null;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Value updated', type: 'success' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
        },

        async deleteValue(valId) {
            if (!confirm('Delete this value?')) return;
            try {
                const res = await authFetch(apiUrl + '/values/' + valId, { method: 'DELETE' });
                const json = await res.json();
                if (res.ok) {
                    this.values = json.data.values || [];
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Value removed', type: 'success' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
        },
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
