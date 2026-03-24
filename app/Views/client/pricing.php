<?php $pageTitle = 'Pricing & Plans — K2Pickleball.com'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6 animate-fade-in-up">Pricing & Plans</div>
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight animate-fade-in-up" style="animation-delay:0.1s">
            Choose the Right Plan for<br><span class="gradient-gold">Your Facility</span>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay:0.2s">
            Start with a <span class="text-gold-400 font-semibold">free 7-day trial</span> — no credit card required. Pick the plan that matches your facility size and grow at your own pace.
        </p>
    </div>
</section>

<!-- Plans from Database -->
<section class="py-16 lg:py-24 relative" x-data="pricingPlans()" x-init="loadPlans()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Billing Toggle -->
        <div class="flex items-center justify-center gap-4 mb-14" data-animate>
            <span class="text-sm font-medium" :class="!yearly ? 'text-white' : 'text-slate-500'">Monthly</span>
            <button @click="yearly = !yearly" class="relative w-14 h-7 rounded-full transition-colors duration-300" :class="yearly ? 'bg-gold-500' : 'bg-navy-700'">
                <span class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white shadow transition-transform duration-300" :class="yearly ? 'translate-x-7' : ''"></span>
            </button>
            <span class="text-sm font-medium" :class="yearly ? 'text-white' : 'text-slate-500'">Yearly <span class="text-gold-400 text-xs font-semibold">(Save up to 20%)</span></span>
        </div>

        <!-- Loading -->
        <div x-show="loading" class="text-center py-20">
            <svg class="animate-spin h-10 w-10 text-gold-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <p class="mt-4 text-slate-400">Loading plans...</p>
        </div>

        <!-- Plans Grid -->
        <div x-show="!loading" x-cloak class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto" data-animate>
            <template x-for="plan in plans" :key="plan.id">
                <div class="relative rounded-2xl glass-card p-8 flex flex-col transition-all duration-300 hover:border-gold-500/30 hover:-translate-y-1"
                     :class="plan.featured ? 'border-2 border-gold-500/40 shadow-gold' : 'border border-navy-700/40'">
                    <!-- Featured badge -->
                    <div x-show="plan.featured" class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="px-4 py-1 text-xs font-bold text-navy-950 gradient-gold-bg rounded-full uppercase tracking-wider">Most Popular</span>
                    </div>

                    <div class="mb-6">
                        <h3 class="font-display text-xl font-bold text-white" x-text="plan.name"></h3>
                        <p class="text-sm text-slate-400 mt-1" x-text="plan.description || 'Perfect for your facility'"></p>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold gradient-gold" x-text="'$' + (yearly ? plan.price_yearly_monthly : plan.price_monthly)"></span>
                            <span class="text-slate-400 text-sm">/month</span>
                        </div>
                        <p x-show="yearly" x-cloak class="text-xs text-gold-400 mt-1" x-text="'Billed $' + plan.price_yearly + '/year'"></p>
                    </div>

                    <!-- 7-Day Trial Banner -->
                    <div class="mb-6 p-3 rounded-lg bg-gold-500/5 border border-gold-500/15">
                        <p class="text-xs text-gold-400 font-medium text-center">
                            <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                            7-day free trial — No credit card required
                        </p>
                    </div>

                    <!-- Features -->
                    <ul class="flex-1 space-y-3 mb-8">
                        <template x-for="feature in plan.feature_list" :key="feature">
                            <li class="flex items-start gap-2 text-sm">
                                <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                                <span class="text-slate-300" x-text="feature"></span>
                            </li>
                        </template>
                        <li x-show="plan.max_facilities" class="flex items-start gap-2 text-sm">
                            <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <span class="text-slate-300" x-text="'Up to ' + plan.max_facilities + ' facilities'"></span>
                        </li>
                        <li x-show="plan.max_courts" class="flex items-start gap-2 text-sm">
                            <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <span class="text-slate-300" x-text="'Up to ' + plan.max_courts + ' courts'"></span>
                        </li>
                        <li x-show="plan.max_users" class="flex items-start gap-2 text-sm">
                            <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <span class="text-slate-300" x-text="'Up to ' + plan.max_users + ' staff users'"></span>
                        </li>
                    </ul>

                    <a :href="'<?= $baseUrl ?>/register?plan=' + plan.slug"
                       class="block w-full text-center px-6 py-3.5 text-sm font-bold rounded-xl transition-all duration-300"
                       :class="plan.featured ? 'text-navy-950 gradient-gold-bg shadow-gold hover:shadow-gold-lg' : 'text-gold-400 border border-gold-500/30 hover:bg-gold-500/10'">
                        Start Free Trial
                    </a>
                </div>
            </template>
        </div>

        <!-- Fallback if no plans in DB -->
        <div x-show="!loading && plans.length === 0" x-cloak class="text-center py-16">
            <p class="text-slate-400 text-lg">Plans are being configured. Please contact us for pricing details.</p>
            <a href="<?= $baseUrl ?>/contact" class="mt-4 inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl">Contact Us</a>
        </div>
    </div>
</section>

<!-- Partnership Fee Structure -->
<section class="py-24 lg:py-32 border-y border-navy-800/60 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                Partnership <span class="gradient-gold">Fee Structure</span>
            </h2>
            <p class="mt-4 text-lg text-slate-400">For full-service partnership including launch support, operational expertise, and white-label platform deployment.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto" data-animate>
            <?php
            $fees = [
                ['Launch Fee', '$30K — $75K+', 'One-time investment for comprehensive launch support'],
                ['Platform Fee', '3% — 6%', 'Of gross revenue — aligned incentives'],
                ['Minimum Monthly', '$500/mo', 'Baseline for platform maintenance & support'],
                ['Processing', '3.0% + 30¢', 'Per transaction via Square integration'],
            ];
            foreach ($fees as $fee): ?>
            <div class="text-center p-6 rounded-xl glass-card card-hover">
                <div class="text-2xl font-extrabold gradient-gold mb-2"><?= $fee[1] ?></div>
                <div class="text-sm font-bold text-white mb-1"><?= $fee[0] ?></div>
                <div class="text-xs text-slate-500"><?= $fee[2] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- What's Included -->
<section class="py-24 lg:py-32 relative">
    <div class="absolute inset-0 grid-bg opacity-20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                Everything Included in Your <span class="gradient-gold">Partnership</span>
            </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" data-animate>
            <?php
            $included = [
                ['Complete K2Pickleball Platform', 'Full access to reservations, POS, payments, memberships, events, analytics, and every feature we build — now and in the future.'],
                ['Launch Strategy', 'Market analysis, pricing strategy, programming recommendations, and a customized launch playbook for your facility.'],
                ['Site Selection Support', 'Guidance on location selection, facility layout optimization, and buildout consulting based on our operational experience.'],
                ['Marketing Launch Package', 'Pre-launch marketing campaigns, social media templates, grand opening event planning, and initial customer acquisition strategy.'],
                ['White-Label Branding', 'Your brand, your domain. The platform operates under your identity with your logo, colors, and custom URL.'],
                ['Dedicated Partner Success', 'Ongoing performance reviews, optimization recommendations, and direct access to our operations team.'],
                ['24/7 Platform Access', 'Cloud-hosted infrastructure with automatic updates, backups, security patches, and 99.9% uptime commitment.'],
                ['Training & Onboarding', 'Comprehensive staff training, video tutorials, documentation, and hands-on onboarding support at launch.'],
                ['Payment Processing', 'Full Square integration for in-person and online payments. PCI-compliant, secure, and seamlessly integrated.'],
            ];
            foreach ($included as $inc): ?>
            <div class="glass-card card-hover rounded-xl p-6">
                <div class="h-10 w-10 rounded-xl bg-gold-500/10 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                </div>
                <h4 class="text-base font-bold text-white mb-2"><?= $inc[0] ?></h4>
                <p class="text-sm text-slate-400 leading-relaxed"><?= $inc[1] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-24 lg:py-32 relative">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-animate>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                Frequently Asked <span class="gradient-gold">Questions</span>
            </h2>
        </div>

        <div class="space-y-4" x-data="{open: null}" data-animate>
            <?php
            $faqs = [
                ['Is there a free trial?', 'Yes! Every plan includes a 7-day free trial with full platform access. No credit card is required to start. You can explore the entire K2Pickleball Platform before committing.'],
                ['What does the launch fee cover?', 'The launch fee is a one-time investment that covers site selection consulting, buildout guidance, launch strategy development, marketing support, full platform deployment, staff training, and ongoing onboarding. The exact fee depends on the scope and scale of your facility.'],
                ['How is the ongoing platform fee calculated?', 'The platform fee is calculated as a percentage (3-6%) of your facility\'s gross revenue collected through the K2Pickleball Platform. This includes court reservations, memberships, retail sales, event registrations, and any other transactions processed through the system.'],
                ['Can I upgrade or downgrade my plan?', 'Absolutely. You can change your plan at any time from your admin dashboard. Upgrades take effect immediately, and downgrades take effect at the end of your current billing cycle.'],
                ['Is there a long-term contract?', 'We structure our partnerships as long-term relationships because that\'s how we deliver the most value. Specific terms are discussed during the consultation process and are designed to be fair and transparent.'],
                ['Do I need technical staff to run the platform?', 'No. The K2Pickleball Platform is designed for facility operators, not IT teams. Everything is cloud-hosted and managed by K2. Your staff only needs to know how to use the intuitive web interface — we handle everything else.'],
            ];
            foreach ($faqs as $fi => $faq): ?>
            <div class="glass-card rounded-xl overflow-hidden">
                <button @click="open = open === <?= $fi ?> ? null : <?= $fi ?>" class="w-full flex items-center justify-between px-6 py-5 text-left">
                    <span class="font-display text-base font-bold text-white"><?= htmlspecialchars($faq[0]) ?></span>
                    <svg class="w-5 h-5 text-gold-500 transition-transform duration-300" :class="{'rotate-180': open === <?= $fi ?>}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === <?= $fi ?>" x-collapse>
                    <div class="px-6 pb-5 text-sm text-slate-400 leading-relaxed"><?= htmlspecialchars($faq[1]) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 lg:py-32 relative overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-gold-500/8 rounded-full blur-[120px]"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center" data-animate>
        <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
            Start Your <span class="gradient-gold">Free Trial</span> Today
        </h2>
        <p class="mt-6 text-lg text-slate-400 max-w-2xl mx-auto">
            Get 7 days of full access to the K2Pickleball Platform — no credit card required. Ready for a full partnership? Schedule a consultation with our team.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $baseUrl ?>/register" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 text-base font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300">
                Start Free Trial
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="<?= $baseUrl ?>/demo" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-medium text-slate-300 hover:text-white border border-navy-700 hover:border-gold-500/30 rounded-xl transition-all">
                Schedule Consultation
            </a>
        </div>
    </div>
</section>

<script>
function pricingPlans() {
    return {
        plans: [],
        yearly: false,
        loading: true,
        async loadPlans() {
            try {
                const res = await fetch((window.APP_BASE || '') + '/api/plans');
                const json = await res.json();
                if (json.status === 'success') {
                    const rawPlans = json.data?.data || json.data || [];
                    this.plans = rawPlans.map((p, i) => {
                        const features = Array.isArray(p.features) ? p.features : (p.features ? JSON.parse(p.features) : []);
                        const fmt = v => { const n = parseFloat(v || 0); return Number.isInteger(n) ? n.toString() : n.toFixed(2); };
                        return {
                            ...p,
                            price_monthly: fmt(p.price_monthly),
                            price_yearly: fmt(p.price_yearly),
                            price_yearly_monthly: fmt(parseFloat(p.price_yearly || 0) / 12),
                            feature_list: features.map(s => s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())),
                            featured: i === 1 || (p.slug && p.slug.includes('pro'))
                        };
                    });
                }
            } catch (e) {
                console.error('Failed to load plans:', e);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
