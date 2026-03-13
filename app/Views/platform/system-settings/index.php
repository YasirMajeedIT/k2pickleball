<?php
$title = 'System Settings';
$breadcrumbs = [['label' => 'System Settings']];

ob_start();
?>
<div x-data="systemSettings()" x-init="init()" class="space-y-6">
    <!-- Loading -->
    <template x-if="loading">
        <div class="flex items-center justify-center py-20">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-primary-200 border-t-primary-600"></div>
        </div>
    </template>

    <!-- Settings Form -->
    <template x-if="!loading">
        <div class="max-w-3xl mx-auto">
            <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30">
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white">Platform Settings</h3>
                    <p class="text-sm text-surface-400 mt-0.5">Manage system-wide configuration options</p>
                </div>

                <!-- Empty state -->
                <template x-if="settings.length === 0">
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-10 w-10 text-surface-300 dark:text-surface-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-sm font-medium text-surface-500 mb-4">No system settings configured yet</p>
                        <button @click="addSetting()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Setting
                        </button>
                    </div>
                </template>

                <!-- Settings list -->
                <template x-if="settings.length > 0">
                    <div>
                        <div class="divide-y divide-surface-100 dark:divide-surface-800">
                            <template x-for="(setting, idx) in settings" :key="idx">
                                <div class="px-6 py-4 flex items-start gap-4">
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="text-xs font-semibold text-surface-500 mb-1 block">Key</label>
                                            <input type="text" x-model="setting.key" placeholder="setting_key"
                                                   class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 outline-none">
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-surface-500 mb-1 block">Value</label>
                                            <input type="text" x-model="setting.value" placeholder="value"
                                                   class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 outline-none">
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-surface-500 mb-1 block">Group</label>
                                            <input type="text" x-model="setting.group" placeholder="general"
                                                   class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 outline-none">
                                        </div>
                                    </div>
                                    <button @click="settings.splice(idx, 1)" class="mt-6 text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30 flex items-center justify-between">
                            <button @click="addSetting()" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Setting
                            </button>
                            <button @click="save()" :disabled="saving"
                                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary-500/20 hover:from-primary-700 hover:to-primary-800 disabled:opacity-50 transition-all">
                                <template x-if="saving">
                                    <div class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></div>
                                </template>
                                <span x-text="saving ? 'Saving...' : 'Save Settings'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>

<script>
function systemSettings() {
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    return {
        settings: [],
        loading: true,
        saving: false,

        async init() {
            try {
                const res = await fetch(APP_BASE + '/api/platform/settings', { headers });
                if (res.status === 401) { window.location.href = APP_BASE + '/admin/login'; return; }
                const json = await res.json();
                this.settings = (json.data || []).map(s => ({
                    key: s.key || '',
                    value: s.value || '',
                    group: s.group || 'general',
                    type: s.type || 'string'
                }));
            } catch (e) { console.error(e); }
            this.loading = false;
        },

        addSetting() {
            this.settings.push({ key: '', value: '', group: 'general', type: 'string' });
        },

        async save() {
            this.saving = true;
            try {
                const res = await fetch(APP_BASE + '/api/platform/settings', {
                    method: 'PUT',
                    headers: { ...headers, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ settings: this.settings })
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Settings saved', type: 'success' } }));
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed to save', type: 'error' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
            this.saving = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
