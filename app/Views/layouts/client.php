<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'K2 Pickleball — Professional Court Scheduling Software') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? 'Premium scheduling and management software for pickleball clubs, sports facilities, and recreation centers.') ?>">
    <script>window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>';</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b',950:'#022c22' },
                        surface: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',850:'#172033',900:'#0f172a',950:'#020617' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'] },
                    boxShadow: {
                        'glow': '0 0 20px rgba(16,185,129,0.15)',
                        'glow-lg': '0 0 40px rgba(16,185,129,0.2)',
                    },
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        .gradient-text { background: linear-gradient(135deg, #10b981 0%, #34d399 50%, #6ee7b7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-glow { background: radial-gradient(ellipse 80% 60% at 50% -20%, rgba(16,185,129,0.15) 0%, transparent 70%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
    </style>
</head>
<body class="font-sans bg-surface-950 text-white antialiased">

    <!-- Navigation -->
    <nav x-data="{ open: false, scrolled: false }" 
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
         :class="scrolled ? 'bg-surface-950/95 backdrop-blur-xl border-b border-surface-800/50 shadow-lg' : 'bg-transparent'"
         class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                <!-- Logo -->
                <a href="<?= $baseUrl ?>/" class="flex items-center gap-3 group">
                    <div class="relative">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/25 group-hover:shadow-brand-500/40 transition-shadow">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-tight text-white">K2</span>
                        <span class="text-xl font-light tracking-tight text-brand-400 ml-0.5">Pickleball</span>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden lg:flex items-center gap-1">
                    <?php
                    $navItems = [
                        ['label' => 'Product', 'url' => ($baseUrl ?? '') . '/product'],
                        ['label' => 'Pricing', 'url' => ($baseUrl ?? '') . '/pricing'],
                        ['label' => 'About', 'url' => ($baseUrl ?? '') . '/about'],
                        ['label' => 'Contact', 'url' => ($baseUrl ?? '') . '/contact'],
                    ];
                    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
                    foreach ($navItems as $item):
                        $isActive = rtrim($currentPath, '/') === rtrim($item['url'], '/');
                    ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $isActive ? 'text-brand-400 bg-brand-500/10' : 'text-surface-300 hover:text-white hover:bg-surface-800/50' ?>">
                        <?= $item['label'] ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Desktop CTA -->
                <div class="hidden lg:flex items-center gap-3">
                    <a href="<?= $baseUrl ?>/login" class="px-4 py-2 text-sm font-medium text-surface-300 hover:text-white transition-colors">Sign In</a>
                    <a href="<?= $baseUrl ?>/register" class="px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-xl shadow-lg shadow-brand-600/25 hover:shadow-brand-500/30 transition-all">
                        Get Started Free
                    </a>
                </div>

                <!-- Mobile hamburger -->
                <button @click="open = !open" class="lg:hidden p-2 rounded-lg text-surface-400 hover:text-white hover:bg-surface-800/50 transition-colors">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Mobile menu -->
            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                 class="lg:hidden pb-4 border-t border-surface-800/50">
                <div class="pt-4 space-y-1">
                    <?php foreach ($navItems as $item):
                        $isActive = rtrim($currentPath, '/') === rtrim($item['url'], '/');
                    ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="block px-4 py-2.5 rounded-lg text-sm font-medium <?= $isActive ? 'text-brand-400 bg-brand-500/10' : 'text-surface-300 hover:text-white hover:bg-surface-800/50' ?>">
                        <?= $item['label'] ?>
                    </a>
                    <?php endforeach; ?>
                    <div class="pt-3 mt-3 border-t border-surface-800/50 space-y-2 px-4">
                        <a href="<?= $baseUrl ?>/login" class="block w-full text-center py-2.5 text-sm font-medium text-surface-300 hover:text-white rounded-lg border border-surface-700 hover:border-surface-600 transition-colors">Sign In</a>
                        <a href="<?= $baseUrl ?>/register" class="block w-full text-center py-2.5 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-lg transition-colors">Get Started Free</a>
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
    <footer class="border-t border-surface-800/50 bg-surface-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 lg:gap-12">
                <!-- Brand -->
                <div class="col-span-2 md:col-span-4 lg:col-span-2">
                    <a href="<?= $baseUrl ?>/" class="flex items-center gap-3 mb-5">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-lg font-bold text-white">K2</span>
                            <span class="text-lg font-light text-brand-400 ml-0.5">Pickleball</span>
                        </div>
                    </a>
                    <p class="text-sm text-surface-400 leading-relaxed max-w-xs">
                        Professional scheduling and management software for pickleball clubs and sports facilities worldwide.
                    </p>
                </div>

                <!-- Product -->
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Product</h4>
                    <ul class="space-y-3">
                        <li><a href="<?= $baseUrl ?>/product" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">Features</a></li>
                        <li><a href="<?= $baseUrl ?>/pricing" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">Pricing</a></li>
                        <li><a href="<?= $baseUrl ?>/demo" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">Request Demo</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Company</h4>
                    <ul class="space-y-3">
                        <li><a href="<?= $baseUrl ?>/about" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">About</a></li>
                        <li><a href="<?= $baseUrl ?>/contact" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">Contact</a></li>
                        <li><a href="<?= $baseUrl ?>/privacy-policy" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="<?= $baseUrl ?>/terms" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">Terms of Service</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Account</h4>
                    <ul class="space-y-3">
                        <li><a href="<?= $baseUrl ?>/login" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">Sign In</a></li>
                        <li><a href="<?= $baseUrl ?>/register" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">Register</a></li>
                        <li><a href="<?= $baseUrl ?>/portal" class="text-sm text-surface-400 hover:text-brand-400 transition-colors">Dashboard</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-surface-800/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-surface-500">&copy; <?= date('Y') ?> K2 Pickleball. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="text-surface-500 hover:text-brand-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="text-surface-500 hover:text-brand-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
