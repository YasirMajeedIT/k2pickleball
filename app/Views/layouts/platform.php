<?php
/**
 * Platform Super Admin Layout — Premium SaaS Design
 * Full-page layout for platform-level administration (managing all organizations, plans, system).
 */
?>
<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Platform Admin', ENT_QUOTES) ?> | K2 Platform</title>
    <script>window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>';</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#faf5ff',100:'#f3e8ff',200:'#e9d5ff',300:'#d8b4fe',400:'#c084fc',500:'#a855f7',600:'#9333ea',700:'#7e22ce',800:'#6b21a8',900:'#581c87',950:'#3b0764' },
                        surface: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'] },
                    boxShadow: {
                        'soft': '0 1px 3px 0 rgba(0,0,0,0.08), 0 1px 2px -1px rgba(0,0,0,0.08)',
                        'medium': '0 4px 6px -1px rgba(0,0,0,0.08), 0 2px 4px -2px rgba(0,0,0,0.08)',
                    },
                }
            }
        };
    </script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(168,85,247,0.2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(168,85,247,0.4); }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }
    </style>
</head>
<body class="font-sans bg-surface-100 dark:bg-surface-950 text-surface-700 dark:text-surface-300 antialiased">
    <!-- Toast Notifications -->
    <div x-data="{ toasts: [] }" @toast.window="toasts.push({...$event.detail, id: Date.now()}); setTimeout(() => toasts.shift(), 4000)"
        class="fixed right-6 bottom-6 z-[100] flex flex-col gap-3">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition duration-300 ease-out"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition duration-200 ease-in"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 :class="{
                    'bg-emerald-500/90 border-emerald-400/20': toast.type === 'success',
                    'bg-red-500/90 border-red-400/20': toast.type === 'error',
                    'bg-primary-500/90 border-primary-400/20': toast.type === 'info',
                    'bg-amber-500/90 border-amber-400/20': toast.type === 'warning'
                 }" class="rounded-xl px-5 py-3.5 text-white shadow-lg text-sm max-w-xs backdrop-blur-sm border font-medium">
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    <div class="flex min-h-screen">
        <!-- Platform Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-[280px] transform bg-gradient-to-b from-purple-950 via-purple-950 to-surface-950 text-white shadow-xl transition-transform duration-300 lg:static lg:translate-x-0 border-r border-purple-900/50">
            <div class="flex h-16 items-center justify-between px-6 border-b border-purple-800/40">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 font-bold text-sm shadow-lg shadow-primary-500/20">K2</div>
                    <div>
                        <span class="text-base font-bold tracking-tight">Platform</span>
                        <span class="block text-[10px] text-purple-300/60 uppercase tracking-wider font-semibold">Super Admin</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white/50 hover:text-white rounded-lg p-1 hover:bg-white/5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="py-4 px-3 space-y-0.5 overflow-y-auto sidebar-scroll h-[calc(100vh-8rem)]">
                <p class="mb-3 ml-3 text-[11px] font-semibold uppercase tracking-[0.15em] text-purple-400/50">Navigation</p>
                <?php
                $platformNav = [
                    ['label' => 'Dashboard', 'url' => ($baseUrl ?? '') . '/platform', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>'],
                    ['label' => 'Organizations', 'url' => ($baseUrl ?? '') . '/platform/organizations', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>'],
                    ['label' => 'Plans', 'url' => ($baseUrl ?? '') . '/platform/plans', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75"/>'],
                    ['label' => 'Subscriptions', 'url' => ($baseUrl ?? '') . '/platform/subscriptions', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>'],
                    ['label' => 'Revenue', 'url' => ($baseUrl ?? '') . '/platform/revenue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                    ['label' => 'Invoices', 'url' => ($baseUrl ?? '') . '/platform/invoices', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>'],
                    ['label' => 'Extensions', 'url' => ($baseUrl ?? '') . '/platform/extensions', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/>'],
                    ['label' => 'Announcements', 'url' => ($baseUrl ?? '') . '/platform/announcements', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a1.125 1.125 0 01-1.536-.41L7.28 17.607a48.682 48.682 0 013.06-1.767m0-5.76c-.253-.962-.584-1.892-.985-2.783-.247-.55-.06-1.21.463-1.511l.657-.38a1.125 1.125 0 011.536.41l1.389 2.406a48.682 48.682 0 01-3.06 1.767zM16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm4.5 0h.75"/>'],
                    ['label' => 'System Users', 'url' => ($baseUrl ?? '') . '/platform/system-users', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>'],
                    ['label' => 'System Settings', 'url' => ($baseUrl ?? '') . '/platform/system-settings', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                    ['label' => 'Audit Logs', 'url' => ($baseUrl ?? '') . '/platform/audit-logs', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>'],
                ];
                $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
                foreach ($platformNav as $item): 
                    $isActive = $currentPath === $item['url'] || ($item['url'] !== '/platform' && str_starts_with($currentPath, $item['url']));
                ?>
                <a href="<?= $item['url'] ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium border transition-all duration-200 <?= $isActive ? 'bg-white/10 text-white border-white/10 shadow-sm' : 'text-purple-200/70 border-transparent hover:bg-white/[0.04] hover:text-white' ?>">
                    <svg class="h-[18px] w-[18px] shrink-0 <?= $isActive ? 'text-primary-300' : 'text-purple-400/50' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?= $item['icon'] ?></svg>
                    <?= $item['label'] ?>
                    <?php if ($isActive): ?><span class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-400"></span><?php endif; ?>
                </a>
                <?php endforeach; ?>
            </nav>
            <div class="border-t border-purple-800/40 px-4 py-4">
                <a href="<?= ($baseUrl ?? '') . '/admin' ?>" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-medium text-purple-300/60 hover:text-white hover:bg-white/[0.04] transition-all border border-transparent hover:border-white/5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                    Switch to Org Admin
                </a>
            </div>
        </aside>

        <!-- Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             x-transition:enter="transition-opacity duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-surface-950/60 backdrop-blur-sm lg:hidden" style="display:none;"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-surface-200 dark:border-surface-800 bg-white/80 dark:bg-surface-900/80 backdrop-blur-xl px-4 lg:px-8 shadow-soft">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-surface-500 hover:text-surface-700 dark:text-surface-400 rounded-xl p-2 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <span class="inline-flex items-center rounded-lg bg-gradient-to-r from-purple-500/10 to-primary-500/10 border border-purple-200/30 dark:border-purple-500/20 px-3 py-1 text-xs font-semibold text-purple-700 dark:text-purple-300">
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Super Admin
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="flex h-10 w-10 items-center justify-center rounded-xl border border-surface-200 bg-surface-50 hover:bg-surface-100 dark:border-surface-700 dark:bg-surface-800 dark:hover:bg-surface-700 transition-all">
                        <svg x-show="!darkMode" class="h-[18px] w-[18px] text-surface-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                        <svg x-show="darkMode" class="h-[18px] w-[18px] text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                    </button>
                    <div class="h-6 w-px bg-surface-200 dark:bg-surface-700 mx-1"></div>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2.5 rounded-xl px-2 py-1.5 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 text-white text-sm font-bold shadow-soft">SA</div>
                            <span class="hidden sm:block text-sm font-semibold text-surface-800 dark:text-white">Super Admin</span>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-3 w-56 rounded-2xl bg-white dark:bg-surface-800 py-1 shadow-lg border border-surface-200 dark:border-surface-700 overflow-hidden" style="display:none;">
                            <a href="<?= ($baseUrl ?? '') . '/admin' ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                                <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                Org Admin Panel
                            </a>
                            <hr class="my-1 border-surface-100 dark:border-surface-700">
                            <button onclick="platformLogout()" class="flex items-center gap-3 w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-8 max-w-[1600px] mx-auto w-full">
                <?php if (!empty($breadcrumbs)): ?>
                <nav class="mb-6 flex items-center gap-1.5 text-sm">
                    <a href="<?= ($baseUrl ?? '') . '/platform' ?>" class="text-primary-500 hover:text-primary-600 font-medium transition-colors">Platform</a>
                    <?php foreach ($breadcrumbs as $crumb): ?>
                        <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <?php if (isset($crumb['url'])): ?>
                            <a href="<?= $crumb['url'] ?>" class="text-primary-500 hover:text-primary-600 font-medium transition-colors"><?= htmlspecialchars($crumb['label'], ENT_QUOTES) ?></a>
                        <?php else: ?>
                            <span class="text-surface-600 dark:text-surface-400 font-medium"><?= htmlspecialchars($crumb['label'], ENT_QUOTES) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    <script>
        if (!localStorage.getItem('access_token')) {
            window.location.href = '<?= htmlspecialchars(($baseUrl ?? '') . '/admin/login', ENT_QUOTES) ?>';
        }
        function platformLogout() {
            const token = localStorage.getItem('refresh_token');
            fetch(APP_BASE + '/api/auth/logout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + localStorage.getItem('access_token') },
                body: JSON.stringify({ refresh_token: token })
            }).finally(() => {
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                window.location.href = APP_BASE + '/admin/login';
            });
        }
    </script>
</body>
</html>
</html>
