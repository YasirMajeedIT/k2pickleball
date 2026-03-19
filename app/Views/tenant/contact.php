<?php
/**
 * Tenant Contact Page — K2 Navy/Gold Theme
 * Contact form + org contact info sidebar. Client-side only (no server POST endpoint yet).
 */
$orgName  = htmlspecialchars($org['name'] ?? 'Our Organization', ENT_QUOTES, 'UTF-8');
$orgEmail = htmlspecialchars($org['email'] ?? '', ENT_QUOTES, 'UTF-8');
$orgPhone = htmlspecialchars($org['phone'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div x-data="contactForm()">
    <!-- Hero -->
    <section class="relative bg-navy-900 overflow-hidden py-20">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute inset-0 hero-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Contact
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white">Get in Touch</h1>
            <p class="mt-4 text-lg text-slate-400 max-w-2xl">Have a question or feedback? We'd love to hear from you.</p>
        </div>
    </section>

    <!-- Content -->
    <section class="relative py-12 bg-navy-950 min-h-[50vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Contact Form -->
                <div class="lg:col-span-2 glass-card rounded-2xl p-8 gold-border" data-animate>
                    <h2 class="text-xl font-display font-bold text-white mb-6">Send Us a Message</h2>

                    <!-- Success -->
                    <div x-show="sent" x-transition class="p-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-emerald-400 font-bold">Message Sent!</p>
                        <p class="text-slate-400 text-sm mt-1">Thank you for reaching out. We'll get back to you soon.</p>
                        <button @click="resetForm()" class="mt-4 text-sm text-gold-400 hover:text-gold-300 underline">Send another message</button>
                    </div>

                    <form x-show="!sent" @submit.prevent="send()" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Name *</label>
                                <input type="text" x-model="form.name" required
                                       class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                                       placeholder="Your name">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email *</label>
                                <input type="email" x-model="form.email" required
                                       class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                                       placeholder="you@example.com">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Subject *</label>
                            <input type="text" x-model="form.subject" required
                                   class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                                   placeholder="What's this about?">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Message *</label>
                            <textarea x-model="form.message" rows="5" required
                                      class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all resize-none"
                                      placeholder="Tell us more..."></textarea>
                        </div>

                        <div x-show="error" class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-sm text-red-400" x-text="error"></div>

                        <button type="submit" :disabled="submitting" class="w-full py-3.5 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-gold transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="submitting ? 'Sending...' : 'Send Message'"></span>
                        </button>
                    </form>
                </div>

                <!-- Contact Info Sidebar -->
                <div class="lg:col-span-1 space-y-6" data-animate>
                    <div class="glass-card rounded-2xl p-6 gold-border">
                        <h3 class="text-sm font-semibold text-gold-400 uppercase tracking-wider mb-4">Contact Info</h3>
                        <div class="space-y-4">
                            <?php if ($orgEmail): ?>
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gold-500/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500 mb-0.5">Email</div>
                                    <a href="mailto:<?= $orgEmail ?>" class="text-sm text-white hover:text-gold-400 transition-colors"><?= $orgEmail ?></a>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($orgPhone): ?>
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gold-500/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500 mb-0.5">Phone</div>
                                    <a href="tel:<?= $orgPhone ?>" class="text-sm text-white hover:text-gold-400 transition-colors"><?= $orgPhone ?></a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 gold-border">
                        <h3 class="text-sm font-semibold text-gold-400 uppercase tracking-wider mb-4">Quick Links</h3>
                        <nav class="space-y-2">
                            <a href="/schedule" class="flex items-center gap-2 text-sm text-slate-400 hover:text-gold-400 transition-colors py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                View Schedule
                            </a>
                            <a href="/book-court" class="flex items-center gap-2 text-sm text-slate-400 hover:text-gold-400 transition-colors py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><line x1="3" y1="12" x2="21" y2="12" stroke-width="2"/></svg>
                                <?= htmlspecialchars($org['system_categories']['book-a-court']['name'] ?? 'Book a Court') ?>
                            </a>
                            <a href="/facilities" class="flex items-center gap-2 text-sm text-slate-400 hover:text-gold-400 transition-colors py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                Our Facilities
                            </a>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<script>
function contactForm() {
    return {
        form: { name: '', email: '', subject: '', message: '' },
        submitting: false,
        sent: false,
        error: '',

        async send() {
            this.error = '';
            if (!this.form.name.trim() || !this.form.email.trim() || !this.form.subject.trim() || !this.form.message.trim()) {
                this.error = 'Please fill in all required fields.';
                return;
            }
            this.submitting = true;
            // Simulate send — no server endpoint yet
            await new Promise(r => setTimeout(r, 1200));
            this.sent = true;
            this.submitting = false;
        },

        resetForm() {
            this.form = { name: '', email: '', subject: '', message: '' };
            this.sent = false;
            this.error = '';
        }
    };
}
</script>
