<?php $pageTitle = 'Contact — K2 Pickleball Platform'; ?>

<!-- Hero -->
<section class="relative pt-32 pb-20 hero-glow overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold-500/10 border border-gold-500/20 text-xs font-semibold text-gold-400 uppercase tracking-widest mb-6 animate-fade-in-up">Get in Touch</div>
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight animate-fade-in-up" style="animation-delay:0.1s">
            Let's Start the <span class="gradient-gold">Conversation</span>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay:0.2s">
            Whether you're ready to partner or just exploring, our team is here to help.
        </p>
    </div>
</section>

<!-- Contact Form + Info -->
<section class="py-24 lg:py-32 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-5 gap-12 lg:gap-16" data-animate>
            <!-- Form -->
            <div class="lg:col-span-3">
                <div class="glass-card rounded-2xl p-8 sm:p-10">
                    <h2 class="font-display text-2xl font-extrabold text-white mb-6">Send Us a Message</h2>
                    <form x-data="{ sending: false, sent: false }" @submit.prevent="sending = true; setTimeout(() => { sending = false; sent = true; }, 1500)" class="space-y-5">
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">First Name</label>
                                <input type="text" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="John">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Last Name</label>
                                <input type="text" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="Smith">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                            <input type="email" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="john@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Subject</label>
                            <select required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm">
                                <option value="" class="bg-navy-900">Select a topic</option>
                                <option value="partnership" class="bg-navy-900">Partnership Inquiry</option>
                                <option value="demo" class="bg-navy-900">Schedule a Demo</option>
                                <option value="support" class="bg-navy-900">Existing Partner Support</option>
                                <option value="press" class="bg-navy-900">Press & Media</option>
                                <option value="other" class="bg-navy-900">General Inquiry</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Message</label>
                            <textarea rows="5" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm resize-none" placeholder="Tell us about your facility plans or how we can help..."></textarea>
                        </div>
                        <button type="submit" :disabled="sending" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-gold-lg transition-all duration-300 disabled:opacity-70">
                            <span x-show="!sending && !sent">Send Message</span>
                            <span x-show="sending" x-cloak>Sending...</span>
                            <span x-show="sent" x-cloak>Message Sent!</span>
                            <svg x-show="!sending && !sent" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Info Sidebar -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card rounded-xl p-6">
                    <div class="h-10 w-10 rounded-xl bg-gold-500/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1">Email</h3>
                    <p class="text-sm text-slate-400">partnerships@k2pickleball.com</p>
                </div>

                <div class="glass-card rounded-xl p-6">
                    <div class="h-10 w-10 rounded-xl bg-gold-500/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1">Location</h3>
                    <p class="text-sm text-slate-400">Tampa Bay, Florida<br>United States</p>
                </div>

                <div class="glass-card rounded-xl p-6">
                    <div class="h-10 w-10 rounded-xl bg-gold-500/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1">Response Time</h3>
                    <p class="text-sm text-slate-400">We respond to all partnership inquiries within one business day.</p>
                </div>

                <div class="glass-card rounded-xl p-6 gold-border">
                    <h3 class="text-base font-bold text-white mb-2">Ready to Partner?</h3>
                    <p class="text-sm text-slate-400 mb-4">Skip the form and schedule a live consultation with our partnership team.</p>
                    <a href="<?= $baseUrl ?>/demo" class="inline-flex items-center gap-2 text-sm font-bold gradient-gold hover:underline">
                        Schedule Consultation
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
