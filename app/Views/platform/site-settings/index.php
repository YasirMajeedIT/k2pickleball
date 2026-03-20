<?php
$title = 'Site Settings';
$breadcrumbs = [['label' => 'Site Settings']];

ob_start();
?>

<div x-data="siteSettingsPage()" x-init="loadSettings()">

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-surface-900 dark:text-white">Site Settings</h2>
        <p class="text-sm text-surface-400 mt-1">Control maintenance mode and site access settings.</p>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
    </div>

    <div x-show="!loading" class="space-y-6">

        <!-- Maintenance Mode Card -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/10">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-surface-900 dark:text-white">Maintenance Mode</h3>
                    <p class="text-xs text-surface-400">When enabled, visitors will see a maintenance page instead of the site.</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-5">
                <!-- Toggle -->
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Enable Maintenance Mode</span>
                        <p class="text-xs text-surface-400 mt-0.5">The site will show a maintenance page for all visitors</p>
                    </div>
                    <button @click="settings.maintenance_mode = !settings.maintenance_mode" type="button"
                            :class="settings.maintenance_mode ? 'bg-red-500' : 'bg-surface-200 dark:bg-surface-700'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <span :class="settings.maintenance_mode ? 'translate-x-5' : 'translate-x-0'"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"></span>
                    </button>
                </div>
                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Maintenance Message</label>
                    <textarea x-model="settings.maintenance_message" rows="3"
                              class="w-full rounded-xl border border-surface-200 bg-white py-2.5 px-4 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft resize-none"
                              placeholder="We are performing scheduled maintenance..."></textarea>
                </div>
                <!-- Allowed IPs -->
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Allowed IPs (bypass maintenance)</label>
                    <input type="text" x-model="allowedIpsText"
                           class="w-full rounded-xl border border-surface-200 bg-white py-2.5 px-4 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft"
                           placeholder="Comma-separated IPs, e.g. 192.168.1.1, 10.0.0.5">
                    <p class="text-xs text-surface-400 mt-1">These IP addresses can still access the site during maintenance.</p>
                </div>
            </div>
        </div>

        <!-- Password Protection Card -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/10">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-surface-900 dark:text-white">Password Protection</h3>
                    <p class="text-xs text-surface-400">Require visitors to enter a password to view the site.</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-5">
                <!-- Toggle -->
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Enable Password Protection</span>
                        <p class="text-xs text-surface-400 mt-0.5">Visitors must enter the password below to access the site</p>
                    </div>
                    <button @click="settings.password_protection_enabled = !settings.password_protection_enabled" type="button"
                            :class="settings.password_protection_enabled ? 'bg-amber-500' : 'bg-surface-200 dark:bg-surface-700'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                        <span :class="settings.password_protection_enabled ? 'translate-x-5' : 'translate-x-0'"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"></span>
                    </button>
                </div>
                <!-- Password Input -->
                <div x-show="settings.password_protection_enabled" x-collapse>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Site Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" x-model="settings.site_password"
                               class="w-full rounded-xl border border-surface-200 bg-white py-2.5 px-4 pr-10 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white shadow-soft"
                               placeholder="Enter site password">
                        <button @click="showPassword = !showPassword" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                    <p class="text-xs text-surface-400 mt-1">Visitors will be prompted to enter this password before viewing any page.</p>
                </div>
            </div>
        </div>

        <!-- Current Status Summary -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft p-6">
            <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-300 mb-3">Current Site Status</h3>
            <div class="flex flex-wrap gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                      :class="settings.maintenance_mode ? 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'">
                    <span class="w-1.5 h-1.5 rounded-full" :class="settings.maintenance_mode ? 'bg-red-500' : 'bg-emerald-500'"></span>
                    <span x-text="settings.maintenance_mode ? 'Maintenance Mode ON' : 'Site Online'"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                      :class="settings.password_protection_enabled ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-300'">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    <span x-text="settings.password_protection_enabled ? 'Password Protected' : 'No Password'"></span>
                </span>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-between">
            <div x-show="saveSuccess" x-cloak x-transition class="flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                Settings saved successfully
            </div>
            <div x-show="saveError" x-cloak class="text-sm text-red-500" x-text="saveError"></div>
            <button @click="saveSettings()" :disabled="saving"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-2.5 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all disabled:opacity-60">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                <svg x-show="saving" x-cloak class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span x-text="saving ? 'Saving...' : 'Save Settings'"></span>
            </button>
        </div>
    </div>
</div>

<script>
function siteSettingsPage() {
    const BASE = '<?= $baseUrl ?? '' ?>';
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'Content-Type': 'application/json' };

    return {
        loading: true,
        saving: false,
        saveSuccess: false,
        saveError: '',
        showPassword: false,
        allowedIpsText: '',
        settings: {
            maintenance_mode: false,
            maintenance_message: '',
            password_protection_enabled: false,
            site_password: '',
            maintenance_allowed_ips: []
        },

        async loadSettings() {
            this.loading = true;
            try {
                const res = await fetch(BASE + '/api/platform/site-settings', { headers });
                const json = await res.json();
                if (json.status === 'success' && json.data) {
                    json.data.forEach(s => {
                        if (s.key in this.settings) {
                            this.settings[s.key] = s.value;
                        }
                    });
                    // Convert allowed IPs array to text
                    const ips = this.settings.maintenance_allowed_ips;
                    this.allowedIpsText = Array.isArray(ips) ? ips.join(', ') : '';
                }
            } catch (e) { console.error('Failed to load settings:', e); }
            this.loading = false;
        },

        async saveSettings() {
            this.saving = true;
            this.saveSuccess = false;
            this.saveError = '';

            // Parse IPs from text
            const ips = this.allowedIpsText
                .split(',')
                .map(ip => ip.trim())
                .filter(ip => ip.length > 0);

            const payload = {
                settings: {
                    maintenance_mode: this.settings.maintenance_mode,
                    maintenance_message: this.settings.maintenance_message,
                    password_protection_enabled: this.settings.password_protection_enabled,
                    site_password: this.settings.site_password,
                    maintenance_allowed_ips: ips
                }
            };

            try {
                const res = await fetch(BASE + '/api/platform/site-settings', {
                    method: 'PUT', headers, body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (res.ok) {
                    this.saveSuccess = true;
                    setTimeout(() => this.saveSuccess = false, 3000);
                } else {
                    this.saveError = json.error || json.message || 'Failed to save settings';
                }
            } catch (e) {
                this.saveError = 'Network error. Please try again.';
            }
            this.saving = false;
        }
    };
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
