<?php
/**
 * Tenant About Page — K2 Navy/Gold Theme
 * Org story, mission, values. Dynamic from $org context.
 */
$orgName = htmlspecialchars($org['name'] ?? 'Our Organization', ENT_QUOTES, 'UTF-8');
?>

<div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 50)">
    <!-- Hero -->
    <section class="relative bg-navy-900 overflow-hidden py-24">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute inset-0 hero-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                About Us
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white">About <?= $orgName ?></h1>
            <p class="mt-4 text-lg text-slate-400 max-w-2xl mx-auto">Where passion for the game meets world-class facilities and community.</p>
        </div>
    </section>

    <!-- Mission -->
    <section class="relative py-16 bg-navy-950">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-animate>
                    <div class="inline-flex items-center gap-2 text-xs font-bold text-gold-500 uppercase tracking-widest mb-4">
                        <span class="w-8 h-px bg-gold-500"></span> Our Mission
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-display font-extrabold text-white mb-6">Building a Stronger Community Through Sport</h2>
                    <p class="text-slate-400 leading-relaxed mb-4">We believe in the power of sport to bring people together. Our mission is to provide a welcoming, inclusive environment where players of all skill levels can enjoy the game, improve their abilities, and build lasting friendships.</p>
                    <p class="text-slate-400 leading-relaxed">From beginners stepping onto the court for the first time to competitive players honing their craft, we are committed to delivering an exceptional experience every single day.</p>
                </div>
                <div class="glass-card rounded-2xl p-8 gold-border" data-animate>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-xl gradient-gold-bg flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="text-xl font-extrabold text-gold-400">Community</div>
                            <div class="text-xs text-slate-500 mt-1">Building connections</div>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-xl gradient-gold-bg flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            </div>
                            <div class="text-xl font-extrabold text-gold-400">Excellence</div>
                            <div class="text-xs text-slate-500 mt-1">Top-tier facilities</div>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-xl gradient-gold-bg flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <div class="text-xl font-extrabold text-gold-400">Passion</div>
                            <div class="text-xs text-slate-500 mt-1">Love of the game</div>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-xl gradient-gold-bg flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div class="text-xl font-extrabold text-gold-400">Growth</div>
                            <div class="text-xs text-slate-500 mt-1">For every skill level</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What We Offer -->
    <section class="relative py-16 bg-navy-950">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10" data-animate>
                <div class="inline-flex items-center gap-2 text-xs font-bold text-gold-500 uppercase tracking-widest mb-4">
                    <span class="w-8 h-px bg-gold-500"></span> What We Offer <span class="w-8 h-px bg-gold-500"></span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-display font-extrabold text-white">Everything You Need to Play</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-card rounded-2xl p-6 gold-border text-center transition-all hover:shadow-gold" data-animate>
                    <div class="w-14 h-14 rounded-2xl gradient-gold-bg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><line x1="3" y1="12" x2="21" y2="12" stroke-width="2"/></svg>
                    </div>
                    <h3 class="text-lg font-display font-bold text-white mb-2">Court Reservations</h3>
                    <p class="text-sm text-slate-400">Book courts online anytime. Choose your preferred time, court type, and duration with instant confirmation.</p>
                </div>
                <div class="glass-card rounded-2xl p-6 gold-border text-center transition-all hover:shadow-gold" data-animate>
                    <div class="w-14 h-14 rounded-2xl gradient-gold-bg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-lg font-display font-bold text-white mb-2">Programs & Classes</h3>
                    <p class="text-sm text-slate-400">From beginner clinics to competitive leagues, find the perfect program for your skill level and schedule.</p>
                </div>
                <div class="glass-card rounded-2xl p-6 gold-border text-center transition-all hover:shadow-gold" data-animate>
                    <div class="w-14 h-14 rounded-2xl gradient-gold-bg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-display font-bold text-white mb-2">Events & Socials</h3>
                    <p class="text-sm text-slate-400">Join community tournaments, round robins, and social events. Meet new players and enjoy the camaraderie.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="relative py-16 bg-navy-950">
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-animate>
            <div class="glass-card rounded-2xl p-10 gold-border">
                <h2 class="text-2xl sm:text-3xl font-display font-extrabold text-white mb-4">Ready to Get Started?</h2>
                <p class="text-slate-400 mb-8">Browse the schedule and start playing today.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="/schedule" class="px-8 py-3 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-gold transition-all">View Schedule</a>
                    <a href="/register" class="px-8 py-3 rounded-xl font-bold text-sm border border-gold-500/30 text-gold-400 hover:bg-gold-500/10 transition-all">Create Account</a>
                </div>
            </div>
        </div>
    </section>
</div>
