<?php
/**
 * Tenant Sessions (Programs) Listing — K2 Navy/Gold Theme
 * Fully functional: fetches from /api/public/sessions, filterable, searchable.
 */
$orgName = htmlspecialchars($org['name'] ?? 'Sports Club');
?>

<div x-data="sessionsPage()" x-init="load()">
    <!-- Page Header -->
    <section class="relative bg-navy-900 overflow-hidden py-20">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute inset-0 hero-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Programs
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white">Our Programs</h1>
            <p class="mt-4 text-lg text-slate-400 max-w-2xl">Explore our programs and find sessions that match your skill level and interests.</p>
        </div>
    </section>

    <!-- Filters Bar -->
    <section class="sticky top-16 lg:top-20 z-30 glass border-b border-gold-500/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="search" @input.debounce.300ms="filter()"
                           placeholder="Search programs..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-navy-800/80 border border-navy-700 text-white placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-gold-500/30 focus:border-gold-500/50 outline-none transition-all">
                </div>
                <div class="flex flex-wrap gap-2">
                    <button @click="selectedCategory = null; filter()"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-all"
                            :class="!selectedCategory ? 'gradient-gold-bg text-navy-950 shadow-gold' : 'bg-navy-800 text-slate-300 border border-navy-700 hover:border-gold-500/30 hover:text-white'">All</button>
                    <template x-for="cat in categories" :key="cat.id">
                        <button @click="selectedCategory = cat.id; filter()"
                                class="px-4 py-2 rounded-full text-sm font-semibold transition-all"
                                :class="selectedCategory === cat.id ? 'text-white shadow-lg' : 'bg-navy-800 text-slate-300 border border-navy-700 hover:border-gold-500/30 hover:text-white'"
                                :style="selectedCategory === cat.id ? 'background:' + (cat.color || '#d4af37') : ''"
                                x-text="cat.name"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading -->
    <section x-show="loading" class="py-20 text-center bg-navy-950">
        <div class="inline-flex items-center gap-3 text-slate-500">
            <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading programs...
        </div>
    </section>

    <!-- Cards Grid -->
    <section x-show="!loading" class="relative py-16 bg-navy-950 min-h-[50vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-show="filtered.length > 0" class="mb-8 text-sm text-slate-500">
                <span x-text="filtered.length"></span> program<span x-show="filtered.length !== 1">s</span> found
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <template x-for="s in filtered" :key="s.id">
                    <div class="group glass-card rounded-2xl overflow-hidden flex flex-col glass-card-hover">
                        <!-- Image / Gradient Header -->
                        <div class="h-44 relative overflow-hidden">
                            <template x-if="s.picture_url">
                                <img :src="s.picture_url" :alt="s.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </template>
                            <template x-if="!s.picture_url">
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-navy-800 to-navy-900">
                                    <svg class="w-12 h-12 text-gold-500/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                            </template>
                            <div class="absolute top-3 left-3">
                                <span class="text-xs px-2.5 py-1 rounded-full font-semibold text-white shadow-sm" :style="'background:' + (s.category_color || '#d4af37')" x-text="s.category_name || 'General'"></span>
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-lg font-bold text-white group-hover:text-gold-400 transition-colors" x-text="s.name"></h3>
                            <p x-show="s.tagline" class="mt-1 text-sm text-slate-400 line-clamp-2" x-text="s.tagline"></p>

                            <div class="flex flex-wrap gap-2 mt-3">
                                <span x-show="s.skill_levels" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-navy-800 text-slate-400 font-medium border border-navy-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    <span x-text="s.skill_levels"></span>
                                </span>
                                <span x-show="s.duration_minutes" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-navy-800 text-slate-400 font-medium border border-navy-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span x-text="s.duration_minutes + ' min'"></span>
                                </span>
                                <span x-show="s.max_players" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-navy-800 text-slate-400 font-medium border border-navy-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span x-text="'Max ' + s.max_players"></span>
                                </span>
                            </div>

                            <div class="mt-auto pt-5 flex items-end justify-between">
                                <div>
                                    <template x-if="s.base_price > 0">
                                        <div><span class="text-xs text-slate-500">from</span> <span class="text-2xl font-extrabold text-white" x-text="'$' + parseFloat(s.base_price).toFixed(2)"></span></div>
                                    </template>
                                    <template x-if="!s.base_price || s.base_price == 0">
                                        <span class="text-2xl font-extrabold gradient-gold">Free</span>
                                    </template>
                                </div>
                                <a :href="'/sessions#' + s.id" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all">
                                    View Details
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="!loading && filtered.length === 0" class="text-center py-20">
                <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-lg font-semibold text-slate-400">No programs found</p>
                <p class="text-sm text-slate-600 mt-1">Try adjusting your filters or search term.</p>
            </div>
        </div>
    </section>
</div>

<script>
function sessionsPage() {
    return {
        allSessions: [], filtered: [], categories: [], selectedCategory: null, search: '', loading: true,
        async load() {
            this.loading = true;
            try {
                const [sessRes, catRes] = await Promise.all([
                    fetch(baseApi + '/api/public/sessions'),
                    fetch(baseApi + '/api/public/categories')
                ]);
                this.allSessions = (await sessRes.json()).data || [];
                this.categories = (await catRes.json()).data || [];
                this.filter();
            } catch(e) { this.allSessions = []; this.filtered = []; }
            this.loading = false;
        },
        filter() {
            let list = [...this.allSessions];
            if (this.selectedCategory) list = list.filter(s => s.category_id == this.selectedCategory);
            if (this.search.trim()) {
                const term = this.search.trim().toLowerCase();
                list = list.filter(s => (s.name||'').toLowerCase().includes(term) || (s.tagline||'').toLowerCase().includes(term) || (s.category_name||'').toLowerCase().includes(term));
            }
            this.filtered = list;
        }
    };
}
</script>
