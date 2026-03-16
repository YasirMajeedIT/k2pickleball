<?php
$title = 'Player Details';
$breadcrumbs = [
    ['label' => 'Players', 'url' => ($baseUrl ?? '') . '/admin/players'],
    ['label' => 'View'],
];
$backUrl  = ($baseUrl ?? '') . '/admin/players';
$editUrl  = ($baseUrl ?? '') . '/admin/players/' . ($id ?? '') . '/edit';
$apiUrl   = ($baseUrl ?? '') . '/api/players/' . ($id ?? '');

ob_start();
?>
<div x-data="playerShow()" x-init="init()">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="relative group">
                <template x-if="player.avatar_url">
                    <img :src="APP_BASE + player.avatar_url" alt="Avatar" class="h-16 w-16 rounded-2xl object-cover border-2 border-surface-200 dark:border-surface-700 shadow-soft">
                </template>
                <template x-if="!player.avatar_url">
                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-lg shadow-soft"
                         x-text="(player.first_name||'')[0] + (player.last_name||'')[0]"></div>
                </template>
            </div>
            <div>
                <h2 class="text-xl font-bold text-surface-900 dark:text-white" x-text="(player.first_name || '') + ' ' + (player.last_name || '')"></h2>
                <p class="mt-0.5 text-sm text-surface-500" x-text="player.email || ''"></p>
            </div>
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
                Edit
            </a>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-20 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
    </div>

    <!-- Content -->
    <div x-show="!loading" class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Left: Main info -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Personal Info -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Personal Information</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Name</dt>
                            <dd class="mt-1 text-sm font-medium text-surface-800 dark:text-surface-100" x-text="(player.first_name || '') + ' ' + (player.last_name || '')"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Status</dt>
                            <dd class="mt-1"><span x-html="statusBadge(player.status)"></span></dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Email</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.email || '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Phone</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.phone || '—'"></dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Date of Birth</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.date_of_birth ? new Date(player.date_of_birth).toLocaleDateString() : '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Gender</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300 capitalize" x-text="player.gender ? player.gender.replace('_', ' ') : '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Joined</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.date_joined ? new Date(player.date_joined).toLocaleDateString() : '—'"></dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skill & Rating -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Skill & Rating</h3>
                </div>
                <div class="grid grid-cols-4 gap-0 divide-x divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Skill Level</dt>
                        <dd class="mt-1 text-sm font-medium capitalize text-surface-800 dark:text-surface-100" x-text="player.skill_level || '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Rating</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.rating || '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">DUPR Rating</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.dupr_rating || '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">DUPR ID</dt>
                        <dd class="mt-1 text-sm font-mono text-surface-500" x-text="player.dupr_id || '—'"></dd>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div x-show="player.address || player.city" class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Address</h3>
                </div>
                <div class="grid grid-cols-3 gap-0 divide-x divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-4 col-span-3">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Street</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.address || '—'"></dd>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-0 divide-x divide-surface-100 dark:divide-surface-800 border-t border-surface-100 dark:border-surface-800">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">City</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.city || '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">State</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.state || '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">ZIP</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.zip_code || '—'"></dd>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div x-show="player.medical_notes || player.notes" class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Notes</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div x-show="player.medical_notes" class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Medical Notes</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300 whitespace-pre-line" x-text="player.medical_notes"></dd>
                    </div>
                    <div x-show="player.notes" class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">General Notes</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300 whitespace-pre-line" x-text="player.notes"></dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right column -->
        <div class="space-y-6">

            <!-- Avatar Card -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Avatar</h3>
                </div>
                <div class="px-6 py-6 flex flex-col items-center gap-4">
                    <template x-if="player.avatar_url">
                        <img :src="APP_BASE + player.avatar_url" class="h-32 w-32 rounded-2xl object-cover border-2 border-surface-200 dark:border-surface-700 shadow-soft">
                    </template>
                    <template x-if="!player.avatar_url">
                        <div class="h-32 w-32 rounded-2xl bg-gradient-to-br from-surface-200 to-surface-300 dark:from-surface-700 dark:to-surface-600 flex items-center justify-center text-4xl font-bold text-surface-400 dark:text-surface-500"
                             x-text="(player.first_name||'')[0] + (player.last_name||'')[0]"></div>
                    </template>
                    <div class="flex gap-2">
                        <label class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700 cursor-pointer transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Upload
                            <input type="file" accept="image/*" class="hidden" x-on:change="uploadAvatar($event)">
                        </label>
                        <button x-show="player.avatar_url" x-on:click="removeAvatar()"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 dark:border-red-500/30 px-3 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Remove
                        </button>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-red-500 to-red-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Emergency Contact</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Name</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.emergency_contact_name || '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Phone</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="player.emergency_contact_phone || '—'"></dd>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Preferences</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-surface-600 dark:text-surface-300">Waiver Signed</span>
                        <span x-html="player.is_waiver ? '&check;' : '&times;'" :class="player.is_waiver ? 'text-green-500' : 'text-surface-400'" class="text-lg font-bold"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-surface-600 dark:text-surface-300">Teen Player</span>
                        <span x-html="player.is_teen ? '&check;' : '&times;'" :class="player.is_teen ? 'text-green-500' : 'text-surface-400'" class="text-lg font-bold"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-surface-600 dark:text-surface-300">Email Marketing</span>
                        <span x-html="player.is_email_marketing ? '&check;' : '&times;'" :class="player.is_email_marketing ? 'text-green-500' : 'text-surface-400'" class="text-lg font-bold"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-surface-600 dark:text-surface-300">SMS Marketing</span>
                        <span x-html="player.is_sms_marketing ? '&check;' : '&times;'" :class="player.is_sms_marketing ? 'text-green-500' : 'text-surface-400'" class="text-lg font-bold"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function playerShow() {
    const apiUrl = '<?= htmlspecialchars($apiUrl) ?>';
    return {
        player: {},
        loading: true,
        async init() {
            try {
                const res = await authFetch(apiUrl);
                const json = await res.json();
                if (json.data) this.player = json.data;
            } catch (e) { console.error(e); }
            finally { this.loading = false; }
        },
        statusBadge(status) {
            const map = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', inactive: 'bg-surface-100 text-surface-600', suspended: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' };
            return '<span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize ' + (map[status] || 'bg-surface-100 text-surface-600') + '">' + (status || '—') + '</span>';
        },
        async uploadAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;
            const fd = new FormData();
            fd.append('avatar', file);
            try {
                const res = await authFetch(apiUrl + '/avatar', { method: 'POST', body: fd });
                const json = await res.json();
                if (res.ok && json.data) {
                    this.player = json.data;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Avatar uploaded', type: 'success' } }));
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Upload failed', type: 'error' } }));
                }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            event.target.value = '';
        },
        async removeAvatar() {
            if (!confirm('Remove avatar?')) return;
            try {
                const res = await authFetch(apiUrl + '/avatar', { method: 'DELETE' });
                const json = await res.json();
                if (res.ok && json.data) {
                    this.player = json.data;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Avatar removed', type: 'success' } }));
                }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        },
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
