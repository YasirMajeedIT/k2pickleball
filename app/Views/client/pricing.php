<?php $pageTitle = 'Partnership — K2 Pickleball Platform'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6 animate-fade-in-up">Partnership Structure</div>
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight animate-fade-in-up" style="animation-delay:0.1s">
            A Partnership Built for<br><span class="gradient-gold">Mutual Success</span>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay:0.2s">
            Our partnership model aligns K2's success directly with yours. We only succeed when your facility thrives — creating a true commitment to your long-term growth.
        </p>
    </div>
</section>

<!-- Fee Structure -->
<section class="py-24 lg:py-32 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16" data-animate>
            <!-- Main Card -->
            <div class="rounded-2xl glass-card gold-border p-8 sm:p-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-gold-500/5 rounded-full blur-[80px]"></div>
                <div class="relative">
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white mb-8">Partnership Fees</h2>

                    <!-- Launch Fee -->
                    <div class="pb-6 mb-6 border-b border-navy-700/60">
                        <div class="flex items-baseline justify-between mb-1">
                            <h3 class="font-display text-lg font-bold text-white">Launch Fee</h3>
                            <span class="text-2xl font-extrabold gradient-gold">$30K — $75K+</span>
                        </div>
                        <p class="text-sm text-slate-400">One-time investment covering site selection, buildout consulting, launch strategy, marketing support, and full platform deployment.</p>
                    </div>

                    <!-- Ongoing Platform Fee -->
                    <div class="pb-6 mb-6 border-b border-navy-700/60">
                        <div class="flex items-baseline justify-between mb-1">
                            <h3 class="font-display text-lg font-bold text-white">Ongoing Platform Fee</h3>
                            <span class="text-2xl font-extrabold gradient-gold">3% — 6%</span>
                        </div>
                        <p class="text-sm text-slate-400">Percentage of gross revenue. Our success is tied to yours — we're invested in helping you grow.</p>
                    </div>

                    <!-- Minimum Monthly -->
                    <div class="pb-6 mb-6 border-b border-navy-700/60">
                        <div class="flex items-baseline justify-between mb-1">
                            <h3 class="font-display text-lg font-bold text-white">Minimum Monthly Fee</h3>
                            <span class="text-2xl font-extrabold gradient-gold">$500<span class="text-base text-slate-400">/mo</span></span>
                        </div>
                        <p class="text-sm text-slate-400">Ensures ongoing platform maintenance, hosting, support, and continuous feature development.</p>
                    </div>

                    <!-- Payment Processing -->
                    <div>
                        <div class="flex items-baseline justify-between mb-1">
                            <h3 class="font-display text-lg font-bold text-white">Payment Processing</h3>
                            <span class="text-2xl font-extrabold gradient-gold">3.0% + 30¢</span>
                        </div>
                        <p class="text-sm text-slate-400">Industry-standard processing via Square. Secure, reliable, and integrated across all channels.</p>
                    </div>
                </div>
            </div>

            <!-- Why This Model -->
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white mb-6">Why a Partnership Model?</h2>
                <p class="text-lg text-slate-400 leading-relaxed mb-8">
                    Unlike traditional software vendors who charge flat fees regardless of your success, our revenue-based model means K2 is genuinely motivated to help your facility succeed.
                </p>

                <?php
                $reasons = [
                    ['Aligned Incentives', 'We earn more only when you earn more — guaranteeing our team is always working to optimize your revenue.'],
                    ['Lower Upfront Risk', 'The percentage model means your costs scale with your revenue instead of draining capital during the startup phase.'],
                    ['Continuous Innovation', 'Because our revenue depends on your success, we continuously invest in new features, improvements, and market insights.'],
                    ['Dedicated Support', 'You get partner-level attention — not a generic helpdesk. Our growth team works alongside yours.'],
                ];
                foreach ($reasons as $r): ?>
                <div class="flex gap-4 mb-6">
                    <div class="h-10 w-10 rounded-xl bg-gold-500/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-white"><?= $r[0] ?></h4>
                        <p class="text-sm text-slate-400 mt-1 leading-relaxed"><?= $r[1] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- What's Included -->
<section class="py-24 lg:py-32 border-y border-navy-800/60 relative">
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
                ['Complete K2 Platform', 'Full access to reservations, POS, payments, memberships, events, analytics, and every feature we build — now and in the future.'],
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
                ['What does the launch fee cover?', 'The launch fee is a one-time investment that covers site selection consulting, buildout guidance, launch strategy development, marketing support, full platform deployment, staff training, and ongoing onboarding. The exact fee depends on the scope and scale of your facility.'],
                ['How is the ongoing platform fee calculated?', 'The platform fee is calculated as a percentage (3-6%) of your facility\'s gross revenue collected through the K2 Platform. This includes court reservations, memberships, retail sales, event registrations, and any other transactions processed through the system.'],
                ['Is there a long-term contract?', 'We structure our partnerships as long-term relationships because that\'s how we deliver the most value. Specific terms are discussed during the consultation process and are designed to be fair and transparent.'],
                ['What happens if my revenue is below the minimum fee threshold?', 'The $500/month minimum ensures we can continue providing platform hosting, maintenance, security updates, and support regardless of seasonal fluctuations. Most facilities exceed this threshold within the first few months of operation.'],
                ['Can I use my own payment processor?', 'The K2 Platform is integrated with Square for payment processing, which provides industry-leading security, reliability, and features. This deep integration enables our POS, online payments, and financial reporting to work seamlessly.'],
                ['Do I need technical staff to run the platform?', 'No. The K2 Platform is designed for facility operators, not IT teams. Everything is cloud-hosted and managed by K2. Your staff only needs to know how to use the intuitive web interface — we handle everything else.'],
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
            Ready to Partner with <span class="gradient-gold">K2?</span>
        </h2>
        <p class="mt-6 text-lg text-slate-400 max-w-2xl mx-auto">
            Schedule a consultation with our team to discuss your facility vision and explore how K2 can help you launch and grow a thriving pickleball business.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $baseUrl ?>/demo" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 text-base font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300">
                Schedule Consultation
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="<?= $baseUrl ?>/contact" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-medium text-slate-300 hover:text-white border border-navy-700 hover:border-gold-500/30 rounded-xl transition-all">
                Contact Us
            </a>
        </div>
    </div>
</section>
