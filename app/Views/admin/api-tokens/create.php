<?php
$title = 'Generate API Token';
$breadcrumbs = [['label' => 'API Tokens', 'url' => ($baseUrl ?? '') . '/admin/api-tokens'], ['label' => 'Generate']];

ob_start();
?>
<div x-data="apiTokenForm()" class="mx-auto max-w-3xl space-y-6">
    <!-- Create Form -->
    <template x-if="!generatedToken">
        <form @submit.prevent="submitForm()" class="rounded-2xl bg-white dark:bg-surface-800/60 shadow-soft border border-surface-200/60 dark:border-surface-700/50 overflow-hidden">
            <div class="border-b border-surface-200 dark:border-surface-700/50 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/80">
                <h3 class="text-base font-semibold text-surface-900 dark:text-white">Generate New API Token</h3>
                <p class="text-xs text-surface-500 mt-0.5">Create a token for API integrations</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Token Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.name" required placeholder="My Integration" class="w-full rounded-xl border-surface-300 shadow-soft focus:border-primary-500 focus:ring-primary-500 dark:border-surface-600 dark:bg-surface-900 dark:text-white px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Abilities</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                        <label class="inline-flex items-center gap-2.5 text-sm rounded-xl border border-surface-200 dark:border-surface-700 px-3 py-2.5 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors cursor-pointer">
                            <input type="checkbox" value="*" x-model="form.abilities" class="rounded border-surface-300 text-primary-500 dark:border-surface-600 dark:bg-surface-900 focus:ring-primary-500">
                            <span class="font-semibold text-surface-700 dark:text-surface-300">Full Access (*)</span>
                        </label>
                        <template x-for="ability in availableAbilities" :key="ability">
                            <label class="inline-flex items-center gap-2.5 text-sm rounded-xl border border-surface-200 dark:border-surface-700 px-3 py-2.5 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors cursor-pointer">
                                <input type="checkbox" :value="ability" x-model="form.abilities" class="rounded border-surface-300 text-primary-500 dark:border-surface-600 dark:bg-surface-900 focus:ring-primary-500">
                                <span x-text="ability" class="text-surface-600 dark:text-surface-400"></span>
                            </label>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Expires At</label>
                    <input type="date" x-model="form.expires_at" class="w-full rounded-xl border-surface-300 shadow-soft focus:border-primary-500 focus:ring-primary-500 dark:border-surface-600 dark:bg-surface-900 dark:text-white px-4 py-3 text-sm">
                    <p class="mt-1.5 text-xs text-surface-500">Leave blank for no expiration</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-surface-200 dark:border-surface-700/50 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/80">
                <a href="<?= ($baseUrl ?? '') . '/admin/api-tokens' ?>" class="rounded-xl border border-surface-300 dark:border-surface-600 px-4 py-2.5 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Cancel</a>
                <button type="submit" :disabled="submitting" class="rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:from-primary-700 hover:to-primary-800 disabled:opacity-50 shadow-soft transition-all">Generate Token</button>
            </div>
        </form>
    </template>

    <!-- Token Display (shown once) -->
    <template x-if="generatedToken">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 shadow-soft p-6">
            <div class="flex items-start gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 shrink-0 shadow-lg shadow-emerald-500/20">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-emerald-800 dark:text-emerald-200">Token Generated Successfully</h3>
                    <p class="text-sm text-emerald-700 dark:text-emerald-300 mt-1">Copy this token now. You won't be able to see it again.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-white dark:bg-surface-900 rounded-xl border border-emerald-200 dark:border-emerald-700 p-4 shadow-soft">
                <code class="flex-1 text-sm font-mono text-surface-800 dark:text-surface-200 break-all" x-text="generatedToken"></code>
                <button @click="navigator.clipboard.writeText(generatedToken); copied = true; setTimeout(() => copied = false, 2000)"
                    class="shrink-0 rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-3.5 py-2 text-xs font-medium text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all">
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
            <div class="mt-4">
                <a href="<?= ($baseUrl ?? '') . '/admin/api-tokens' ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to API Tokens
                </a>
            </div>
        </div>
    </template>
</div>

<script>
function apiTokenForm() {
    const token = localStorage.getItem('access_token');
    return {
        form: { name: '', abilities: ['*'], expires_at: '' },
        availableAbilities: ['read', 'write', 'delete', 'facilities.read', 'facilities.write', 'courts.read', 'courts.write', 'users.read', 'users.write', 'payments.read', 'payments.write'],
        errors: {},
        submitting: false,
        generatedToken: null,
        copied: false,
        async submitForm() {
            this.submitting = true;
            try {
                const body = { ...this.form };
                if (!body.expires_at) delete body.expires_at;
                const res = await fetch(APP_BASE + '/api/api-tokens', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (res.ok && json.data?.plain_token) {
                    this.generatedToken = json.data.plain_token;
                } else {
                    this.errors = json.errors || {};
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed', type: 'error' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
            this.submitting = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
