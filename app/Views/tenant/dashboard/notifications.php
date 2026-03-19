<?php
/**
 * Player Dashboard — Notifications — K2 Navy/Gold Theme
 * Simple notification list with read/unread state.
 */
?>

<div x-data="notificationsPage()" x-init="load()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-display font-extrabold text-white">Notifications</h1>
        <button x-show="notifications.length > 0 && unreadCount > 0" @click="markAllRead()"
                class="text-xs text-gold-500 hover:text-gold-400 font-semibold transition-colors">
            Mark all as read
        </button>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-10 text-center">
        <svg class="animate-spin w-6 h-6 text-gold-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <div x-show="!loading">
        <!-- Empty -->
        <div x-show="notifications.length === 0" class="glass-card rounded-2xl p-10 gold-border text-center">
            <svg class="w-10 h-10 text-navy-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p class="text-slate-500 text-sm">No notifications yet.</p>
            <p class="text-slate-600 text-xs mt-1">We'll notify you about booking updates, new sessions, and more.</p>
        </div>

        <!-- List -->
        <div class="space-y-2">
            <template x-for="n in notifications" :key="n.id">
                <div @click="markRead(n)" class="glass-card rounded-xl gold-border px-5 py-4 flex items-start gap-4 cursor-pointer hover:bg-navy-850/50 transition-all"
                     :class="{ 'border-l-2 border-l-gold-500': !n.read_at }">
                    <!-- Icon -->
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                         :class="n.read_at ? 'bg-navy-800' : 'gradient-gold-bg'">
                        <!-- Booking -->
                        <svg x-show="n.type === 'booking'" class="w-4 h-4" :class="n.read_at ? 'text-slate-500' : 'text-navy-950'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <!-- Payment -->
                        <svg x-show="n.type === 'payment'" class="w-4 h-4" :class="n.read_at ? 'text-slate-500' : 'text-navy-950'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <!-- General -->
                        <svg x-show="!['booking','payment'].includes(n.type)" class="w-4 h-4" :class="n.read_at ? 'text-slate-500' : 'text-navy-950'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold truncate" :class="n.read_at ? 'text-slate-400' : 'text-white'" x-text="n.title || 'Notification'"></div>
                        <div class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="n.message || n.body || ''"></div>
                        <div class="text-[10px] text-slate-600 mt-1" x-text="timeAgo(n.created_at)"></div>
                    </div>
                    <!-- Unread dot -->
                    <div x-show="!n.read_at" class="w-2 h-2 rounded-full bg-gold-500 mt-2 flex-shrink-0"></div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function notificationsPage() {
    return {
        notifications: [],
        loading: true,
        get unreadCount() { return this.notifications.filter(n => !n.read_at).length; },

        async load() {
            const app = document.querySelector('[x-data*=dashboardApp]')?.__x?.$data;
            if (!app?.authFetch) { this.loading = false; return; }
            try {
                const resp = await app.authFetch(baseApi + '/api/auth/me');
                const json = await resp.json();
                if (json.success && json.data) {
                    this.notifications = json.data.notifications || [];
                }
            } catch (e) { console.error('Failed to load notifications', e); }
            finally { this.loading = false; }
        },

        async markRead(n) {
            if (n.read_at) return;
            n.read_at = new Date().toISOString();
            // Optionally POST to mark read on server
            // const app = document.querySelector('[x-data*=dashboardApp]')?.__x?.$data;
            // await app.authFetch(baseApi + '/api/notifications/' + n.id + '/read', { method: 'POST' });
        },

        markAllRead() {
            this.notifications.forEach(n => {
                if (!n.read_at) n.read_at = new Date().toISOString();
            });
        },

        timeAgo(dt) {
            if (!dt) return '';
            const diff = (Date.now() - new Date(dt).getTime()) / 1000;
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
            return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }
    };
}
</script>
