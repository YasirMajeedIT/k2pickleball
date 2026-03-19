<?php
/**
 * Tenant Dashboard Layout — K2 Navy/Gold Theme
 * Authenticated player dashboard: sidebar, header, auth guard, toast.
 * Child views included via $contentView.
 */
$orgName     = htmlspecialchars($org['name'] ?? 'Sports Club', ENT_QUOTES, 'UTF-8');
$primaryColor = $branding['primary_color'] ?? '#d4af37';
$logoUrl     = $branding['logo_url'] ?? '';
$orgId       = (int)($org['id'] ?? 0);
$facilities  = $org['facilities'] ?? [];
$courtCat    = $org['system_categories']['book-a-court'] ?? ['name' => 'Book a Court', 'is_active' => true];
$courtCatName = htmlspecialchars($courtCat['name']);
$courtCatActive = (bool) $courtCat['is_active'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — <?= $orgName ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy: { 950:'#060d1a', 900:'#0b1629', 850:'#101f36', 800:'#162844', 700:'#1e3658', 600:'#27466e', 500:'#3160a0', 400:'#4a7ec4' },
                    gold: { 300:'#f0d878', 400:'#e8c84e', 500:'#d4af37', 600:'#b8952d', 700:'#9c7c24', 800:'#7d6420' },
                    surface: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a' },
                },
                fontFamily: { display: ['"Plus Jakarta Sans"', 'sans-serif'], body: ['Inter', 'sans-serif'] },
                boxShadow: { gold: '0 4px 20px rgba(212,175,55,.15)', 'gold-lg': '0 8px 40px rgba(212,175,55,.2)' },
            }
        }
    }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-gold-bg { background: linear-gradient(135deg, #e8c84e 0%, #d4af37 50%, #b8952d 100%); }
        .glass { background: rgba(11,22,41,.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .glass-card { background: rgba(22,40,68,.4); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .gold-border { border: 1px solid rgba(212,175,55,.08); }
    </style>
    <script>
        const baseApi = window.location.origin;
        const ORG = {
            id: <?= $orgId ?>,
            name: <?= json_encode($orgName) ?>,
            facilities: <?= json_encode($facilities) ?>,
        };
    </script>
</head>
<body class="bg-navy-950 text-white" x-data="dashboardApp()" x-init="init()">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 h-16 glass border-b border-gold-500/10 flex items-center px-4 lg:px-6">
        <!-- Mobile Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-navy-800 transition-all mr-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <!-- Logo -->
        <a href="/" class="flex items-center gap-2 mr-auto">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $orgName ?>" class="h-8 object-contain">
            <?php else: ?>
                <div class="w-8 h-8 rounded-lg gradient-gold-bg flex items-center justify-center">
                    <span class="text-sm font-extrabold text-navy-950"><?= mb_substr($orgName, 0, 1) ?></span>
                </div>
            <?php endif; ?>
            <span class="font-display font-bold text-white text-sm hidden sm:inline"><?= $orgName ?></span>
        </a>

        <!-- User Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full gradient-gold-bg flex items-center justify-center">
                    <span class="text-xs font-bold text-navy-950" x-text="player?.first_name?.charAt(0)?.toUpperCase() || '?'"></span>
                </div>
                <span class="hidden sm:inline text-sm font-medium text-slate-300" x-text="player?.first_name || 'Player'"></span>
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-navy-900 border border-navy-700 rounded-xl shadow-xl overflow-hidden z-50">
                <a href="/dashboard/profile" class="block px-4 py-2.5 text-sm text-slate-300 hover:bg-navy-800 hover:text-gold-400 transition-colors">My Profile</a>
                <a href="/" class="block px-4 py-2.5 text-sm text-slate-300 hover:bg-navy-800 hover:text-gold-400 transition-colors">Public Site</a>
                <button @click="logout()" class="w-full text-left px-4 py-2.5 text-sm text-red-400 hover:bg-navy-800 transition-colors border-t border-navy-700">Sign Out</button>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="fixed top-16 left-0 bottom-0 w-64 z-40 bg-navy-900 border-r border-navy-800 overflow-y-auto transform transition-transform lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <!-- Sidebar Overlay (mobile) -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-[-1] lg:hidden" x-transition.opacity></div>

        <nav class="px-4 py-6 space-y-1">
            <?php
            $navItems = [
                ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                ['label' => 'My Bookings', 'url' => '/dashboard/bookings', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                ['label' => 'Profile', 'url' => '/dashboard/profile', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
                ['label' => 'Notifications', 'url' => '/dashboard/notifications', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>'],
            ];
            $currentPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
            foreach ($navItems as $item):
                $isActive = $currentPath === trim($item['url'], '/');
            ?>
            <a href="<?= $item['url'] ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive ? 'gradient-gold-bg text-navy-950 shadow-gold' : 'text-slate-400 hover:text-white hover:bg-navy-800' ?>">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $item['icon'] ?></svg>
                <?= $item['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <div class="px-4 mt-4 pt-4 border-t border-navy-800">
            <a href="/schedule" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gold-500 hover:bg-gold-500/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Book a Session
            </a>
            <?php if ($courtCatActive): ?>
            <a href="/book-court" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gold-500 hover:bg-gold-500/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><line x1="3" y1="12" x2="21" y2="12" stroke-width="2"/></svg>
                <?= $courtCatName ?>
            </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-16 min-h-screen">
        <div class="p-6 lg:p-8 max-w-6xl">
            <?php
            $contentPath = dirname(__DIR__) . '/' . $contentView;
            if (file_exists($contentPath)) {
                include $contentPath;
            }
            ?>
        </div>
    </main>

    <!-- Toast -->
    <div x-show="toast.show" x-transition x-cloak
         class="fixed bottom-6 right-6 z-50 glass-card rounded-xl px-5 py-3 shadow-xl border"
         :class="{
             'border-emerald-500/30 text-emerald-400': toast.type === 'success',
             'border-red-500/30 text-red-400': toast.type === 'error',
             'border-gold-500/30 text-gold-400': toast.type === 'info',
         }"
         x-text="toast.message">
    </div>

    <script>
    function dashboardApp() {
        return {
            player: null,
            sidebarOpen: false,
            toast: { show: false, message: '', type: 'info' },

            async init() {
                const token = localStorage.getItem('player_token');
                if (!token) { window.location.href = '/login'; return; }
                try {
                    const resp = await this.authFetch(baseApi + '/api/auth/me');
                    const json = await resp.json();
                    if (json.success && json.data) {
                        this.player = json.data;
                    } else {
                        localStorage.removeItem('player_token');
                        localStorage.removeItem('player_refresh');
                        window.location.href = '/login';
                    }
                } catch(e) {
                    window.location.href = '/login';
                }
            },

            async authFetch(url, opts = {}) {
                const token = localStorage.getItem('player_token');
                opts.headers = {
                    ...(opts.headers || {}),
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                };
                return fetch(url, opts);
            },

            async logout() {
                try {
                    await this.authFetch(baseApi + '/api/auth/logout', {
                        method: 'POST',
                        body: JSON.stringify({ refresh_token: localStorage.getItem('player_refresh') || '' })
                    });
                } catch(e) {}
                localStorage.removeItem('player_token');
                localStorage.removeItem('player_refresh');
                window.location.href = '/login';
            },

            showToast(message, type = 'info') {
                this.toast = { show: true, message, type };
                setTimeout(() => this.toast.show = false, 4000);
            }
        };
    }
    </script>
</body>
</html>
