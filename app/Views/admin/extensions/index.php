<?php
$title = 'Extensions';
$breadcrumbs = [['label' => 'Extensions']];

ob_start();
?>
<div x-data="extensionsManager()" x-init="loadCatalog()" class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-surface-900 dark:text-white">Extensions</h2>
            <p class="text-sm text-surface-500 mt-0.5">Browse and manage integrations for your organization</p>
        </div>
    </div>

    <!-- Success / Error Banner -->
    <div x-show="banner.show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         :class="banner.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/30 dark:text-emerald-300' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-300'"
         class="flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium" style="display:none;">
        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <template x-if="banner.type === 'success'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></template>
            <template x-if="banner.type !== 'success'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></template>
        </svg>
        <span x-text="banner.message"></span>
        <button @click="banner.show = false" class="ml-auto opacity-60 hover:opacity-100">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Loading skeleton -->
    <template x-if="loading">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="n in 3">
                <div class="rounded-2xl bg-white dark:bg-surface-800/60 border border-surface-200/60 dark:border-surface-700/50 p-6 animate-pulse">
                    <div class="flex items-start gap-4">
                        <div class="h-12 w-12 rounded-xl bg-surface-200 dark:bg-surface-700"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-surface-200 dark:bg-surface-700 rounded w-2/3"></div>
                            <div class="h-3 bg-surface-200 dark:bg-surface-700 rounded w-full"></div>
                            <div class="h-3 bg-surface-200 dark:bg-surface-700 rounded w-4/5"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <!-- Empty state -->
    <template x-if="!loading && catalog.length === 0">
        <div class="rounded-2xl bg-white dark:bg-surface-800/60 border border-surface-200/60 dark:border-surface-700/50 py-20 text-center">
            <svg class="mx-auto h-12 w-12 text-surface-300 dark:text-surface-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/>
            </svg>
            <p class="text-base font-semibold text-surface-700 dark:text-surface-300">No extensions available</p>
            <p class="text-sm text-surface-500 mt-1">Extensions will appear here once the platform activates them.</p>
        </div>
    </template>

    <!-- Extension Cards Grid -->
    <template x-if="!loading && catalog.length > 0">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="ext in catalog" :key="ext.slug">
                <div class="group rounded-2xl bg-white dark:bg-surface-800/60 border border-surface-200/60 dark:border-surface-700/50 shadow-soft hover:shadow-md hover:border-primary-200 dark:hover:border-primary-500/30 transition-all duration-200 flex flex-col">

                    <!-- Card body -->
                    <div class="p-6 flex-1">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0 h-12 w-12 rounded-xl flex items-center justify-center"
                                 :class="ext.installed ? 'bg-gradient-to-br from-primary-500 to-primary-700 shadow-lg shadow-primary-500/20' : 'bg-surface-100 dark:bg-surface-700'">
                                <svg class="h-6 w-6" :class="ext.installed ? 'text-white' : 'text-surface-400 dark:text-surface-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/>
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-semibold text-surface-900 dark:text-white leading-tight" x-text="ext.name"></h3>
                                    <span x-show="ext.installed"
                                          class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Installed
                                    </span>
                                </div>
                                <span class="inline-block mt-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 border border-surface-200 dark:border-surface-600 px-2 py-0.5 text-[10px] font-medium text-surface-500 capitalize" x-text="ext.category || 'general'"></span>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-surface-500 dark:text-surface-400 leading-relaxed line-clamp-3" x-text="ext.description || 'No description available.'"></p>
                    </div>

                    <!-- Card footer -->
                    <div class="border-t border-surface-100 dark:border-surface-700/50 px-6 py-4 flex items-center gap-2">
                        <!-- Install / Uninstall -->
                        <template x-if="!ext.installed">
                            <button @click="install(ext.slug)"
                                    :disabled="ext.busy"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-3.5 py-2 text-xs font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                                <svg x-show="!ext.busy" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <svg x-show="ext.busy" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span x-text="ext.busy ? 'Installing...' : 'Install'"></span>
                            </button>
                        </template>

                        <template x-if="ext.installed">
                            <div class="flex items-center gap-2 w-full">
                                <!-- Configure button -->
                                <button @click="openSettings(ext)"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-surface-100 dark:bg-surface-700 border border-surface-200 dark:border-surface-600 px-3.5 py-2 text-xs font-semibold text-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600 transition-all">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Configure
                                </button>

                                <!-- Uninstall -->
                                <button @click="uninstall(ext.slug)"
                                        :disabled="ext.busy"
                                        class="ml-auto inline-flex items-center gap-1.5 rounded-xl border border-red-200 dark:border-red-500/30 px-3.5 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                                    <svg x-show="!ext.busy" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <svg x-show="ext.busy" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <span x-text="ext.busy ? 'Removing...' : 'Uninstall'"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <!-- ===================== SETTINGS PANEL (slide-in) ===================== -->
    <div x-show="settingsPanel.open" class="fixed inset-0 z-[100] flex" @keydown.escape.window="settingsPanel.open = false" style="display:none;">
        <!-- Backdrop -->
        <div x-show="settingsPanel.open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="settingsPanel.open = false"
             class="fixed inset-0 bg-surface-950/60 backdrop-blur-sm"></div>

        <!-- Panel -->
        <div x-show="settingsPanel.open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="ml-auto relative w-full max-w-xl bg-white dark:bg-surface-900 h-full overflow-y-auto shadow-2xl flex flex-col">

            <!-- Panel header -->
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-surface-200 dark:border-surface-700/50 bg-white dark:bg-surface-900 px-6 py-4">
                <div>
                    <h3 class="text-base font-semibold text-surface-900 dark:text-white" x-text="settingsPanel.ext ? settingsPanel.ext.name + ' — Settings' : 'Settings'"></h3>
                    <p class="text-xs text-surface-500 mt-0.5">Configure this extension for your organization</p>
                </div>
                <button @click="settingsPanel.open = false" class="rounded-xl p-2 text-surface-400 hover:text-surface-600 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Loading settings -->
            <template x-if="settingsPanel.loading">
                <div class="flex items-center justify-center py-20 text-surface-500">
                    <svg class="animate-spin h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Loading settings...
                </div>
            </template>

            <template x-if="!settingsPanel.loading">
                <div class="flex-1 px-6 py-6 space-y-8">

                    <!-- Org-level settings section -->
                    <div x-show="settingsPanel.schema && settingsPanel.schema.length > 0">
                        <h4 class="text-sm font-semibold text-surface-700 dark:text-surface-300 mb-4 flex items-center gap-2">
                            <svg class="h-4 w-4 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Organization Settings
                        </h4>
                        <div class="rounded-2xl border border-surface-200/60 dark:border-surface-700/50 bg-surface-50/50 dark:bg-surface-800/40 p-5 space-y-4">
                            <template x-for="field in settingsPanel.schema" :key="field.key">
                                <div>
                                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5" x-text="field.label || field.key"></label>
                                    <p x-show="field.description" class="text-xs text-surface-500 mb-2" x-text="field.description"></p>

                                    <!-- Text / password / number -->
                                    <template x-if="field.type === 'text' || field.type === 'password' || field.type === 'number'">
                                        <input :type="field.type" x-model="settingsPanel.orgSettings[field.key]" :placeholder="field.placeholder || ''"
                                               class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 px-4 py-2.5 text-sm shadow-soft focus:border-primary-500 focus:ring-primary-500">
                                    </template>

                                    <!-- Select -->
                                    <template x-if="field.type === 'select'">
                                        <select x-model="settingsPanel.orgSettings[field.key]"
                                                class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 px-4 py-2.5 text-sm shadow-soft focus:border-primary-500 focus:ring-primary-500">
                                            <template x-for="opt in (field.options || [])" :key="opt.value">
                                                <option :value="opt.value" x-text="opt.label"></option>
                                            </template>
                                        </select>
                                    </template>

                                    <!-- Boolean toggle -->
                                    <template x-if="field.type === 'boolean'">
                                        <label class="inline-flex items-center gap-3 cursor-pointer">
                                            <input type="checkbox" x-model="settingsPanel.orgSettings[field.key]" class="sr-only peer">
                                            <div class="w-10 h-6 bg-surface-300 dark:bg-surface-600 peer-checked:bg-primary-500 rounded-full transition-colors relative after:absolute after:top-0.5 after:left-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-transform peer-checked:after:translate-x-4"></div>
                                            <span class="text-sm text-surface-600 dark:text-surface-400" x-text="settingsPanel.orgSettings[field.key] ? 'Enabled' : 'Disabled'"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            <div class="pt-2">
                                <button @click="saveOrgSettings()" :disabled="settingsPanel.saving"
                                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2.5 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all disabled:opacity-60">
                                    <svg x-show="settingsPanel.saving" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <span x-text="settingsPanel.saving ? 'Saving...' : 'Save Organization Settings'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Per-facility settings section -->
                    <div x-show="settingsPanel.facilities && settingsPanel.facilities.length > 0">
                        <h4 class="text-sm font-semibold text-surface-700 dark:text-surface-300 mb-4 flex items-center gap-2">
                            <svg class="h-4 w-4 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                            Per-Facility Settings
                        </h4>
                        <div class="space-y-4">
                            <template x-for="facility in settingsPanel.facilities" :key="facility.id">
                                <div class="rounded-2xl border border-surface-200/60 dark:border-surface-700/50 bg-surface-50/50 dark:bg-surface-800/40 p-5">
                                    <div class="flex items-center justify-between mb-4">
                                        <h5 class="text-sm font-semibold text-surface-800 dark:text-white" x-text="facility.name"></h5>
                                        <span x-show="facility.saved" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
                                              class="text-xs font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Saved
                                        </span>
                                    </div>

                                    <!-- Square Terminal device pairing UI -->
                                    <template x-if="settingsPanel.ext && settingsPanel.ext.slug === 'square-terminal-pos'">
                                        <div class="space-y-4" x-data="terminalPairing(facility, settingsPanel.ext.slug)" x-init="loadStatus()">
                                            <!-- Current device status -->
                                            <div class="rounded-xl border p-3" :class="pairedDeviceId ? 'border-emerald-200 dark:border-emerald-700/50 bg-emerald-50 dark:bg-emerald-900/20' : 'border-amber-200 dark:border-amber-700/50 bg-amber-50 dark:bg-amber-900/20'">
                                                <div class="flex items-center gap-2">
                                                    <span class="h-2.5 w-2.5 rounded-full" :class="pairedDeviceId ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                                    <span class="text-xs font-semibold" :class="pairedDeviceId ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300'"
                                                          x-text="pairedDeviceId ? '✓ Terminal Paired' : '⚠ No Terminal Paired'"></span>
                                                    <span x-show="saving" class="ml-auto text-[10px] text-primary-500 animate-pulse">Saving...</span>
                                                    <span x-show="saved" x-transition class="ml-auto text-[10px] text-emerald-600 font-medium">✓ Saved</span>
                                                </div>
                                                <p x-show="pairedDeviceId" class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1 font-mono" x-text="'Device: ' + pairedDeviceId"></p>
                                                <p x-show="!pairedDeviceId" class="text-[10px] text-amber-600 dark:text-amber-400 mt-1">Pair a terminal device to enable POS payments at this facility.</p>
                                            </div>

                                            <!-- Available devices list -->
                                            <div>
                                                <div class="flex items-center justify-between mb-2">
                                                    <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400">Available Devices</label>
                                                    <button @click="refreshDevices()" :disabled="loadingDevices" class="text-[10px] text-primary-500 hover:text-primary-600 font-medium">
                                                        <span x-show="!loadingDevices">⟳ Refresh</span>
                                                        <span x-show="loadingDevices">Loading...</span>
                                                    </button>
                                                </div>
                                                <div x-show="loadingDevices" class="text-center py-3">
                                                    <div class="inline-block h-4 w-4 rounded-full border-2 border-surface-200 border-t-primary-500 animate-spin"></div>
                                                </div>
                                                <div x-show="!loadingDevices && devices.length === 0" class="text-xs text-surface-400 py-2 text-center">No devices found. Pair a new terminal below.</div>
                                                <div x-show="!loadingDevices && devices.length > 0" class="space-y-2">
                                                    <template x-for="dev in devices" :key="dev.id">
                                                        <div class="flex items-center justify-between rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2.5">
                                                            <div>
                                                                <span class="text-xs font-medium text-surface-700 dark:text-surface-200" x-text="dev.name || 'Terminal'"></span>
                                                                <span class="ml-2 text-[10px] font-mono text-surface-400" x-text="dev.id"></span>
                                                                <span class="ml-2 inline-flex items-center rounded-full px-1.5 py-0.5 text-[9px] font-semibold"
                                                                      :class="dev.status === 'PAIRED' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-surface-100 text-surface-500'"
                                                                      x-text="dev.status"></span>
                                                            </div>
                                                            <button @click="selectDevice(dev.id, facility)" :disabled="saving" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors disabled:opacity-50"
                                                                    :class="pairedDeviceId === dev.id ? 'bg-emerald-500 text-white' : 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 hover:bg-primary-200'"
                                                                    x-text="pairedDeviceId === dev.id ? '✓ Selected' : 'Use This'"></button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Pair new device -->
                                            <div class="rounded-xl border border-dashed border-surface-300 dark:border-surface-600 bg-surface-50/50 dark:bg-surface-800/30 p-4">
                                                <h6 class="text-xs font-semibold text-surface-700 dark:text-surface-300 mb-2">Pair New Terminal Device</h6>
                                                <p class="text-[10px] text-surface-500 mb-3">Generate a pairing code to display on the Square Terminal. Enter the code on the device to complete pairing.</p>
                                                <div class="flex gap-2">
                                                    <input type="text" x-model="pairName" placeholder="Device name (e.g. Front Desk)" class="flex-1 rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 px-3 py-2 text-sm shadow-soft focus:border-primary-500 focus:ring-primary-500">
                                                    <button @click="pairNewDevice()" :disabled="pairing" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 px-4 py-2 text-xs font-semibold text-white hover:from-purple-700 hover:to-purple-800 shadow-soft transition-all disabled:opacity-60">
                                                        <span x-show="!pairing">🔗 Pair</span>
                                                        <span x-show="pairing">Pairing...</span>
                                                    </button>
                                                </div>
                                                <!-- Pairing code display -->
                                                <div x-show="pairingCode" class="mt-3 rounded-xl border border-purple-200 dark:border-purple-700/50 bg-purple-50 dark:bg-purple-900/20 p-3 text-center">
                                                    <p class="text-[10px] text-purple-600 dark:text-purple-400 mb-1">Enter this code on the terminal device:</p>
                                                    <p class="text-2xl font-bold text-purple-700 dark:text-purple-300 tracking-widest font-mono" x-text="pairingCode"></p>
                                                    <p x-show="pairingExpiry" class="text-[10px] text-purple-400 mt-1" x-text="'Expires: ' + pairingExpiry"></p>
                                                </div>
                                                <p x-show="pairError" class="text-[10px] text-red-500 mt-2" x-text="pairError"></p>
                                            </div>

                                            <!-- Manual device ID entry (fallback) -->
                                            <details class="group">
                                                <summary class="text-[10px] text-surface-400 cursor-pointer hover:text-surface-600 transition-colors">Advanced: Enter device ID manually</summary>
                                                <div class="mt-2">
                                                    <input type="text" x-model="facility.settings.device_id" placeholder="e.g. device:abc123xyz"
                                                           class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 px-4 py-2.5 text-sm font-mono shadow-soft focus:border-primary-500 focus:ring-primary-500">
                                                </div>
                                            </details>
                                        </div>
                                    </template>

                                    <!-- Generic facility setting fields from schema -->
                                    <template x-if="!settingsPanel.ext || settingsPanel.ext.slug !== 'square-terminal-pos'">
                                        <template x-for="field in (settingsPanel.facilitySchema || [])" :key="field.key">
                                            <div class="mb-3">
                                                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5" x-text="field.label || field.key"></label>
                                                <input :type="field.type === 'password' ? 'password' : 'text'" x-model="facility.settings[field.key]" :placeholder="field.placeholder || ''"
                                                       class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 px-4 py-2.5 text-sm shadow-soft focus:border-primary-500 focus:ring-primary-500">
                                            </div>
                                        </template>
                                    </template>

                                    <div class="mt-4">
                                        <button @click="saveFacilitySettings(facility)" :disabled="facility.saving"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-surface-200 dark:bg-surface-700 border border-surface-300 dark:border-surface-600 px-3.5 py-2 text-xs font-semibold text-surface-700 dark:text-surface-300 hover:bg-surface-300 dark:hover:bg-surface-600 transition-all disabled:opacity-60">
                                            <svg x-show="facility.saving" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                            <span x-text="facility.saving ? 'Saving...' : 'Save for this Facility'"></span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- No settings message -->
                    <div x-show="(!settingsPanel.schema || settingsPanel.schema.length === 0) && (!settingsPanel.facilities || settingsPanel.facilities.length === 0)" class="py-10 text-center text-surface-500 text-sm">
                        This extension has no configurable settings.
                    </div>

                </div>
            </template>
        </div>
    </div>

    <!-- ===================== UNINSTALL CONFIRM MODAL ===================== -->
    <div x-show="confirmUninstall.open" class="fixed inset-0 z-[100] flex items-center justify-center" style="display:none;">
        <div x-show="confirmUninstall.open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="confirmUninstall.open = false"
             class="fixed inset-0 bg-surface-950/60 backdrop-blur-sm"></div>
        <div x-show="confirmUninstall.open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-surface-800 p-6 shadow-xl border border-surface-200 dark:border-surface-700">
            <div class="flex items-center gap-4 mb-4">
                <div class="h-10 w-10 rounded-xl bg-red-100 dark:bg-red-500/15 flex items-center justify-center flex-shrink-0">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-surface-900 dark:text-white">Uninstall Extension?</h3>
                    <p class="text-sm text-surface-500 mt-0.5">This will remove all settings for this extension.</p>
                </div>
            </div>
            <p class="text-sm text-surface-600 dark:text-surface-400 mb-6">
                Are you sure you want to uninstall <strong x-text="confirmUninstall.name" class="text-surface-800 dark:text-white"></strong>? Facility-specific settings will be deleted.
            </p>
            <div class="flex gap-3">
                <button @click="confirmUninstall.open = false"
                        class="flex-1 rounded-xl border border-surface-300 dark:border-surface-600 px-4 py-2.5 text-sm font-semibold text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-700 transition-all">
                    Cancel
                </button>
                <button @click="doUninstall()"
                        class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-all">
                    Uninstall
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function extensionsManager() {
    return {
        loading: true,
        catalog: [],

        banner: { show: false, type: 'success', message: '' },

        settingsPanel: {
            open: false,
            loading: false,
            saving: false,
            ext: null,
            schema: [],
            facilitySchema: [],
            orgSettings: {},
            facilities: [],
        },

        confirmUninstall: {
            open: false,
            slug: null,
            name: '',
        },

        async loadCatalog() {
            this.loading = true;
            try {
                const res = await authFetch(APP_BASE + '/api/extensions/catalog');
                const json = await res.json();
                if (res.ok) {
                    this.catalog = (json.data || []).map(e => ({ ...e, installed: !!e.is_installed, busy: false }));
                } else {
                    this.showBanner('error', json.message || 'Failed to load extensions.');
                }
            } catch (e) {
                this.showBanner('error', 'Network error loading extensions.');
            }
            this.loading = false;
        },

        async install(slug) {
            const ext = this.catalog.find(e => e.slug === slug);
            if (!ext) return;
            ext.busy = true;
            try {
                const res = await authFetch(APP_BASE + '/api/extensions/' + slug + '/install', { method: 'POST' });
                const json = await res.json();
                if (res.ok) {
                    ext.installed = true;
                    ext.is_installed = true;
                    this.showBanner('success', ext.name + ' installed successfully.');
                } else {
                    this.showBanner('error', json.message || 'Installation failed.');
                }
            } catch (e) {
                this.showBanner('error', 'Network error during installation.');
            }
            ext.busy = false;
        },

        uninstall(slug) {
            const ext = this.catalog.find(e => e.slug === slug);
            if (!ext) return;
            this.confirmUninstall.slug = slug;
            this.confirmUninstall.name = ext.name;
            this.confirmUninstall.open = true;
        },

        async doUninstall() {
            const slug = this.confirmUninstall.slug;
            this.confirmUninstall.open = false;
            const ext = this.catalog.find(e => e.slug === slug);
            if (!ext) return;
            ext.busy = true;
            try {
                const res = await authFetch(APP_BASE + '/api/extensions/' + slug + '/uninstall', { method: 'POST' });
                const json = await res.json();
                if (res.ok) {
                    ext.installed = false;
                    ext.is_installed = false;
                    this.showBanner('success', ext.name + ' uninstalled.');
                } else {
                    this.showBanner('error', json.message || 'Uninstall failed.');
                }
            } catch (e) {
                this.showBanner('error', 'Network error during uninstall.');
            }
            ext.busy = false;
        },

        async openSettings(ext) {
            this.settingsPanel.ext = ext;
            this.settingsPanel.open = true;
            this.settingsPanel.loading = true;
            this.settingsPanel.schema = [];
            this.settingsPanel.orgSettings = {};
            this.settingsPanel.facilities = [];
            try {
                const res = await authFetch(APP_BASE + '/api/extensions/' + ext.slug + '/settings');
                const json = await res.json();
                if (res.ok) {
                    const data = json.data || {};
                    this.settingsPanel.schema = (data.extension && data.extension.settings_schema) || data.schema || [];
                    this.settingsPanel.facilitySchema = data.facility_schema || [];
                    this.settingsPanel.orgSettings = { ...(data.org_settings || {}) };
                    this.settingsPanel.facilities = (data.facilities || []).map(f => ({
                        id: f.id,
                        name: f.name,
                        settings: { ...(f.settings || {}) },
                        saving: false,
                        saved: false,
                    }));
                } else {
                    this.showBanner('error', json.message || 'Failed to load settings.');
                    this.settingsPanel.open = false;
                }
            } catch (e) {
                this.showBanner('error', 'Network error loading settings.');
                this.settingsPanel.open = false;
            }
            this.settingsPanel.loading = false;
        },

        async saveOrgSettings() {
            this.settingsPanel.saving = true;
            try {
                const res = await authFetch(APP_BASE + '/api/extensions/' + this.settingsPanel.ext.slug + '/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.settingsPanel.orgSettings),
                });
                const json = await res.json();
                if (res.ok) {
                    this.showBanner('success', 'Organization settings saved.');
                } else {
                    this.showBanner('error', json.message || 'Failed to save settings.');
                }
            } catch (e) {
                this.showBanner('error', 'Network error saving settings.');
            }
            this.settingsPanel.saving = false;
        },

        async saveFacilitySettings(facility) {
            facility.saving = true;
            facility.saved = false;
            try {
                const res = await authFetch(APP_BASE + '/api/extensions/' + this.settingsPanel.ext.slug + '/facilities/' + facility.id + '/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(facility.settings),
                });
                const json = await res.json();
                if (res.ok) {
                    facility.saved = true;
                    setTimeout(() => { facility.saved = false; }, 3000);
                } else {
                    this.showBanner('error', json.message || 'Failed to save facility settings.');
                }
            } catch (e) {
                this.showBanner('error', 'Network error saving facility settings.');
            }
            facility.saving = false;
        },

        showBanner(type, message) {
            this.banner = { show: true, type, message };
            setTimeout(() => { this.banner.show = false; }, 5000);
        },
    };
}

function terminalPairing(facility, extSlug) {
    return {
        pairedDeviceId: facility.settings ? (facility.settings.device_id || '') : '',
        devices: [],
        loadingDevices: false,
        saving: false,
        saved: false,
        pairName: '',
        pairing: false,
        pairingCode: '',
        pairingExpiry: '',
        pairError: '',

        async loadStatus() {
            await this.refreshDevices();
        },

        async refreshDevices() {
            this.loadingDevices = true;
            try {
                const res = await authFetch(APP_BASE + '/api/square-terminal/devices');
                const json = await res.json();
                this.devices = json.data || [];
            } catch(e) {
                this.devices = [];
            }
            this.loadingDevices = false;
        },

        async selectDevice(deviceId, fac) {
            this.pairedDeviceId = deviceId;
            if (fac && fac.settings) fac.settings.device_id = deviceId;
            // Auto-save to backend
            this.saving = true;
            this.saved = false;
            try {
                const settings = fac ? { ...(fac.settings || {}), device_id: deviceId } : { device_id: deviceId };
                const res = await authFetch(APP_BASE + '/api/extensions/' + extSlug + '/facilities/' + facility.id + '/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(settings),
                });
                const json = await res.json();
                if (!res.ok) console.error('Failed to save device:', json.message);
                else { this.saved = true; setTimeout(() => { this.saved = false; }, 3000); }
            } catch(e) { console.error('Error saving device:', e); }
            this.saving = false;
        },

        async pairNewDevice() {
            this.pairing = true;
            this.pairError = '';
            this.pairingCode = '';
            this.pairingExpiry = '';
            try {
                const res = await authFetch(APP_BASE + '/api/square-terminal/devices/pair', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: this.pairName || 'Terminal' }),
                });
                const json = await res.json();
                if (res.ok && json.data) {
                    this.pairingCode = json.data.code || '';
                    this.pairingExpiry = json.data.pair_by ? new Date(json.data.pair_by).toLocaleTimeString() : '';
                    setTimeout(() => this.refreshDevices(), 10000);
                } else {
                    this.pairError = json.message || 'Failed to create pairing code.';
                }
            } catch(e) {
                this.pairError = 'Network error creating pairing code.';
            }
            this.pairing = false;
        },
    };
}
</script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
