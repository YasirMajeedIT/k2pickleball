<?php
/**
 * Sidebar Component — Premium SaaS Design
 * Variables: $currentPath, $org
 */

$navItems = [
    ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => ($baseUrl ?? '') . '/admin', 'match' => '/admin'],
    ['label' => 'Facilities', 'icon' => 'facility', 'url' => ($baseUrl ?? '') . '/admin/facilities', 'match' => '/admin/facilities'],
    ['label' => 'Courts', 'icon' => 'court', 'url' => ($baseUrl ?? '') . '/admin/courts', 'match' => '/admin/courts'],
    ['label' => 'Users', 'icon' => 'users', 'url' => ($baseUrl ?? '') . '/admin/users', 'match' => '/admin/users'],
    ['label' => 'Roles & Permissions', 'icon' => 'roles', 'url' => ($baseUrl ?? '') . '/admin/roles', 'match' => '/admin/roles'],
    ['label' => 'Subscriptions', 'icon' => 'subscription', 'url' => ($baseUrl ?? '') . '/admin/subscriptions', 'match' => '/admin/subscriptions'],
    ['label' => 'Payments', 'icon' => 'payments', 'url' => ($baseUrl ?? '') . '/admin/payments', 'match' => '/admin/payments'],
    ['label' => 'Notifications', 'icon' => 'bell', 'url' => ($baseUrl ?? '') . '/admin/notifications', 'match' => '/admin/notifications'],
    ['label' => 'Files', 'icon' => 'files', 'url' => ($baseUrl ?? '') . '/admin/files', 'match' => '/admin/files'],
    ['label' => 'API Tokens', 'icon' => 'key', 'url' => ($baseUrl ?? '') . '/admin/api-tokens', 'match' => '/admin/api-tokens'],
    ['label' => 'Audit Logs', 'icon' => 'log', 'url' => ($baseUrl ?? '') . '/admin/audit-logs', 'match' => '/admin/audit-logs'],
    ['label' => 'Settings', 'icon' => 'settings', 'url' => ($baseUrl ?? '') . '/admin/settings', 'match' => '/admin/settings'],
    ['label' => 'Extensions', 'icon' => 'extensions', 'url' => ($baseUrl ?? '') . '/admin/extensions', 'match' => '/admin/extensions'],
];

$schedulingItems = [
    ['label' => 'Schedule Dashboard', 'icon' => 'schedule', 'url' => ($baseUrl ?? '') . '/admin/schedule-dashboard', 'match' => '/admin/schedule-dashboard'],
    ['label' => 'Categories', 'icon' => 'categories', 'url' => ($baseUrl ?? '') . '/admin/categories', 'match' => '/admin/categories'],
    ['label' => 'Resources', 'icon' => 'resources', 'url' => ($baseUrl ?? '') . '/admin/resources', 'match' => '/admin/resources'],
    ['label' => 'Players', 'icon' => 'players', 'url' => ($baseUrl ?? '') . '/admin/players', 'match' => '/admin/players'],
];

$packagesItems = [
    ['label' => 'Credit Codes', 'icon' => 'creditcodes', 'url' => ($baseUrl ?? '') . '/admin/credit-codes', 'match' => '/admin/credit-codes'],
    ['label' => 'Gift Certificates', 'icon' => 'giftcerts', 'url' => ($baseUrl ?? '') . '/admin/gift-certificates', 'match' => '/admin/gift-certificates'],
    ['label' => 'Discounts', 'icon' => 'discounts', 'url' => ($baseUrl ?? '') . '/admin/discounts', 'match' => '/admin/discounts'],
];

$legalItems = [
    ['label' => 'Waivers', 'icon' => 'waiver', 'url' => ($baseUrl ?? '') . '/admin/waivers', 'match' => '/admin/waivers'],
];

$myAccountItems = [
    ['label' => 'My Account', 'icon' => 'account', 'url' => ($baseUrl ?? '') . '/admin/account', 'match' => '/admin/account'],
];

// Heroicon-style outline SVGs for a cleaner look
$icons = [
    'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>',
    'facility' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>',
    'court' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z"/>',
    'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
    'roles' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
    'subscription' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>',
    'payments' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'bell' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>',
    'files' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>',
    'key' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>',
    'log' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>',
    'settings' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
    'extensions' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/>',
    'account' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>',
    'schedule' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"/>',
    'invoices' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>',
    'categories' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6h.008v.008H6V6z"/>',
    'resources' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L12 12.75 6.429 9.75m11.142 0l4.179 2.25-9.75 5.25-9.75-5.25 4.179-2.25"/>',
    'players' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>',
    'creditcodes' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/>',
    'giftcerts' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>',
    'discounts' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6h.008v.008H6V6z"/>',
    'waiver' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>',
];

$currentPath = $currentPath ?? '/admin';
?>
<!-- Sidebar Overlay for Mobile -->
<div x-show="sidebarOpen" x-on:click="sidebarOpen = false"
     x-transition:enter="transition-opacity duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[998] bg-surface-950/60 backdrop-blur-sm lg:hidden" x-cloak></div>

<!-- Sidebar -->
<aside class="absolute left-0 top-0 z-[999] flex h-screen w-[280px] flex-col overflow-y-hidden bg-white border-r border-surface-200 dark:bg-gradient-to-b dark:from-surface-900 dark:via-surface-900 dark:to-surface-950 dark:border-surface-800/50 duration-300 ease-linear lg:static lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <!-- Logo -->
    <div class="flex items-center justify-between gap-2 px-6 py-6">
        <a href="<?= ($baseUrl ?? '') . '/admin' ?>" class="flex items-center gap-3 group">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 shadow-lg shadow-primary-500/20 group-hover:shadow-primary-500/40 transition-shadow">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <span class="text-lg font-bold text-surface-900 dark:text-white tracking-tight">K2 Pickleball</span>
            </div>
        </a>
        <button x-on:click="sidebarOpen = false" class="block lg:hidden text-surface-400 hover:text-surface-700 dark:hover:text-white rounded-lg p-1 hover:bg-surface-100 dark:hover:bg-white/5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <div class="flex flex-col overflow-y-auto sidebar-scroll flex-1 px-4 pb-4">
        <nav>
            <p class="mb-3 ml-3 text-[11px] font-semibold uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">Navigation</p>
            <ul class="flex flex-col gap-0.5">
                <?php foreach ($navItems as $item): ?>
                <?php
                    $isActive = ($item['match'] === '/admin')
                        ? ($currentPath === '/admin' || $currentPath === ($baseUrl ?? '') . '/admin')
                        : str_starts_with($currentPath, $item['match']);
                    $activeClass = $isActive
                        ? 'bg-primary-50 text-primary-600 border-primary-200 shadow-sm dark:bg-primary-500/10 dark:text-primary-400 dark:border-primary-500/30'
                        : 'text-surface-600 border-transparent hover:bg-surface-100 hover:text-surface-800 dark:text-surface-400 dark:hover:bg-white/[0.04] dark:hover:text-surface-200';
                ?>
                <li>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="<?= $activeClass ?> group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium border transition-all duration-200">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 <?= $isActive ? 'text-primary-600 dark:text-primary-400' : 'text-surface-400 group-hover:text-surface-600 dark:group-hover:text-surface-300' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?= $icons[$item['icon']] ?? '' ?>
                        </svg>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                        <?php if ($isActive): ?>
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-500 dark:bg-primary-400"></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- Scheduling Section -->
            <p class="mb-3 ml-3 mt-6 text-[11px] font-semibold uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">Scheduling</p>
            <ul class="flex flex-col gap-0.5">
                <?php foreach ($schedulingItems as $item): ?>
                <?php
                    $isActive = str_starts_with($currentPath, $item['match']);
                    $activeClass = $isActive
                        ? 'bg-primary-50 text-primary-600 border-primary-200 shadow-sm dark:bg-primary-500/10 dark:text-primary-400 dark:border-primary-500/30'
                        : 'text-surface-600 border-transparent hover:bg-surface-100 hover:text-surface-800 dark:text-surface-400 dark:hover:bg-white/[0.04] dark:hover:text-surface-200';
                ?>
                <li>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="<?= $activeClass ?> group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium border transition-all duration-200">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 <?= $isActive ? 'text-primary-600 dark:text-primary-400' : 'text-surface-400 group-hover:text-surface-600 dark:group-hover:text-surface-300' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?= $icons[$item['icon']] ?? '' ?>
                        </svg>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                        <?php if ($isActive): ?>
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-500 dark:bg-primary-400"></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- Packages & Discounts Section -->
            <p class="mb-3 ml-3 mt-6 text-[11px] font-semibold uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">Packages &amp; Discounts</p>
            <ul class="flex flex-col gap-0.5">
                <?php foreach ($packagesItems as $item): ?>
                <?php
                    $isActive = str_starts_with($currentPath, $item['match']);
                    $activeClass = $isActive
                        ? 'bg-primary-50 text-primary-600 border-primary-200 shadow-sm dark:bg-primary-500/10 dark:text-primary-400 dark:border-primary-500/30'
                        : 'text-surface-600 border-transparent hover:bg-surface-100 hover:text-surface-800 dark:text-surface-400 dark:hover:bg-white/[0.04] dark:hover:text-surface-200';
                ?>
                <li>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="<?= $activeClass ?> group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium border transition-all duration-200">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 <?= $isActive ? 'text-primary-600 dark:text-primary-400' : 'text-surface-400 group-hover:text-surface-600 dark:group-hover:text-surface-300' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?= $icons[$item['icon']] ?? '' ?>
                        </svg>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                        <?php if ($isActive): ?>
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-500 dark:bg-primary-400"></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- Legal Section -->
            <p class="mb-3 ml-3 mt-6 text-[11px] font-semibold uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">Legal</p>
            <ul class="flex flex-col gap-0.5">
                <?php foreach ($legalItems as $item): ?>
                <?php
                    $isActive = str_starts_with($currentPath, $item['match']);
                    $activeClass = $isActive
                        ? 'bg-primary-50 text-primary-600 border-primary-200 shadow-sm dark:bg-primary-500/10 dark:text-primary-400 dark:border-primary-500/30'
                        : 'text-surface-600 border-transparent hover:bg-surface-100 hover:text-surface-800 dark:text-surface-400 dark:hover:bg-white/[0.04] dark:hover:text-surface-200';
                ?>
                <li>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="<?= $activeClass ?> group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium border transition-all duration-200">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 <?= $isActive ? 'text-primary-600 dark:text-primary-400' : 'text-surface-400 group-hover:text-surface-600 dark:group-hover:text-surface-300' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?= $icons[$item['icon']] ?? '' ?>
                        </svg>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                        <?php if ($isActive): ?>
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-500 dark:bg-primary-400"></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- My Account Section -->
            <p class="mb-3 ml-3 mt-6 text-[11px] font-semibold uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">My Account</p>
            <ul class="flex flex-col gap-0.5">
                <?php foreach ($myAccountItems as $item): ?>
                <?php
                    $isActive = str_starts_with($currentPath, $item['match']);
                    $activeClass = $isActive
                        ? 'bg-primary-50 text-primary-600 border-primary-200 shadow-sm dark:bg-primary-500/10 dark:text-primary-400 dark:border-primary-500/30'
                        : 'text-surface-600 border-transparent hover:bg-surface-100 hover:text-surface-800 dark:text-surface-400 dark:hover:bg-white/[0.04] dark:hover:text-surface-200';
                ?>
                <li>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="<?= $activeClass ?> group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium border transition-all duration-200">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 <?= $isActive ? 'text-primary-600 dark:text-primary-400' : 'text-surface-400 group-hover:text-surface-600 dark:group-hover:text-surface-300' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?= $icons[$item['icon']] ?? '' ?>
                        </svg>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                        <?php if ($isActive): ?>
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-500 dark:bg-primary-400"></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="border-t border-surface-200 dark:border-surface-800/50 px-4 py-4" x-data="sidebarFooter()" x-init="init()">
        <div class="flex items-center gap-3 rounded-xl bg-surface-100 dark:bg-white/[0.04] px-3 py-3">
            <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-bold text-xs shadow-sm flex-shrink-0" x-text="orgInitials">
                <?= htmlspecialchars(strtoupper(substr($org['name'] ?? 'K', 0, 2))) ?>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-surface-800 dark:text-white truncate" x-text="orgName"><?= htmlspecialchars($org['name'] ?? 'Organization') ?></p>
                <p class="text-[11px] text-surface-500 truncate flex items-center gap-1">
                    <span class="inline-block w-1.5 h-1.5 rounded-full" :class="planColor"></span>
                    <span x-text="planLabel">Free Plan</span>
                </p>
            </div>
        </div>
    </div>
</aside>

<script>
function sidebarFooter() {
    return {
        orgName: '<?= htmlspecialchars($org['name'] ?? 'Organization', ENT_QUOTES) ?>',
        orgInitials: '<?= htmlspecialchars(strtoupper(substr($org['name'] ?? 'K', 0, 2)), ENT_QUOTES) ?>',
        planLabel: 'Free Plan',
        planColor: 'bg-surface-400',
        async init() {
            try {
                const res = await authFetch(APP_BASE + '/api/subscriptions/current');
                if (res.ok) {
                    const json = await res.json();
                    const sub = json.data;
                    if (sub && sub.plan) {
                        this.planLabel = sub.plan.name + ' Plan';
                        if (sub.plan.slug === 'professional' || sub.plan.slug === 'pro') {
                            this.planColor = 'bg-primary-500';
                        } else if (sub.plan.slug === 'enterprise') {
                            this.planColor = 'bg-amber-500';
                        } else {
                            this.planColor = 'bg-surface-400';
                        }
                    }
                }
                // Also load org name (reuse cached getMe promise)
                const orgJson = await (typeof getMe === 'function' ? getMe() : authFetch(APP_BASE + '/api/auth/me').then(r => r.json()));
                const user = orgJson.data || orgJson;
                    if (user.organization && user.organization.name) {
                        this.orgName = user.organization.name;
                        this.orgInitials = user.organization.name.substring(0, 2).toUpperCase();
                    }
            } catch (e) { /* silent */ }
        }
    };
}
</script>
