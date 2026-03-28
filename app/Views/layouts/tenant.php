<?php
/**
 * Tenant Public Layout — K2 Navy/Gold Premium Theme
 * ---
 * Modern, SEO-friendly, mobile-first layout for organization public websites.
 * Supports dynamic branding: logo, primary color, fonts, org name.
 * Consistent with the K2 Platform marketing site's navy/gold design system.
 */

$orgName     = htmlspecialchars($org['name'] ?? 'Sports Club');
$primaryColor = $branding['primary_color'] ?? '#d4af37';
$accentColor  = $branding['accent_color'] ?? '#4a7ec4';
$logoUrl      = $branding['logo_url'] ?? null;
$tagline      = htmlspecialchars($branding['tagline'] ?? 'Book. Play. Compete.');
$heroImage    = $branding['hero_image'] ?? null;

if (!function_exists('hexToHSL')) {
    function hexToHSL(string $hex): array {
        $hex = ltrim($hex, '#');
        if (strlen($hex) < 6) $hex = str_pad($hex, 6, '0');
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        if ($max === $min) { $h = $s = 0; }
        else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            $h = match ($max) {
                $r => (($g - $b) / $d + ($g < $b ? 6 : 0)) / 6,
                $g => (($b - $r) / $d + 2) / 6,
                default => (($r - $g) / $d + 4) / 6,
            };
        }
        return [round($h * 360), round($s * 100), round($l * 100)];
    }
}
[$ph, $ps, $pl] = hexToHSL($primaryColor);

$facilities = $org['facilities'] ?? [];
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $orgName) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? "{$orgName} — {$tagline}") ?>">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? $orgName) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription ?? "{$orgName} — {$tagline}") ?>">
    <meta property="og:type" content="website">
    <?php if ($logoUrl): ?><meta property="og:image" content="<?= htmlspecialchars($logoUrl) ?>"><?php endif; ?>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SportsActivityLocation",
        "name": <?= json_encode($org['name'] ?? '') ?>,
        "url": <?= json_encode(($_SERVER['REQUEST_SCHEME'] ?? 'https') . '://' . ($_SERVER['HTTP_HOST'] ?? '')) ?>,
        "telephone": <?= json_encode($org['phone'] ?? '') ?>,
        "email": <?= json_encode($org['email'] ?? '') ?>
    }
    </script>

    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>/assets/vendor/css/fonts.css">
    <script src="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>/assets/vendor/js/tailwindcss.js"></script>
    <script defer src="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>/assets/vendor/js/alpine.min.js"></script>

    <script>
        window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>';
        window.ORG = <?= json_encode([
            'id'         => $org['id'] ?? 0,
            'name'       => $org['name'] ?? '',
            'slug'       => $org['slug'] ?? '',
            'timezone'   => $org['timezone'] ?? 'America/New_York',
            'currency'   => $org['currency'] ?? 'USD',
            'facilities' => array_map(fn($f) => ['id' => $f['id'], 'name' => $f['name'], 'slug' => $f['slug'], 'city' => $f['city'] ?? '', 'state' => $f['state'] ?? ''], $facilities),
        ]) ?>;

        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  'hsl(<?= $ph ?>, <?= $ps ?>%, 97%)',
                            100: 'hsl(<?= $ph ?>, <?= $ps ?>%, 93%)',
                            200: 'hsl(<?= $ph ?>, <?= $ps ?>%, 85%)',
                            300: 'hsl(<?= $ph ?>, <?= $ps ?>%, 72%)',
                            400: 'hsl(<?= $ph ?>, <?= $ps ?>%, 60%)',
                            500: 'hsl(<?= $ph ?>, <?= $ps ?>%, <?= $pl ?>%)',
                            600: 'hsl(<?= $ph ?>, <?= $ps ?>%, <?= max(15, $pl - 8) ?>%)',
                            700: 'hsl(<?= $ph ?>, <?= $ps ?>%, <?= max(10, $pl - 15) ?>%)',
                            800: 'hsl(<?= $ph ?>, <?= $ps ?>%, <?= max(8, $pl - 22) ?>%)',
                            900: 'hsl(<?= $ph ?>, <?= $ps ?>%, <?= max(5, $pl - 30) ?>%)',
                        },
                        navy: {
                            950:'#060d1a', 900:'#0b1629', 850:'#101f36', 800:'#162844',
                            700:'#1e3658', 600:'#27466e', 500:'#3160a0', 400:'#4a7ec4',
                        },
                        gold: {
                            300:'#f0d878', 400:'#e8c84e', 500:'#d4af37', 600:'#b8952d',
                            700:'#9c7c24', 800:'#7d6420',
                        },
                        surface: {
                            50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',
                            500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617',
                        },
                    },
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
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
        .gradient-text { background: linear-gradient(135deg, <?= htmlspecialchars($primaryColor) ?>, #d4af37); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-glow { background: radial-gradient(ellipse 80% 50% at 50% 0%, rgba(212,175,55,0.08) 0%, transparent 70%); }
        .section-glow { background: radial-gradient(ellipse 60% 40% at 50% 50%, rgba(212,175,55,0.04) 0%, transparent 70%); }
        .glass { background: rgba(11,22,41,0.6); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .glass-card { background: rgba(22,40,68,0.3); backdrop-filter: blur(12px); border: 1px solid rgba(212,175,55,0.08); }
        .glass-card-hover { transition: all 0.4s cubic-bezier(0.25,0.46,0.45,0.94); }
        .glass-card-hover:hover { transform: translateY(-6px); box-shadow: 0 25px 60px rgba(0,0,0,0.4), 0 0 30px rgba(212,175,55,0.06); border-color: rgba(212,175,55,0.2); }
        .card-hover { transition: all 0.4s cubic-bezier(0.25,0.46,0.45,0.94); }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 25px 60px rgba(0,0,0,0.4), 0 0 30px rgba(212,175,55,0.06); border-color: rgba(212,175,55,0.2); }
        .gold-border { border: 1px solid rgba(212,175,55,0.15); }
        .gold-border-hover:hover { border-color: rgba(212,175,55,0.35); }
        .grid-bg { background-image: linear-gradient(rgba(212,175,55,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(212,175,55,0.03) 1px, transparent 1px); background-size: 60px 60px; }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
        .animate-fade-in-up { animation: fadeInUp 0.7s cubic-bezier(0.25,0.46,0.45,0.94) forwards; opacity:0; }
        @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        .animate-shimmer { background: linear-gradient(90deg, transparent 0%, rgba(212,175,55,0.06) 50%, transparent 100%); background-size: 200% 100%; animation: shimmer 3s ease-in-out infinite; }
        @keyframes pulse-gold { 0%,100%{box-shadow:0 0 0 0 rgba(212,175,55,0.3)} 50%{box-shadow:0 0 0 8px rgba(212,175,55,0)} }
        .animate-pulse-gold { animation: pulse-gold 2s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .animate-float { animation: float 6s ease-in-out infinite; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #060d1a; }
        ::-webkit-scrollbar-thumb { background: #1e3658; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #27466e; }
    </style>
</head>
<body class="font-sans bg-navy-950 text-white antialiased" x-data="tenantApp()" x-init="init()">

    <!-- ═══ HEADER / GLASS NAVIGATION ═══ -->
    <header x-data="{ mobileOpen: false }"
            :class="scrolled ? 'glass border-b border-gold-500/10 shadow-lg shadow-navy-950/50' : 'bg-transparent'"
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group flex-shrink-0">
                    <?php if ($logoUrl): ?>
                        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= $orgName ?>" class="h-10 w-auto">
                    <?php else: ?>
                        <div class="h-10 w-10 rounded-xl bg-navy-800 border border-gold-500/20 flex items-center justify-center group-hover:border-gold-500/40 transition-all">
                            <span class="text-gold-500 font-bold text-lg"><?= mb_strtoupper(mb_substr($org['name'] ?? 'S', 0, 1)) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="font-display">
                        <span class="text-xl font-extrabold tracking-tight text-white"><?= $orgName ?></span>
                    </div>
                </a>

                <!-- Desktop Nav (dynamic from API) -->
                <nav class="hidden lg:flex items-center gap-1" x-show="navItems.length > 0" x-cloak>
                    <template x-for="item in navItems" :key="item.id">
                        <div class="relative" x-data="{ dropOpen: false }">
                            <!-- Dropdown parent -->
                            <template x-if="item.type === 'dropdown' && item.children && item.children.length > 0">
                                <div>
                                    <button @click="dropOpen = !dropOpen" @click.outside="dropOpen = false"
                                            class="flex items-center gap-1 px-3 py-2 rounded-lg text-[13px] font-medium tracking-wide uppercase transition-all duration-300"
                                            :class="currentPath.startsWith(item.url) ? 'text-gold-500 bg-gold-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5'">
                                        <span x-text="item.label"></span>
                                        <svg class="w-3.5 h-3.5 transition-transform" :class="dropOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="dropOpen" x-cloak x-transition
                                         class="absolute left-0 top-full mt-2 w-56 bg-navy-900 border border-navy-700 rounded-xl shadow-2xl shadow-navy-950/70 py-2 z-50">
                                        <a :href="item.url" class="block px-4 py-2.5 text-sm text-slate-300 hover:bg-navy-800 hover:text-white transition-colors font-medium">
                                            All Schedule
                                        </a>
                                        <div class="border-t border-navy-800 my-1"></div>
                                        <template x-for="child in item.children" :key="child.id">
                                            <a :href="child.url" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-300 hover:bg-navy-800 hover:text-white transition-colors">
                                                <span x-show="child.color" class="w-2 h-2 rounded-full flex-shrink-0" :style="'background:' + child.color"></span>
                                                <span x-text="child.label"></span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <!-- Regular link -->
                            <template x-if="item.type !== 'dropdown' || !item.children || item.children.length === 0">
                                <a :href="item.url" :target="item.target || '_self'"
                                   class="px-3 py-2 rounded-lg text-[13px] font-medium tracking-wide uppercase transition-all duration-300"
                                   :class="currentPath === item.url || (item.url !== '/' && currentPath.startsWith(item.url)) ? 'text-gold-500 bg-gold-500/10' : 'text-slate-400 hover:text-white hover:bg-white/5'"
                                   x-text="item.label"></a>
                            </template>
                        </div>
                    </template>
                </nav>

                <!-- Location Switcher + Auth CTA -->
                <div class="hidden lg:flex items-center gap-3">
                    <!-- Location Switcher -->
                    <?php if (count($facilities) > 1): ?>
                    <div class="relative" x-data="{ locOpen: false }">
                        <button @click="locOpen = !locOpen" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 border border-navy-700 hover:border-gold-500/30 transition-all">
                            <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-text="selectedFacility ? selectedFacility.name : 'All Locations'" class="max-w-[140px] truncate"></span>
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="locOpen" @click.outside="locOpen = false" x-cloak x-transition
                             class="absolute right-0 top-full mt-2 w-64 bg-navy-900 border border-navy-700 rounded-xl shadow-2xl shadow-navy-950/70 py-2 z-50">
                            <button @click="selectFacility(null); locOpen = false"
                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-navy-800 transition-colors"
                                    :class="!selectedFacility ? 'text-gold-500 font-semibold bg-gold-500/10' : 'text-slate-300'">
                                All Locations
                            </button>
                            <?php foreach ($facilities as $f): ?>
                            <button @click="selectFacility(<?= htmlspecialchars(json_encode(['id' => $f['id'], 'name' => $f['name'], 'slug' => $f['slug'], 'city' => $f['city'] ?? '', 'state' => $f['state'] ?? ''])) ?>); locOpen = false"
                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-navy-800 transition-colors"
                                    :class="selectedFacility?.id === <?= (int)$f['id'] ?> ? 'text-gold-500 font-semibold bg-gold-500/10' : 'text-slate-300'">
                                <div class="font-medium"><?= htmlspecialchars($f['name']) ?></div>
                                <?php if (!empty($f['city'])): ?>
                                <div class="text-xs text-slate-500"><?= htmlspecialchars($f['city'] . ($f['state'] ? ', ' . $f['state'] : '')) ?></div>
                                <?php endif; ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Auth -->
                    <template x-if="!player">
                        <div class="flex items-center gap-3">
                            <a href="/login" class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-gold-400 transition-colors">Sign In</a>
                            <a href="/register" class="group/btn relative px-5 py-2.5 text-sm font-semibold text-navy-950 gradient-gold-bg rounded-lg shadow-gold hover:shadow-gold-lg transition-all duration-300">
                                Join Now
                            </a>
                        </div>
                    </template>
                    <template x-if="player">
                        <div class="relative" x-data="{ userOpen: false }">
                            <button @click="userOpen = !userOpen" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/5 transition-colors">
                                <div class="w-8 h-8 rounded-full gradient-gold-bg flex items-center justify-center text-navy-950 text-sm font-bold" x-text="player.first_name?.charAt(0)?.toUpperCase() || '?'"></div>
                                <span class="text-sm font-medium text-slate-300" x-text="player.first_name"></span>
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="userOpen" @click.outside="userOpen = false" x-cloak x-transition
                                 class="absolute right-0 top-full mt-2 w-56 bg-navy-900 border border-navy-700 rounded-xl shadow-2xl shadow-navy-950/70 py-2 z-50">
                                <div class="px-4 py-2 border-b border-navy-700">
                                    <div class="text-sm font-semibold text-white" x-text="player.first_name + ' ' + (player.last_name || '')"></div>
                                    <div class="text-xs text-slate-500" x-text="player.email"></div>
                                </div>
                                <a href="/dashboard" class="block w-full text-left px-4 py-2.5 text-sm text-slate-300 hover:bg-navy-800 hover:text-white transition-colors">Dashboard</a>
                                <a href="/dashboard/bookings" class="block w-full text-left px-4 py-2.5 text-sm text-slate-300 hover:bg-navy-800 hover:text-white transition-colors">My Bookings</a>
                                <a href="/dashboard/profile" class="block w-full text-left px-4 py-2.5 text-sm text-slate-300 hover:bg-navy-800 hover:text-white transition-colors">Profile</a>
                                <div class="border-t border-navy-700 mt-1 pt-1">
                                    <button @click="logout()" class="w-full text-left px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">Sign Out</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                 class="lg:hidden pb-4 border-t border-gold-500/10">
                <div class="pt-4 space-y-1">
                    <template x-for="item in navItems" :key="item.id">
                        <div>
                            <a :href="item.url"
                               class="block px-4 py-2.5 rounded-lg text-sm font-medium"
                               :class="currentPath === item.url || (item.url !== '/' && currentPath.startsWith(item.url)) ? 'text-gold-500 bg-gold-500/10' : 'text-slate-300 hover:text-white hover:bg-white/5'"
                               x-text="item.label"></a>
                            <!-- Mobile dropdown children -->
                            <template x-if="item.children && item.children.length > 0">
                                <div class="pl-6 space-y-1">
                                    <template x-for="child in item.children" :key="child.id">
                                        <a :href="child.url"
                                           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-white/5">
                                            <span x-show="child.color" class="w-2 h-2 rounded-full flex-shrink-0" :style="'background:' + child.color"></span>
                                            <span x-text="child.label"></span>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                    <?php if (count($facilities) > 1): ?>
                    <div class="px-4 pt-3 mt-2 border-t border-navy-800">
                        <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Location</p>
                        <select @change="selectFacility($event.target.value ? JSON.parse($event.target.value) : null)"
                                class="w-full px-3 py-2 bg-navy-800 border border-navy-700 text-slate-200 rounded-lg text-sm">
                            <option value="">All Locations</option>
                            <?php foreach ($facilities as $f): ?>
                            <option value='<?= htmlspecialchars(json_encode(['id' => $f['id'], 'name' => $f['name'], 'slug' => $f['slug'], 'city' => $f['city'] ?? '', 'state' => $f['state'] ?? ''])) ?>'><?= htmlspecialchars($f['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="pt-3 mt-3 border-t border-navy-800 space-y-2 px-4">
                        <a href="/login" class="block w-full text-center py-2.5 text-sm font-medium text-slate-300 hover:text-white rounded-lg border border-navy-700 hover:border-gold-500/30 transition-colors">Sign In</a>
                        <a href="/register" class="block w-full text-center py-2.5 text-sm font-semibold text-navy-950 gradient-gold-bg rounded-lg transition-colors">Join Now</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Spacer for fixed header -->
    <div class="h-16 lg:h-20"></div>

    <!-- ═══ LOCATION BANNER ═══ -->
    <div x-show="selectedFacility" x-cloak class="bg-gold-500/10 border-b border-gold-500/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                <span class="font-semibold text-gold-400" x-text="selectedFacility?.name"></span>
                <span class="text-slate-400" x-text="selectedFacility?.city ? '· ' + selectedFacility.city + (selectedFacility.state ? ', ' + selectedFacility.state : '') : ''"></span>
            </div>
            <button @click="selectFacility(null)" class="text-xs text-gold-500/70 hover:text-gold-400 font-medium transition-colors">Clear</button>
        </div>
    </div>

    <!-- ═══ MAIN CONTENT ═══ -->
    <main>
        <?php
        $contentPath = dirname(__DIR__) . '/' . $contentView;
        if (file_exists($contentPath)) {
            include $contentPath;
        }
        ?>
    </main>

    <!-- ═══ FOOTER ═══ -->
    <footer class="relative border-t border-gold-500/10 bg-navy-950">
        <div class="absolute inset-0 grid-bg opacity-30"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 lg:gap-12">
                <!-- Brand -->
                <div class="col-span-2 md:col-span-4 lg:col-span-2">
                    <a href="/" class="flex items-center gap-3 mb-5">
                        <?php if ($logoUrl): ?>
                            <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= $orgName ?>" class="h-10 w-auto brightness-150">
                        <?php else: ?>
                            <div class="h-9 w-9 rounded-xl bg-navy-800 border border-gold-500/20 flex items-center justify-center">
                                <span class="text-gold-500 font-bold text-lg"><?= mb_strtoupper(mb_substr($org['name'] ?? 'S', 0, 1)) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="font-display">
                            <span class="text-lg font-extrabold text-white"><?= $orgName ?></span>
                        </div>
                    </a>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-sm"><?= htmlspecialchars($tagline) ?></p>
                </div>

                <!-- Explore Links (dynamic) -->
                <div>
                    <h4 class="text-xs font-semibold text-gold-500 uppercase tracking-wider mb-4">Explore</h4>
                    <ul class="space-y-3" id="footer-explore-links">
                        <li><a href="/schedule" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Schedule</a></li>
                        <li><a href="/facilities" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Locations</a></li>
                        <li><a href="/about" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">About Us</a></li>
                        <li><a href="/contact" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Account Links -->
                <div>
                    <h4 class="text-xs font-semibold text-gold-500 uppercase tracking-wider mb-4">Account</h4>
                    <ul class="space-y-3">
                        <li><a href="/login" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Sign In</a></li>
                        <li><a href="/register" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Create Account</a></li>
                        <li><a href="/dashboard" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Dashboard</a></li>
                        <li><a href="/dashboard/bookings" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">My Bookings</a></li>
                        <li><a href="/contact" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Locations -->
                <?php if (!empty($facilities)): ?>
                <div>
                    <h4 class="text-xs font-semibold text-gold-500 uppercase tracking-wider mb-4">Locations</h4>
                    <ul class="space-y-3">
                        <?php foreach (array_slice($facilities, 0, 6) as $f): ?>
                        <li>
                            <a href="/facilities/<?= htmlspecialchars($f['slug']) ?>" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">
                                <?= htmlspecialchars($f['name']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <div class="mt-12 pt-8 border-t border-navy-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-600">&copy; <?= date('Y') ?> <?= $orgName ?>. All rights reserved.</p>
                <p class="text-xs text-slate-600">Powered by <a href="/" class="text-gold-500/70 hover:text-gold-400 font-medium transition-colors">K2Pickleball</a></p>
            </div>
        </div>
    </footer>

    <!-- ═══ TOAST NOTIFICATIONS ═══ -->
    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 z-[200] max-w-sm glass-card rounded-xl shadow-2xl p-4 flex items-start gap-3">
        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
             :class="toast.type === 'success' ? 'bg-emerald-500/20 text-emerald-400' : toast.type === 'error' ? 'bg-red-500/20 text-red-400' : 'bg-blue-500/20 text-blue-400'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="toast.type==='success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                <path x-show="toast.type==='error'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                <path x-show="toast.type==='info'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-white" x-text="toast.title"></p>
            <p x-show="toast.message" class="text-xs text-slate-400 mt-0.5" x-text="toast.message"></p>
        </div>
        <button @click="toast.show = false" class="text-slate-500 hover:text-slate-300 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- ═══ CORE JS ═══ -->
    <script>
    const baseApi = window.APP_BASE || '';
    window.baseApi = baseApi; // Expose globally for Alpine components in view files

    async function authFetch(url, opts = {}) {
        const token = localStorage.getItem('player_token');
        if (token) {
            opts.headers = { ...opts.headers, 'Authorization': 'Bearer ' + token };
        }
        return fetch(url, opts);
    }

    function tenantApp() {
        return {
            scrolled: false,
            player: null,
            selectedFacility: null,
            toast: { show: false, type: 'info', title: '', message: '' },
            navItems: [],
            hasMemberships: false,
            currentPath: (window.location.pathname.replace(/\/+$/, '') || '/'),

            init() {
                window.addEventListener('scroll', () => { this.scrolled = window.scrollY > 20; });
                const saved = localStorage.getItem('selected_facility');
                if (saved) {
                    try { this.selectedFacility = JSON.parse(saved); } catch(e) {}
                }
                const token = localStorage.getItem('player_token');
                if (token) this.loadPlayer();
                this.loadNavigation();
            },

            async loadNavigation() {
                try {
                    const res = await fetch(baseApi + '/api/public/navigation');
                    const json = await res.json();
                    if (json.data) {
                        this.navItems = json.data.items || [];
                        this.hasMemberships = json.data.has_memberships || false;
                    }
                } catch(e) {
                    // Fallback: use static nav if API fails
                    this.navItems = [
                        { id: 'f-home', label: 'Home', url: '/', type: 'link' },
                        { id: 'f-schedule', label: 'Schedule', url: '/schedule', type: 'link' },
                        { id: 'f-about', label: 'About', url: '/about', type: 'link' },
                        { id: 'f-contact', label: 'Contact', url: '/contact', type: 'link' },
                    ];
                }
            },

            selectFacility(facility) {
                this.selectedFacility = facility;
                if (facility) {
                    localStorage.setItem('selected_facility', JSON.stringify(facility));
                } else {
                    localStorage.removeItem('selected_facility');
                }
                window.dispatchEvent(new CustomEvent('facility-changed', { detail: facility }));
            },

            async loadPlayer() {
                try {
                    const res = await authFetch(baseApi + '/api/auth/me');
                    if (res.ok) {
                        const json = await res.json();
                        this.player = json.data || json.user || json;
                    } else {
                        localStorage.removeItem('player_token');
                        localStorage.removeItem('player_refresh');
                    }
                } catch(e) {}
            },

            logout() {
                const refresh = localStorage.getItem('player_refresh');
                if (refresh) {
                    authFetch(baseApi + '/api/auth/logout', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ refresh_token: refresh })
                    }).catch(() => {});
                }
                localStorage.removeItem('player_token');
                localStorage.removeItem('player_refresh');
                this.player = null;
                window.location.href = '/';
            },

            showToast(title, type = 'success', message = '') {
                this.toast = { show: true, type, title, message };
                setTimeout(() => { this.toast.show = false; }, 4000);
            },
        };
    }

    </script>

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
