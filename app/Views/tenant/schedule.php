<?php
/**
 * Tenant Schedule Page — K2 Navy/Gold Theme
 * Fully functional: fetches from /api/public/schedule, week/list view, category filter, date nav.
 */
?>

<div x-data="schedulePage()" x-init="load()">
    <!-- Page Header -->
    <section class="relative bg-navy-900 overflow-hidden py-20">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute inset-0 hero-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Schedule
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white">Class Schedule</h1>
            <p class="mt-4 text-lg text-slate-400 max-w-2xl">Browse upcoming classes and reserve your spot.</p>
        </div>
    </section>

    <!-- Controls Bar -->
    <section class="sticky top-16 lg:top-20 z-30 glass border-b border-gold-500/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <!-- Date Nav -->
                <div class="flex items-center gap-2">
                    <button @click="prevWeek()" class="p-2 rounded-lg bg-navy-800 border border-navy-700 text-slate-300 hover:text-white hover:border-gold-500/30 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="goToday()" class="px-4 py-2 rounded-lg text-sm font-semibold bg-navy-800 border border-navy-700 text-slate-300 hover:text-white hover:border-gold-500/30 transition-all">Today</button>
                    <button @click="nextWeek()" class="p-2 rounded-lg bg-navy-800 border border-navy-700 text-slate-300 hover:text-white hover:border-gold-500/30 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <span class="ml-2 text-sm font-medium text-white" x-text="weekLabel()"></span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Category Filter -->
                    <div class="flex flex-wrap gap-2" x-show="categories.length > 0" x-cloak>
                        <button @click="categoryFilter = null; load()" class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all" :class="!categoryFilter ? 'gradient-gold-bg text-navy-950' : 'bg-navy-800 text-slate-400 border border-navy-700 hover:text-white'">All</button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button @click="categoryFilter = cat.id; load()" class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all" :class="categoryFilter === cat.id ? 'text-white' : 'bg-navy-800 text-slate-400 border border-navy-700 hover:text-white'" :style="categoryFilter === cat.id ? 'background:' + (cat.color || '#d4af37') : ''" x-text="cat.name"></button>
                        </template>
                    </div>
                    <!-- View Toggle -->
                    <div class="flex bg-navy-800 border border-navy-700 rounded-lg overflow-hidden">
                        <button @click="viewMode = 'week'; load()" class="px-3 py-1.5 text-xs font-semibold transition-all" :class="viewMode === 'week' ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">Week</button>
                        <button @click="viewMode = 'list'; load()" class="px-3 py-1.5 text-xs font-semibold transition-all" :class="viewMode === 'list' ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">List</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading -->
    <div x-show="loading" class="py-20 text-center bg-navy-950">
        <div class="inline-flex items-center gap-3 text-slate-500">
            <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading classes...
        </div>
    </div>

    <!-- Week View -->
    <section x-show="!loading && viewMode === 'week'" class="relative py-8 bg-navy-950 min-h-[60vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4">
                <template x-for="day in weekDays()" :key="day">
                    <div class="glass-card rounded-xl p-4 gold-border">
                        <div class="text-center mb-3 pb-3 border-b border-navy-700/50">
                            <div class="text-xs text-gold-500 uppercase tracking-wider font-semibold" x-text="dayLabel(day)"></div>
                            <div class="text-lg font-bold text-white" x-text="dayNum(day)"></div>
                        </div>
                        <div class="space-y-2">
                            <template x-for="cls in classesForDay(day)" :key="cls.id">
                                <a :href="'/schedule/' + cls.id" class="block p-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 hover:border-gold-500/30 hover:bg-navy-800 transition-all group">
                                    <div class="text-[11px] font-medium text-gold-400" x-text="formatTime(cls.start_time)"></div>
                                    <div class="text-xs font-bold text-white group-hover:text-gold-400 transition-colors mt-0.5 line-clamp-2" x-text="cls.session_type_name || cls.session_name || 'Session'"></div>
                                    <div class="flex items-center justify-between mt-1.5">
                                        <span class="text-[10px] px-1.5 py-0.5 rounded font-medium text-white/80" :style="'background:' + (cls.category_color || '#d4af37')" x-text="cls.category_name || ''"></span>
                                        <span class="text-[10px] font-bold" :class="cls.is_full ? 'text-red-400' : 'text-emerald-400'" x-text="cls.is_full ? 'Full' : cls.spots_left + ' left'"></span>
                                    </div>
                                </a>
                            </template>
                            <div x-show="classesForDay(day).length === 0" class="text-center py-4">
                                <span class="text-xs text-slate-600">No classes</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- List View -->
    <section x-show="!loading && viewMode === 'list'" class="relative py-8 bg-navy-950 min-h-[60vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <template x-for="day in weekDays()" :key="day">
                <div x-show="classesForDay(day).length > 0" class="mb-8">
                    <h3 class="text-sm font-bold text-gold-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span x-text="dayLabel(day) + ', ' + dayFull(day)"></span>
                        <span class="flex-1 h-px bg-navy-700"></span>
                    </h3>
                    <div class="space-y-3">
                        <template x-for="cls in classesForDay(day)" :key="cls.id">
                            <a :href="'/schedule/' + cls.id" class="flex items-center gap-4 p-4 glass-card rounded-xl glass-card-hover group">
                                <div class="flex-shrink-0 w-16 text-center">
                                    <div class="text-sm font-bold text-gold-400" x-text="formatTime(cls.start_time)"></div>
                                    <div class="text-[10px] text-slate-500" x-text="formatTime(cls.end_time)"></div>
                                </div>
                                <div class="w-0.5 h-10 rounded-full" :style="'background:' + (cls.category_color || '#d4af37')"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-white group-hover:text-gold-400 transition-colors" x-text="cls.session_type_name || cls.session_name || 'Session'"></div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-medium text-white/80" :style="'background:' + (cls.category_color || '#d4af37')" x-text="cls.category_name"></span>
                                        <span x-show="cls.skill_levels" class="text-[10px] text-slate-500" x-text="cls.skill_levels"></span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <div x-show="cls.price > 0" class="text-sm font-extrabold text-white" x-text="'$' + parseFloat(cls.price).toFixed(2)"></div>
                                    <div x-show="!cls.price || cls.price == 0" class="text-sm font-extrabold gradient-gold">Free</div>
                                    <div class="text-xs font-semibold mt-0.5" :class="cls.is_full ? 'text-red-400' : 'text-emerald-400'" x-text="cls.is_full ? 'Full' : cls.spots_left + ' spots'"></div>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <div x-show="classes.length === 0" class="text-center py-20">
                <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-lg font-semibold text-slate-400">No classes this week</p>
                <p class="text-sm text-slate-600 mt-1">Try navigating to a different week or adjusting filters.</p>
            </div>
        </div>
    </section>
</div>

<script>
function schedulePage() {
    return {
        classes: [], categories: [], weekStart: null, viewMode: 'week', categoryFilter: null, loading: true,

        init() {
            this.weekStart = this.getMonday(new Date());
        },

        async load() {
            this.loading = true;
            if (!this.weekStart) this.weekStart = this.getMonday(new Date());
            try {
                if (!this.categories.length) {
                    this.categories = (await (await fetch(baseApi + '/api/public/categories')).json()).data || [];
                }
                const start = this.fmtDate(this.weekStart);
                const end = this.fmtDate(this.addDays(this.weekStart, 6));
                let url = baseApi + '/api/public/schedule?start=' + start + '&end=' + end;
                const fac = window.tenantApp?.selectedFacility || JSON.parse(localStorage.getItem('selected_facility') || 'null');
                if (fac?.id) url += '&facility_id=' + fac.id;
                else if (ORG.facilities.length) url += '&facility_id=' + ORG.facilities[0].id;
                if (this.categoryFilter) url += '&category_id=' + this.categoryFilter;
                this.classes = (await (await fetch(url)).json()).data || [];
            } catch(e) { this.classes = []; }
            this.loading = false;
        },

        weekDays() {
            const days = [];
            for (let i = 0; i < 7; i++) days.push(this.fmtDate(this.addDays(this.weekStart, i)));
            return days;
        },
        classesForDay(day) { return this.classes.filter(c => c.start_time?.substring(0,10) === day); },
        weekLabel() {
            const s = this.weekStart;
            const e = this.addDays(s, 6);
            return s.toLocaleDateString('en-US', {month:'short', day:'numeric'}) + ' — ' + e.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
        },
        dayLabel(d) { return new Date(d + 'T12:00:00').toLocaleDateString('en-US', {weekday:'short'}); },
        dayNum(d) { return new Date(d + 'T12:00:00').toLocaleDateString('en-US', {day:'numeric'}); },
        dayFull(d) { return new Date(d + 'T12:00:00').toLocaleDateString('en-US', {month:'long', day:'numeric'}); },

        prevWeek() { this.weekStart = this.addDays(this.weekStart, -7); this.load(); },
        nextWeek() { this.weekStart = this.addDays(this.weekStart, 7); this.load(); },
        goToday() { this.weekStart = this.getMonday(new Date()); this.load(); },

        getMonday(d) { const dt = new Date(d); const day = dt.getDay(); const diff = dt.getDate() - day + (day === 0 ? -6 : 1); dt.setDate(diff); dt.setHours(0,0,0,0); return dt; },
        addDays(d, n) { const dt = new Date(d); dt.setDate(dt.getDate() + n); return dt; },
        fmtDate(d) { return d.toISOString().split('T')[0]; },
        formatTime(dt) { return dt ? new Date(dt).toLocaleTimeString('en-US', {hour:'numeric', minute:'2-digit', hour12:true}) : ''; },
    };
}
window.addEventListener('facility-changed', () => {
    document.querySelectorAll('[x-data]').forEach(el => { if (el.__x?.$data?.load) el.__x.$data.load(); });
});
</script>
