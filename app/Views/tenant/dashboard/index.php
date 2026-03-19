<?php
/**
 * Player Dashboard Home — K2 Navy/Gold Theme
 * Welcome message, quick stats, upcoming bookings preview.
 */
?>

<div x-data="dashboardHome()" x-init="load()">
    <!-- Welcome -->
    <div class="mb-8">
        <h1 class="text-2xl font-display font-extrabold text-white">
            Welcome back, <span class="text-gold-400" x-text="playerName()"></span>
        </h1>
        <p class="text-slate-400 mt-1">Here's what's coming up for you.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="glass-card rounded-xl p-5 gold-border">
            <div class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Upcoming</div>
            <div class="text-3xl font-extrabold text-gold-400" x-text="upcoming.length"></div>
            <div class="text-xs text-slate-500 mt-1">sessions booked</div>
        </div>
        <div class="glass-card rounded-xl p-5 gold-border">
            <div class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">This Month</div>
            <div class="text-3xl font-extrabold text-gold-400" x-text="monthCount"></div>
            <div class="text-xs text-slate-500 mt-1">sessions attended</div>
        </div>
        <div class="glass-card rounded-xl p-5 gold-border">
            <div class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Quick Action</div>
            <a href="/schedule" class="inline-flex items-center gap-2 mt-2 px-4 py-2 rounded-lg gradient-gold-bg text-navy-950 text-sm font-bold hover:shadow-gold transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Book a Session
            </a>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-10 text-center">
        <svg class="animate-spin w-6 h-6 text-gold-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <!-- Upcoming Bookings -->
    <div x-show="!loading" class="glass-card rounded-2xl gold-border overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-navy-700/50">
            <h2 class="font-display font-bold text-white">Upcoming Bookings</h2>
            <a href="/dashboard/bookings" class="text-sm text-gold-500 hover:text-gold-400 transition-colors">View all &rarr;</a>
        </div>

        <!-- Empty -->
        <div x-show="upcoming.length === 0" class="p-10 text-center">
            <svg class="w-10 h-10 text-navy-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-slate-500 text-sm">No upcoming bookings.</p>
            <a href="/schedule" class="text-sm text-gold-500 hover:text-gold-400 mt-2 inline-block">Browse schedule &rarr;</a>
        </div>

        <!-- Booking List -->
        <div class="divide-y divide-navy-700/30">
            <template x-for="b in upcoming.slice(0, 5)" :key="b.id">
                <a :href="'/schedule/' + (b.class_id || b.id)" class="flex items-center gap-4 px-6 py-4 hover:bg-navy-800/30 transition-colors group">
                    <!-- Date Badge -->
                    <div class="w-12 h-12 rounded-xl gradient-gold-bg flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-[10px] font-bold text-navy-950 uppercase leading-none" x-text="fmtMonth(b.start_time)"></span>
                        <span class="text-lg font-extrabold text-navy-950 leading-none" x-text="fmtDay(b.start_time)"></span>
                    </div>
                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-white group-hover:text-gold-400 transition-colors truncate" x-text="b.session_type_name || b.session_name || 'Session'"></div>
                        <div class="text-xs text-slate-500" x-text="fmtTime(b.start_time) + ' – ' + fmtTime(b.end_time)"></div>
                    </div>
                    <!-- Status -->
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                          :class="{
                              'bg-emerald-500/10 text-emerald-400': b.status === 'registered',
                              'bg-amber-500/10 text-amber-400': b.status === 'waitlisted',
                              'bg-slate-500/10 text-slate-400': b.status !== 'registered' && b.status !== 'waitlisted'
                          }"
                          x-text="b.status"></span>
                </a>
            </template>
        </div>
    </div>
</div>

<script>
function dashboardHome() {
    return {
        upcoming: [],
        monthCount: 0,
        loading: true,

        async load() {
            const app = document.querySelector('[x-data*=dashboardApp]')?.__x?.$data;
            if (!app?.authFetch) { this.loading = false; return; }
            try {
                const resp = await app.authFetch(baseApi + '/api/auth/me');
                const json = await resp.json();
                if (json.success && json.data) {
                    const now = new Date();
                    const bookings = json.data.bookings || json.data.upcoming_bookings || [];
                    this.upcoming = bookings.filter(b => b.status !== 'cancelled' && new Date(b.start_time) >= now).sort((a,b) => new Date(a.start_time) - new Date(b.start_time));
                    this.monthCount = json.data.month_sessions || this.upcoming.filter(b => { const d = new Date(b.start_time); return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear(); }).length;
                }
            } catch(e) { console.error('Failed to load dashboard data', e); }
            finally { this.loading = false; }
        },

        playerName() {
            const app = document.querySelector('[x-data*=dashboardApp]')?.__x?.$data;
            return app?.player?.first_name || 'Player';
        },

        fmtMonth(dt) { return new Date(dt).toLocaleDateString('en-US', { month: 'short' }); },
        fmtDay(dt) { return new Date(dt).getDate(); },
        fmtTime(dt) { return new Date(dt).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }); },
    };
}
</script>
