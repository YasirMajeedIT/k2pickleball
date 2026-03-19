<?php $pageTitle = 'Schedule Consultation — K2 Pickleball Platform'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6 animate-fade-in-up">Free Consultation</div>
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight animate-fade-in-up" style="animation-delay:0.1s">
            Schedule Your <span class="gradient-gold">Consultation</span>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay:0.2s">
            Speak with our partnership team about your facility vision. No pressure, no commitment — just an honest conversation about what K2 can do for you.
        </p>
    </div>
</section>

<!-- Form + Sidebar -->
<section class="py-24 lg:py-32 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-5 gap-12 lg:gap-16" data-animate>
            <!-- Form -->
            <div class="lg:col-span-3">
                <div class="glass-card rounded-2xl p-8 sm:p-10">
                    <h2 class="font-display text-2xl font-extrabold text-white mb-2">Tell Us About Your Plans</h2>
                    <p class="text-sm text-slate-400 mb-8">Complete the form below and our team will reach out within one business day to schedule your consultation.</p>

                    <form x-data="{ sending: false, sent: false, facilityStage: '' }" @submit.prevent="sending = true; setTimeout(() => { sending = false; sent = true; }, 1500)" class="space-y-5">
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">First Name *</label>
                                <input type="text" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="John">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Last Name *</label>
                                <input type="text" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="Smith">
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Email *</label>
                                <input type="email" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="john@example.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Phone</label>
                                <input type="tel" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="(555) 123-4567">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Where are you in the process? *</label>
                            <select x-model="facilityStage" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm">
                                <option value="" class="bg-navy-900">Select your stage</option>
                                <option value="exploring" class="bg-navy-900">Just exploring the idea</option>
                                <option value="planning" class="bg-navy-900">Actively planning a facility</option>
                                <option value="location" class="bg-navy-900">Have a location identified</option>
                                <option value="building" class="bg-navy-900">Currently in buildout</option>
                                <option value="operating" class="bg-navy-900">Already operating a facility</option>
                                <option value="expanding" class="bg-navy-900">Expanding to additional locations</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Planned Location (City, State)</label>
                            <input type="text" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="Tampa, FL">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Number of Courts Planned</label>
                            <select class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm">
                                <option value="" class="bg-navy-900">Select range</option>
                                <option value="2-4" class="bg-navy-900">2-4 courts</option>
                                <option value="5-8" class="bg-navy-900">5-8 courts</option>
                                <option value="9-12" class="bg-navy-900">9-12 courts</option>
                                <option value="13+" class="bg-navy-900">13+ courts</option>
                                <option value="unsure" class="bg-navy-900">Not sure yet</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tell us about your vision</label>
                            <textarea rows="4" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm resize-none" placeholder="Share any details about your facility plans, target market, timeline, or questions you have..."></textarea>
                        </div>

                        <button type="submit" :disabled="sending" class="w-full inline-flex items-center justify-center gap-2.5 px-8 py-4 text-base font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300 disabled:opacity-70">
                            <span x-show="!sending && !sent">Request Consultation</span>
                            <span x-show="sending" x-cloak>Submitting...</span>
                            <span x-show="sent" x-cloak>Request Submitted!</span>
                            <svg x-show="!sending && !sent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card rounded-xl p-6 gold-border">
                    <h3 class="font-display text-lg font-bold text-white mb-4">What to Expect</h3>
                    <div class="space-y-4">
                        <?php
                        $steps = [
                            ['1', 'Discovery Call', 'A 30-minute conversation about your vision, market, and goals.'],
                            ['2', 'Platform Demo', 'Live walkthrough of the K2 Platform tailored to your use case.'],
                            ['3', 'Partnership Proposal', 'Custom partnership terms based on your facility scope and timeline.'],
                            ['4', 'Launch Planning', 'Detailed launch roadmap with milestones and support schedule.'],
                        ];
                        foreach ($steps as $step): ?>
                        <div class="flex gap-3">
                            <div class="h-8 w-8 rounded-lg bg-gold-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-gold-500"><?= $step[0] ?></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white"><?= $step[1] ?></h4>
                                <p class="text-xs text-slate-400 mt-0.5"><?= $step[2] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="glass-card rounded-xl p-6">
                    <h3 class="font-display text-lg font-bold text-white mb-3">No Obligation</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        This consultation is completely free with no strings attached. We believe in earning your partnership, not pressuring you into it.
                    </p>
                </div>

                <div class="glass-card rounded-xl p-6">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="text-3xl font-extrabold gradient-gold font-display">$1M+</div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Annual revenue generated at our flagship Tampa Bay facility — proof that the K2 model works.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
