<?php
/**
 * Player Dashboard — My Bookings — K2 Navy/Gold Theme
 * Tabbed view: upcoming, past, cancelled.
 */
?>

<div x-data="myBookings()" x-init="load()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-display font-extrabold text-white">My Bookings</h1>
        <a href="/schedule" class="flex items-center gap-2 px-4 py-2 rounded-lg gradient-gold-bg text-navy-950 text-sm font-bold hover:shadow-gold transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Book Session
        </a>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 mb-6 bg-navy-900 rounded-xl p-1 border border-navy-800 w-fit">
        <template x-for="tab in ['upcoming', 'past', 'cancelled']" :key="tab">
            <button @click="activeTab = tab; filter()" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all capitalize"
                    :class="activeTab === tab ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">
                <span x-text="tab"></span>
                <span class="ml-1 text-xs opacity-60" x-text="'(' + countTab(tab) + ')'"></span>
            </button>
        </template>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-10 text-center">
        <svg class="animate-spin w-6 h-6 text-gold-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <!-- Bookings List -->
    <div x-show="!loading" class="space-y-3">
        <!-- Empty -->
        <div x-show="filtered.length === 0" class="glass-card rounded-2xl p-10 gold-border text-center">
            <svg class="w-10 h-10 text-navy-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-slate-500 text-sm">
                <span x-show="activeTab === 'upcoming'">No upcoming bookings.</span>
                <span x-show="activeTab === 'past'">No past bookings yet.</span>
                <span x-show="activeTab === 'cancelled'">No cancelled bookings.</span>
            </p>
        </div>

        <!-- Booking Cards -->
        <template x-for="b in filtered" :key="b.id">
            <div class="glass-card rounded-xl gold-border overflow-hidden hover:shadow-gold/5 transition-all">
                <a :href="'/schedule/' + (b.class_id || b.id)" class="flex items-center gap-4 px-5 py-4 group">
                    <!-- Date Badge -->
                    <div class="w-14 h-14 rounded-xl gradient-gold-bg flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-[10px] font-bold text-navy-950 uppercase leading-none" x-text="fmtMonth(b.start_time)"></span>
                        <span class="text-xl font-extrabold text-navy-950 leading-none" x-text="fmtDay(b.start_time)"></span>
                    </div>
                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-white group-hover:text-gold-400 transition-colors truncate" x-text="b.session_type_name || b.session_name || 'Session'"></div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            <span x-text="fmtWeekday(b.start_time)"></span> &middot;
                            <span x-text="fmtTime(b.start_time) + ' – ' + fmtTime(b.end_time)"></span>
                        </div>
                        <template x-if="parseFloat(b.amount_paid) > 0">
                            <div class="text-xs text-slate-500 mt-1">Paid: $<span x-text="parseFloat(b.amount_paid).toFixed(2)"></span></div>
                        </template>
                    </div>
                    <!-- Status + Arrow -->
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                              :class="{
                                  'bg-emerald-500/10 text-emerald-400': b.status === 'registered',
                                  'bg-amber-500/10 text-amber-400': b.status === 'waitlisted',
                                  'bg-red-500/10 text-red-400': b.status === 'cancelled',
                                  'bg-slate-500/10 text-slate-400': !['registered','waitlisted','cancelled'].includes(b.status)
                              }"
                              x-text="b.status"></span>
                        <svg class="w-4 h-4 text-slate-600 group-hover:text-gold-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            </div>
        </template>
    </div>
</div>

<script>
function myBookings() {
    return {
        all: [],
        filtered: [],
        activeTab: 'upcoming',
        loading: true,

        async load() {
            const app = document.querySelector('[x-data*=dashboardApp]')?.__x?.$data;
            if (!app?.authFetch) { this.loading = false; return; }
            try {
                const resp = await app.authFetch(baseApi + '/api/auth/me');
                const json = await resp.json();
                if (json.success && json.data) {
                    this.all = json.data.bookings || json.data.upcoming_bookings || [];
                }
            } catch(e) { console.error('Failed to load bookings', e); }
            finally { this.loading = false; this.filter(); }
        },

        filter() {
            const now = new Date();
            if (this.activeTab === 'upcoming') {
                this.filtered = this.all.filter(b => b.status !== 'cancelled' && new Date(b.start_time) >= now).sort((a,b) => new Date(a.start_time) - new Date(b.start_time));
            } else if (this.activeTab === 'past') {
                this.filtered = this.all.filter(b => b.status !== 'cancelled' && new Date(b.start_time) < now).sort((a,b) => new Date(b.start_time) - new Date(a.start_time));
            } else {
                this.filtered = this.all.filter(b => b.status === 'cancelled').sort((a,b) => new Date(b.start_time) - new Date(a.start_time));
            }
        },

        countTab(tab) {
            const now = new Date();
            if (tab === 'upcoming') return this.all.filter(b => b.status !== 'cancelled' && new Date(b.start_time) >= now).length;
            if (tab === 'past') return this.all.filter(b => b.status !== 'cancelled' && new Date(b.start_time) < now).length;
            return this.all.filter(b => b.status === 'cancelled').length;
        },

        fmtMonth(dt) { return new Date(dt).toLocaleDateString('en-US', { month: 'short' }); },
        fmtDay(dt) { return new Date(dt).getDate(); },
        fmtWeekday(dt) { return new Date(dt).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }); },
        fmtTime(dt) { return new Date(dt).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }); },
    };
}
</script>
