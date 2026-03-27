<?php
$title = 'Theme & Branding';
$breadcrumbs = [
    ['label' => 'Design', 'url' => '#'],
    ['label' => 'Theme & Branding'],
];

ob_start();
?>
<div x-data="themeEditor()" x-init="load()">

    <!-- Page header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Theme & Branding</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Customize your public site's colors, fonts, logo, and layout style.</p>
        </div>
        <div class="flex items-center gap-3">
            <a :href="previewUrl" target="_blank"
               class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-semibold text-surface-700 dark:text-surface-200 hover:border-primary-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Preview Site
            </a>
            <button @click="save()" :disabled="saving"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-all disabled:opacity-50">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
            </button>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-20 text-center">
        <svg class="animate-spin w-6 h-6 text-primary-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
        <p class="mt-3 text-sm text-surface-500">Loading theme settings...</p>
    </div>

    <div x-show="!loading" class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Left column: settings panels -->
        <div class="xl:col-span-2 space-y-6">

            <!-- Branding -->
            <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="font-semibold text-surface-900 dark:text-white">Branding</h2>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">Logo, tagline, and hero image for your public site.</p>
                </div>
                <div class="px-6 py-5 space-y-5">

                    <!-- Logo URL -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Logo URL</label>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                <img x-show="branding.logo_url" :src="branding.logo_url" class="w-10 h-10 object-contain" @error="branding.logo_url = ''">
                                <svg x-show="!branding.logo_url" class="w-6 h-6 text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            </div>
                            <input x-model="branding.logo_url" type="url" placeholder="https://example.com/logo.png"
                                   class="flex-1 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-900 dark:text-white placeholder:text-surface-400 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Tagline -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Tagline</label>
                        <input x-model="branding.tagline" type="text" placeholder="Book. Play. Compete."
                               class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-900 dark:text-white placeholder:text-surface-400 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all">
                        <p class="mt-1 text-xs text-surface-400">Shown as the subtitle on your home page hero.</p>
                    </div>

                    <!-- Hero Image URL -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Hero Image URL</label>
                        <div class="space-y-2">
                            <input x-model="branding.hero_image" type="url" placeholder="https://example.com/hero.jpg"
                                   class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-900 dark:text-white placeholder:text-surface-400 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all">
                            <div x-show="branding.hero_image" class="rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700 h-28 bg-surface-100 dark:bg-surface-800">
                                <img :src="branding.hero_image" class="w-full h-full object-cover" @error="branding.hero_image = ''">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Colors -->
            <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="font-semibold text-surface-900 dark:text-white">Colors</h2>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">Pick your brand colors. Changes show in the live preview.</p>
                </div>
                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <!-- Primary Color -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Primary Color</label>
                        <div class="flex items-center gap-3">
                            <div class="relative w-10 h-10 rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700 cursor-pointer flex-shrink-0 shadow-sm" :style="'background:' + theme.primary_color">
                                <input type="color" x-model="theme.primary_color" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                            </div>
                            <input x-model="theme.primary_color" type="text" maxlength="7" placeholder="#d4af37"
                                   class="flex-1 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm font-mono text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all">
                        </div>
                        <p class="mt-1.5 text-xs text-surface-400">Buttons, links, and accents</p>
                    </div>

                    <!-- Accent Color -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Accent Color</label>
                        <div class="flex items-center gap-3">
                            <div class="relative w-10 h-10 rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700 cursor-pointer flex-shrink-0 shadow-sm" :style="'background:' + theme.accent_color">
                                <input type="color" x-model="theme.accent_color" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                            </div>
                            <input x-model="theme.accent_color" type="text" maxlength="7" placeholder="#4a7ec4"
                                   class="flex-1 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm font-mono text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all">
                        </div>
                        <p class="mt-1.5 text-xs text-surface-400">Secondary highlights and tags</p>
                    </div>

                    <!-- Background Color -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Background Color</label>
                        <div class="flex items-center gap-3">
                            <div class="relative w-10 h-10 rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700 cursor-pointer flex-shrink-0 shadow-sm" :style="'background:' + theme.background_color">
                                <input type="color" x-model="theme.background_color" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                            </div>
                            <input x-model="theme.background_color" type="text" maxlength="7" placeholder="#060d1a"
                                   class="flex-1 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm font-mono text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all">
                        </div>
                        <p class="mt-1.5 text-xs text-surface-400">Site background / dark base</p>
                    </div>

                    <!-- Text Color -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Text Color</label>
                        <div class="flex items-center gap-3">
                            <div class="relative w-10 h-10 rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700 cursor-pointer flex-shrink-0 shadow-sm" :style="'background:' + theme.text_color">
                                <input type="color" x-model="theme.text_color" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                            </div>
                            <input x-model="theme.text_color" type="text" maxlength="7" placeholder="#f8fafc"
                                   class="flex-1 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm font-mono text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all">
                        </div>
                        <p class="mt-1.5 text-xs text-surface-400">Primary text on dark background</p>
                    </div>

                </div>
            </div>

            <!-- Fonts -->
            <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="font-semibold text-surface-900 dark:text-white">Typography</h2>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">Google Fonts are supported — enter the exact font name.</p>
                </div>
                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Display / Heading Font</label>
                        <input x-model="theme.font_display" type="text" placeholder="Plus Jakarta Sans"
                               class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-900 dark:text-white placeholder:text-surface-400 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Body Font</label>
                        <input x-model="theme.font_body" type="text" placeholder="Inter"
                               class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-900 dark:text-white placeholder:text-surface-400 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Layout Style -->
            <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="font-semibold text-surface-900 dark:text-white">Layout Style</h2>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">Visual style for cards, navigation bar, and footer.</p>
                </div>
                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Navigation Style</label>
                        <select x-model="theme.nav_style"
                                class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-700 dark:text-surface-300 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none">
                            <option value="glass">Glass (blur)</option>
                            <option value="solid">Solid</option>
                            <option value="minimal">Minimal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Card Style</label>
                        <select x-model="theme.card_style"
                                class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-700 dark:text-surface-300 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none">
                            <option value="glass">Glass</option>
                            <option value="bordered">Bordered</option>
                            <option value="elevated">Elevated</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Footer Style</label>
                        <select x-model="theme.footer_style"
                                class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-700 dark:text-surface-300 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 outline-none">
                            <option value="standard">Standard</option>
                            <option value="minimal">Minimal</option>
                            <option value="expanded">Expanded</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 pb-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <button @click="theme.hero_overlay = !theme.hero_overlay" type="button"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full transition-colors border-2 border-transparent"
                                :class="theme.hero_overlay ? 'bg-primary-500' : 'bg-surface-300 dark:bg-surface-600'">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition-transform"
                                  :class="theme.hero_overlay ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                        <div>
                            <p class="text-sm font-medium text-surface-700 dark:text-surface-300">Hero Overlay</p>
                            <p class="text-xs text-surface-400">Darken the hero image for better text contrast</p>
                        </div>
                    </label>
                </div>
            </div>

        </div>

        <!-- Right column: live preview -->
        <div class="xl:col-span-1">
            <div class="sticky top-6 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="px-5 py-3.5 border-b border-surface-100 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-300">Live Preview</h3>
                </div>

                <!-- Mock site preview -->
                <div class="p-4">
                    <div class="rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700" :style="'background:' + theme.background_color">

                        <!-- Mock nav -->
                        <div class="px-3 py-2 flex items-center justify-between border-b gap-2"
                             :class="theme.nav_style === 'glass' ? 'bg-white/5 backdrop-blur-md border-white/10' : (theme.nav_style === 'solid' ? 'border-white/10' : 'border-transparent')"
                             :style="theme.nav_style === 'solid' ? 'background:' + theme.background_color : ''">
                            <div class="flex items-center gap-2">
                                <img x-show="branding.logo_url" :src="branding.logo_url" class="h-6 w-auto object-contain" @error="branding.logo_url = ''">
                                <span x-show="!branding.logo_url" class="text-xs font-bold" :style="'color:' + theme.primary_color">Your Logo</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs rounded-full px-2 py-0.5 font-medium" :style="'background:' + theme.primary_color + '22; color:' + theme.primary_color">Book Now</span>
                            </div>
                        </div>

                        <!-- Mock hero -->
                        <div class="relative px-4 py-8 text-center overflow-hidden"
                             :style="branding.hero_image ? 'background-image:url(' + branding.hero_image + ');background-size:cover;background-position:center' : 'background:' + theme.background_color">
                            <div x-show="branding.hero_image && theme.hero_overlay" class="absolute inset-0 bg-black/50"></div>
                            <div class="relative">
                                <p class="text-xs font-bold mb-1" :style="'color:' + theme.primary_color">PICKLEBALL</p>
                                <h3 class="text-sm font-bold leading-snug" :style="'color:' + theme.text_color; 'font-family:' + theme.font_display">
                                    Welcome to K2 Pickleball
                                </h3>
                                <p class="text-xs mt-1 opacity-70" :style="'color:' + theme.text_color" x-text="branding.tagline || 'Book. Play. Compete.'"></p>
                                <button class="mt-3 text-xs px-3 py-1.5 rounded-full font-semibold" :style="'background:' + theme.primary_color + '; color: #fff'">
                                    View Schedule
                                </button>
                            </div>
                        </div>

                        <!-- Mock card row -->
                        <div class="px-3 pb-3 grid grid-cols-2 gap-2 mt-2">
                            <template x-for="n in [1,2]">
                                <div class="rounded-lg p-3 text-left"
                                     :class="theme.card_style === 'glass' ? 'bg-white/5 border border-white/10' : (theme.card_style === 'bordered' ? 'border border-white/20' : 'shadow-lg bg-white/8')"
                                     :style="theme.card_style === 'bordered' ? 'border-color:' + theme.primary_color + '33' : ''">
                                    <div class="w-4 h-4 rounded-full mb-1.5" :style="'background:' + theme.primary_color + '33'"></div>
                                    <p class="text-xs font-semibold" :style="'color:' + theme.text_color">Open Play</p>
                                    <p class="text-xs opacity-50 mt-0.5" :style="'color:' + theme.text_color">Mon 9am</p>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

                <!-- Color swatches -->
                <div class="px-5 pb-5 flex items-center gap-2 flex-wrap">
                    <template x-for="(color, key) in colorKeys">
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-md border border-surface-200 dark:border-surface-700 shadow-sm flex-shrink-0"
                                 :style="'background:' + (key === 'primary' ? theme.primary_color : key === 'accent' ? theme.accent_color : key === 'bg' ? theme.background_color : theme.text_color)"></div>
                            <span class="text-xs text-surface-400 capitalize" x-text="color"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border px-4 py-3 shadow-lg text-sm font-medium"
         :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/30 dark:text-emerald-400' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-400'">
        <svg x-show="toast.type === 'success'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <svg x-show="toast.type === 'error'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
function themeEditor() {
    return {
        loading: true,
        saving: false,
        previewUrl: '/',
        toast: { show: false, type: 'success', message: '' },
        colorKeys: { primary: 'Primary', accent: 'Accent', bg: 'Background', text: 'Text' },
        theme: {
            primary_color:    '#d4af37',
            accent_color:     '#4a7ec4',
            background_color: '#060d1a',
            text_color:       '#f8fafc',
            font_display:     'Plus Jakarta Sans',
            font_body:        'Inter',
            nav_style:        'glass',
            footer_style:     'standard',
            hero_overlay:     true,
            card_style:       'glass',
        },
        branding: {
            logo_url:    '',
            tagline:     '',
            hero_image:  '',
        },

        async load() {
            this.loading = true;
            this.previewUrl = window.location.protocol + '//' + window.location.host.replace(/^admin\./, '');

            const token = localStorage.getItem('access_token') || '';
            const headers = { 'Authorization': 'Bearer ' + token };

            try {
                const [themeRes, brandingRes] = await Promise.all([
                    fetch('/api/public/theme'),
                    fetch('/api/settings/branding', { headers }),
                ]);
                if (themeRes.ok) {
                    const j = await themeRes.json();
                    if (j.data) this.theme = { ...this.theme, ...j.data };
                    if (typeof this.theme.hero_overlay === 'string') {
                        this.theme.hero_overlay = this.theme.hero_overlay === '1' || this.theme.hero_overlay === 'true';
                    }
                }
                if (brandingRes.ok) {
                    const j = await brandingRes.json();
                    if (j.data) this.branding = { ...this.branding, ...j.data };
                }
            } catch(e) {
                this.showToast('Could not load current settings', 'error');
            }
            this.loading = false;
        },

        async save() {
            this.saving = true;
            const token = localStorage.getItem('access_token') || '';
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token,
            };
            try {
                const [tRes, bRes] = await Promise.all([
                    fetch('/api/settings/theme', {
                        method: 'PUT', headers,
                        body: JSON.stringify(this.theme),
                    }),
                    fetch('/api/settings/branding', {
                        method: 'PUT', headers,
                        body: JSON.stringify(this.branding),
                    }),
                ]);
                if (tRes.ok && bRes.ok) {
                    this.showToast('Theme & branding saved', 'success');
                } else {
                    const j = await (tRes.ok ? bRes : tRes).json();
                    this.showToast(j.message || 'Save failed', 'error');
                }
            } catch(e) {
                this.showToast('Network error, please try again', 'error');
            }
            this.saving = false;
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, type, message };
            setTimeout(() => { this.toast.show = false; }, 4000);
        },
    };
}
</script>
<?php
$content = ob_get_clean();
include dirname(dirname(__DIR__)) . '/layouts/admin.php';
