<?php $pageTitle = 'Pricing — K2 Pickleball'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-16 hero-glow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20 text-sm font-medium text-brand-400 mb-6">Pricing</div>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-[1.1]">
                Plans that scale <span class="gradient-text">with you</span>
            </h1>
            <p class="mt-6 text-lg text-surface-400">Simple, transparent pricing for every facility. All plans include core scheduling and management features.</p>
        </div>
    </div>
</section>

<!-- Pricing Cards -->
<section class="pb-24" x-data="pricingPage()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Toggle -->
        <div class="flex items-center justify-center gap-3 mb-12">
            <span class="text-sm" :class="!annual ? 'text-white font-medium' : 'text-surface-400'">Monthly</span>
            <button @click="annual = !annual" class="relative h-6 w-11 rounded-full transition-colors" :class="annual ? 'bg-brand-600' : 'bg-surface-700'">
                <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform" :class="annual ? 'translate-x-5' : ''"></span>
            </button>
            <span class="text-sm" :class="annual ? 'text-white font-medium' : 'text-surface-400'">Annual <span class="text-brand-400 text-xs font-medium">Save 17%</span></span>
        </div>

        <!-- Plans -->
        <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <template x-for="plan in plans" :key="plan.id">
                <div class="relative rounded-2xl border p-6 lg:p-8 card-hover" :class="plan.is_featured ? 'border-brand-500/40 bg-surface-900/80 shadow-glow' : 'border-surface-800/60 bg-surface-900/30'">
                    <div x-show="plan.is_featured" class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-brand-600 text-xs font-semibold text-white shadow-lg shadow-brand-600/30">Most Popular</div>
                    <h3 class="text-xl font-bold text-white" x-text="plan.name"></h3>
                    <p class="mt-1 text-sm text-surface-400" x-text="plan.description"></p>
                    <div class="mt-5 flex items-baseline gap-1">
                        <span class="text-4xl font-extrabold text-white" x-text="'$' + (annual ? Math.round(plan.price_yearly) : Math.round(plan.price_monthly))"></span>
                        <template x-if="plan.price_monthly > 0">
                            <span class="text-sm text-surface-400" x-text="annual ? '/year' : '/month'"></span>
                        </template>
                        <template x-if="plan.price_monthly == 0">
                            <span class="text-sm text-surface-400">forever</span>
                        </template>
                    </div>
                    <a :href="baseUrl + '/register'" class="mt-6 block w-full text-center py-3 rounded-xl text-sm font-semibold transition-all" :class="plan.is_featured ? 'bg-brand-600 hover:bg-brand-500 text-white shadow-lg shadow-brand-600/25' : 'border border-surface-700 hover:border-surface-600 text-surface-300 hover:text-white'">
                        <span x-text="plan.price_monthly == 0 ? 'Get Started Free' : (plan.is_featured ? 'Get Started' : 'Choose Plan')"></span>
                    </a>
                    <ul class="mt-6 space-y-3">
                        <template x-for="feature in plan.feature_list" :key="feature">
                            <li class="flex items-center gap-3 text-sm text-surface-300">
                                <svg class="w-4 h-4 text-brand-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                                <span x-text="feature"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </template>
        </div>

        <!-- Loading state -->
        <div x-show="loading" class="max-w-5xl mx-auto">
            <div class="grid md:grid-cols-3 gap-6">
                <template x-for="i in 3" :key="i">
                    <div class="rounded-2xl border border-surface-800/60 bg-surface-900/30 p-8 animate-pulse">
                        <div class="h-6 bg-surface-800 rounded w-24 mb-4"></div>
                        <div class="h-4 bg-surface-800 rounded w-32 mb-6"></div>
                        <div class="h-10 bg-surface-800 rounded w-20 mb-6"></div>
                        <div class="h-10 bg-surface-800 rounded w-full mb-6"></div>
                        <div class="space-y-3">
                            <div class="h-4 bg-surface-800 rounded w-full"></div>
                            <div class="h-4 bg-surface-800 rounded w-3/4"></div>
                            <div class="h-4 bg-surface-800 rounded w-5/6"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</section>

<!-- Feature Comparison -->
<section class="py-24 bg-surface-900/30 border-y border-surface-800/40">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold tracking-tight">Full feature <span class="gradient-text">comparison</span></h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-surface-800/40">
                        <th class="text-left py-4 pr-4 text-surface-400 font-medium">Feature</th>
                        <th class="text-center py-4 px-4 text-white font-semibold">Free</th>
                        <th class="text-center py-4 px-4 text-white font-semibold">Professional</th>
                        <th class="text-center py-4 px-4 text-white font-semibold">Enterprise</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-800/30">
                    <?php
                    $comparison = [
                        ['feature' => 'Facilities', 'free' => '1', 'pro' => '5', 'ent' => 'Unlimited'],
                        ['feature' => 'Courts', 'free' => '4', 'pro' => '20', 'ent' => 'Unlimited'],
                        ['feature' => 'Users / Members', 'free' => '10', 'pro' => '100', 'ent' => 'Unlimited'],
                        ['feature' => 'Court Scheduling', 'free' => true, 'pro' => true, 'ent' => true],
                        ['feature' => 'Member Management', 'free' => true, 'pro' => true, 'ent' => true],
                        ['feature' => 'Online Payments', 'free' => false, 'pro' => true, 'ent' => true],
                        ['feature' => 'Invoicing', 'free' => false, 'pro' => true, 'ent' => true],
                        ['feature' => 'Tournament Management', 'free' => false, 'pro' => true, 'ent' => true],
                        ['feature' => 'League Management', 'free' => false, 'pro' => true, 'ent' => true],
                        ['feature' => 'Custom Reports', 'free' => false, 'pro' => true, 'ent' => true],
                        ['feature' => 'API Access', 'free' => false, 'pro' => true, 'ent' => true],
                        ['feature' => 'Custom Branding', 'free' => false, 'pro' => false, 'ent' => true],
                        ['feature' => 'Priority Support', 'free' => false, 'pro' => false, 'ent' => true],
                        ['feature' => 'SLA Guarantee', 'free' => false, 'pro' => false, 'ent' => true],
                        ['feature' => 'Dedicated Account Manager', 'free' => false, 'pro' => false, 'ent' => true],
                    ];
                    foreach ($comparison as $row): ?>
                    <tr>
                        <td class="py-3 pr-4 text-surface-300"><?= $row['feature'] ?></td>
                        <?php foreach (['free', 'pro', 'ent'] as $tier):
                            $val = $row[$tier]; ?>
                        <td class="py-3 px-4 text-center">
                            <?php if ($val === true): ?>
                            <svg class="w-5 h-5 text-brand-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php elseif ($val === false): ?>
                            <svg class="w-5 h-5 text-surface-700 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/></svg>
                            <?php else: ?>
                            <span class="text-surface-300 font-medium"><?= htmlspecialchars($val) ?></span>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold tracking-tight">Frequently asked <span class="gradient-text">questions</span></h2>
        </div>

        <div class="space-y-4" x-data="{ openFaq: null }">
            <?php
            $faqs = [
                ['q' => 'Can I try K2 Pickleball for free?', 'a' => 'Yes! Our Free plan is available forever with no credit card required. It includes core scheduling and management features for up to 4 courts and 10 users.'],
                ['q' => 'Can I switch plans at any time?', 'a' => 'Absolutely. You can upgrade or downgrade your plan at any time. Upgrades take effect immediately, and downgrades take effect at the end of your current billing cycle.'],
                ['q' => 'Do you offer annual billing discounts?', 'a' => 'Yes — annual billing saves you approximately 17% compared to monthly billing. You can switch between monthly and annual billing from your portal.'],
                ['q' => 'What payment methods do you accept?', 'a' => 'We accept all major credit cards through our secure Square payment integration. For Enterprise plans, we also offer invoiced billing.'],
                ['q' => 'Is there a setup fee?', 'a' => 'No. There are no setup fees, hidden costs, or lock-in contracts. Pay only for the plan you choose.'],
                ['q' => 'Do you offer discounts for non-profits?', 'a' => 'Yes, we offer special pricing for non-profit organizations, recreation districts, and educational institutions. Contact our sales team for details.'],
            ];
            foreach ($faqs as $i => $faq): ?>
            <div class="rounded-2xl border border-surface-800/60 bg-surface-900/30 overflow-hidden">
                <button @click="openFaq === <?= $i ?> ? openFaq = null : openFaq = <?= $i ?>" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-sm font-semibold text-white"><?= htmlspecialchars($faq['q']) ?></span>
                    <svg class="w-5 h-5 text-surface-400 transition-transform flex-shrink-0 ml-4" :class="openFaq === <?= $i ?> ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openFaq === <?= $i ?>" x-collapse>
                    <div class="px-6 pb-4 text-sm text-surface-400 leading-relaxed"><?= htmlspecialchars($faq['a']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 border-t border-surface-800/40">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold tracking-tight">Still have questions?</h2>
        <p class="mt-4 text-lg text-surface-400">Our team is here to help you choose the right plan for your facility.</p>
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $baseUrl ?>/demo" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">Request a Demo</a>
            <a href="<?= $baseUrl ?>/contact" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-medium text-surface-300 hover:text-white border border-surface-700 hover:border-surface-600 rounded-2xl transition-all">Contact Sales</a>
        </div>
    </div>
</section>

<script>
function pricingPage() {
    const baseUrl = '<?= $baseUrl ?>';
    return {
        annual: false,
        plans: [],
        loading: true,
        baseUrl,
        init() {
            fetch(baseUrl + '/api/plans')
                .then(r => r.json())
                .then(data => {
                    const planData = data.data || data;
                    this.plans = planData.map((p, i) => ({
                        ...p,
                        is_featured: i === 1,
                        price_monthly: parseFloat(p.price_monthly) || 0,
                        price_yearly: parseFloat(p.price_yearly) || 0,
                        feature_list: p.features ? (typeof p.features === 'string' ? JSON.parse(p.features) : p.features) : [],
                        description: p.description || ''
                    }));
                    this.loading = false;
                })
                .catch(() => {
                    // Fallback to hardcoded plans
                    this.plans = [
                        { id: 1, name: 'Free', description: 'Perfect for getting started', price_monthly: 0, price_yearly: 0, is_featured: false, feature_list: ['1 facility', '4 courts', '10 users', 'Basic scheduling', 'Court management'] },
                        { id: 2, name: 'Professional', description: 'For growing clubs & facilities', price_monthly: 49.99, price_yearly: 499.99, is_featured: true, feature_list: ['5 facilities', '20 courts', '100 users', 'Online payments', 'Tournaments', 'Analytics', 'API access'] },
                        { id: 3, name: 'Enterprise', description: 'For large organizations', price_monthly: 149.99, price_yearly: 1499.99, is_featured: false, feature_list: ['Unlimited everything', 'Custom branding', 'Priority support', 'SLA guarantee', 'Dedicated manager'] },
                    ];
                    this.loading = false;
                });
        }
    }
}
</script>
