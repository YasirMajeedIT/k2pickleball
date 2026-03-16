<?php
/**
 * Top Bar Component — Premium SaaS Design
 * Variables: $user, $title
 */
$user = $user ?? [];
$userName = trim(($user['first_name'] ?? 'Admin') . ' ' . ($user['last_name'] ?? ''));
$userEmail = $user['email'] ?? '';
$userRole = $user['role'] ?? 'Administrator';
$initials = strtoupper(substr($user['first_name'] ?? 'A', 0, 1) . substr($user['last_name'] ?? '', 0, 1));
?>
<header class="sticky top-0 z-[99] flex w-full bg-white/80 backdrop-blur-xl dark:bg-surface-900/80 border-b border-surface-200/60 dark:border-surface-800/60">
    <div class="flex flex-grow items-center justify-between px-4 py-3 md:px-6 lg:px-8">
        <div class="flex items-center gap-3 lg:hidden">
            <!-- Hamburger -->
            <button x-on:click="sidebarOpen = !sidebarOpen"
                    class="flex items-center justify-center rounded-xl border border-surface-200 bg-white p-2 shadow-soft hover:bg-surface-50 dark:border-surface-700 dark:bg-surface-800 dark:hover:bg-surface-700 lg:hidden">
                <svg class="w-5 h-5 text-surface-600 dark:text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <!-- Search Bar -->
        <div class="hidden sm:block">
            <div class="relative group">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2">
                    <svg class="w-4 h-4 text-surface-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" placeholder="Search anything..."
                       class="w-full bg-surface-50 pl-10 pr-4 py-2.5 text-sm text-surface-700 rounded-xl border border-surface-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-200 dark:focus:border-primary-500 xl:w-80 placeholder:text-surface-400">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Notifications -->
            <div x-data="{ dropOpen: false }" class="relative">
                <button x-on:click="dropOpen = !dropOpen" class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-surface-200 bg-surface-50 hover:bg-surface-100 hover:border-surface-300 dark:border-surface-700 dark:bg-surface-800 dark:hover:bg-surface-700 transition-all">
                    <svg class="w-[18px] h-[18px] text-surface-500 dark:text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <span class="absolute -top-0.5 -right-0.5 h-4 w-4 rounded-full bg-red-500 text-[10px] text-white flex items-center justify-center font-medium ring-2 ring-white dark:ring-surface-900" id="notification-badge" style="display:none;">0</span>
                </button>

                <div x-show="dropOpen" x-on:click.away="dropOpen = false" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute -right-16 mt-3 flex w-80 flex-col rounded-2xl border border-surface-200 bg-white shadow-lg dark:border-surface-700 dark:bg-surface-800 sm:right-0 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-surface-100 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800">
                        <h5 class="text-sm font-semibold text-surface-800 dark:text-white">Notifications</h5>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto px-5 py-4" id="notification-list">
                        <div class="flex flex-col items-center gap-2 py-4">
                            <svg class="w-10 h-10 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                            <p class="text-sm text-surface-400">No new notifications</p>
                        </div>
                    </div>
                    <div class="border-t border-surface-100 dark:border-surface-700 px-5 py-3 bg-surface-50/50 dark:bg-surface-800">
                        <a href="<?= ($baseUrl ?? '') . '/admin/notifications' ?>" class="text-xs font-medium text-primary-500 hover:text-primary-600 transition-colors">View All Notifications &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle -->
            <div x-data="themeToggle()" class="relative">
                <button x-on:click="cycle()"
                        :title="label"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-surface-200 bg-surface-50 hover:bg-surface-100 hover:border-surface-300 dark:border-surface-700 dark:bg-surface-800 dark:hover:bg-surface-700 transition-all">
                    <!-- Sun (light) -->
                    <svg x-show="theme === 'light'" class="w-[18px] h-[18px] text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m8.66-9H21M3 12H2m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14A7 7 0 0012 5z"/></svg>
                    <!-- Moon (dark) -->
                    <svg x-show="theme === 'dark'" class="w-[18px] h-[18px] text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                    <!-- Monitor (system) -->
                    <svg x-show="theme === 'system'" class="w-[18px] h-[18px] text-surface-500 dark:text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </button>
            </div>

            <!-- Divider -->
            <div class="h-6 w-px bg-surface-200 dark:bg-surface-700 mx-1"></div>

            <!-- User Dropdown -->
            <div x-data="{ dropOpen: false }" class="relative">
                <button x-on:click="dropOpen = !dropOpen" class="flex items-center gap-3 rounded-xl px-2 py-1.5 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                    <span class="hidden text-right lg:block">
                        <span class="block text-sm font-semibold text-surface-800 dark:text-white"><?= htmlspecialchars($userName) ?></span>
                        <span class="block text-[11px] text-surface-400 dark:text-surface-500"><?= htmlspecialchars($userRole) ?></span>
                    </span>
                    <div class="relative">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white font-bold text-sm shadow-soft">
                            <?= htmlspecialchars($initials) ?>
                        </span>
                        <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-accent-500 border-2 border-white dark:border-surface-900"></span>
                    </div>
                </button>

                <div x-show="dropOpen" x-on:click.away="dropOpen = false" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-3 flex w-64 flex-col rounded-2xl border border-surface-200 bg-white shadow-lg dark:border-surface-700 dark:bg-surface-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-surface-100 dark:border-surface-700 bg-surface-50/30 dark:bg-surface-800">
                        <p class="text-sm font-semibold text-surface-800 dark:text-white"><?= htmlspecialchars($userName) ?></p>
                        <p class="text-xs text-surface-400 mt-0.5"><?= htmlspecialchars($userEmail) ?></p>
                    </div>
                    <div class="py-2 px-2">
                        <a href="<?= ($baseUrl ?? '') . '/admin/account' ?>" class="flex items-center gap-3 px-3 py-2.5 text-sm text-surface-600 hover:bg-surface-50 dark:text-surface-300 dark:hover:bg-surface-700/50 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            My Account
                        </a>
                    </div>
                    <div class="border-t border-surface-100 dark:border-surface-700 py-2 px-2">
                        <a href="#" onclick="logout()" class="flex items-center gap-3 px-3 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                            Sign Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function logout() {
    const token = localStorage.getItem('refresh_token');
    fetch(APP_BASE + '/api/auth/logout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + localStorage.getItem('access_token') },
        body: JSON.stringify({ refresh_token: token })
    }).finally(() => {
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        localStorage.removeItem('user');
        window.location.href = APP_BASE + '/admin/login';
    });
}
</script>
