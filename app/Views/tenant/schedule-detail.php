<?php
/**
 * Tenant Schedule Detail / Class Booking Page — K2 Navy/Gold Theme
 * Fully functional: class detail, pricing tiers, discount codes, booking with auth.
 */
$classId = $classId ?? 0;
?>

<div x-data="classDetailPage()" x-init="load()">
    <!-- Loading -->
    <div x-show="loading" class="py-32 text-center bg-navy-950">
        <svg class="animate-spin w-8 h-8 text-gold-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <!-- Content -->
    <div x-show="!loading && cls" x-cloak>
        <!-- Header -->
        <section class="relative bg-navy-900 overflow-hidden py-16">
            <div class="absolute inset-0 grid-bg opacity-40"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <a href="/schedule" class="inline-flex items-center gap-1.5 text-sm text-gold-500 hover:text-gold-400 font-medium mb-6 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Schedule
                </a>
                <div class="flex flex-wrap items-start gap-4">
                    <span class="text-xs px-3 py-1 rounded-full font-semibold text-white" :style="'background:' + (cls.category_color || '#d4af37')" x-text="cls.category_name || 'Session'"></span>
                    <span x-show="cls.skill_levels" class="text-xs px-3 py-1 rounded-full bg-navy-800 text-slate-400 border border-navy-700 font-medium" x-text="cls.skill_levels"></span>
                </div>
                <h1 class="mt-4 text-3xl sm:text-4xl font-display font-extrabold text-white" x-text="cls.session_type_name || cls.session_name || 'Session'"></h1>
                <div class="mt-3 flex flex-wrap items-center gap-4 text-slate-400">
                    <span class="inline-flex items-center gap-1.5 text-sm">
                        <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-text="formatDateFull(cls.start_time)"></span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-sm">
                        <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="formatTime(cls.start_time) + ' — ' + formatTime(cls.end_time)"></span>
                    </span>
                </div>
            </div>
        </section>

        <!-- Body -->
        <section class="relative py-12 bg-navy-950">
            <div class="absolute inset-0 section-glow"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-10">
                    <!-- Left: Details -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Description -->
                        <div x-show="cls.description" class="glass-card rounded-2xl p-6 gold-border">
                            <h3 class="text-lg font-bold text-white mb-3">About This Session</h3>
                            <div class="text-sm text-slate-400 leading-relaxed whitespace-pre-line" x-text="cls.description"></div>
                        </div>

                        <!-- Courts -->
                        <div x-show="cls.courts && cls.courts.length" class="glass-card rounded-2xl p-6 gold-border">
                            <h3 class="text-lg font-bold text-white mb-4">Assigned Courts</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <template x-for="court in cls.courts" :key="court.id">
                                    <div class="p-3 rounded-lg bg-navy-800/50 border border-navy-700/50 text-center">
                                        <div class="text-sm font-bold text-white" x-text="court.name"></div>
                                        <div class="text-xs text-slate-500 mt-0.5" x-text="court.sport_type || 'Court'"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Pricing Tiers -->
                        <div class="glass-card rounded-2xl p-6 gold-border">
                            <h3 class="text-lg font-bold text-white mb-4">Pricing</h3>
                            <div class="space-y-3">
                                <!-- Base price -->
                                <label class="flex items-center gap-3 p-3 rounded-lg bg-navy-800/50 border border-navy-700/50 cursor-pointer hover:border-gold-500/30 transition-all" :class="selectedPackage === 'single' ? 'border-gold-500/50 bg-gold-500/5' : ''">
                                    <input type="radio" x-model="selectedPackage" value="single" class="accent-yellow-500">
                                    <div class="flex-1"><span class="text-sm font-bold text-white">Single Session</span></div>
                                    <span class="text-lg font-extrabold text-white" x-text="cls.price > 0 ? '$' + parseFloat(cls.price).toFixed(2) : 'Free'"></span>
                                </label>
                                <!-- Hot Deal -->
                                <template x-if="cls.hot_deal">
                                    <label class="flex items-center gap-3 p-3 rounded-lg bg-navy-800/50 border border-navy-700/50 cursor-pointer hover:border-gold-500/30 transition-all relative overflow-hidden" :class="selectedPackage === 'hot_deal' ? 'border-gold-500/50 bg-gold-500/5' : ''">
                                        <div class="absolute top-0 right-0 px-2 py-0.5 text-[10px] font-bold bg-red-500 text-white rounded-bl-lg" x-text="cls.hot_deal.label || 'Hot Deal'"></div>
                                        <input type="radio" x-model="selectedPackage" value="hot_deal" class="accent-yellow-500">
                                        <div class="flex-1">
                                            <span class="text-sm font-bold text-white" x-text="cls.hot_deal.label || 'Hot Deal'"></span>
                                            <span x-show="cls.hot_deal.expires_at" class="block text-xs text-red-400 mt-0.5" x-text="'Expires ' + formatDateFull(cls.hot_deal.expires_at)"></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs line-through text-slate-500" x-text="'$' + parseFloat(cls.hot_deal.original_price).toFixed(2)"></span>
                                            <span class="text-lg font-extrabold text-red-400 ml-1" x-text="'$' + parseFloat(cls.hot_deal.deal_price).toFixed(2)"></span>
                                        </div>
                                    </label>
                                </template>
                                <!-- Early Bird -->
                                <template x-if="cls.early_bird && isEarlyBirdActive()">
                                    <label class="flex items-center gap-3 p-3 rounded-lg bg-navy-800/50 border border-navy-700/50 cursor-pointer hover:border-gold-500/30 transition-all" :class="selectedPackage === 'early_bird' ? 'border-gold-500/50 bg-gold-500/5' : ''">
                                        <input type="radio" x-model="selectedPackage" value="early_bird" class="accent-yellow-500">
                                        <div class="flex-1">
                                            <span class="text-sm font-bold text-white">Early Bird</span>
                                            <span class="block text-xs text-emerald-400 mt-0.5" x-text="'Book before ' + formatDateFull(cls.early_bird.cutoff_date)"></span>
                                        </div>
                                        <span class="text-lg font-extrabold text-emerald-400" x-text="'$' + parseFloat(cls.early_bird.discounted_price).toFixed(2)"></span>
                                    </label>
                                </template>
                                <!-- Rolling Packages -->
                                <template x-for="pkg in (cls.rolling_prices || [])" :key="pkg.id">
                                    <label class="flex items-center gap-3 p-3 rounded-lg bg-navy-800/50 border border-navy-700/50 cursor-pointer hover:border-gold-500/30 transition-all" :class="selectedPackage === 'pkg_' + pkg.id ? 'border-gold-500/50 bg-gold-500/5' : ''">
                                        <input type="radio" x-model="selectedPackage" :value="'pkg_' + pkg.id" class="accent-yellow-500">
                                        <div class="flex-1">
                                            <span class="text-sm font-bold text-white" x-text="pkg.num_weeks + '-Week Package'"></span>
                                            <span class="block text-xs text-slate-500 mt-0.5" x-text="'$' + parseFloat(pkg.price_per_session).toFixed(2) + '/session'"></span>
                                        </div>
                                        <span class="text-lg font-extrabold text-white" x-text="'$' + parseFloat(pkg.total_price).toFixed(2)"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Booking Panel -->
                    <div class="lg:col-span-1">
                        <div class="glass-card rounded-2xl p-6 gold-border sticky top-28">
                            <!-- Availability -->
                            <div class="text-center mb-6 pb-6 border-b border-navy-700/50">
                                <div class="text-3xl font-extrabold" :class="cls.is_full ? 'text-red-400' : 'gradient-gold'" x-text="cls.is_full ? 'Full' : cls.spots_left + ' Spots Left'"></div>
                                <div class="text-sm text-slate-500 mt-1" x-text="cls.booked_count + ' of ' + cls.max_participants + ' registered'"></div>
                                <div class="w-full h-2 bg-navy-800 rounded-full mt-3 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500" :class="cls.is_full ? 'bg-red-500' : 'gradient-gold-bg'" :style="'width:' + Math.min(100, (cls.booked_count / cls.max_participants) * 100) + '%'"></div>
                                </div>
                            </div>

                            <!-- Effective Price -->
                            <div class="text-center mb-6">
                                <div class="text-xs text-slate-500 uppercase tracking-wider">Your Price</div>
                                <div class="text-4xl font-extrabold text-white mt-1" x-text="'$' + effectivePrice().toFixed(2)"></div>
                            </div>

                            <!-- Booking Form -->
                            <div x-show="!bookingSuccess">
                                <div x-show="!cls.is_full">
                                    <!-- Discount Code -->
                                    <div class="mb-4">
                                        <button @click="showDiscount = !showDiscount" class="text-xs text-gold-500 hover:text-gold-400 font-medium transition-colors">Have a discount code?</button>
                                        <div x-show="showDiscount" x-cloak class="mt-2 flex gap-2">
                                            <input type="text" x-model="form.discount_code" placeholder="Enter code" class="flex-1 px-3 py-2 bg-navy-800 border border-navy-700 text-white placeholder-slate-500 rounded-lg text-sm focus:border-gold-500/50 outline-none">
                                            <button @click="validateDiscount()" class="px-4 py-2 text-sm font-semibold text-navy-950 gradient-gold-bg rounded-lg hover:shadow-gold transition-all" :disabled="!form.discount_code">Apply</button>
                                        </div>
                                        <div x-show="discountMsg" class="mt-1 text-xs" :class="discountValid ? 'text-emerald-400' : 'text-red-400'" x-text="discountMsg"></div>
                                    </div>

                                    <div class="space-y-3">
                                        <input type="text" x-model="form.name" placeholder="Full Name *" class="w-full px-4 py-3 bg-navy-800 border border-navy-700 text-white placeholder-slate-500 rounded-xl text-sm focus:border-gold-500/50 outline-none transition-all">
                                        <input type="email" x-model="form.email" placeholder="Email Address *" class="w-full px-4 py-3 bg-navy-800 border border-navy-700 text-white placeholder-slate-500 rounded-xl text-sm focus:border-gold-500/50 outline-none transition-all">
                                        <input type="tel" x-model="form.phone" placeholder="Phone (optional)" class="w-full px-4 py-3 bg-navy-800 border border-navy-700 text-white placeholder-slate-500 rounded-xl text-sm focus:border-gold-500/50 outline-none transition-all">
                                    </div>

                                    <div x-show="bookingError" class="mt-3 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-sm text-red-400" x-text="bookingError"></div>

                                    <button @click="book()" :disabled="bookingLoading" class="w-full mt-4 py-4 text-base font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                        <svg x-show="bookingLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        <span x-text="bookingLoading ? 'Booking...' : 'Book Now'"></span>
                                    </button>
                                </div>
                                <div x-show="cls.is_full" class="text-center py-4">
                                    <p class="text-sm text-slate-400 mb-3">This class is currently full.</p>
                                    <a href="/schedule" class="text-sm text-gold-500 hover:text-gold-400 font-medium">Browse other classes &rarr;</a>
                                </div>
                            </div>

                            <!-- Success -->
                            <div x-show="bookingSuccess" x-cloak class="text-center py-4">
                                <div class="w-16 h-16 rounded-full gradient-gold-bg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="text-xl font-extrabold text-white">Booking Confirmed!</h3>
                                <p class="text-sm text-slate-400 mt-2">Check your email for confirmation details.</p>
                                <a href="/schedule" class="inline-flex items-center gap-2 mt-4 text-sm text-gold-500 hover:text-gold-400 font-medium">Back to Schedule <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Not Found -->
    <div x-show="!loading && !cls" class="py-32 text-center bg-navy-950">
        <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-lg font-semibold text-slate-400">Class not found</p>
        <a href="/schedule" class="text-sm text-gold-500 hover:text-gold-400 font-medium mt-2 inline-block">Back to Schedule</a>
    </div>
</div>

<script>
function classDetailPage() {
    return {
        cls: null, loading: true, selectedPackage: 'single',
        showDiscount: false, discountMsg: '', discountValid: false, discountAmt: 0,
        bookingLoading: false, bookingError: '', bookingSuccess: false,
        form: { name: '', email: '', phone: '', discount_code: '' },

        async load() {
            this.loading = true;
            try {
                const id = <?= (int)$classId ?>;
                const res = await fetch(baseApi + '/api/public/classes/' + id);
                const json = await res.json();
                this.cls = json.data || null;
                // Pre-fill from logged-in player
                const app = document.querySelector('[x-data]')?.__x;
                const player = window.tenantApp?.player || app?.$data?.player;
                if (player) {
                    this.form.name = (player.first_name || '') + ' ' + (player.last_name || '');
                    this.form.email = player.email || '';
                    this.form.phone = player.phone || '';
                }
            } catch(e) { this.cls = null; }
            this.loading = false;
        },

        effectivePrice() {
            if (!this.cls) return 0;
            if (this.discountValid && this.discountAmt > 0) {
                const base = this.selectedBasePrice();
                return Math.max(0, base - this.discountAmt);
            }
            return this.selectedBasePrice();
        },
        selectedBasePrice() {
            if (!this.cls) return 0;
            if (this.selectedPackage === 'hot_deal' && this.cls.hot_deal) return parseFloat(this.cls.hot_deal.deal_price);
            if (this.selectedPackage === 'early_bird' && this.cls.early_bird) return parseFloat(this.cls.early_bird.discounted_price);
            if (this.selectedPackage.startsWith('pkg_')) {
                const pkgId = parseInt(this.selectedPackage.replace('pkg_', ''));
                const pkg = (this.cls.rolling_prices || []).find(p => p.id === pkgId);
                if (pkg) return parseFloat(pkg.total_price);
            }
            return parseFloat(this.cls.price || 0);
        },
        isEarlyBirdActive() {
            if (!this.cls?.early_bird?.cutoff_date) return false;
            return new Date() < new Date(this.cls.early_bird.cutoff_date);
        },

        async validateDiscount() {
            if (!this.form.discount_code) return;
            try {
                const url = baseApi + '/api/session-types/' + this.cls.session_type_id + '/classes/' + this.cls.id + '/validate-credit-code';
                const res = await fetch(url, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ code: this.form.discount_code }) });
                const json = await res.json();
                if (res.ok && json.valid) {
                    this.discountValid = true;
                    this.discountAmt = parseFloat(json.discount_amount || 0);
                    this.discountMsg = json.message || 'Discount applied!';
                } else {
                    this.discountValid = false; this.discountAmt = 0;
                    this.discountMsg = json.message || 'Invalid code';
                }
            } catch(e) { this.discountValid = false; this.discountMsg = 'Could not validate code'; }
        },

        async book() {
            this.bookingError = '';
            if (!this.form.name.trim() || !this.form.email.trim()) { this.bookingError = 'Name and email are required.'; return; }
            this.bookingLoading = true;
            try {
                const url = baseApi + '/api/session-types/' + this.cls.session_type_id + '/classes/' + this.cls.id + '/book';
                const token = localStorage.getItem('player_token');
                const headers = { 'Content-Type': 'application/json' };
                if (token) headers['Authorization'] = 'Bearer ' + token;
                const body = {
                    name: this.form.name, email: this.form.email, phone: this.form.phone,
                    package: this.selectedPackage, discount_code: this.form.discount_code || undefined,
                };
                const res = await fetch(url, { method: 'POST', headers, body: JSON.stringify(body) });
                const json = await res.json();
                if (res.ok) {
                    this.bookingSuccess = true;
                    if (window.tenantApp) window.tenantApp.showToast('Booking confirmed!', 'success', 'Check your email for details.');
                } else {
                    this.bookingError = json.message || json.error || 'Booking failed. Please try again.';
                }
            } catch(e) { this.bookingError = 'Network error. Please try again.'; }
            this.bookingLoading = false;
        },

        formatDateFull(dt) { return dt ? new Date(dt).toLocaleDateString('en-US', { weekday:'long', month:'long', day:'numeric', year:'numeric' }) : ''; },
        formatTime(dt) { return dt ? new Date(dt).toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', hour12:true }) : ''; },
    };
}
</script>
