<?php $pageTitle = 'K2 Platform — Launch & Operate Your Own Pickleball Facility'; ?>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center hero-glow pt-20 overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute top-32 right-[15%] w-80 h-80 bg-gold-500/5 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-20 left-[10%] w-96 h-96 bg-navy-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute top-1/4 left-[5%] w-1.5 h-1.5 bg-gold-500/40 rounded-full animate-pulse"></div>
        <div class="absolute top-1/3 right-[8%] w-2 h-2 bg-gold-400/30 rounded-full animate-pulse" style="animation-delay:1s"></div>
        <div class="absolute bottom-1/4 left-[20%] w-1 h-1 bg-gold-500/50 rounded-full animate-pulse" style="animation-delay:2s"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-5xl mx-auto text-center">
            <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-gold-500/10 border border-gold-500/20 mb-8 animate-fade-in-up">
                <span class="w-2 h-2 rounded-full bg-gold-500 animate-pulse-gold"></span>
                <span class="text-sm font-medium text-gold-400 tracking-wide">Proven Track Record — $1M+ Annual Revenue at Flagship Facility</span>
            </div>

            <h1 class="font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-extrabold tracking-tight leading-[0.95] animate-fade-in-up" style="animation-delay:0.1s">
                Launch &amp; Operate<br>
                <span class="gradient-gold">Your Own Pickleball</span><br>
                Facility
            </h1>

            <p class="mt-8 text-lg sm:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay:0.2s">
                The K2 Platform Partnership gives you the <span class="text-white font-medium">proven systems</span>, <span class="text-white font-medium">white-label technology</span>, and <span class="text-white font-medium">operational expertise</span> to build a profitable pickleball business — under your own brand.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay:0.3s">
                <a href="<?= $baseUrl ?>/demo" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 text-base font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300 hover:-translate-y-0.5">
                    Schedule a Consultation
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="<?= $baseUrl ?>/product" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 text-base font-medium text-slate-300 hover:text-white border border-navy-700 hover:border-gold-500/30 rounded-xl transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Explore the Platform
                </a>
            </div>

            <div class="mt-14 flex flex-wrap items-center justify-center gap-x-10 gap-y-4 animate-fade-in-up" style="animation-delay:0.4s">
                <?php foreach (['Multiple Operating Facilities', 'Your Brand, Your Business', 'Aligned Incentive Model'] as $trust): ?>
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <svg class="w-4 h-4 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                    <?= $trust ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Dashboard Preview -->
        <div class="mt-20 relative max-w-5xl mx-auto animate-fade-in-up" style="animation-delay:0.5s">
            <div class="rounded-2xl border border-navy-700/60 bg-navy-900/70 backdrop-blur-sm shadow-2xl shadow-navy-950/80 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 bg-navy-800/60 border-b border-navy-700/40">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/60"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/60"></div>
                    </div>
                    <div class="flex-1 flex justify-center">
                        <div class="px-4 py-1 rounded-lg bg-navy-700/50 text-xs text-slate-500 font-mono">your-facility.k2platform.com/admin</div>
                    </div>
                </div>
                <div class="p-6 bg-gradient-to-br from-navy-900 to-navy-950">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <?php
                        $dashStats = [
                            ['label' => 'Active Members', 'value' => '1,247', 'change' => '+18% growth'],
                            ['label' => 'Revenue (MTD)', 'value' => '$87.4K', 'change' => '+12% vs last'],
                            ['label' => 'Court Bookings', 'value' => '3,891', 'change' => '94% fill rate'],
                            ['label' => 'Classes This Week', 'value' => '156', 'change' => '12 sold out'],
                        ];
                        foreach ($dashStats as $s): ?>
                        <div class="rounded-xl bg-navy-800/50 border border-navy-700/30 p-4">
                            <div class="text-xs text-slate-500 mb-2"><?= $s['label'] ?></div>
                            <div class="text-xl font-bold text-white"><?= $s['value'] ?></div>
                            <div class="text-xs text-gold-500/70 mt-1"><?= $s['change'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2 rounded-xl bg-navy-800/30 border border-navy-700/20 h-32 flex items-center justify-center">
                            <div class="flex items-end gap-1.5 h-16">
                                <?php foreach ([40,55,45,65,50,70,60,75,55,80,65,85] as $h): ?>
                                <div class="w-3 rounded-t bg-gradient-to-t from-gold-600/40 to-gold-400/60" style="height:<?= $h ?>%"></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="rounded-xl bg-navy-800/30 border border-navy-700/20 h-32 flex items-center justify-center">
                            <div class="relative w-16 h-16">
                                <svg class="w-16 h-16 -rotate-90" viewBox="0 0 36 36"><circle cx="18" cy="18" r="14" fill="none" stroke="rgba(212,175,55,0.1)" stroke-width="3"/><circle cx="18" cy="18" r="14" fill="none" stroke="rgba(212,175,55,0.6)" stroke-width="3" stroke-dasharray="66 22" stroke-linecap="round"/></svg>
                                <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gold-400">94%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute -bottom-12 left-1/2 -translate-x-1/2 w-3/4 h-24 bg-gold-500/8 blur-[60px]"></div>
        </div>
    </div>
</section>

<!-- Proven Success -->
<section class="py-24 lg:py-32 relative">
    <div class="absolute inset-0 section-glow"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6">Proven Success</div>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
                Built on <span class="gradient-gold">Real Results</span>
            </h2>
            <p class="mt-5 text-lg text-slate-400">
                With a proven track record of generating <span class="text-white font-semibold">$1 Million+ Annual Revenue</span> at our 6-court flagship Tampa facility, we've developed the playbook for profitable pickleball operations.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6" data-animate>
            <?php
            $provenStats = [
                ['value' => '$1M+', 'label' => 'Annual Revenue', 'sub' => 'Flagship Tampa Facility'],
                ['value' => '6', 'label' => 'Indoor Courts', 'sub' => 'Premium Playing Experience'],
                ['value' => '94%', 'label' => 'Court Utilization', 'sub' => 'Peak Hour Average'],
                ['value' => '1,200+', 'label' => 'Active Members', 'sub' => 'And Growing Monthly'],
            ];
            foreach ($provenStats as $stat): ?>
            <div class="text-center p-8 rounded-2xl glass-card card-hover">
                <div class="text-4xl sm:text-5xl font-extrabold font-display gradient-gold mb-2"><?= $stat['value'] ?></div>
                <div class="text-base font-semibold text-white mb-1"><?= $stat['label'] ?></div>
                <div class="text-sm text-slate-500"><?= $stat['sub'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Partnership Model -->
<section class="py-24 lg:py-32 border-y border-navy-800/60 relative overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div data-animate>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6">The Partnership</div>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    Your Brand.<br><span class="gradient-gold">Our Playbook.</span>
                </h2>
                <p class="mt-6 text-lg text-slate-400 leading-relaxed">
                    Through the K2 Platform Partnership, we help entrepreneurs successfully open and operate their own pickleball facilities using our operational playbook and white-label software system.
                </p>
                <p class="mt-4 text-base text-slate-500">
                    Partners operate under their own brand and identity, while leveraging our technology, systems, and operational experience to build a profitable and sustainable facility.
                </p>

                <div class="mt-8 space-y-4">
                    <?php foreach ([
                        'Proven operating model refined through real-world facility management',
                        'White-label K2 Platform software — fully branded as your own',
                        'Complete launch support from site selection to grand opening',
                        'Ongoing operational guidance and technology updates',
                    ] as $benefit): ?>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-1 w-5 h-5 rounded-full bg-gold-500/20 flex items-center justify-center">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        </div>
                        <span class="text-slate-300"><?= $benefit ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-10">
                    <a href="<?= $baseUrl ?>/pricing" class="inline-flex items-center gap-2 px-7 py-3.5 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold transition-all duration-300">
                        View Partnership Structure
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>

            <div class="relative" data-animate>
                <div class="rounded-2xl glass-card p-8 lg:p-10">
                    <div class="text-center mb-8">
                        <h3 class="font-display text-2xl font-bold gradient-gold">Partnership Structure</h3>
                        <p class="text-sm text-slate-500 mt-1">Aligned incentives — we succeed when you succeed</p>
                    </div>
                    <div class="space-y-5">
                        <?php
                        $fees = [
                            ['label' => 'Launch Fee', 'value' => '$30K to $75K & Up', 'desc' => 'Comprehensive launch support package'],
                            ['label' => 'Ongoing Platform Fee', 'value' => '3% to 6% of Revenue', 'desc' => 'Scales with your success'],
                            ['label' => 'Minimum Platform Fee', 'value' => '$500 per Month', 'desc' => 'Predictable baseline cost'],
                            ['label' => 'Payment Processing', 'value' => '3.0% + 30¢', 'desc' => 'Per transaction, passed through'],
                        ];
                        foreach ($fees as $fee): ?>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-navy-900/60 border border-navy-700/40">
                            <div>
                                <div class="text-xs font-semibold text-gold-500 uppercase tracking-wider"><?= $fee['label'] ?></div>
                                <div class="text-xs text-slate-500 mt-0.5"><?= $fee['desc'] ?></div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-white whitespace-nowrap"><?= $fee['value'] ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 p-4 rounded-xl bg-gold-500/5 border border-gold-500/15">
                        <p class="text-xs text-gold-400/80 leading-relaxed text-center italic">
                            "By aligning incentives through the platform model, we succeed when our partners succeed."
                        </p>
                    </div>
                </div>
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-gold-500/5 rounded-full blur-[40px]"></div>
                <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-navy-600/10 rounded-full blur-[50px]"></div>
            </div>
        </div>
    </div>
</section>

<!-- Launch Support -->
<section class="py-24 lg:py-32 relative">
    <div class="absolute inset-0 section-glow"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6">Launch Support</div>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
                From Concept to <span class="gradient-gold">Grand Opening</span>
            </h2>
            <p class="mt-5 text-lg text-slate-400">
                Our goal is to help partners avoid common startup mistakes and open efficiently with a proven operating model.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6" data-animate>
            <?php
            $steps = [
                ['num' => '01', 'title' => 'Site Selection & Feasibility', 'desc' => 'Market analysis, location evaluation, and facility feasibility assessment to find the optimal site.', 'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
                ['num' => '02', 'title' => 'Layout & Buildout', 'desc' => 'Court layout recommendations, facility design, vendor introductions, and equipment sourcing.', 'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21'],
                ['num' => '03', 'title' => 'Strategy & Operations', 'desc' => 'Pricing strategy, programming approach, staffing structure, and operational framework.', 'icon' => 'M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6'],
                ['num' => '04', 'title' => 'Launch & Marketing', 'desc' => 'Timeline planning, marketing assistance, grand opening support, and ongoing guidance.', 'icon' => 'M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z'],
            ];
            foreach ($steps as $step): ?>
            <div class="group p-6 rounded-2xl glass-card card-hover">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-3xl font-extrabold font-display text-gold-500/20 group-hover:text-gold-500/40 transition-colors"><?= $step['num'] ?></span>
                    <div class="h-8 w-8 rounded-lg bg-gold-500/10 flex items-center justify-center group-hover:bg-gold-500/20 transition-colors">
                        <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $step['icon'] ?>"/></svg>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-white mb-2"><?= $step['title'] ?></h3>
                <p class="text-sm text-slate-400 leading-relaxed"><?= $step['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- The K2 Platform Features -->
<section class="py-24 lg:py-32 border-y border-navy-800/60 relative">
    <div class="absolute inset-0 grid-bg opacity-20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6">The K2 Platform</div>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
                The Operating System for<br><span class="gradient-gold">Pickleball Facilities</span>
            </h2>
            <p class="mt-5 text-lg text-slate-400">
                Developed through the real-world operation of multiple facilities, managing day-to-day operations and customer interactions.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6" data-animate>
            <?php
            $features = [
                ['icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5', 'title' => 'Online Reservations', 'desc' => 'Court scheduling with real-time availability and automated booking'],
                ['icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z', 'title' => 'Point-of-Sale', 'desc' => 'Complete POS with retail sales, waivers, and integrated payments'],
                ['icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Payment Processing', 'desc' => 'Integrated payments with automatic invoicing and financial tracking'],
                ['icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z', 'title' => 'Player Profiles', 'desc' => 'Customer database with player profiles and activity history'],
                ['icon' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342', 'title' => 'Events & Classes', 'desc' => 'Manage events, classes, leagues, and programs with enrollment'],
                ['icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z', 'title' => 'Memberships', 'desc' => 'Flexible membership and package management with auto billing'],
                ['icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z', 'title' => 'Analytics', 'desc' => 'Reporting dashboards with actionable business insights'],
                ['icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => '24/7 Access', 'desc' => 'Cloud-based platform accessible anytime, anywhere, any device'],
            ];
            foreach ($features as $f): ?>
            <div class="group p-5 rounded-2xl glass-card card-hover text-center">
                <div class="h-12 w-12 mx-auto rounded-xl bg-gold-500/10 flex items-center justify-center mb-4 group-hover:bg-gold-500/20 transition-colors">
                    <svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $f['icon'] ?>"/></svg>
                </div>
                <h3 class="text-base font-bold text-white mb-1.5"><?= $f['title'] ?></h3>
                <p class="text-xs text-slate-400 leading-relaxed"><?= $f['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Quote -->
<section class="py-24 lg:py-32 relative">
    <div class="absolute inset-0 section-glow"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center" data-animate>
        <svg class="w-12 h-12 text-gold-500/30 mx-auto mb-8" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
        <blockquote class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-snug">
            The goal of the K2 Platform Partnership is to create long-term relationships with facility operators by providing the systems, technology, and operational framework necessary to
            <span class="gradient-gold">build profitable pickleball businesses.</span>
        </blockquote>
        <div class="mt-8 flex items-center justify-center gap-4">
            <div class="h-px w-12 bg-gold-500/30"></div>
            <span class="text-sm font-medium text-gold-500">K2 Platform Partnership</span>
            <div class="h-px w-12 bg-gold-500/30"></div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 lg:py-32 relative overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-gold-500/8 rounded-full blur-[120px]"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center" data-animate>
        <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
            Ready to Launch Your<br><span class="gradient-gold">Pickleball Facility?</span>
        </h2>
        <p class="mt-6 text-lg text-slate-400 max-w-2xl mx-auto">
            Join the K2 Platform Partnership and leverage our proven systems, technology, and expertise to build a profitable business under your own brand.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $baseUrl ?>/demo" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 text-base font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300 hover:-translate-y-0.5">
                Schedule Your Consultation
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="<?= $baseUrl ?>/contact" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-medium text-slate-300 hover:text-white border border-navy-700 hover:border-gold-500/30 rounded-xl transition-all duration-300">
                Contact Our Team
            </a>
        </div>
    </div>
</section>
