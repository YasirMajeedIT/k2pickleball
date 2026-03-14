<?php $pageTitle = 'K2 Scheduling Software — Features & Capabilities'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20 text-sm font-medium text-brand-400 mb-6">Product</div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1]">
                The operating system for <span class="gradient-text">sports facilities</span>
            </h1>
            <p class="mt-6 text-lg text-surface-400 leading-relaxed max-w-2xl">
                K2 Pickleball Scheduling is a comprehensive platform that handles everything from court bookings and member management to payments and analytics — all in one place.
            </p>
        </div>
    </div>
</section>

<!-- Core Modules -->
<section class="py-24 border-t border-surface-800/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php
        $modules = [
            [
                'badge' => 'Scheduling',
                'title' => 'Smart Court Scheduling',
                'desc' => 'An intelligent scheduling engine that eliminates double-bookings, supports recurring reservations, and optimizes court utilization automatically.',
                'features' => ['Drag-and-drop calendar interface', 'Recurring & one-time bookings', 'Conflict detection & resolution', 'Time-slot optimization', 'Walk-in & advance booking modes', 'Multi-court batch scheduling'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>',
            ],
            [
                'badge' => 'Members',
                'title' => 'Member & User Management',
                'desc' => 'Complete member lifecycle management from onboarding to activity tracking. Organize members with roles, skill levels, and custom permissions.',
                'features' => ['Member profiles with skill ratings', 'Role-based access control', 'Activity & booking history', 'Group & team management', 'Check-in tracking', 'Custom permission sets'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
            ],
            [
                'badge' => 'Facilities',
                'title' => 'Multi-Facility Management',
                'desc' => 'Manage multiple locations, courts, and amenities from a centralized dashboard. Track real-time availability across your entire organization.',
                'features' => ['Multi-location support', 'Court configuration & types', 'Amenity tracking', 'Facility hours & closures', 'Maintenance scheduling', 'Real-time availability map'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>',
            ],
            [
                'badge' => 'Payments',
                'title' => 'Integrated Payment Processing',
                'desc' => 'Accept payments securely via Square integration. Automatic invoicing, subscription billing, and comprehensive financial reporting built in.',
                'features' => ['Square payment integration', 'Subscription billing', 'Automatic invoicing', 'Refund management', 'Financial dashboards', 'Revenue forecasting'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>',
            ],
            [
                'badge' => 'Analytics',
                'title' => 'Reports & Analytics',
                'desc' => 'Data-driven insights to optimize your business. Revenue tracking, utilization reports, member growth analytics, and custom dashboards.',
                'features' => ['Revenue dashboards', 'Court utilization heatmaps', 'Member growth tracking', 'Booking trend analysis', 'Custom report builder', 'Export to CSV/PDF'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>',
            ],
        ];
        foreach ($modules as $i => $mod): ?>
        <div class="<?= $i > 0 ? 'mt-24' : '' ?> grid lg:grid-cols-2 gap-12 items-center <?= $i % 2 ? 'lg:flex-row-reverse' : '' ?>">
            <div class="<?= $i % 2 ? 'lg:order-2' : '' ?>">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20 text-sm font-medium text-brand-400 mb-4"><?= $mod['badge'] ?></div>
                <h2 class="text-3xl font-extrabold tracking-tight text-white"><?= $mod['title'] ?></h2>
                <p class="mt-4 text-surface-400 leading-relaxed"><?= $mod['desc'] ?></p>
                <ul class="mt-6 grid grid-cols-2 gap-3">
                    <?php foreach ($mod['features'] as $feat): ?>
                    <li class="flex items-center gap-2 text-sm text-surface-300">
                        <svg class="w-4 h-4 text-brand-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        <?= $feat ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="<?= $i % 2 ? 'lg:order-1' : '' ?> rounded-2xl border border-surface-800/60 bg-surface-900/40 p-8 flex items-center justify-center min-h-[280px]">
                <svg class="w-24 h-24 text-brand-500/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $mod['icon'] ?></svg>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Extensions -->
<section class="py-24 bg-surface-900/30 border-y border-surface-800/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20 text-sm font-medium text-brand-400 mb-4">Extensions</div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                Extend with <span class="gradient-text">powerful add-ons</span>
            </h2>
            <p class="mt-4 text-lg text-surface-400">Our extensions marketplace lets you add exactly the features you need.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php
            $extensions = [
                ['name' => 'Tournament Manager', 'desc' => 'Run brackets, round-robins & ladder tournaments'],
                ['name' => 'League Management', 'desc' => 'Seasonal leagues with standings & stats'],
                ['name' => 'Online Booking', 'desc' => 'Public booking portal for members'],
                ['name' => 'Mobile App', 'desc' => 'Native iOS & Android companion app'],
                ['name' => 'Advanced Analytics', 'desc' => 'Deep-dive reports & data exports'],
                ['name' => 'DUPR Integration', 'desc' => 'Sync player ratings automatically'],
                ['name' => 'Custom Branding', 'desc' => 'White-label with your logo & colors'],
                ['name' => 'API Access', 'desc' => 'Build custom integrations & automations'],
            ];
            foreach ($extensions as $ext): ?>
            <div class="p-4 rounded-xl border border-surface-800/60 bg-surface-900/30 hover:border-brand-500/20 transition-colors">
                <h3 class="text-sm font-semibold text-white"><?= $ext['name'] ?></h3>
                <p class="mt-1 text-xs text-surface-500"><?= $ext['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
            Ready to see it in action?
        </h2>
        <p class="mt-4 text-lg text-surface-400">Start with our free plan or schedule a personalized demo with our team.</p>
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $baseUrl ?>/register" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">Get Started Free</a>
            <a href="<?= $baseUrl ?>/demo" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-medium text-surface-300 hover:text-white border border-surface-700 hover:border-surface-600 rounded-2xl transition-all">Request a Demo</a>
        </div>
    </div>
</section>
