<?php $pageTitle = 'About — K2Pickleball.com'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6 animate-fade-in-up">Our Story</div>
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight animate-fade-in-up" style="animation-delay:0.1s">
            Built by Operators,<br><span class="gradient-gold">For Operators</span>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay:0.2s">
            K2 was born from real-world experience operating pickleball facilities — not a boardroom. Every feature exists because we needed it ourselves.
        </p>
    </div>
</section>

<!-- Origin Story -->
<section class="py-24 lg:py-32 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center" data-animate>
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    From a 6-Court Facility to a <span class="gradient-gold">National Platform</span>
                </h2>
                <div class="mt-6 space-y-4 text-base text-slate-400 leading-relaxed">
                    <p>K2 started with a single question: why isn't there a purpose-built platform for pickleball facilities? Existing solutions were either generic gym software adapted for courts or expensive custom development that couldn't scale.</p>
                    <p>So we built our own. Starting at our flagship Tampa Bay facility, we developed every feature through real operations — testing with real players, real staff, and real revenue pressure.</p>
                    <p>The result is a platform that doesn't just manage your facility — it <strong class="text-white">operates</strong> it. From the first online booking to the monthly revenue report, K2 handles the complexity so you can focus on building a thriving pickleball community.</p>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-8 relative overflow-hidden">
                <div class="absolute inset-0 animate-shimmer"></div>
                <div class="relative space-y-6">
                    <div class="flex items-center gap-4 pb-6 border-b border-navy-700/60">
                        <div class="text-4xl font-extrabold gradient-gold font-display">$1M+</div>
                        <div class="text-sm text-slate-400">Annual revenue at our<br>flagship Tampa facility</div>
                    </div>
                    <div class="flex items-center gap-4 pb-6 border-b border-navy-700/60">
                        <div class="text-4xl font-extrabold gradient-gold font-display">6</div>
                        <div class="text-sm text-slate-400">Courts operating at<br>94% utilization</div>
                    </div>
                    <div class="flex items-center gap-4 pb-6 border-b border-navy-700/60">
                        <div class="text-4xl font-extrabold gradient-gold font-display">1,200+</div>
                        <div class="text-sm text-slate-400">Active members managed<br>through the platform</div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-4xl font-extrabold gradient-gold font-display">24/7</div>
                        <div class="text-sm text-slate-400">Platform availability<br>with automated operations</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section class="py-24 lg:py-32 border-y border-navy-800/60 relative">
    <div class="absolute inset-0 grid-bg opacity-20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                What Drives <span class="gradient-gold">K2</span>
            </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" data-animate>
            <?php
            $values = [
                ['Operator-First Design', 'Every feature is designed from the perspective of someone running a facility — not a software engineer. If it doesn\'t make operations smoother, it doesn\'t ship.', 'M11.42 15.17l-5.7-5.7a2 2 0 010-2.83l.06-.06a2 2 0 012.83 0l5.7 5.7a2 2 0 010 2.83l-.06.06a2 2 0 01-2.83 0zM20 12a8 8 0 11-16 0 8 8 0 0116 0z'],
                ['Revenue Alignment', 'Our partnership model means we only succeed when you succeed. We\'re not chasing seat licenses — we\'re invested in your facility\'s growth.', 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Continuous Evolution', 'Pickleball is growing fast, and so are we. New features, integrations, and optimizations ship regularly — all included in your partnership.', 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75'],
                ['Data-Driven Decisions', 'We built comprehensive analytics because we believe every facility decision should be backed by data — from programming schedules to pricing strategy.', 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
                ['Community Building', 'We believe pickleball facilities are more than courts — they\'re community hubs. Our platform helps you build and nurture that community.', 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
                ['Transparency Always', 'No hidden fees, no surprise charges, no enterprise sales games. Our pricing is straightforward, our partnership terms are clear, and our communication is honest.', 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ];
            foreach ($values as $val): ?>
            <div class="glass-card card-hover rounded-xl p-6">
                <div class="h-12 w-12 rounded-xl bg-gold-500/10 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $val[2] ?>"/></svg>
                </div>
                <h3 class="font-display text-lg font-bold text-white mb-2"><?= $val[0] ?></h3>
                <p class="text-sm text-slate-400 leading-relaxed"><?= $val[1] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Tampa Proof -->
<section class="py-24 lg:py-32 relative">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-animate>
        <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
            Proven in <span class="gradient-gold">Tampa Bay</span>
        </h2>
        <p class="mt-4 text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed">
            Our flagship Tampa Bay facility serves as the proving ground for every K2 feature. With $1M+ in annual revenue and 1,200+ active members, it demonstrates the full potential of the platform and partnership model.
        </p>
        <blockquote class="mt-10 glass-card rounded-2xl p-8 text-left">
            <p class="text-lg text-slate-300 leading-relaxed italic">
                "Every piece of the K2Pickleball Platform was built to solve a real problem we encountered operating our Tampa Bay facility. That's the difference — we're not guessing what facility operators need. We know, because we are one."
            </p>
            <div class="mt-6 flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-gold-500/20 flex items-center justify-center">
                    <span class="text-sm font-bold text-gold-400">K2</span>
                </div>
                <div>
                    <div class="text-sm font-bold text-white">K2 Team</div>
                    <div class="text-xs text-slate-500">Tampa Bay, Florida</div>
                </div>
            </div>
        </blockquote>
    </div>
</section>

<!-- CTA -->
<section class="py-24 lg:py-32 border-t border-navy-800/60 relative overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-gold-500/8 rounded-full blur-[120px]"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center" data-animate>
        <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
            Let's Build Something <span class="gradient-gold">Great Together</span>
        </h2>
        <p class="mt-6 text-lg text-slate-400 max-w-2xl mx-auto">
            We're looking for ambitious facility operators who want to build the future of pickleball. If that's you, let's talk.
        </p>
        <div class="mt-10">
            <a href="<?= $baseUrl ?>/demo" class="inline-flex items-center justify-center gap-2.5 px-8 py-4 text-base font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300">
                Schedule Consultation
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>
</section>
