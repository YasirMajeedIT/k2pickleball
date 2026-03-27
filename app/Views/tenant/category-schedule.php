<?php
/**
 * Tenant Category Schedule Page — K2 Navy/Gold Theme
 * Dynamic page per category. Fetches category info + filtered schedule.
 * Receives $categorySlug from TenantController::categoryPage().
 */
$slug = htmlspecialchars($categorySlug ?? '');
?>

<div x-data="categorySchedule('<?= $slug ?>')" x-init="load()">
    <!-- Loading -->
    <div x-show="loading" class="py-32 text-center bg-navy-950 min-h-screen">
        <div class="inline-flex items-center gap-3 text-slate-500">
            <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading...
        </div>
    </div>

    <!-- Not Found -->
    <div x-show="!loading && !category" class="py-32 text-center bg-navy-950 min-h-screen">
        <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
        </div>
        <p class="text-lg font-semibold text-slate-400">Category not found</p>
        <a href="/schedule" class="inline-block mt-4 text-sm text-gold-400 hover:text-gold-300 font-medium">&#8592; Back to Schedule</a>
    </div>

    <!-- Category Page Content -->
    <template x-if="!loading && category">
        <div>
            <!-- Hero Banner -->
            <section class="relative overflow-hidden py-16 sm:py-20"
                     :style="'background: linear-gradient(135deg, ' + (category.color || '#162844') + '22, #0b1629 70%)'">
                <div class="absolute inset-0 grid-bg opacity-40"></div>
                <div class="absolute inset-0 hero-glow"></div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-start gap-5">
                        <!-- Category icon/image -->
                        <div x-show="category.image_url" class="flex-shrink-0 hidden sm:block">
                            <img :src="category.image_url" :alt="category.name" class="w-20 h-20 rounded-2xl object-cover border border-white/10 shadow-lg">
                        </div>
                        <div x-show="!category.image_url" class="flex-shrink-0 hidden sm:block">
                            <div class="w-20 h-20 rounded-2xl flex items-center justify-center" :style="'background: ' + (category.color || '#d4af37') + '22; border: 1px solid ' + (category.color || '#d4af37') + '44'">
                                <svg class="w-10 h-10" :style="'color: ' + (category.color || '#d4af37')" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-3"
                                 :style="'background: ' + (category.color || '#d4af37') + '22; color: ' + (category.color || '#d4af37')">
                                <span class="w-2 h-2 rounded-full" :style="'background: ' + (category.color || '#d4af37')"></span>
                                <span x-text="category.name"></span>
                            </div>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white" x-text="category.name"></h1>
                            <p x-show="category.description" class="mt-3 text-lg text-slate-400 max-w-2xl" x-text="category.description"></p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Controls Bar -->
            <section class="sticky top-16 lg:top-20 z-30 glass border-b border-gold-500/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <!-- Date Navigation -->
                        <div class="flex items-center gap-2">
                            <button @click="prev()" class="p-2 rounded-lg bg-navy-800 border border-navy-700 text-slate-300 hover:text-white hover:border-gold-500/30 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button @click="goToday()" class="px-4 py-2 rounded-lg text-sm font-semibold bg-navy-800 border border-navy-700 text-slate-300 hover:text-white hover:border-gold-500/30 transition-all">Today</button>
                            <button @click="next()" class="p-2 rounded-lg bg-navy-800 border border-navy-700 text-slate-300 hover:text-white hover:border-gold-500/30 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="relative">
                                <button class="flex items-center gap-2 ml-1 px-3 py-2 rounded-lg bg-navy-800 border border-navy-700 text-sm font-medium text-white hover:border-gold-500/30 transition-all">
                                    <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span x-text="dateLabel()"></span>
                                </button>
                                <input type="date" @change="jumpToDate($event.target.value)"
                                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" :value="fmtDate(currentDate)">
                            </div>
                        </div>

                        <!-- View Toggle -->
                        <div class="flex bg-navy-800 border border-navy-700 rounded-lg overflow-hidden">
                            <button @click="switchView('week')" class="px-3 py-1.5 text-xs font-semibold transition-all" :class="viewMode === 'week' ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">Week</button>
                            <button @click="switchView('today')" class="px-3 py-1.5 text-xs font-semibold transition-all" :class="viewMode === 'today' ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">Day</button>
                            <button @click="switchView('list')" class="px-3 py-1.5 text-xs font-semibold transition-all" :class="viewMode === 'list' ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">List</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Schedule Loading -->
            <div x-show="scheduleLoading" class="py-20 text-center bg-navy-950">
                <div class="inline-flex items-center gap-3 text-slate-500">
                    <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Loading classes...
                </div>
            </div>

            <!-- ═══ WEEK VIEW ═══ -->
            <section x-show="!scheduleLoading && viewMode === 'week'" class="relative py-8 bg-navy-950 min-h-[50vh]">
                <div class="absolute inset-0 section-glow"></div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4">
                        <template x-for="day in weekDays()" :key="day">
                            <div class="glass-card rounded-xl p-4 gold-border" :class="{'border-gold-500/30 bg-gold-500/5': day === todayStr}">
                                <div class="text-center mb-3 pb-3 border-b border-navy-700/50">
                                    <div class="text-xs uppercase tracking-wider font-semibold" :style="'color: ' + (category.color || '#d4af37')" x-text="dayLabel(day)"></div>
                                    <div class="text-lg font-bold" :class="day === todayStr ? 'text-gold-400' : 'text-white'" x-text="dayNum(day)"></div>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="cls in classesForDay(day)" :key="cls.id">
                                        <a :href="'/schedule/' + cls.id" class="block p-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 hover:border-gold-500/30 hover:bg-navy-800 transition-all group">
                                            <div class="text-[11px] font-medium" :style="'color: ' + (category.color || '#d4af37')" x-text="formatTime(cls.start_time)"></div>
                                            <div class="text-xs font-bold text-white group-hover:text-gold-400 transition-colors mt-0.5 line-clamp-2" x-text="cls.session_type_name || cls.session_name || 'Session'"></div>
                                            <div class="flex items-center justify-between mt-1.5">
                                                <span x-show="cls.price > 0" class="text-[10px] font-bold text-white" x-text="'$' + parseFloat(cls.price).toFixed(2)"></span>
                                                <span x-show="!cls.price || cls.price == 0" class="text-[10px] font-bold gradient-gold">Free</span>
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

            <!-- ═══ DAY VIEW ═══ -->
            <section x-show="!scheduleLoading && viewMode === 'today'" class="relative py-8 bg-navy-950 min-h-[50vh]">
                <div class="absolute inset-0 section-glow"></div>
                <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-14 h-14 rounded-xl glass-card flex flex-col items-center justify-center" :style="'border-color: ' + (category.color || '#d4af37') + '44'">
                            <span class="text-[10px] uppercase font-bold" :style="'color: ' + (category.color || '#d4af37')" x-text="dayLabel(fmtDate(currentDate))"></span>
                            <span class="text-xl font-extrabold text-white leading-none" x-text="dayNum(fmtDate(currentDate))"></span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white" x-text="currentDate.toLocaleDateString('en-US', {weekday:'long', month:'long', day:'numeric', year:'numeric'})"></h2>
                            <p class="text-sm text-slate-400" x-text="classesForDay(fmtDate(currentDate)).length + ' class' + (classesForDay(fmtDate(currentDate)).length !== 1 ? 'es' : '')"></p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <template x-for="cls in classesForDay(fmtDate(currentDate))" :key="cls.id">
                            <a :href="'/schedule/' + cls.id" class="flex items-center gap-4 p-4 glass-card rounded-xl glass-card-hover group">
                                <div class="flex-shrink-0 w-20 text-center">
                                    <div class="text-sm font-bold" :style="'color: ' + (category.color || '#d4af37')" x-text="formatTime(cls.start_time)"></div>
                                    <div class="text-[10px] text-slate-500" x-text="formatTime(cls.end_time)"></div>
                                </div>
                                <div class="w-1 h-12 rounded-full" :style="'background:' + (category.color || '#d4af37')"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-base font-bold text-white group-hover:text-gold-400 transition-colors" x-text="cls.session_type_name || cls.session_name || 'Session'"></div>
                                    <div x-show="cls.skill_levels" class="text-xs text-slate-500 mt-1" x-text="cls.skill_levels"></div>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <div x-show="cls.price > 0" class="text-base font-extrabold text-white" x-text="'$' + parseFloat(cls.price).toFixed(2)"></div>
                                    <div x-show="!cls.price || cls.price == 0" class="text-base font-extrabold gradient-gold">Free</div>
                                    <div class="text-xs font-semibold mt-1" :class="cls.is_full ? 'text-red-400' : 'text-emerald-400'" x-text="cls.is_full ? 'Full' : cls.spots_left + ' spots left'"></div>
                                </div>
                            </a>
                        </template>
                    </div>
                    <div x-show="classesForDay(fmtDate(currentDate)).length === 0" class="text-center py-20">
                        <p class="text-lg font-semibold text-slate-400">No classes today</p>
                        <p class="text-sm text-slate-600 mt-1">Try a different date or check the week view.</p>
                    </div>
                </div>
            </section>

            <!-- ═══ LIST VIEW ═══ -->
            <section x-show="!scheduleLoading && viewMode === 'list'" class="relative py-8 bg-navy-950 min-h-[50vh]">
                <div class="absolute inset-0 section-glow"></div>
                <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <template x-for="day in activeDays()" :key="day">
                        <div class="mb-8">
                            <h3 class="text-sm font-bold uppercase tracking-wider mb-3 flex items-center gap-2" :style="'color: ' + (category.color || '#d4af37')">
                                <span x-text="dayLabel(day) + ', ' + dayFull(day)"></span>
                                <span class="text-slate-500 text-xs font-normal normal-case" x-text="'(' + classesForDay(day).length + ')'"></span>
                                <span class="flex-1 h-px bg-navy-700"></span>
                            </h3>
                            <div class="space-y-3">
                                <template x-for="cls in classesForDay(day)" :key="cls.id">
                                    <a :href="'/schedule/' + cls.id" class="flex items-center gap-4 p-4 glass-card rounded-xl glass-card-hover group">
                                        <div class="flex-shrink-0 w-16 text-center">
                                            <div class="text-sm font-bold" :style="'color: ' + (category.color || '#d4af37')" x-text="formatTime(cls.start_time)"></div>
                                            <div class="text-[10px] text-slate-500" x-text="formatTime(cls.end_time)"></div>
                                        </div>
                                        <div class="w-0.5 h-10 rounded-full" :style="'background:' + (category.color || '#d4af37')"></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-bold text-white group-hover:text-gold-400 transition-colors" x-text="cls.session_type_name || cls.session_name || 'Session'"></div>
                                            <div x-show="cls.skill_levels" class="text-xs text-slate-500 mt-1" x-text="cls.skill_levels"></div>
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
                        <p class="text-lg font-semibold text-slate-400">No classes scheduled</p>
                        <p class="text-sm text-slate-600 mt-1">Check back for upcoming classes in this category.</p>
                    </div>
                </div>
            </section>
        </div>
    </template>
</div>

<script>
function categorySchedule(slug) {
    const today = new Date(); today.setHours(0,0,0,0);
    return {
        slug: slug,
        category: null,
        classes: [],
        loading: true,
        scheduleLoading: false,
        currentDate: new Date(today),
        weekStart: null,
        viewMode: 'week',
        todayStr: '',

        init() {
            this.todayStr = this.fmtDate(today);
            this.weekStart = this.getMonday(new Date(today));
        },

        async load() {
            this.loading = true;
            try {
                const res = await fetch(baseApi + '/api/public/category/' + encodeURIComponent(this.slug));
                const json = await res.json();
                this.category = json.data || null;
            } catch(e) {
                this.category = null;
            }
            this.loading = false;
            if (this.category) {
                // Apply default view from category settings
                if (this.category.view_settings?.default_view) {
                    this.viewMode = this.category.view_settings.default_view;
                }
                await this.loadSchedule();
            }
        },

        async loadSchedule() {
            this.scheduleLoading = true;
            try {
                const range = this.getDateRange();
                let url = baseApi + '/api/public/schedule?start=' + range.start + '&end=' + range.end;
                const fac = JSON.parse(localStorage.getItem('selected_facility') || 'null');
                if (fac?.id) url += '&facility_id=' + fac.id;
                else if (ORG.facilities.length) url += '&facility_id=' + ORG.facilities[0].id;
                url += '&category_id=' + this.category.id;
                this.classes = (await (await fetch(url)).json()).data || [];
            } catch(e) { this.classes = []; }
            this.scheduleLoading = false;
        },

        switchView(mode) { this.viewMode = mode; this.loadSchedule(); },

        getDateRange() {
            switch (this.viewMode) {
                case 'today': return { start: this.fmtDate(this.currentDate), end: this.fmtDate(this.currentDate) };
                case 'list': return { start: this.fmtDate(this.weekStart), end: this.fmtDate(this.addDays(this.weekStart, 13)) };
                default: return { start: this.fmtDate(this.weekStart), end: this.fmtDate(this.addDays(this.weekStart, 6)) };
            }
        },
        dateLabel() {
            switch (this.viewMode) {
                case 'today': return this.currentDate.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
                case 'list': { const s = this.weekStart, e = this.addDays(s, 13); return s.toLocaleDateString('en-US', {month:'short', day:'numeric'}) + ' — ' + e.toLocaleDateString('en-US', {month:'short', day:'numeric'}); }
                default: { const s = this.weekStart, e = this.addDays(s, 6); return s.toLocaleDateString('en-US', {month:'short', day:'numeric'}) + ' — ' + e.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}); }
            }
        },

        prev() {
            if (this.viewMode === 'today') { this.currentDate = this.addDays(this.currentDate, -1); }
            else if (this.viewMode === 'list') { this.weekStart = this.addDays(this.weekStart, -14); this.currentDate = new Date(this.weekStart); }
            else { this.weekStart = this.addDays(this.weekStart, -7); this.currentDate = new Date(this.weekStart); }
            this.loadSchedule();
        },
        next() {
            if (this.viewMode === 'today') { this.currentDate = this.addDays(this.currentDate, 1); }
            else if (this.viewMode === 'list') { this.weekStart = this.addDays(this.weekStart, 14); this.currentDate = new Date(this.weekStart); }
            else { this.weekStart = this.addDays(this.weekStart, 7); this.currentDate = new Date(this.weekStart); }
            this.loadSchedule();
        },
        goToday() { this.currentDate = new Date(today); this.weekStart = this.getMonday(new Date(today)); this.loadSchedule(); },
        jumpToDate(dateStr) {
            if (!dateStr) return;
            const d = new Date(dateStr + 'T12:00:00');
            if (isNaN(d)) return;
            this.currentDate = d; this.weekStart = this.getMonday(d); this.loadSchedule();
        },

        weekDays() { const days = []; for (let i = 0; i < 7; i++) days.push(this.fmtDate(this.addDays(this.weekStart, i))); return days; },
        activeDays() {
            const range = this.getDateRange(); const days = [];
            let cur = new Date(range.start + 'T12:00:00'); const endD = new Date(range.end + 'T12:00:00');
            while (cur <= endD) { const d = this.fmtDate(cur); if (this.classesForDay(d).length > 0) days.push(d); cur = this.addDays(cur, 1); }
            return days;
        },
        classesForDay(day) { return this.classes.filter(c => c.start_time?.substring(0,10) === day); },

        dayLabel(d) { return new Date(d + 'T12:00:00').toLocaleDateString('en-US', {weekday:'short'}); },
        dayNum(d) { return new Date(d + 'T12:00:00').toLocaleDateString('en-US', {day:'numeric'}); },
        dayFull(d) { return new Date(d + 'T12:00:00').toLocaleDateString('en-US', {month:'long', day:'numeric'}); },

        getMonday(d) { const dt = new Date(d); const day = dt.getDay(); const diff = dt.getDate() - day + (day === 0 ? -6 : 1); dt.setDate(diff); dt.setHours(0,0,0,0); return dt; },
        addDays(d, n) { const dt = new Date(d); dt.setDate(dt.getDate() + n); return dt; },
        fmtDate(d) { return d.toISOString().split('T')[0]; },
        formatTime(dt) { return dt ? new Date(dt).toLocaleTimeString('en-US', {hour:'numeric', minute:'2-digit', hour12:true}) : ''; },
    };
}
window.addEventListener('facility-changed', () => {
    document.querySelectorAll('[x-data]').forEach(el => { if (el.__x?.$data?.loadSchedule) el.__x.$data.loadSchedule(); });
});
</script>
