<?php
/**
 * Tenant Facilities Page — K2 Navy/Gold Theme
 * Lists all active facilities with courts summary. Uses /api/public/facilities.
 */
?>

<div x-data="facilitiesPage()" x-init="load()">
    <!-- Page Header -->
    <section class="relative bg-navy-900 overflow-hidden py-20">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute inset-0 hero-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Locations
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white">Our Facilities</h1>
            <p class="mt-4 text-lg text-slate-400 max-w-2xl">Explore our world-class courts and venues. Every location is built for the best playing experience.</p>
        </div>
    </section>

    <!-- Loading -->
    <div x-show="loading" class="py-20 text-center bg-navy-950">
        <div class="inline-flex items-center gap-3 text-slate-500">
            <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading facilities...
        </div>
    </div>

    <!-- Facilities Grid -->
    <section x-show="!loading" class="relative py-12 bg-navy-950 min-h-[50vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Empty -->
            <div x-show="facilities.length === 0" class="text-center py-16">
                <div class="glass-card rounded-2xl p-10 inline-block max-w-md">
                    <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <p class="text-white font-bold">No Facilities</p>
                    <p class="text-slate-400 text-sm mt-1">No active facilities are currently available.</p>
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="facilities.length > 0">
                <template x-for="f in facilities" :key="f.id">
                    <a :href="'/facilities/' + f.slug" class="group glass-card rounded-2xl overflow-hidden gold-border card-hover transition-all" data-animate>
                        <!-- Image -->
                        <div class="relative h-48 overflow-hidden">
                            <template x-if="f.image_url">
                                <img :src="f.image_url" :alt="f.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </template>
                            <template x-if="!f.image_url">
                                <div class="w-full h-full bg-gradient-to-br from-navy-800 to-navy-900 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-navy-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                            </template>
                            <!-- Status badge -->
                            <div class="absolute top-3 right-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase" :class="f.status === 'active' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400'" x-text="f.status"></span>
                            </div>
                        </div>
                        <!-- Content -->
                        <div class="p-5">
                            <h3 class="text-lg font-display font-bold text-white group-hover:text-gold-400 transition-colors" x-text="f.name"></h3>
                            <p class="text-sm text-slate-400 mt-1" x-text="f.tagline || ''"></p>
                            <div class="flex items-center gap-1.5 mt-3 text-xs text-slate-500">
                                <svg class="w-3.5 h-3.5 text-gold-500/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span x-text="[f.city, f.state].filter(Boolean).join(', ') || 'Location TBD'"></span>
                            </div>
                            <div class="mt-4 pt-4 border-t border-navy-700/50 flex items-center justify-between">
                                <span class="text-xs text-gold-500 font-semibold group-hover:text-gold-400 transition-colors">View Details &rarr;</span>
                            </div>
                        </div>
                    </a>
                </template>
            </div>

        </div>
    </section>
</div>

<script>
function facilitiesPage() {
    return {
        loading: true,
        facilities: [],

        async load() {
            try {
                const resp = await fetch(`${window.baseApi}/api/public/facilities`);
                const json = await resp.json();
                this.facilities = json.data || [];
            } catch(e) { console.error('Failed to load facilities', e); }
            finally { this.loading = false; }
        }
    };
}
</script>
