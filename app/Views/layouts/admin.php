<?php

declare(strict_types=1);

/**
 * Admin Panel Layout — Premium SaaS Design
 * Variables available: $title, $content, $user, $org, $breadcrumbs
 */

$title = $title ?? 'Dashboard';
$user = $user ?? [];
$org = $org ?? [];
$breadcrumbs = $breadcrumbs ?? [];
$currentPath = $_SERVER['REQUEST_URI'] ?? '/admin';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($title) ?> — K2 Pickleball</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81',950:'#1e1b4b' },
                        accent:  { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857' },
                        surface: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'] },
                    boxShadow: {
                        'soft': '0 1px 3px 0 rgba(0,0,0,0.08), 0 1px 2px -1px rgba(0,0,0,0.08)',
                        'medium': '0 4px 6px -1px rgba(0,0,0,0.08), 0 2px 4px -2px rgba(0,0,0,0.08)',
                        'glow': '0 0 20px rgba(99,102,241,0.15)',
                    },
                    borderRadius: { '2xl': '1rem', '3xl': '1.5rem' },
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js (for dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>';</script>

    <style>
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,0.4); }

        /* Sidebar scrollbar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

        /* Smooth transitions */
        * { transition-property: background-color, border-color, color, box-shadow; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        a, button { transition-duration: 200ms; }
    </style>
</head>
<body class="font-sans text-surface-700 bg-surface-100 dark:bg-surface-950 dark:text-surface-300 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">

            <!-- Top Bar -->
            <?php include __DIR__ . '/../components/topbar.php'; ?>

            <!-- Page Content -->
            <main class="mx-auto w-full max-w-[1600px] p-4 md:p-6 lg:p-8">
                <!-- Breadcrumbs -->
                <?php if (!empty($breadcrumbs)): ?>
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-bold text-surface-900 dark:text-white tracking-tight">
                        <?= htmlspecialchars($title) ?>
                    </h2>
                    <nav>
                        <ol class="flex items-center gap-1.5 text-sm">
                            <li>
                                <a href="<?= ($baseUrl ?? '') . '/admin' ?>" class="text-primary-500 hover:text-primary-600 font-medium">Dashboard</a>
                            </li>
                            <?php foreach ($breadcrumbs as $bc): ?>
                            <li class="text-surface-400">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </li>
                            <?php if (isset($bc['url'])): ?>
                            <li><a href="<?= htmlspecialchars($bc['url']) ?>" class="text-primary-500 hover:text-primary-600 font-medium"><?= htmlspecialchars($bc['label']) ?></a></li>
                            <?php else: ?>
                            <li class="text-surface-600 dark:text-surface-400 font-medium"><?= htmlspecialchars($bc['label']) ?></li>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                </div>
                <?php endif; ?>

                <!-- Injected Content -->
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div x-data="toasts()" x-on:toast.window="add($event.detail)" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3">
        <template x-for="toast in list" :key="toast.id">
            <div x-show="toast.visible"
                 x-transition:enter="transition duration-300 ease-out"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition duration-200 ease-in"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="flex items-center gap-3 rounded-xl px-5 py-3.5 shadow-lg backdrop-blur-sm min-w-[320px] border"
                 :class="{
                     'bg-emerald-500/90 text-white border-emerald-400/20': toast.type === 'success',
                     'bg-red-500/90 text-white border-red-400/20': toast.type === 'error',
                     'bg-amber-500/90 text-white border-amber-400/20': toast.type === 'warning',
                     'bg-primary-500/90 text-white border-primary-400/20': toast.type === 'info',
                 }">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <span x-text="toast.message" class="text-sm font-medium"></span>
                <button x-on:click="remove(toast.id)" class="ml-auto text-white/70 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    <script>
    function toasts() {
        return {
            list: [],
            add(detail) {
                const id = Date.now();
                this.list.push({ id, ...detail, visible: true });
                setTimeout(() => this.remove(id), 4000);
            },
            remove(id) {
                const t = this.list.find(x => x.id === id);
                if (t) t.visible = false;
                setTimeout(() => { this.list = this.list.filter(x => x.id !== id); }, 300);
            }
        }
    }
    </script>
</body>
</html>
