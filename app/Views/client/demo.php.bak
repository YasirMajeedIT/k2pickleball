<?php $pageTitle = 'Request a Demo — K2 Pickleball'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20 text-sm font-medium text-brand-400 mb-6">Schedule a Demo</div>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-[1.1]">
                See K2 Pickleball <span class="gradient-text">in action</span>
            </h1>
            <p class="mt-6 text-lg text-surface-400">Get a personalized walkthrough of our platform. Our team will show you exactly how K2 can work for your facility.</p>
        </div>
    </div>
</section>

<!-- Demo Form + Benefits -->
<section class="pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-5 gap-12">
            <!-- Form -->
            <div class="lg:col-span-3" x-data="demoForm()">
                <div class="p-8 rounded-2xl border border-surface-800/60 bg-surface-900/30">
                    <h2 class="text-xl font-bold text-white mb-6">Request your free demo</h2>
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-surface-300 mb-2">First Name *</label>
                                <input type="text" x-model="form.firstName" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="John">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-300 mb-2">Last Name *</label>
                                <input type="text" x-model="form.lastName" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Doe">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Work Email *</label>
                            <input type="email" x-model="form.email" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="john@yourclub.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Phone Number</label>
                            <input type="tel" x-model="form.phone" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="(555) 123-4567">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Facility Name *</label>
                            <input type="text" x-model="form.facility" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Ace Pickleball Club">
                        </div>
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-surface-300 mb-2">Number of Courts</label>
                                <select x-model="form.courts" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors">
                                    <option value="">Select range</option>
                                    <option>1–4 courts</option>
                                    <option>5–10 courts</option>
                                    <option>11–20 courts</option>
                                    <option>20+ courts</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-300 mb-2">Current Software</label>
                                <select x-model="form.currentSoftware" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors">
                                    <option value="">Select option</option>
                                    <option>None / Spreadsheets</option>
                                    <option>CourtReserve</option>
                                    <option>PlayByPoint</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Anything else we should know?</label>
                            <textarea x-model="form.notes" rows="3" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors resize-none" placeholder="Tell us about your facility and goals..."></textarea>
                        </div>

                        <div x-show="success" x-transition class="p-4 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-400 text-sm">
                            Thank you! Our team will reach out within 1 business day to schedule your demo.
                        </div>

                        <button type="submit" :disabled="loading" class="w-full inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 disabled:cursor-not-allowed rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                            <span x-show="!loading">Request Demo</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                Submitting...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Benefits sidebar -->
            <div class="lg:col-span-2 space-y-6">
                <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30">
                    <h3 class="text-lg font-semibold text-white mb-4">What to expect</h3>
                    <ul class="space-y-4">
                        <?php
                        $steps = [
                            ['num' => '1', 'title' => 'We reach out', 'desc' => 'Our team will contact you within 1 business day to schedule a time.'],
                            ['num' => '2', 'title' => '30-minute walkthrough', 'desc' => 'A personalized demo tailored to your facility\'s needs and goals.'],
                            ['num' => '3', 'title' => 'Q&A session', 'desc' => 'Ask anything — pricing, features, migration, integrations, and more.'],
                            ['num' => '4', 'title' => 'Free trial setup', 'desc' => 'Walk away with a free account ready to explore on your own.'],
                        ];
                        foreach ($steps as $step): ?>
                        <li class="flex gap-3">
                            <div class="h-8 w-8 rounded-lg bg-brand-500/10 border border-brand-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-brand-400"><?= $step['num'] ?></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-white"><?= $step['title'] ?></h4>
                                <p class="text-xs text-surface-400 mt-0.5"><?= $step['desc'] ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30">
                    <div class="flex items-center gap-1 mb-3">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg class="w-4 h-4 text-brand-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <?php endfor; ?>
                    </div>
                    <p class="text-sm text-surface-300 italic">"The demo sold us immediately. They showed us exactly how K2 would fit our 8-court facility and we signed up that week."</p>
                    <p class="mt-3 text-sm font-medium text-white">Mike Torres</p>
                    <p class="text-xs text-surface-500">Operations Director, Summit Sports</p>
                </div>

                <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30">
                    <h3 class="text-sm font-semibold text-white mb-2">Prefer to explore on your own?</h3>
                    <p class="text-xs text-surface-400 mb-3">Create a free account and start using K2 Pickleball right away.</p>
                    <a href="<?= $baseUrl ?>/register" class="inline-flex items-center gap-2 text-sm font-medium text-brand-400 hover:text-brand-300 transition-colors">
                        Start Free →
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function demoForm() {
    return {
        form: { firstName: '', lastName: '', email: '', phone: '', facility: '', courts: '', currentSoftware: '', notes: '' },
        loading: false,
        success: false,
        submit() {
            this.loading = true;
            setTimeout(() => {
                this.loading = false;
                this.success = true;
                this.form = { firstName: '', lastName: '', email: '', phone: '', facility: '', courts: '', currentSoftware: '', notes: '' };
            }, 1000);
        }
    }
}
</script>
