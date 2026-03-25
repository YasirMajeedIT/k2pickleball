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
<html lang="en">
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

    <!-- Quill.js (rich text editor) -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <!-- Flatpickr (date picker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- FullCalendar 6.x (local) -->
    <script src="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>/assets/js/fullcalendar.min.js"></script>
    <script>window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>';</script>
    <script>
        // Apply saved theme before first paint to avoid flash
        (function() {
            var t = localStorage.getItem('k2_theme') || 'light';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
        // Auth guard — redirect to admin login if no token
        if (!localStorage.getItem('access_token')) {
            window.location.href = (window.APP_BASE || '') + '/admin/login';
        }
    </script>

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

        /* Flatpickr dark mode */
        .dark .flatpickr-calendar { background: #1e293b; border-color: #334155; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); }
        .dark .flatpickr-months .flatpickr-month, .dark .flatpickr-current-month .flatpickr-monthDropdown-months { background: #1e293b; color: #e2e8f0; }
        .dark .flatpickr-weekdays, .dark span.flatpickr-weekday { background: #1e293b; color: #94a3b8; }
        .dark .flatpickr-day { color: #e2e8f0; }
        .dark .flatpickr-day:hover { background: #334155; border-color: #334155; }
        .dark .flatpickr-day.today { border-color: #6366f1; }
        .dark .flatpickr-day.selected { background: #6366f1; border-color: #6366f1; }
        .dark .flatpickr-day.flatpickr-disabled { color: #475569; }
        .dark .flatpickr-current-month input.cur-year { color: #e2e8f0; }
        .dark .flatpickr-months .flatpickr-prev-month svg, .dark .flatpickr-months .flatpickr-next-month svg { fill: #94a3b8; }
        .dark .flatpickr-innerContainer { border-bottom: none; }
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

            <!-- Trial Expiry Banner -->
            <div x-data="trialBanner()" x-init="init()" x-cloak>
                <!-- Expired -->
                <div x-show="status === 'expired'" class="flex items-center justify-between gap-4 bg-red-600 text-white px-6 py-3 text-sm font-medium">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Your free trial has expired. Subscribe to keep using K2 Pickleball.
                    </span>
                    <a href="<?= ($baseUrl ?? '') ?>/admin/account#subscription" class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-white text-red-600 hover:bg-red-50 font-semibold px-4 py-1.5 text-xs transition-colors">
                        Choose a Plan <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
                <!-- Expiring soon (≤ 7 days) -->
                <div x-show="status === 'expiring'" class="flex items-center justify-between gap-4 bg-amber-500 text-white px-6 py-3 text-sm font-medium">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Your trial expires in <strong x-text="daysLeft + ' day' + (daysLeft === 1 ? '' : 's')"></strong>. Upgrade now to avoid interruption.</span>
                    </span>
                    <a href="<?= ($baseUrl ?? '') ?>/admin/account#subscription" class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-white text-amber-600 hover:bg-amber-50 font-semibold px-4 py-1.5 text-xs transition-colors">
                        Upgrade Now <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>

            <!-- Announcement Banners -->
            <div x-data="announcementBanner()" x-init="init()" x-cloak>
                <template x-for="ann in visible" :key="ann.id">
                    <div class="flex items-center justify-between gap-4 px-6 py-3 text-sm font-medium text-white"
                         :class="{
                             'bg-blue-600':   ann.type === 'info',
                             'bg-amber-500':  ann.type === 'warning',
                             'bg-red-600':    ann.type === 'critical',
                             'bg-purple-600': ann.type === 'maintenance'
                         }">
                        <span class="flex min-w-0 items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                            <span class="font-semibold" x-text="ann.title"></span>
                            <span class="hidden sm:inline opacity-90 truncate" x-text="ann.message"></span>
                        </span>
                        <button @click="dismiss(ann.id)" class="flex-shrink-0 ml-4 rounded-full p-1 hover:bg-white/20 transition-colors" aria-label="Dismiss">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

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
    // Shared /api/auth/me promise — all components reuse this single request
    window._mePromise = null;
    function getMe() {
        if (!window._mePromise) {
            window._mePromise = authFetch(APP_BASE + '/api/auth/me')
                .then(r => r.json())
                .catch(() => ({}));
        }
        return window._mePromise;
    }

    function trialBanner() {
        return {
            status: '', // 'expiring' | 'expired' | ''
            daysLeft: 0,
            async init() {
                try {
                    const json = await getMe();
                    const user = json.data || json;
                    const org = user.organization || null;
                    if (!org || org.status !== 'trial') return;
                    const trialEnds = org.trial_ends_at ? new Date(org.trial_ends_at) : null;
                    if (!trialEnds) return;
                    const now = new Date();
                    const msLeft = trialEnds - now;
                    const days = Math.ceil(msLeft / 86400000);
                    if (days <= 0) {
                        this.status = 'expired';
                    } else if (days <= 7) {
                        this.daysLeft = days;
                        this.status = 'expiring';
                    }
                } catch (_) {}
            }
        };
    }

    function announcementBanner() {
        return {
            visible: [],
            async init() {
                try {
                    const res = await authFetch(APP_BASE + '/api/announcements/active');
                    if (!res.ok) return;
                    const json = await res.json();
                    const dismissed = JSON.parse(localStorage.getItem('k2_dismissed_announcements') || '[]');
                    this.visible = (json.data || []).filter(a => !dismissed.includes(a.id));
                } catch (_) {}
            },
            dismiss(id) {
                this.visible = this.visible.filter(a => a.id !== id);
                const dismissed = JSON.parse(localStorage.getItem('k2_dismissed_announcements') || '[]');
                dismissed.push(id);
                localStorage.setItem('k2_dismissed_announcements', JSON.stringify(dismissed));
            }
        };
    }

    function themeToggle() {
        const THEMES = ['light', 'dark', 'system'];
        const LABELS = { light: 'Light mode', dark: 'Dark mode', system: 'System theme' };

        function applyTheme(t) {
            const isDark = t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', isDark);
            localStorage.setItem('k2_theme', t);
        }

        const saved = localStorage.getItem('k2_theme') || 'light';
        return {
            theme: saved,
            get label() { return LABELS[this.theme]; },
            cycle() {
                const next = THEMES[(THEMES.indexOf(this.theme) + 1) % THEMES.length];
                this.theme = next;
                applyTheme(next);
            }
        };
    }

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

    /**
     * Centralized auth-aware fetch wrapper.
     * Handles 401 → token refresh with mutex to prevent concurrent refresh races.
     */
    window._refreshing = null;

    async function refreshAuth() {
        const rt = localStorage.getItem('refresh_token');
        if (!rt) return false;
        try {
            const res = await fetch(APP_BASE + '/api/auth/refresh', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ refresh_token: rt })
            });
            if (!res.ok) return false;
            const rd = await res.json();
            const d = rd.data || rd;
            localStorage.setItem('access_token', d.access_token);
            if (d.refresh_token) localStorage.setItem('refresh_token', d.refresh_token);
            return true;
        } catch { return false; }
    }

    async function authFetch(url, options = {}) {
        options.headers = options.headers || {};
        options.headers['Authorization'] = 'Bearer ' + localStorage.getItem('access_token');
        if (!options.headers['Content-Type'] && !(options.body instanceof FormData)) {
            options.headers['Content-Type'] = 'application/json';
        }

        let res = await fetch(url, options);
        if (res.status === 401) {
            if (!window._refreshing) {
                window._refreshing = refreshAuth().finally(() => { window._refreshing = null; });
            }
            const ok = await window._refreshing;
            if (ok) {
                options.headers['Authorization'] = 'Bearer ' + localStorage.getItem('access_token');
                res = await fetch(url, options);
            } else {
                // Session expired — clear tokens and redirect to login
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                window.location.href = (window.APP_BASE || '') + '/login';
                return res;
            }
        }
        return res;
    }
    </script>
</body>
</html>
