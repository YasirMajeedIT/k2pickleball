<?php $pageTitle = 'About K2 Pickleball — Our Mission & Team'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20 text-sm font-medium text-brand-400 mb-6">About Us</div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1]">
                Built by players,<br><span class="gradient-text">for players</span>
            </h1>
            <p class="mt-6 text-lg text-surface-400 leading-relaxed">
                We started K2 Pickleball because we saw sports facilities struggling with outdated tools and fragmented systems. Our mission is to give every facility — from a single court to a multi-location enterprise — the technology to thrive.
            </p>
        </div>
    </div>
</section>

<!-- Values -->
<section class="py-24 border-t border-surface-800/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold tracking-tight">Our <span class="gradient-text">values</span></h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $values = [
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>', 'title' => 'Performance First', 'desc' => 'Speed and reliability aren\'t optional. Every feature is optimized for real-time use on the court.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/>', 'title' => 'Simplicity', 'desc' => 'Powerful doesn\'t have to mean complex. We obsess over making every workflow intuitive.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>', 'title' => 'Security', 'desc' => 'Your data is protected with enterprise-grade encryption, JWT authentication, and strict access controls.'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>', 'title' => 'Community', 'desc' => 'We build with and for the pickleball community. Your feedback shapes our product roadmap.'],
            ];
            foreach ($values as $v): ?>
            <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30">
                <div class="h-12 w-12 rounded-xl bg-brand-500/10 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $v['icon'] ?></svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2"><?= $v['title'] ?></h3>
                <p class="text-sm text-surface-400 leading-relaxed"><?= $v['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Story -->
<section class="py-24 bg-surface-900/30 border-y border-surface-800/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight">Our <span class="gradient-text">story</span></h2>
                <div class="mt-6 space-y-4 text-surface-400 leading-relaxed">
                    <p>K2 Pickleball was born from a simple frustration: managing court bookings with spreadsheets and phone calls just didn't scale. As pickleball exploded in popularity, facilities needed professional-grade tools to keep up with demand.</p>
                    <p>We assembled a team of software engineers and facility operators who understood both sides — the technology and the day-to-day reality of running a sports center. The result is a platform that's as intuitive for front-desk staff as it is powerful for facility directors.</p>
                    <p>Today, K2 Pickleball powers hundreds of facilities across the country, from small community centers to multi-location enterprises. And we're just getting started.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/50 text-center">
                    <div class="text-3xl font-extrabold gradient-text">2023</div>
                    <div class="mt-1 text-sm text-surface-400">Founded</div>
                </div>
                <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/50 text-center">
                    <div class="text-3xl font-extrabold gradient-text">500+</div>
                    <div class="mt-1 text-sm text-surface-400">Facilities</div>
                </div>
                <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/50 text-center">
                    <div class="text-3xl font-extrabold gradient-text">150k+</div>
                    <div class="mt-1 text-sm text-surface-400">Monthly Bookings</div>
                </div>
                <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/50 text-center">
                    <div class="text-3xl font-extrabold gradient-text">99.9%</div>
                    <div class="mt-1 text-sm text-surface-400">Uptime</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold tracking-tight">Meet the <span class="gradient-text">team</span></h2>
            <p class="mt-4 text-surface-400">A passionate group of engineers, designers, and sports enthusiasts.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $team = [
                ['name' => 'Alex Kim', 'role' => 'CEO & Co-Founder', 'initials' => 'AK'],
                ['name' => 'Jordan Patel', 'role' => 'CTO & Co-Founder', 'initials' => 'JP'],
                ['name' => 'Casey Rivera', 'role' => 'Head of Product', 'initials' => 'CR'],
                ['name' => 'Morgan Lee', 'role' => 'Head of Engineering', 'initials' => 'ML'],
            ];
            foreach ($team as $member): ?>
            <div class="text-center p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30">
                <div class="h-20 w-20 rounded-full bg-gradient-to-br from-brand-400/20 to-brand-600/20 border border-brand-500/20 flex items-center justify-center mx-auto text-xl font-bold text-brand-400"><?= $member['initials'] ?></div>
                <h3 class="mt-4 text-base font-semibold text-white"><?= $member['name'] ?></h3>
                <p class="text-sm text-surface-400"><?= $member['role'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 border-t border-surface-800/40">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold tracking-tight">Want to join us?</h2>
        <p class="mt-4 text-lg text-surface-400">We're always looking for talented people who are passionate about sports and technology.</p>
        <div class="mt-8">
            <a href="<?= $baseUrl ?>/contact" class="inline-flex items-center gap-2 px-8 py-4 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">Get in Touch</a>
        </div>
    </div>
</section>
