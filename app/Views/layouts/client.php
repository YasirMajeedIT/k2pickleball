<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'K2Pickleball.com — Launch & Operate Your Own Pickleball Facility') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? 'The proven system for launching and operating profitable pickleball venues. White-label facility management software backed by real-world operational expertise.') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? 'K2Pickleball.com') ?>">
    <meta property="og:description" content="Launch & operate your own pickleball facility with K2Pickleball.com — proven systems, technology, and operational expertise.">
    <meta property="og:type" content="website">
    <script>window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>';</script>
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>/assets/vendor/css/fonts.css">
    <script src="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>/assets/vendor/js/tailwindcss.js"></script>
    <script defer src="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>/assets/vendor/js/alpine.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            950:'#060d1a', 900:'#0b1629', 850:'#101f36', 800:'#162844',
                            700:'#1e3658', 600:'#27466e', 500:'#3160a0', 400:'#4a7ec4',
                        },
                        gold: {
                            300:'#f0d878', 400:'#e8c84e', 500:'#d4af37', 600:'#b8952d',
                            700:'#9c7c24', 800:'#7d6420',
                        },
                        slate: {
                            50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',
                            400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',
                            800:'#1e293b',900:'#0f172a',
                        },
                    },
                    fontFamily: {
                        display: ['Poppins', 'system-ui', 'sans-serif'],
                        sans: ['Poppins', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'gold': '0 0 30px rgba(212,175,55,0.15)',
                        'gold-lg': '0 0 60px rgba(212,175,55,0.2)',
                        'navy': '0 25px 50px rgba(6,13,26,0.5)',
                    },
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        body { background: #060d1a; }
        .gradient-gold { background: linear-gradient(135deg, #d4af37 0%, #f0d878 50%, #e8c84e 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .gradient-gold-bg { background: linear-gradient(135deg, #d4af37 0%, #e8c84e 100%); }
        .hero-glow { background: radial-gradient(ellipse 80% 50% at 50% 0%, rgba(212,175,55,0.08) 0%, transparent 70%); }
        .section-glow { background: radial-gradient(ellipse 60% 40% at 50% 50%, rgba(212,175,55,0.04) 0%, transparent 70%); }
        .glass { background: rgba(11,22,41,0.6); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .glass-card { background: rgba(22,40,68,0.3); backdrop-filter: blur(12px); border: 1px solid rgba(212,175,55,0.08); }
        .card-hover { transition: all 0.4s cubic-bezier(0.25,0.46,0.45,0.94); }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 25px 60px rgba(0,0,0,0.4), 0 0 30px rgba(212,175,55,0.06); border-color: rgba(212,175,55,0.2); }
        .gold-border { border: 1px solid rgba(212,175,55,0.15); }
        .gold-border-hover:hover { border-color: rgba(212,175,55,0.35); }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
        .animate-fade-in-up { animation: fadeInUp 0.7s cubic-bezier(0.25,0.46,0.45,0.94) forwards; opacity:0; }
        @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        .animate-shimmer { background: linear-gradient(90deg, transparent 0%, rgba(212,175,55,0.06) 50%, transparent 100%); background-size: 200% 100%; animation: shimmer 3s ease-in-out infinite; }
        @keyframes pulse-gold { 0%,100%{box-shadow:0 0 0 0 rgba(212,175,55,0.3)} 50%{box-shadow:0 0 0 8px rgba(212,175,55,0)} }
        .animate-pulse-gold { animation: pulse-gold 2s ease-in-out infinite; }
        .grid-bg { background-image: linear-gradient(rgba(212,175,55,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(212,175,55,0.03) 1px, transparent 1px); background-size: 60px 60px; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #060d1a; }
        ::-webkit-scrollbar-thumb { background: #1e3658; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #27466e; }
    </style>
</head>
<body class="font-sans bg-navy-950 text-white antialiased">

    <!-- Navigation -->
    <nav x-data="{ open: false, scrolled: false }" 
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
         :class="scrolled ? 'glass border-b border-gold-500/10 shadow-lg shadow-navy-950/50' : 'bg-transparent'"
         class="fixed top-0 left-0 right-0 z-50 transition-all duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                <!-- Logo -->
                <a href="<?= $baseUrl ?>/" class="flex items-center gap-3 group">
                    <div class="relative">
                        <div class="h-10 w-10 rounded-xl bg-navy-800 border border-gold-500/20 flex items-center justify-center group-hover:border-gold-500/40 transition-all">
                            <svg class="w-6 h-6 text-gold-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="font-display">
                        <span class="text-xl font-extrabold tracking-tight text-white">K2</span>
                        <span class="text-xl font-light tracking-tight text-gold-500 ml-1">Pickleball</span>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden lg:flex items-center gap-1">
                    <?php
                    $navItems = [
                        ['label' => 'Platform', 'url' => ($baseUrl ?? '') . '/product'],
                        ['label' => 'Partnership', 'url' => ($baseUrl ?? '') . '/pricing'],
                        ['label' => 'About', 'url' => ($baseUrl ?? '') . '/about'],
                        ['label' => 'Contact', 'url' => ($baseUrl ?? '') . '/contact'],
                    ];
                    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
                    foreach ($navItems as $item):
                        $isActive = rtrim($currentPath, '/') === rtrim($item['url'], '/');
                    ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="px-4 py-2 rounded-lg text-[13px] font-medium tracking-wide uppercase transition-all duration-300 <?= $isActive ? 'text-gold-500 bg-gold-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                        <?= $item['label'] ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Desktop CTA -->
                <div class="hidden lg:flex items-center gap-4">
                    <a href="<?= $baseUrl ?>/login" class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-gold-400 transition-colors">Partner Login</a>
                    <a href="<?= $baseUrl ?>/demo" class="group/btn relative px-6 py-2.5 text-sm font-semibold text-navy-950 gradient-gold-bg rounded-lg shadow-gold hover:shadow-gold-lg transition-all duration-300 overflow-hidden">
                        <span class="relative z-10">Schedule Consultation</span>
                    </a>
                </div>

                <!-- Mobile hamburger -->
                <button @click="open = !open" class="lg:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Mobile menu -->
            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                 class="lg:hidden pb-4 border-t border-gold-500/10">
                <div class="pt-4 space-y-1">
                    <?php foreach ($navItems as $item):
                        $isActive = rtrim($currentPath, '/') === rtrim($item['url'], '/');
                    ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium <?= $isActive ? 'text-gold-500 bg-gold-500/10' : 'text-slate-300 hover:text-white hover:bg-white/5' ?>">
                        <?= $item['label'] ?>
                    </a>
                    <?php endforeach; ?>
                    <div class="pt-3 mt-3 border-t border-navy-800 space-y-2 px-4">
                        <a href="<?= $baseUrl ?>/login" class="block w-full text-center py-2.5 text-sm font-medium text-slate-300 hover:text-white rounded-lg border border-navy-700 hover:border-gold-500/30 transition-colors">Partner Login</a>
                        <a href="<?= $baseUrl ?>/demo" class="block w-full text-center py-2.5 text-sm font-semibold text-navy-950 gradient-gold-bg rounded-lg transition-colors">Schedule Consultation</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?php include dirname(__DIR__) . '/' . $contentView; ?>
    </main>

    <!-- Footer -->
    <footer class="relative border-t border-gold-500/10 bg-navy-950">
        <div class="absolute inset-0 grid-bg opacity-30"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 lg:gap-12">
                <!-- Brand -->
                <div class="col-span-2 md:col-span-4 lg:col-span-2">
                    <a href="<?= $baseUrl ?>/" class="flex items-center gap-3 mb-5">
                        <div class="h-9 w-9 rounded-xl bg-navy-800 border border-gold-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gold-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                            </svg>
                        </div>
                        <div class="font-display">
                            <span class="text-lg font-extrabold text-white">K2</span>
                            <span class="text-lg font-light text-gold-500 ml-1">Pickleball</span>
                        </div>
                    </a>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-sm">
                        The proven system for launching and operating profitable pickleball facilities. Technology, expertise, and operational support — all in one partnership.
                    </p>
                    <div class="mt-6 flex items-center gap-3">
                        <a href="#" class="h-9 w-9 rounded-lg bg-navy-800 border border-navy-700 flex items-center justify-center text-slate-500 hover:text-gold-500 hover:border-gold-500/30 transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="h-9 w-9 rounded-lg bg-navy-800 border border-navy-700 flex items-center justify-center text-slate-500 hover:text-gold-500 hover:border-gold-500/30 transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                        <a href="#" class="h-9 w-9 rounded-lg bg-navy-800 border border-navy-700 flex items-center justify-center text-slate-500 hover:text-gold-500 hover:border-gold-500/30 transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Partnership -->
                <div>
                    <h4 class="text-xs font-semibold text-gold-500 uppercase tracking-wider mb-4">Partnership</h4>
                    <ul class="space-y-3">
                        <li><a href="<?= $baseUrl ?>/product" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">The K2Pickleball Platform</a></li>
                        <li><a href="<?= $baseUrl ?>/pricing" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Partnership Structure</a></li>
                        <li><a href="<?= $baseUrl ?>/demo" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Schedule Consultation</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h4 class="text-xs font-semibold text-gold-500 uppercase tracking-wider mb-4">Company</h4>
                    <ul class="space-y-3">
                        <li><a href="<?= $baseUrl ?>/about" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">About K2</a></li>
                        <li><a href="<?= $baseUrl ?>/contact" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Contact</a></li>
                        <li><a href="<?= $baseUrl ?>/privacy-policy" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="<?= $baseUrl ?>/terms" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Terms of Service</a></li>
                    </ul>
                </div>

                <!-- Partners -->
                <div>
                    <h4 class="text-xs font-semibold text-gold-500 uppercase tracking-wider mb-4">Partners</h4>
                    <ul class="space-y-3">
                        <li><a href="<?= $baseUrl ?>/login" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Partner Login</a></li>
                        <li><a href="<?= $baseUrl ?>/register" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Apply Now</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-navy-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-600">&copy; <?= date('Y') ?> K2Pickleball.com. All rights reserved.</p>
                <p class="text-xs text-slate-600">Built with proven operational expertise from Tampa Bay's flagship pickleball facility.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll Animation Observer -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
    });
    </script>

</body>
</html>
