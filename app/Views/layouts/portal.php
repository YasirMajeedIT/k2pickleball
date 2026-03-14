<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard — K2 Pickleball') ?></title>
    <script>window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>';</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b',950:'#022c22' },
                        surface: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',850:'#172033',900:'#0f172a',950:'#020617' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .shadow-soft { box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06); }
    </style>
    <script>
        // Auth guard — redirect to login if no token
        if (!localStorage.getItem('access_token')) {
            window.location.href = (window.APP_BASE || '') + '/login';
        }
    </script>
</head>
<body class="font-sans bg-surface-950 text-surface-200 antialiased">
    <div x-data="portalShell()" x-init="init()" class="min-h-screen flex">

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 w-64 bg-surface-900 border-r border-surface-800/60 transition-transform duration-200 lg:translate-x-0 lg:static lg:inset-auto">
            <!-- Logo -->
            <div class="h-16 flex items-center gap-3 px-5 border-b border-surface-800/60">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/20">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold text-white">K2</span>
                    <span class="text-lg font-light text-brand-400 ml-0.5">Portal</span>
                </div>
            </div>

            <!-- Nav -->
            <nav class="p-4 space-y-1">
                <?php
                $portalNav = [
                    ['label' => 'Dashboard', 'url' => ($baseUrl ?? '') . '/portal', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>'],
                    ['label' => 'Subscription', 'url' => ($baseUrl ?? '') . '/portal/subscription', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>'],
                    ['label' => 'Invoices', 'url' => ($baseUrl ?? '') . '/portal/invoices', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>'],
                    ['label' => 'Settings', 'url' => ($baseUrl ?? '') . '/portal/settings', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                ];
                $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
                foreach ($portalNav as $item):
                    $isActive = rtrim($currentPath, '/') === rtrim($item['url'], '/');
                ?>
                <a href="<?= htmlspecialchars($item['url']) ?>"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors <?= $isActive ? 'text-brand-400 bg-brand-500/10' : 'text-surface-400 hover:text-white hover:bg-surface-800/50' ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $item['icon'] ?></svg>
                    <?= $item['label'] ?>
                </a>
                <?php endforeach; ?>

                <!-- Divider -->
                <div class="pt-4 mt-4 border-t border-surface-800/60">
                    <a href="<?= $baseUrl ?>/admin" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-white hover:bg-surface-800/50 transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        Open Admin Panel
                    </a>
                    <a href="<?= $baseUrl ?>/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-white hover:bg-surface-800/50 transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                        Back to Website
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Overlay for mobile -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/50 lg:hidden"></div>

        <!-- Main -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Topbar -->
            <header class="h-16 bg-surface-900/80 backdrop-blur-xl border-b border-surface-800/60 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-20">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-surface-400 hover:text-white hover:bg-surface-800/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
                <div class="hidden lg:block"></div>

                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-3 px-3 py-1.5 rounded-xl hover:bg-surface-800/50 transition-colors">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-sm font-bold text-white" x-text="userInitials"></div>
                        <span class="hidden sm:block text-sm font-medium text-surface-300" x-text="userName"></span>
                        <svg class="w-4 h-4 text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false"
                         x-transition class="absolute right-0 mt-2 w-56 bg-surface-800 border border-surface-700/60 rounded-xl shadow-xl py-2 z-50">
                        <div class="px-4 py-2 border-b border-surface-700/60">
                            <p class="text-sm font-medium text-white" x-text="userName"></p>
                            <p class="text-xs text-surface-400" x-text="userEmail"></p>
                        </div>
                        <a href="<?= $baseUrl ?>/portal/settings" class="block px-4 py-2 text-sm text-surface-300 hover:text-white hover:bg-surface-700/50 transition-colors">Account Settings</a>
                        <button @click="logout()" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-surface-700/50 transition-colors">Sign Out</button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-8">
                <?php include dirname(__DIR__) . '/' . $contentView; ?>
            </main>
        </div>
    </div>

    <script>
    function portalShell() {
        return {
            sidebarOpen: false,
            userName: '',
            userEmail: '',
            userInitials: '',
            async init() {
                try {
                    const res = await fetch(APP_BASE + '/api/auth/me', {
                        headers: { 'Authorization': 'Bearer ' + localStorage.getItem('access_token') }
                    });
                    if (!res.ok) { this.handleAuthError(); return; }
                    const data = await res.json();
                    const u = data.data || data;
                    this.userName = (u.first_name || '') + ' ' + (u.last_name || '');
                    this.userEmail = u.email || '';
                    this.userInitials = ((u.first_name?.[0] || '') + (u.last_name?.[0] || '')).toUpperCase();
                } catch { this.handleAuthError(); }
            },
            handleAuthError() {
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                window.location.href = APP_BASE + '/login';
            },
            async logout() {
                try {
                    await fetch(APP_BASE + '/api/auth/logout', {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + localStorage.getItem('access_token') }
                    });
                } catch {}
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                window.location.href = APP_BASE + '/login';
            }
        }
    }
    </script>
</body>
</html>
