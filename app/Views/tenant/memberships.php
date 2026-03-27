<?php
/**
 * Tenant Memberships Page — K2 Navy/Gold Theme
 * Lists active membership plans with pricing, benefits, and sign-up CTAs.
 */
?>

<div x-data="membershipsPage()" x-init="load()">
    <!-- Page Header -->
    <section class="relative bg-navy-900 overflow-hidden py-16 sm:py-20">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute inset-0 hero-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                Memberships
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white">Membership Plans</h1>
            <p class="mt-4 text-lg text-slate-400 max-w-2xl mx-auto">Choose the plan that fits your game. Get exclusive perks, savings, and priority access.</p>
        </div>
    </section>

    <!-- Loading -->
    <div x-show="loading" class="py-20 text-center bg-navy-950">
        <div class="inline-flex items-center gap-3 text-slate-500">
            <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading plans...
        </div>
    </div>

    <!-- Plans Grid -->
    <section x-show="!loading && plans.length > 0" class="relative py-12 sm:py-16 bg-navy-950 min-h-[50vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <template x-for="plan in plans" :key="plan.id">
                    <div class="glass-card rounded-2xl p-6 lg:p-8 gold-border glass-card-hover flex flex-col relative"
                         :class="plan.is_featured ? 'ring-2 ring-gold-500/30' : ''">
                        <!-- Featured badge -->
                        <div x-show="plan.is_featured" class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="px-4 py-1 rounded-full gradient-gold-bg text-navy-950 text-xs font-bold uppercase tracking-wider">Most Popular</span>
                        </div>

                        <!-- Plan Name -->
                        <div class="mb-6">
                            <h3 class="text-xl font-display font-bold text-white" x-text="plan.name"></h3>
                            <p x-show="plan.description" class="text-sm text-slate-400 mt-2 line-clamp-3" x-text="plan.description"></p>
                        </div>

                        <!-- Pricing -->
                        <div class="mb-6 pb-6 border-b border-navy-700/50">
                            <div class="flex items-end gap-1">
                                <span class="text-4xl font-display font-extrabold text-white" x-text="'$' + parseFloat(plan.price).toFixed(2)"></span>
                                <span class="text-sm text-slate-400 mb-1" x-text="'/' + (plan.duration_months > 1 ? plan.duration_months + ' months' : 'month')"></span>
                            </div>
                            <div x-show="plan.setup_fee > 0" class="text-xs text-slate-500 mt-1" x-text="'+ $' + parseFloat(plan.setup_fee).toFixed(2) + ' one-time setup fee'"></div>
                        </div>

                        <!-- Benefits / Features -->
                        <div class="flex-1 mb-6">
                            <ul class="space-y-3">
                                <!-- Max bookings -->
                                <li x-show="plan.max_bookings_per_month" class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm text-slate-300" x-text="plan.max_bookings_per_month + ' bookings per month'"></span>
                                </li>
                                <li x-show="!plan.max_bookings_per_month" class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm text-slate-300">Unlimited bookings</span>
                                </li>
                                <!-- Discount -->
                                <li x-show="plan.discount_percentage > 0" class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm text-slate-300" x-text="plan.discount_percentage + '% off all sessions'"></span>
                                </li>
                                <!-- Guest passes -->
                                <li x-show="plan.guest_passes_per_month > 0" class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm text-slate-300" x-text="plan.guest_passes_per_month + ' guest passes per month'"></span>
                                </li>
                                <!-- Priority booking -->
                                <li x-show="plan.priority_booking" class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm text-slate-300">Priority booking access</span>
                                </li>
                                <!-- Categories included -->
                                <template x-for="cat in (plan.categories || [])" :key="cat.id">
                                    <li class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="text-sm text-slate-300" x-text="cat.name + ' access'"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Availability -->
                        <div x-show="plan.max_members > 0" class="mb-4">
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5">
                                <span x-text="(plan.max_members - (plan.current_members || 0)) + ' spots remaining'"></span>
                                <span x-text="(plan.current_members || 0) + '/' + plan.max_members + ' members'"></span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-navy-800">
                                <div class="h-full rounded-full gradient-gold-bg transition-all" :style="'width:' + Math.min(100, ((plan.current_members || 0) / plan.max_members) * 100) + '%'"></div>
                            </div>
                        </div>

                        <!-- CTA -->
                        <a href="/register" class="block w-full text-center py-3 rounded-xl text-sm font-bold transition-all duration-300"
                           :class="plan.is_featured ? 'gradient-gold-bg text-navy-950 shadow-gold hover:shadow-gold-lg' : 'bg-navy-800 text-white border border-navy-700 hover:border-gold-500/30 hover:text-gold-400'">
                            Get Started
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- No Plans -->
    <section x-show="!loading && plans.length === 0" class="relative py-20 bg-navy-950 min-h-[50vh]">
        <div class="text-center max-w-md mx-auto px-4">
            <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            </div>
            <p class="text-lg font-semibold text-slate-400">No membership plans available</p>
            <p class="text-sm text-slate-600 mt-1">Check back soon for membership offerings.</p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section x-show="!loading && plans.length > 0" class="relative py-12 sm:py-16 bg-navy-950 border-t border-navy-800">
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-display font-bold text-white text-center mb-8">Frequently Asked Questions</h2>
            <div class="space-y-3" x-data="{ openFaq: null }">
                <div class="glass-card rounded-xl gold-border overflow-hidden">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full flex items-center justify-between px-5 py-4 text-left">
                        <span class="text-sm font-semibold text-white">Can I cancel my membership anytime?</span>
                        <svg class="w-4 h-4 text-gold-500 transition-transform" :class="openFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 1" x-collapse>
                        <p class="px-5 pb-4 text-sm text-slate-400">Yes, you can cancel your membership at any time. Your benefits will remain active until the end of your current billing period.</p>
                    </div>
                </div>
                <div class="glass-card rounded-xl gold-border overflow-hidden">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full flex items-center justify-between px-5 py-4 text-left">
                        <span class="text-sm font-semibold text-white">When does my membership start?</span>
                        <svg class="w-4 h-4 text-gold-500 transition-transform" :class="openFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 2" x-collapse>
                        <p class="px-5 pb-4 text-sm text-slate-400">Your membership benefits activate immediately upon payment. Your billing cycle starts from the date of purchase.</p>
                    </div>
                </div>
                <div class="glass-card rounded-xl gold-border overflow-hidden">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full flex items-center justify-between px-5 py-4 text-left">
                        <span class="text-sm font-semibold text-white">Can I upgrade or downgrade my plan?</span>
                        <svg class="w-4 h-4 text-gold-500 transition-transform" :class="openFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 3" x-collapse>
                        <p class="px-5 pb-4 text-sm text-slate-400">Yes! You can change your plan at any time. Upgrades take effect immediately, and downgrades apply at the start of your next billing period.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function membershipsPage() {
    return {
        plans: [],
        loading: true,

        async load() {
            this.loading = true;
            try {
                const res = await fetch(baseApi + '/api/public/membership-plans');
                const json = await res.json();
                this.plans = json.data || [];
            } catch(e) {
                this.plans = [];
            }
            this.loading = false;
        }
    };
}
</script>
