<?php
/**
 * Tenant Home Page — K2 Navy/Gold Premium Theme
 * Hero, upcoming sessions, facilities, CTA — all with glassmorphism and gold accents.
 */
$orgName = htmlspecialchars($org['name'] ?? 'Sports Club');
$tagline = htmlspecialchars($branding['tagline'] ?? 'Book. Play. Compete.');
$heroImage = $branding['hero_image'] ?? null;
?>

<!-- ═══ HERO SECTION ═══ -->
<section class="relative overflow-hidden min-h-[85vh] flex items-center">
    <div class="absolute inset-0 bg-navy-950"></div>
    <div class="absolute inset-0 grid-bg"></div>
    <div class="absolute inset-0 hero-glow"></div>
    <?php if ($heroImage): ?>
    <div class="absolute inset-0">
        <img src="<?= htmlspecialchars($heroImage) ?>" alt="" class="w-full h-full object-cover opacity-15">
        <div class="absolute inset-0 bg-gradient-to-r from-navy-950 via-navy-950/80 to-navy-950/40"></div>
    </div>
    <?php endif; ?>
    <div class="absolute top-20 right-1/4 w-96 h-96 bg-gold-500/5 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-20 left-10 w-72 h-72 bg-primary-500/5 rounded-full blur-3xl animate-float" style="animation-delay:2s"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-0 w-full">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div data-animate>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full glass-card text-gold-400 text-sm font-medium mb-8">
                    <span class="w-2 h-2 rounded-full bg-gold-500 animate-pulse-gold"></span>
                    Now Booking
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-display font-extrabold leading-[1.1] tracking-tight">
                    Welcome to<br>
                    <span class="gradient-gold"><?= $orgName ?></span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-slate-400 leading-relaxed max-w-lg font-light"><?= $tagline ?></p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="/schedule" class="group/btn px-8 py-4 text-base font-semibold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300 inline-flex items-center gap-2">
                        View Schedule
                        <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <?php if ($courtCatActive): ?>
                    <a href="/book-court" class="px-8 py-4 text-base font-semibold text-white border border-gold-500/30 hover:border-gold-500/50 hover:bg-gold-500/5 rounded-xl transition-all duration-300 inline-flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <?= $courtCatName ?>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="mt-14 flex flex-wrap gap-8">
                    <?php if (!empty($facilities)): ?>
                    <div class="text-center">
                        <div class="text-3xl font-extrabold gradient-gold"><?= count($facilities) ?></div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider mt-1">Location<?= count($facilities) > 1 ? 's' : '' ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="text-center" x-data="{count:0}" x-init="fetch(baseApi+'/api/public/sessions').then(r=>r.json()).then(d=>{count=d.data?.length||0})">
                        <div class="text-3xl font-extrabold gradient-gold" x-text="count">—</div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider mt-1">Programs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-extrabold gradient-gold">24/7</div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider mt-1">Online Booking</div>
                    </div>
                </div>
            </div>

            <!-- Decorative glass panel -->
            <div class="hidden lg:block" data-animate>
                <div class="relative">
                    <div class="glass-card rounded-2xl p-6 gold-border animate-float">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-xl gradient-gold-bg flex items-center justify-center">
                                <svg class="w-6 h-6 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="text-white font-bold">Easy Booking</div>
                                <div class="text-slate-400 text-sm">Reserve in seconds</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 rounded-lg bg-navy-800/50 border border-navy-700/50">
                                <div><div class="text-sm font-medium text-white">Round Robin</div><div class="text-xs text-slate-500">Tomorrow · 10:00 AM</div></div>
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-500/20 text-emerald-400">Open</span>
                            </div>
                            <div class="flex justify-between items-center p-3 rounded-lg bg-navy-800/50 border border-navy-700/50">
                                <div><div class="text-sm font-medium text-white">Skills Clinic</div><div class="text-xs text-slate-500">Fri · 2:00 PM</div></div>
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-gold-500/20 text-gold-400">3 Left</span>
                            </div>
                            <div class="flex justify-between items-center p-3 rounded-lg bg-navy-800/50 border border-navy-700/50">
                                <div><div class="text-sm font-medium text-white">League Play</div><div class="text-xs text-slate-500">Sat · 6:00 PM</div></div>
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-red-500/20 text-red-400">Full</span>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -inset-4 bg-gradient-to-r from-gold-500/5 via-primary-500/5 to-gold-500/5 rounded-3xl blur-xl -z-10"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-navy-950 to-transparent"></div>
</section>

<!-- ═══ UPCOMING SESSIONS ═══ -->
<section class="relative py-24 bg-navy-950" x-data="upcomingSessions()" x-init="load()">
    <div class="absolute inset-0 section-glow"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14" data-animate>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Coming Up
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white">Upcoming Sessions</h2>
            <p class="mt-4 text-lg text-slate-400 max-w-2xl mx-auto">Browse what's coming up and reserve your spot today.</p>
        </div>

        <div class="flex flex-wrap justify-center gap-2 mb-10" x-show="categories.length > 0" x-cloak>
            <button @click="filterCategory = null; load()" class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300" :class="!filterCategory ? 'gradient-gold-bg text-navy-950 shadow-gold' : 'glass-card text-slate-300 hover:text-white'">All</button>
            <template x-for="cat in categories" :key="cat.id">
                <button @click="filterCategory = cat.id; load()" class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300" :class="filterCategory === cat.id ? 'text-navy-950 shadow-lg' : 'glass-card text-slate-300 hover:text-white'" :style="filterCategory === cat.id ? 'background:' + (cat.color || '#d4af37') : ''" x-text="cat.name"></button>
            </template>
        </div>

        <div x-show="loading" class="text-center py-16">
            <div class="inline-flex items-center gap-3 text-slate-500">
                <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Loading schedule...
            </div>
        </div>

        <div x-show="!loading && sessions.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="s in sessions.slice(0, 9)" :key="s.id">
                <a :href="'/schedule/' + s.id" class="group block glass-card rounded-2xl overflow-hidden glass-card-hover">
                    <div class="h-1 w-full" :style="'background:' + (s.category_color || '#d4af37')"></div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-xs text-slate-500 mb-3">
                            <svg class="w-3.5 h-3.5 text-gold-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span x-text="formatDate(s.start_time)"></span>
                            <span class="text-navy-600">·</span>
                            <span x-text="formatTime(s.start_time) + ' - ' + formatTime(s.end_time)"></span>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-gold-400 transition-colors" x-text="s.session_type_name || s.session_name || 'Session'"></h3>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-medium text-white/90" :style="'background:' + (s.category_color || '#d4af37')" x-text="s.category_name || 'General'"></span>
                            <span x-show="s.skill_levels" class="text-xs px-2.5 py-0.5 rounded-full bg-navy-800 text-slate-400 font-medium border border-navy-700" x-text="s.skill_levels"></span>
                        </div>
                        <div class="flex items-end justify-between mt-5 pt-4 border-t border-navy-700/50">
                            <div>
                                <div x-show="s.price > 0" class="text-2xl font-extrabold text-white" x-text="'$' + parseFloat(s.price).toFixed(2)"></div>
                                <div x-show="!s.price || s.price == 0" class="text-2xl font-extrabold gradient-gold">Free</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold" :class="s.is_full ? 'text-red-400' : 'text-emerald-400'" x-text="s.is_full ? 'Full' : s.spots_left + ' spots left'"></div>
                                <div class="text-[10px] text-slate-600" x-text="s.booked_count + ' registered'"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        <div x-show="!loading && sessions.length === 0" class="text-center py-20">
            <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-lg font-semibold text-slate-400">No upcoming sessions</p>
            <p class="text-sm text-slate-600 mt-1">Check back soon for new sessions and classes.</p>
        </div>

        <div x-show="sessions.length > 9" class="text-center mt-12">
            <a href="/schedule" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold gradient-gold-bg text-navy-950 rounded-xl shadow-gold hover:shadow-gold-lg transition-all">View Full Schedule <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></a>
        </div>
    </div>
</section>

<!-- ═══ FACILITIES ═══ -->
<?php if (count($facilities) > 0): ?>
<section class="relative py-24 bg-navy-900">
    <div class="absolute inset-0 grid-bg opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14" data-animate>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">Our Venues</div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white">Our Locations</h2>
            <p class="mt-4 text-lg text-slate-400">Find a facility near you and start playing.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-<?= min(count($facilities), 3) ?> gap-8">
            <?php foreach (array_slice($facilities, 0, 6) as $f): ?>
            <a href="/facilities/<?= htmlspecialchars($f['slug']) ?>" class="group block glass-card rounded-2xl overflow-hidden glass-card-hover" data-animate>
                <div class="h-48 bg-gradient-to-br from-navy-800 to-navy-850 flex items-center justify-center relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-gold-500/5 to-primary-500/5 group-hover:from-gold-500/10 group-hover:to-primary-500/10 transition-all duration-500"></div>
                    <svg class="w-16 h-16 text-gold-500/30 group-hover:text-gold-500/50 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-white group-hover:text-gold-400 transition-colors"><?= htmlspecialchars($f['name']) ?></h3>
                    <?php if (!empty($f['city'])): ?>
                    <p class="mt-1 text-sm text-slate-500"><?= htmlspecialchars($f['city'] . (!empty($f['state']) ? ', ' . $f['state'] : '')) ?></p>
                    <?php endif; ?>
                    <div class="mt-4 flex items-center gap-2 text-gold-500 text-sm font-semibold group-hover:gap-3 transition-all">View Location <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ CTA ═══ -->
<section class="relative py-24 bg-navy-950 overflow-hidden">
    <div class="absolute inset-0 hero-glow"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gold-500/5 rounded-full blur-3xl"></div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-animate>
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white">Ready to Play?</h2>
        <p class="mt-6 text-lg text-slate-400 max-w-2xl mx-auto">Create your free account to book sessions, track your progress, and join our community.</p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="/register" class="px-8 py-4 text-base font-semibold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300">Create Free Account</a>
            <a href="/schedule" class="px-8 py-4 text-base font-semibold text-white border border-navy-700 hover:border-gold-500/30 hover:bg-gold-500/5 rounded-xl transition-all inline-flex items-center gap-2">Browse Schedule <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></a>
        </div>
    </div>
</section>

<script>
function upcomingSessions() {
    return {
        sessions: [], categories: [], loading: true, filterCategory: null,
        async load() {
            this.loading = true;
            try {
                if (!this.categories.length) {
                    const catRes = await fetch(baseApi + '/api/public/categories');
                    this.categories = (await catRes.json()).data || [];
                }
                let url = baseApi + '/api/public/schedule?start=' + this.today() + '&end=' + this.futureDate(14);
                const fac = window.tenantApp?.selectedFacility || JSON.parse(localStorage.getItem('selected_facility') || 'null');
                if (fac?.id) url += '&facility_id=' + fac.id;
                else if (ORG.facilities.length) url += '&facility_id=' + ORG.facilities[0].id;
                if (this.filterCategory) url += '&category_id=' + this.filterCategory;
                const res = await fetch(url);
                this.sessions = (await res.json()).data || [];
            } catch(e) { this.sessions = []; }
            this.loading = false;
        },
        today() { return new Date().toISOString().split('T')[0]; },
        futureDate(days) { const d = new Date(); d.setDate(d.getDate() + days); return d.toISOString().split('T')[0]; },
        formatDate(dt) { return dt ? new Date(dt).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }) : ''; },
        formatTime(dt) { return dt ? new Date(dt).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) : ''; },
    };
}
window.addEventListener('facility-changed', () => {
    document.querySelectorAll('[x-data]').forEach(el => { if (el.__x?.$data?.load) el.__x.$data.load(); });
});
</script>
