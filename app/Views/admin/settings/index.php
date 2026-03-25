<?php
$title = 'Settings';
$breadcrumbs = [['label' => 'Settings']];

ob_start();
?>
<div x-data="settingsManager()" x-init="loadSettings()" class="space-y-6">
    <!-- Settings Groups Tabs -->
    <div class="border-b border-surface-200 dark:border-surface-700/50">
        <nav class="flex space-x-1 overflow-x-auto" aria-label="Settings groups">
            <template x-for="group in groups" :key="group">
                <button @click="activeGroup = group; loadSettings()"
                    :class="activeGroup === group
                        ? 'border-primary-500 text-primary-600 dark:text-primary-400 bg-primary-50/50 dark:bg-primary-500/5'
                        : 'border-transparent text-surface-500 hover:text-surface-700 hover:border-surface-300 dark:text-surface-400'"
                    class="whitespace-nowrap border-b-2 rounded-t-lg px-4 py-3 text-sm font-medium capitalize transition-all">
                    <span x-text="group"></span>
                </button>
            </template>
        </nav>
    </div>

    <!-- Settings Table -->
    <div class="rounded-2xl bg-white dark:bg-surface-800/60 shadow-soft border border-surface-200/60 dark:border-surface-700/50 overflow-hidden">
        <div class="flex items-center justify-between border-b border-surface-200 dark:border-surface-700/50 px-6 py-4">
            <div>
                <h3 class="text-base font-semibold text-surface-900 dark:text-white capitalize" x-text="activeGroup + ' Settings'"></h3>
                <p class="text-xs text-surface-500 mt-0.5">Manage configuration for this group</p>
            </div>
            <button @click="showAddModal = true"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2.5 text-sm font-medium text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Setting
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-surface-50/50 dark:bg-surface-800/80">
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Key</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Value</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Type</th>
                        <th class="px-6 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-surface-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 dark:divide-surface-700/50">
                    <template x-if="loading">
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center gap-2 text-surface-500">
                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Loading...
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loading && settings.length === 0">
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-10 w-10 text-surface-300 dark:text-surface-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-medium text-surface-500">No settings in this group</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="setting in settings" :key="setting.key">
                        <tr class="hover:bg-primary-50/30 dark:hover:bg-primary-500/5 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-surface-900 dark:text-white font-mono" x-text="setting.key"></td>
                            <td class="px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                <template x-if="editingKey !== setting.key">
                                    <span x-text="formatValue(setting.value, setting.type)" class="max-w-xs truncate block"></span>
                                </template>
                                <template x-if="editingKey === setting.key">
                                    <div class="flex items-center gap-2">
                                        <template x-if="editType === 'boolean'">
                                            <select x-model="editValue" class="rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-800 text-sm shadow-soft focus:border-primary-500 focus:ring-primary-500">
                                                <option value="1">True</option>
                                                <option value="0">False</option>
                                            </select>
                                        </template>
                                        <template x-if="editType === 'json'">
                                            <textarea x-model="editValue" rows="3" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-800 text-sm font-mono shadow-soft focus:border-primary-500 focus:ring-primary-500"></textarea>
                                        </template>
                                        <template x-if="editType !== 'boolean' && editType !== 'json'">
                                            <input type="text" x-model="editValue" class="rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-800 text-sm shadow-soft focus:border-primary-500 focus:ring-primary-500">
                                        </template>
                                        <button @click="saveSetting(setting.key)" class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold">Save</button>
                                        <button @click="editingKey = null" class="text-surface-400 hover:text-surface-600 text-sm">Cancel</button>
                                    </div>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block rounded-lg bg-surface-100 dark:bg-surface-700 border border-surface-200 dark:border-surface-600 px-2.5 py-0.5 text-xs font-medium text-surface-600 dark:text-surface-300" x-text="setting.type || 'string'"></span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="startEdit(setting)" class="text-amber-500 hover:text-amber-600 text-sm font-semibold" x-show="editingKey !== setting.key">Edit</button>
                                <button @click="deleteSetting(setting.key)" class="text-red-500 hover:text-red-600 text-sm font-semibold">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Setting Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center" @click.self="showAddModal = false" style="display:none;">
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-surface-950/60 backdrop-blur-sm"></div>
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md rounded-2xl bg-white dark:bg-surface-800 p-6 shadow-xl border border-surface-200 dark:border-surface-700">
            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-1">Add Setting</h3>
            <p class="text-sm text-surface-500 mb-5">Add a new configuration key to this group</p>
            <form @submit.prevent="addSetting()" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Key</label>
                    <input type="text" x-model="newSetting.key" required class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 px-4 py-3 text-sm shadow-soft focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Value</label>
                    <input type="text" x-model="newSetting.value" required class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 px-4 py-3 text-sm shadow-soft focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Type</label>
                    <select x-model="newSetting.type" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 px-4 py-3 text-sm shadow-soft focus:border-primary-500 focus:ring-primary-500">
                        <option value="string">String</option>
                        <option value="integer">Integer</option>
                        <option value="float">Float</option>
                        <option value="boolean">Boolean</option>
                        <option value="json">JSON</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" @click="showAddModal = false" class="rounded-xl border border-surface-300 dark:border-surface-600 px-4 py-2.5 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function settingsManager() {
    return {
        groups: ['general', 'branding', 'notifications', 'billing', 'integrations', 'advanced'],
        activeGroup: 'general',
        settings: [],
        loading: false,
        editingKey: null,
        editValue: '',
        editType: '',
        showAddModal: false,
        newSetting: { key: '', value: '', type: 'string' },

        async loadSettings() {
            this.loading = true;
            try {
                const res = await authFetch(APP_BASE + '/api/settings/' + this.activeGroup);
                const json = await res.json();
                this.settings = json.data ? Object.entries(json.data).map(([key, value]) => ({
                    key, value: typeof value === 'object' ? JSON.stringify(value) : String(value), type: typeof value
                })) : (Array.isArray(json.data) ? json.data : []);
            } catch (e) { this.settings = []; }
            this.loading = false;
        },

        formatValue(value, type) {
            if (type === 'boolean') return value === '1' || value === 'true' || value === true ? 'True' : 'False';
            if (typeof value === 'object') return JSON.stringify(value);
            const s = String(value);
            return s.length > 80 ? s.substring(0, 80) + '…' : s;
        },

        startEdit(setting) {
            this.editingKey = setting.key;
            this.editValue = setting.value;
            this.editType = setting.type;
        },

        async saveSetting(key) {
            try {
                const payload = {};
                payload[key] = this.editValue;
                await authFetch(APP_BASE + '/api/settings/' + this.activeGroup, {
                    method: 'PUT',
                    body: JSON.stringify(payload)
                });
                this.editingKey = null;
                this.loadSettings();
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Setting updated', type: 'success' } }));
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Failed to update', type: 'error' } }));
            }
        },

        async addSetting() {
            try {
                const payload = {};
                payload[this.newSetting.key] = this.newSetting.value;
                await authFetch(APP_BASE + '/api/settings/' + this.activeGroup, {
                    method: 'PUT',
                    body: JSON.stringify(payload)
                });
                this.showAddModal = false;
                this.newSetting = { key: '', value: '', type: 'string' };
                this.loadSettings();
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Setting added', type: 'success' } }));
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Failed to add', type: 'error' } }));
            }
        },

        async deleteSetting(key) {
            if (!confirm('Delete setting "' + key + '"?')) return;
            try {
                await authFetch(APP_BASE + '/api/settings/' + this.activeGroup + '/' + key, { method: 'DELETE' });
                this.loadSettings();
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Setting deleted', type: 'success' } }));
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Failed to delete', type: 'error' } }));
            }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
