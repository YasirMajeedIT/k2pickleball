<?php
/**
 * Tenant Facility Detail Page — K2 Navy/Gold Theme
 * Shows single facility info, courts list, map placeholder. Uses /api/public/facilities/{slug}.
 */
$slug = $facilitySlug ?? '';
?>

<div x-data="facilityDetailPage()" x-init="load()">
    <!-- Loading -->
    <div x-show="loading" class="min-h-[60vh] flex items-center justify-center bg-navy-950">
        <div class="inline-flex items-center gap-3 text-slate-500">
            <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading facility...
        </div>
    </div>

    <!-- Not Found -->
    <div x-show="!loading && !facility" class="min-h-[60vh] flex items-center justify-center bg-navy-950">
        <div class="text-center glass-card rounded-2xl p-10 max-w-md">
            <div class="text-6xl font-extrabold text-navy-800 mb-3">404</div>
            <p class="text-white font-bold">Facility Not Found</p>
            <p class="text-slate-400 text-sm mt-2">This facility doesn't exist or has been removed.</p>
            <a href="/facilities" class="inline-block mt-6 px-6 py-2.5 rounded-xl gradient-gold-bg text-navy-950 font-bold text-sm hover:shadow-gold transition-all">View All Facilities</a>
        </div>
    </div>

    <!-- Content -->
    <template x-if="!loading && facility">
        <div>
            <!-- Hero -->
            <section class="relative bg-navy-900 overflow-hidden py-20">
                <div class="absolute inset-0 grid-bg opacity-40"></div>
                <div class="absolute inset-0 hero-glow"></div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <a href="/facilities" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-gold-400 transition-colors mb-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        All Facilities
                    </a>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        Facility
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white" x-text="facility.name"></h1>
                    <p class="mt-3 text-lg text-slate-400" x-text="facility.tagline || ''"></p>
                    <div class="flex flex-wrap items-center gap-4 mt-5">
                        <template x-if="facility.city || facility.state">
                            <div class="flex items-center gap-1.5 text-sm text-slate-400">
                                <svg class="w-4 h-4 text-gold-500/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span x-text="[facility.address_line1, facility.city, facility.state, facility.zip_code].filter(Boolean).join(', ')"></span>
                            </div>
                        </template>
                        <template x-if="facility.phone">
                            <div class="flex items-center gap-1.5 text-sm text-slate-400">
                                <svg class="w-4 h-4 text-gold-500/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span x-text="facility.phone"></span>
                            </div>
                        </template>
                        <template x-if="facility.email">
                            <div class="flex items-center gap-1.5 text-sm text-slate-400">
                                <svg class="w-4 h-4 text-gold-500/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span x-text="facility.email"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <!-- Description & Image -->
            <section class="relative py-12 bg-navy-950">
                <div class="absolute inset-0 section-glow"></div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Image -->
                        <div class="glass-card rounded-2xl overflow-hidden gold-border" x-show="facility.image_url">
                            <img :src="facility.image_url" :alt="facility.name" class="w-full h-80 object-cover">
                        </div>
                        <div class="glass-card rounded-2xl overflow-hidden gold-border flex items-center justify-center h-80" x-show="!facility.image_url">
                            <svg class="w-20 h-20 text-navy-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <!-- Info -->
                        <div>
                            <template x-if="facility.description">
                                <div class="glass-card rounded-2xl p-6 gold-border mb-6">
                                    <h2 class="text-lg font-display font-bold text-white mb-3">About This Facility</h2>
                                    <p class="text-slate-400 leading-relaxed" x-text="facility.description"></p>
                                </div>
                            </template>
                            <!-- Quick Stats -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="glass-card rounded-xl p-4 text-center gold-border">
                                    <div class="text-2xl font-extrabold text-gold-400" x-text="facility.courts?.length || 0"></div>
                                    <div class="text-xs text-slate-400 mt-1">Active Courts</div>
                                </div>
                                <div class="glass-card rounded-xl p-4 text-center gold-border">
                                    <div class="text-2xl font-extrabold text-gold-400" x-text="facility.courts?.filter(c => c.is_indoor).length || 0"></div>
                                    <div class="text-xs text-slate-400 mt-1">Indoor Courts</div>
                                </div>
                            </div>
                            <!-- CTAs -->
                            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                                <a href="/schedule" class="flex-1 py-3 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-gold transition-all text-center">View Schedule</a>
                                <?php if (!empty($org['system_categories']['book-a-court']['is_active'])): ?>
                                <a href="/book-court" class="flex-1 py-3 rounded-xl font-bold text-sm bg-navy-800 border border-navy-700 text-white hover:border-gold-500/30 transition-all text-center"><?= htmlspecialchars($org['system_categories']['book-a-court']['name'] ?? 'Book a Court') ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Courts List -->
            <section class="relative py-12 bg-navy-950" x-show="facility.courts && facility.courts.length > 0">
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-2xl font-display font-bold text-white mb-6">Courts</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <template x-for="court in facility.courts" :key="court.id">
                            <div class="glass-card rounded-xl p-5 gold-border" data-animate>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-lg gradient-gold-bg flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-extrabold text-navy-950" x-text="'#' + court.court_number"></span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-sm" x-text="court.name"></h3>
                                        <p class="text-xs text-slate-400 capitalize" x-text="court.sport_type"></p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold" :class="court.is_indoor ? 'bg-blue-500/10 text-blue-400' : 'bg-emerald-500/10 text-emerald-400'" x-text="court.is_indoor ? 'Indoor' : 'Outdoor'"></span>
                                    <template x-if="court.is_lighted">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400">Lighted</span>
                                    </template>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-navy-700/50 text-slate-400" x-text="'Max ' + court.max_players + ' players'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </section>
        </div>
    </template>
</div>

<script>
function facilityDetailPage() {
    return {
        loading: true,
        facility: null,

        async load() {
            const slug = <?= json_encode($slug) ?>;
            if (!slug) { this.loading = false; return; }
            try {
                const resp = await fetch(`${window.baseApi}/api/public/facilities/${encodeURIComponent(slug)}`);
                const json = await resp.json();
                if (json.success) this.facility = json.data;
            } catch(e) { console.error('Failed to load facility', e); }
            finally { this.loading = false; }
        }
    };
}
</script>
