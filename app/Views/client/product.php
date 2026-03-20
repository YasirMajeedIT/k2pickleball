<?php $pageTitle = 'K2Pickleball — Pickleball Facility Management Software'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6 animate-fade-in-up">K2Pickleball Platform</div>
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight animate-fade-in-up" style="animation-delay:0.1s">
                The Operating System for<br><span class="gradient-gold">Pickleball Facilities</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay:0.2s">
                Developed and refined through the real-world operation of multiple pickleball facilities, the K2Pickleball Platform serves as the complete operating system for your facility — managing day-to-day operations and customer interactions.
            </p>
        </div>
    </div>
</section>

<!-- Platform Modules -->
<section class="py-24 lg:py-32 relative">
    <?php
    $modules = [
        [
            'title' => 'Online Reservations & Court Scheduling',
            'desc' => 'Give your players a seamless booking experience with real-time court availability, automated scheduling, and intelligent conflict detection. Support recurring reservations, group bookings, and walk-in management from one unified system.',
            'features' => ['Real-time court availability grid', 'Recurring & group reservations', 'Walk-in and waitlist management', 'Automated confirmation & reminders', 'Mobile-optimized booking flow', 'Multi-facility calendar sync'],
            'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
        ],
        [
            'title' => 'Point-of-Sale & Retail',
            'desc' => 'A complete point-of-sale system built for facility operations. Process retail sales, manage waivers, handle walk-in registrations, and accept equipment rentals — all integrated with Square payment processing.',
            'features' => ['Square Terminal integration', 'Equipment rental tracking', 'Digital waiver collection', 'Walk-in registration flow', 'Receipt & refund management', 'Cash & card processing'],
            'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z',
        ],
        [
            'title' => 'Customer Database & Player Profiles',
            'desc' => 'Build lasting relationships with a comprehensive customer database. Track player profiles, skill levels, activity history, membership status, and preferences — all in one place for personalized service.',
            'features' => ['Complete player profiles', 'Skill level tracking', 'Activity & booking history', 'Membership status management', 'Communication preferences', 'Custom player tags & labels'],
            'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        ],
        [
            'title' => 'Events, Classes & League Management',
            'desc' => 'Run your entire programming calendar — from beginner clinics to competitive leagues. Manage class schedules, handle enrollment, track attendance, and optimize your programming mix for maximum revenue.',
            'features' => ['Class & clinic scheduling', 'League bracket management', 'Automated enrollment & waitlists', 'Attendance tracking', 'Instructor assignment', 'Program revenue analytics'],
            'icon' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342',
        ],
        [
            'title' => 'Memberships & Package Management',
            'desc' => 'Flexible membership tiers, visit packages, gift certificates, credit codes, and subscriptions — all with automated billing. Design the perfect membership structure to maximize recurring revenue and player retention.',
            'features' => ['Tiered membership plans', 'Visit packages & bundles', 'Gift certificates & credit codes', 'Automated recurring billing', 'Discount & promotion engine', 'Membership portal for players'],
            'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
        ],
        [
            'title' => 'Reporting & Analytics Dashboards',
            'desc' => 'Make data-driven decisions with comprehensive analytics. Track revenue, court utilization, membership growth, class attendance, and player engagement — all in real-time dashboards designed for facility operators.',
            'features' => ['Revenue & financial reports', 'Court utilization analytics', 'Membership growth tracking', 'Class attendance metrics', 'Player engagement insights', 'Custom date range filtering'],
            'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
        ],
    ];
    foreach ($modules as $i => $mod):
        $isReversed = $i % 2 === 1;
    ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 <?= $i > 0 ? 'mt-24 lg:mt-32' : '' ?>">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center <?= $isReversed ? 'direction-rtl' : '' ?>" data-animate>
            <!-- Content -->
            <div class="<?= $isReversed ? 'lg:order-2' : '' ?>">
                <div class="h-12 w-12 rounded-xl bg-gold-500/10 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $mod['icon'] ?>"/></svg>
                </div>
                <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight"><?= $mod['title'] ?></h2>
                <p class="mt-4 text-base text-slate-400 leading-relaxed"><?= $mod['desc'] ?></p>
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <?php foreach ($mod['features'] as $feat): ?>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        <span class="text-sm text-slate-300"><?= $feat ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Visual -->
            <div class="<?= $isReversed ? 'lg:order-1' : '' ?>">
                <div class="rounded-2xl glass-card p-6 sm:p-8 relative overflow-hidden">

                <?php if ($i === 0): // Scheduling — Calendar Grid ?>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-white">Court Schedule</h4>
                        <span class="text-xs text-gold-500 font-semibold">Today</span>
                    </div>
                    <div class="grid grid-cols-4 gap-1.5 text-center text-xs mb-3">
                        <div class="text-slate-500 py-1"></div>
                        <div class="text-slate-400 font-medium py-1">Court 1</div>
                        <div class="text-slate-400 font-medium py-1">Court 2</div>
                        <div class="text-slate-400 font-medium py-1">Court 3</div>
                        <?php
                        $times = ['9:00','10:00','11:00','12:00','1:00','2:00'];
                        $slots = [
                            ['bg-gold-500/20 text-gold-400 border border-gold-500/30','bg-navy-700/40 text-slate-500','bg-gold-500/20 text-gold-400 border border-gold-500/30'],
                            ['bg-navy-700/40 text-slate-500','bg-emerald-500/15 text-emerald-400 border border-emerald-500/20','bg-navy-700/40 text-slate-500'],
                            ['bg-emerald-500/15 text-emerald-400 border border-emerald-500/20','bg-gold-500/20 text-gold-400 border border-gold-500/30','bg-emerald-500/15 text-emerald-400 border border-emerald-500/20'],
                            ['bg-gold-500/20 text-gold-400 border border-gold-500/30','bg-gold-500/20 text-gold-400 border border-gold-500/30','bg-navy-700/40 text-slate-500'],
                            ['bg-navy-700/40 text-slate-500','bg-emerald-500/15 text-emerald-400 border border-emerald-500/20','bg-gold-500/20 text-gold-400 border border-gold-500/30'],
                            ['bg-navy-700/40 text-slate-500','bg-navy-700/40 text-slate-500','bg-emerald-500/15 text-emerald-400 border border-emerald-500/20'],
                        ];
                        $labels = [['Booked','Open','Booked'],['Open','League','Open'],['Class','Booked','League'],['Booked','Booked','Open'],['Open','Class','Booked'],['Open','Open','Class']];
                        foreach ($times as $ti => $time): ?>
                        <div class="text-slate-500 py-2 text-right pr-2"><?= $time ?></div>
                        <?php for ($c = 0; $c < 3; $c++): ?>
                        <div class="<?= $slots[$ti][$c] ?> rounded-lg py-2 font-medium"><?= $labels[$ti][$c] ?></div>
                        <?php endfor; endforeach; ?>
                    </div>
                    <div class="flex items-center gap-4 mt-4 pt-3 border-t border-navy-700/40">
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded bg-gold-500/30 border border-gold-500/40"></div><span class="text-xs text-slate-500">Booked</span></div>
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded bg-emerald-500/20 border border-emerald-500/30"></div><span class="text-xs text-slate-500">Program</span></div>
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded bg-navy-700/50"></div><span class="text-xs text-slate-500">Available</span></div>
                    </div>

                <?php elseif ($i === 1): // POS — Receipt ?>
                    <div class="flex items-center justify-between mb-5">
                        <h4 class="text-sm font-bold text-white">Transaction</h4>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 text-xs font-semibold">Complete</span>
                    </div>
                    <div class="space-y-3 pb-4 border-b border-navy-700/40">
                        <div class="flex justify-between text-sm"><span class="text-slate-400">Court Rental (1hr)</span><span class="text-white font-medium">$24.00</span></div>
                        <div class="flex justify-between text-sm"><span class="text-slate-400">Paddle Rental ×2</span><span class="text-white font-medium">$10.00</span></div>
                        <div class="flex justify-between text-sm"><span class="text-slate-400">Water Bottle</span><span class="text-white font-medium">$3.50</span></div>
                    </div>
                    <div class="space-y-2 pt-4 pb-4 border-b border-navy-700/40">
                        <div class="flex justify-between text-sm"><span class="text-slate-400">Subtotal</span><span class="text-white font-medium">$37.50</span></div>
                        <div class="flex justify-between text-sm"><span class="text-slate-400">Tax (7.5%)</span><span class="text-white font-medium">$2.81</span></div>
                    </div>
                    <div class="flex justify-between pt-4">
                        <span class="text-sm font-bold text-white">Total</span>
                        <span class="text-lg font-extrabold gradient-gold">$40.31</span>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                        Paid via Square · Visa ****4242
                    </div>

                <?php elseif ($i === 2): // Player Profiles ?>
                    <div class="flex items-center justify-between mb-5">
                        <h4 class="text-sm font-bold text-white">Player Profiles</h4>
                        <span class="text-xs text-slate-500">1,247 total</span>
                    </div>
                    <?php
                    $players = [
                        ['S.M.', 'Sarah Mitchell', '4.0', 'Premium', 'bg-gold-500/20 text-gold-400', '47 bookings'],
                        ['J.R.', 'James Rodriguez', '3.5', 'Standard', 'bg-navy-600/40 text-slate-300', '32 bookings'],
                        ['A.K.', 'Amy Kim', '4.5', 'Premium', 'bg-gold-500/20 text-gold-400', '58 bookings'],
                        ['D.W.', 'David Walsh', '3.0', 'Standard', 'bg-navy-600/40 text-slate-300', '21 bookings'],
                    ];
                    foreach ($players as $p): ?>
                    <div class="flex items-center gap-3 py-3 border-b border-navy-700/30 last:border-0">
                        <div class="h-9 w-9 rounded-full bg-navy-700/60 flex items-center justify-center text-xs font-bold text-slate-300 flex-shrink-0"><?= $p[0] ?></div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-white truncate"><?= $p[1] ?></div>
                            <div class="text-xs text-slate-500"><?= $p[5] ?> · Level <?= $p[2] ?></div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full <?= $p[4] ?> text-xs font-semibold flex-shrink-0"><?= $p[3] ?></span>
                    </div>
                    <?php endforeach; ?>

                <?php elseif ($i === 3): // Events & Leagues ?>
                    <div class="flex items-center justify-between mb-5">
                        <h4 class="text-sm font-bold text-white">Upcoming Programs</h4>
                        <span class="text-xs text-gold-500 font-semibold">This Week</span>
                    </div>
                    <?php
                    $events = [
                        ['Beginner Clinic', 'Tue 6:00 PM', '12/16 enrolled', 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20', 'Class'],
                        ['Open Play Night', 'Wed 7:00 PM', '28 registered', 'bg-blue-500/15 text-blue-400 border-blue-500/20', 'Event'],
                        ['Mixed Doubles League', 'Thu 6:30 PM', '8/8 teams', 'bg-gold-500/15 text-gold-400 border-gold-500/20', 'League'],
                        ['Advanced Drills', 'Sat 9:00 AM', '8/10 enrolled', 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20', 'Class'],
                    ];
                    foreach ($events as $e): ?>
                    <div class="flex items-center gap-3 py-3 border-b border-navy-700/30 last:border-0">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-white"><?= $e[0] ?></div>
                            <div class="text-xs text-slate-500"><?= $e[1] ?> · <?= $e[2] ?></div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full border <?= $e[3] ?> text-xs font-semibold flex-shrink-0"><?= $e[4] ?></span>
                    </div>
                    <?php endforeach; ?>

                <?php elseif ($i === 4): // Memberships ?>
                    <div class="flex items-center justify-between mb-5">
                        <h4 class="text-sm font-bold text-white">Membership Plans</h4>
                        <span class="text-xs text-slate-500">3 active tiers</span>
                    </div>
                    <?php
                    $plans = [
                        ['Premium', '$149', '/mo', '340 members', 'Unlimited courts, events, guest passes', true],
                        ['Standard', '$79', '/mo', '580 members', '10 court hours, all events included', false],
                        ['Basic', '$39', '/mo', '327 members', '4 court hours, open play access', false],
                    ];
                    foreach ($plans as $pl): ?>
                    <div class="p-3 rounded-xl <?= $pl[5] ? 'bg-gold-500/10 border border-gold-500/20' : 'bg-navy-800/40 border border-navy-700/30' ?> mb-3 last:mb-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-bold <?= $pl[5] ? 'text-gold-400' : 'text-white' ?>"><?= $pl[0] ?></span>
                            <span class="text-sm font-extrabold text-white"><?= $pl[1] ?><span class="text-xs text-slate-500 font-normal"><?= $pl[2] ?></span></span>
                        </div>
                        <div class="text-xs text-slate-500"><?= $pl[4] ?></div>
                        <div class="text-xs text-slate-500 mt-1"><?= $pl[3] ?></div>
                    </div>
                    <?php endforeach; ?>

                <?php else: // Analytics ?>
                    <div class="flex items-center justify-between mb-5">
                        <h4 class="text-sm font-bold text-white">Revenue Overview</h4>
                        <span class="text-xs text-slate-500">Last 30 days</span>
                    </div>
                    <div class="flex items-baseline gap-2 mb-5">
                        <span class="text-3xl font-extrabold gradient-gold font-display">$87,420</span>
                        <span class="text-xs font-semibold text-emerald-400">+12.3%</span>
                    </div>
                    <!-- Mini bar chart -->
                    <div class="flex items-end gap-1.5 h-24 mb-4">
                        <?php
                        $bars = [45,62,55,78,68,82,74,90,85,65,72,95,88,70,80,92];
                        foreach ($bars as $bi => $h): ?>
                        <div class="flex-1 rounded-t bg-gradient-to-t from-gold-600/40 to-gold-500/20 transition-all hover:from-gold-600/60 hover:to-gold-500/40" style="height: <?= $h ?>%"></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="grid grid-cols-3 gap-3 pt-3 border-t border-navy-700/40">
                        <div>
                            <div class="text-xs text-slate-500">Courts</div>
                            <div class="text-sm font-bold text-white">$42.1K</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">Members</div>
                            <div class="text-sm font-bold text-white">$31.8K</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">Retail</div>
                            <div class="text-sm font-bold text-white">$13.5K</div>
                        </div>
                    </div>
                <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</section>

<!-- Additional Capabilities -->
<section class="py-24 lg:py-32 border-y border-navy-800/60 relative">
    <div class="absolute inset-0 grid-bg opacity-20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                Plus Everything Else You <span class="gradient-gold">Need</span>
            </h2>
            <p class="mt-4 text-lg text-slate-400">Built-in tools for every aspect of facility management.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" data-animate>
            <?php
            $extras = [
                ['Multi-Facility Support', 'Manage multiple locations from one dashboard'],
                ['Notifications System', 'In-app and email notifications for staff and players'],
                ['Role-Based Access', 'Fine-grained permissions for staff, managers, and owners'],
                ['API Access', 'Full REST API for custom integrations'],
                ['File Management', 'Document storage, waiver templates, and media uploads'],
                ['Audit Trail', 'Complete logging of all system actions and changes'],
                ['White-Label Branding', 'Your logo, colors, and domain on everything'],
                ['24/7 Cloud Platform', 'Accessible anywhere, automatic updates and backups'],
            ];
            foreach ($extras as $extra): ?>
            <div class="p-4 rounded-xl glass-card card-hover">
                <h4 class="text-sm font-bold text-white mb-1"><?= $extra[0] ?></h4>
                <p class="text-xs text-slate-400"><?= $extra[1] ?></p>
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
            See the K2Pickleball Platform <span class="gradient-gold">In Action</span>
        </h2>
        <p class="mt-6 text-lg text-slate-400 max-w-2xl mx-auto">
            Schedule a personalized demo and discover how our platform can power your pickleball facility operations.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $baseUrl ?>/demo" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 text-base font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300">
                Schedule a Demo
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="<?= $baseUrl ?>/pricing" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-medium text-slate-300 hover:text-white border border-navy-700 hover:border-gold-500/30 rounded-xl transition-all">
                View Partnership Details
            </a>
        </div>
    </div>
</section>
