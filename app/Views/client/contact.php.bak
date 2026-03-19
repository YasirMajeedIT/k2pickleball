<?php $pageTitle = 'Contact Us — K2 Pickleball'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20 text-sm font-medium text-brand-400 mb-6">Contact</div>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-[1.1]">
                Let's <span class="gradient-text">talk</span>
            </h1>
            <p class="mt-6 text-lg text-surface-400">Have questions about K2 Pickleball? We'd love to hear from you. Our team typically responds within a few hours.</p>
        </div>
    </div>
</section>

<!-- Contact Grid -->
<section class="pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-5 gap-12">
            <!-- Contact Form -->
            <div class="lg:col-span-3" x-data="contactForm()">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">First Name</label>
                            <input type="text" x-model="form.firstName" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="John">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Last Name</label>
                            <input type="text" x-model="form.lastName" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Doe">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Email</label>
                        <input type="email" x-model="form.email" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="john@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Subject</label>
                        <select x-model="form.subject" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors">
                            <option value="" disabled>Select a topic</option>
                            <option>General Inquiry</option>
                            <option>Sales Question</option>
                            <option>Technical Support</option>
                            <option>Partnership</option>
                            <option>Billing</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Message</label>
                        <textarea x-model="form.message" required rows="5" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors resize-none" placeholder="Tell us how we can help..."></textarea>
                    </div>

                    <!-- Success message -->
                    <div x-show="success" x-transition class="p-4 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-400 text-sm">
                        Thank you for your message! We'll get back to you shortly.
                    </div>

                    <button type="submit" :disabled="loading" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 disabled:cursor-not-allowed rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                        <span x-show="!loading">Send Message</span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Sending...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="lg:col-span-2 space-y-6">
                <?php
                $contacts = [
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>', 'title' => 'Email', 'value' => 'hello@k2pickleball.com', 'sub' => 'We respond within 24 hours'],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>', 'title' => 'Phone', 'value' => '(555) 123-4567', 'sub' => 'Mon–Fri, 9am–6pm EST'],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>', 'title' => 'Office', 'value' => 'Austin, Texas', 'sub' => 'United States'],
                ];
                foreach ($contacts as $c): ?>
                <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-lg bg-brand-500/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $c['icon'] ?></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-white"><?= $c['title'] ?></h3>
                            <p class="mt-1 text-sm text-surface-300"><?= htmlspecialchars($c['value']) ?></p>
                            <p class="text-xs text-surface-500"><?= $c['sub'] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="p-6 rounded-2xl border border-brand-500/20 bg-brand-500/5">
                    <h3 class="text-sm font-semibold text-white mb-2">Looking for a demo?</h3>
                    <p class="text-sm text-surface-400 mb-4">Schedule a personalized walkthrough with our product team.</p>
                    <a href="<?= $baseUrl ?>/demo" class="inline-flex items-center gap-2 text-sm font-medium text-brand-400 hover:text-brand-300 transition-colors">
                        Request a Demo
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function contactForm() {
    return {
        form: { firstName: '', lastName: '', email: '', subject: '', message: '' },
        loading: false,
        success: false,
        submit() {
            this.loading = true;
            // Simulate form submission (no backend endpoint for contact form yet)
            setTimeout(() => {
                this.loading = false;
                this.success = true;
                this.form = { firstName: '', lastName: '', email: '', subject: '', message: '' };
                setTimeout(() => this.success = false, 5000);
            }, 1000);
        }
    }
}
</script>
