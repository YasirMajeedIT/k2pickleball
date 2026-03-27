<?php
$title = 'Navigation Menu';
$breadcrumbs = [
    ['label' => 'Design', 'url' => '#'],
    ['label' => 'Navigation Menu'],
];

ob_start();
?>
<div x-data="navEditor()" x-init="load()">

    <!-- Page header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Navigation Menu</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Configure the public website navigation. Drag to reorder. Toggle visibility per item.</p>
        </div>
        <div class="flex items-center gap-3">
            <a :href="previewUrl" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-semibold text-surface-700 dark:text-surface-200 hover:border-primary-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Preview Site
            </a>
            <button @click="save()" :disabled="saving"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-all disabled:opacity-50">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
            </button>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-20 text-center">
        <svg class="animate-spin w-6 h-6 text-primary-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
        <p class="mt-3 text-sm text-surface-500">Loading navigation...</p>
    </div>

    <!-- Nav Items Table -->
    <div x-show="!loading" class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">

        <!-- Info banner -->
        <div class="flex items-start gap-3 border-b border-surface-100 dark:border-surface-800 bg-primary-50 dark:bg-primary-500/5 px-5 py-3.5">
            <svg class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-primary-700 dark:text-primary-300">System items (🔒) set the URL and cannot be deleted. Custom items can be added, reordered, and removed freely. The <strong>Schedule</strong> dropdown automatically shows your active categories as sub-items.</p>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-surface-50 dark:bg-surface-800/50">
                <tr class="border-b border-surface-200 dark:border-surface-700/50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider w-8"></th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Label</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider hidden md:table-cell">URL</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider hidden lg:table-cell">Visibility</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-surface-500 uppercase tracking-wider w-20">Show</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-surface-500 uppercase tracking-wider w-20">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in items" :key="item.id || item._tempId">
                    <tr class="border-b border-surface-100 dark:border-surface-800/50 hover:bg-surface-50 dark:hover:bg-white/[0.02] transition-colors"
                        :class="{'bg-amber-50/30 dark:bg-amber-500/5': item._editing}">
                        <!-- Drag handle -->
                        <td class="px-3 py-3 text-surface-300 dark:text-surface-600 cursor-grab">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </td>
                        <!-- Label (inline edit) -->
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span x-show="item.is_system" class="text-xs" title="System item">🔒</span>
                                <input x-model="item.label"
                                       class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-1.5 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all"
                                       :placeholder="item.label || 'Label'">
                            </div>
                        </td>
                        <!-- URL -->
                        <td class="px-5 py-3 hidden md:table-cell">
                            <input x-model="item.url" :readonly="item.is_system == 1"
                                   class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-1.5 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all read-only:bg-surface-50 dark:read-only:bg-surface-800/50 read-only:text-surface-400"
                                   :placeholder="item.url || '/page-url'">
                        </td>
                        <!-- Visibility rule -->
                        <td class="px-5 py-3 hidden lg:table-cell">
                            <select x-model="item.visibility_rule"
                                    class="rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-1.5 text-sm text-surface-700 dark:text-surface-300 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none">
                                <option value="">Always visible</option>
                                <option value="auth_only">Signed-in only</option>
                                <option value="guest_only">Guests only</option>
                                <option value="has_memberships">Only if memberships exist</option>
                            </select>
                        </td>
                        <!-- Toggle -->
                        <td class="px-5 py-3 text-center">
                            <button @click="item.is_visible = item.is_visible ? 0 : 1"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                                    :class="item.is_visible ? 'bg-primary-500' : 'bg-surface-300 dark:bg-surface-600'">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                                      :class="item.is_visible ? 'translate-x-6' : 'translate-x-1'"></span>
                            </button>
                        </td>
                        <!-- Delete (custom items only) -->
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button x-show="index > 0" @click="moveUp(index)" title="Move up"
                                        class="p-1.5 rounded-lg text-surface-400 hover:text-surface-600 dark:hover:text-surface-200 hover:bg-surface-100 dark:hover:bg-white/[0.04] transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </button>
                                <button x-show="index < items.length - 1" @click="moveDown(index)" title="Move down"
                                        class="p-1.5 rounded-lg text-surface-400 hover:text-surface-600 dark:hover:text-surface-200 hover:bg-surface-100 dark:hover:bg-white/[0.04] transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <button x-show="!item.is_system" @click="removeItem(index)" title="Delete"
                                        class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Add custom item -->
        <div class="px-5 py-4 border-t border-surface-100 dark:border-surface-800">
            <button @click="addItem()"
                    class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-surface-300 dark:border-surface-700 px-4 py-2.5 text-sm font-medium text-surface-500 dark:text-surface-400 hover:border-primary-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Custom Link
            </button>
        </div>
    </div>

    <!-- Toast -->
    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border px-4 py-3 shadow-lg text-sm font-medium"
         :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/30 dark:text-emerald-400' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-400'">
        <svg x-show="toast.type === 'success'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <svg x-show="toast.type === 'error'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
function navEditor() {
    return {
        items: [],
        loading: true,
        saving: false,
        previewUrl: '/',
        toast: { show: false, type: 'success', message: '' },
        _tempIdCounter: 1000,

        async load() {
            this.loading = true;
            try {
                const res = await fetch('/api/navigation');
                const json = await res.json();
                this.items = (json.data || []).map(i => ({ ...i }));
                // Detect preview URL from org subdomain
                const host = window.location.host;
                this.previewUrl = window.location.protocol + '//' + host.replace(/^admin\./, '');
            } catch(e) { this.items = []; }
            this.loading = false;
        },

        addItem() {
            this.items.push({
                _tempId: 'new_' + (++this._tempIdCounter),
                id: null,
                label: '',
                url: '/',
                type: 'link',
                target: '_self',
                is_system: 0,
                system_key: null,
                is_visible: 1,
                visibility_rule: '',
                sort_order: (this.items.length + 1) * 10,
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        moveUp(index) {
            if (index <= 0) return;
            [this.items[index - 1], this.items[index]] = [this.items[index], this.items[index - 1]];
            this.items = [...this.items];
        },

        moveDown(index) {
            if (index >= this.items.length - 1) return;
            [this.items[index], this.items[index + 1]] = [this.items[index + 1], this.items[index]];
            this.items = [...this.items];
        },

        async save() {
            this.saving = true;
            // Rebuild sort_order based on current position
            const payload = this.items.map((item, i) => ({
                id: item.id,
                label: item.label,
                url: item.url,
                type: item.type || 'link',
                target: item.target || '_self',
                is_system: item.is_system || 0,
                system_key: item.system_key || null,
                is_visible: item.is_visible ? 1 : 0,
                visibility_rule: item.visibility_rule || null,
                sort_order: (i + 1) * 10,
            }));
            try {
                const res = await fetch('/api/navigation', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + (localStorage.getItem('access_token') || ''),
                    },
                    body: JSON.stringify({ items: payload }),
                });
                const json = await res.json();
                if (json.success !== false && res.ok) {
                    this.showToast('Navigation saved successfully', 'success');
                    // Reload to get fresh ids
                    await this.load();
                } else {
                    this.showToast(json.message || 'Save failed', 'error');
                }
            } catch(e) {
                this.showToast('Network error, please try again', 'error');
            }
            this.saving = false;
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, type, message };
            setTimeout(() => { this.toast.show = false; }, 4000);
        },
    };
}
</script>
<?php
$content = ob_get_clean();
include dirname(dirname(__DIR__)) . '/layouts/admin.php';
