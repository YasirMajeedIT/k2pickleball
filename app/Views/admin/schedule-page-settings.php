<?php
$title = 'Schedule Page Settings';
$breadcrumbs = [
    ['label' => 'Schedule', 'url' => null],
    ['label' => 'Schedule Page Settings'],
];
ob_start();
?>
<div x-data="schedulePageSettings()" x-init="load()" class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-surface-800 dark:text-surface-100">Schedule Page Settings</h1>
            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Configure how your public schedule page looks, what data is displayed, and booking behavior.</p>
        </div>
        <div class="flex items-center gap-3">
            <a :href="previewUrl" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-700 border border-surface-200 dark:border-surface-700 text-sm font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview
            </a>
            <button @click="save()" :disabled="saving" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold transition-all disabled:opacity-50">
                <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span x-text="saving ? 'Saving...' : 'Save Settings'"></span>
            </button>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex items-center justify-center py-20">
        <svg class="animate-spin w-8 h-8 text-primary-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <!-- Success Toast -->
    <div x-show="successMsg" x-cloak x-transition class="fixed top-5 right-5 z-50 px-4 py-3 rounded-xl bg-green-500 text-white text-sm font-medium shadow-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span x-text="successMsg"></span>
    </div>

    <div x-show="!loading" x-cloak class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- ═══ LEFT COLUMN: Display + Filters ═══ -->
        <div class="xl:col-span-2 space-y-6">

            <!-- Page Identity -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="text-sm font-bold text-surface-800 dark:text-surface-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Page Identity
                    </h2>
                    <p class="text-xs text-surface-400 mt-1">Rename the page and set the subtitle shown to visitors.</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1">Page Title</label>
                        <input type="text" x-model="s.page_title" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white" placeholder="Class Schedule">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1">Page Subtitle</label>
                        <input type="text" x-model="s.page_subtitle" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white" placeholder="Browse upcoming classes and reserve your spot.">
                    </div>
                </div>
            </div>

            <!-- View Modes -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="text-sm font-bold text-surface-800 dark:text-surface-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        View Modes
                    </h2>
                    <p class="text-xs text-surface-400 mt-1">Choose which view modes visitors can switch between and the default view.</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-2">Available Views</label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="v in allViews" :key="v.key">
                                <label class="flex items-center gap-2 px-3 py-2 rounded-xl border cursor-pointer transition-all"
                                       :class="enabledViews.includes(v.key)
                                           ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700'
                                           : 'border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-800'">
                                    <input type="checkbox" :value="v.key" x-model="enabledViews" class="rounded border-surface-300 text-primary-500 focus:ring-primary-500/20">
                                    <div>
                                        <span class="text-sm font-medium text-surface-700 dark:text-surface-200" x-text="v.label"></span>
                                        <span class="block text-[10px] text-surface-400" x-text="v.desc"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1">Default View</label>
                        <select x-model="s.default_view" class="w-full max-w-xs rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                            <template x-for="v in allViews.filter(v => enabledViews.includes(v.key))" :key="v.key">
                                <option :value="v.key" x-text="v.label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1">Color Scheme</label>
                        <select x-model="s.color_scheme" class="w-full max-w-xs rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                            <option value="default">Default (Navy/Gold)</option>
                            <option value="category">Category Colors</option>
                            <option value="single">Single Brand Color</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Calendar Card Fields -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="text-sm font-bold text-surface-800 dark:text-surface-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Calendar Card Fields
                    </h2>
                    <p class="text-xs text-surface-400 mt-1">Toggle which details appear on each class card across all views.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="field in cardFields" :key="field.key">
                            <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                                   :class="s[field.key] === '1'
                                       ? 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-300 dark:border-emerald-800'
                                       : 'border-surface-100 dark:border-surface-800 hover:bg-surface-50 dark:hover:bg-surface-800/50'">
                                <input type="checkbox" :checked="s[field.key] === '1'" @change="s[field.key] = $event.target.checked ? '1' : '0'" class="rounded border-surface-300 text-emerald-500 focus:ring-emerald-500/20 mt-0.5 flex-shrink-0">
                                <div class="min-w-0">
                                    <span class="text-sm font-medium text-surface-700 dark:text-surface-200 block" x-text="field.label"></span>
                                    <p class="text-[10px] text-surface-400 leading-relaxed mt-0.5" x-text="field.desc"></p>
                                </div>
                            </label>
                        </template>
                    </div>

                    <!-- Resources on Card — expandable resource picker -->
                    <div class="mt-3 rounded-xl border transition-all"
                         :class="s.show_resources === '1' || s.show_resources === true
                             ? 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-300 dark:border-emerald-800'
                             : 'border-surface-100 dark:border-surface-800'">
                        <label class="flex items-start gap-3 p-3 cursor-pointer">
                            <input type="checkbox" :checked="s.show_resources === '1'" @change="s.show_resources = $event.target.checked ? '1' : '0'" class="rounded border-surface-300 text-emerald-500 focus:ring-emerald-500/20 mt-0.5 flex-shrink-0">
                            <div class="min-w-0">
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-200 block">Resources</span>
                                <p class="text-[10px] text-surface-400 leading-relaxed mt-0.5">Show selected resources on each card (e.g. Skill Level, Age Group, Equipment).</p>
                            </div>
                        </label>
                        <!-- Resource picker expands when toggled on -->
                        <div x-show="s.show_resources === '1' || s.show_resources === true" x-cloak
                             class="border-t border-emerald-200 dark:border-emerald-800 px-4 pb-4 pt-3">
                            <p class="text-xs font-semibold text-surface-600 dark:text-surface-400 mb-2">Select which resources to display on each card:</p>
                            <div x-show="resources.length > 0" class="space-y-1.5">
                                <template x-for="res in resources" :key="res.id">
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-all"
                                           :class="cardResourceIds.includes(String(res.id)) || cardResourceIds.includes(res.id)
                                               ? 'bg-white dark:bg-surface-900 border-emerald-400 dark:border-emerald-600'
                                               : 'border-surface-200 dark:border-surface-700 hover:bg-white dark:hover:bg-surface-800'">
                                        <input type="checkbox" :value="res.id" x-model="cardResourceIds" class="rounded border-surface-300 text-emerald-500 focus:ring-emerald-500/20">
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-medium text-surface-700 dark:text-surface-200" x-text="res.name"></span>
                                            <span class="text-[10px] text-surface-400 ml-1.5" x-text="res.field_type + (res.values?.length ? ' · ' + res.values.length + ' values' : '')"></span>
                                        </div>
                                        <!-- Show values as preview chips -->
                                        <div class="flex flex-wrap gap-1 ml-2" x-show="res.values && res.values.length > 0">
                                            <template x-for="val in (res.values || []).slice(0, 4)" :key="val.id">
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300" x-text="val.name"></span>
                                            </template>
                                            <span x-show="res.values && res.values.length > 4" class="text-[10px] text-surface-400" x-text="'+' + (res.values.length - 4) + ' more'"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                            <p x-show="!resources.length" class="text-xs text-surface-400 px-1">
                                No resources found. <a :href="(window.APP_BASE || '') + '/admin/resources'" class="text-emerald-600 underline">Create resources first</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="text-sm font-bold text-surface-800 dark:text-surface-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filters
                    </h2>
                    <p class="text-xs text-surface-400 mt-1">Enable filters visitors can use to narrow down the schedule.</p>
                </div>
                <div class="p-6 space-y-4">
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-surface-100 dark:border-surface-800 hover:bg-surface-50 dark:hover:bg-surface-800/50 cursor-pointer transition-all">
                        <input type="checkbox" :checked="s.show_category_filter === '1'" @change="s.show_category_filter = $event.target.checked ? '1' : '0'" class="rounded border-surface-300 text-amber-500 focus:ring-amber-500/20">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Category Filter</span>
                            <p class="text-[10px] text-surface-400 mt-0.5">Show category filter pills (e.g. Clinics, Leagues, Open Play).</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-surface-100 dark:border-surface-800 hover:bg-surface-50 dark:hover:bg-surface-800/50 cursor-pointer transition-all">
                        <input type="checkbox" :checked="s.show_resource_filters === '1'" @change="s.show_resource_filters = $event.target.checked ? '1' : '0'" class="rounded border-surface-300 text-amber-500 focus:ring-amber-500/20">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Resource Filters</span>
                            <p class="text-[10px] text-surface-400 mt-0.5">Show custom resource filters (e.g. Skill Level, Age Group from Resources table).</p>
                        </div>
                    </label>
                    <div x-show="s.show_resource_filters === '1' || s.show_resource_filters === true" x-cloak class="pl-10">
                        <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-2">Select Resources to Use as Filters</label>
                        <div class="space-y-2">
                            <template x-for="res in resources" :key="res.id">
                                <div>
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-surface-100 dark:border-surface-800 hover:bg-surface-50 dark:hover:bg-surface-800/50 cursor-pointer transition-all"
                                           :class="resourceFilterIds.includes(String(res.id)) || resourceFilterIds.includes(res.id) ? 'bg-amber-50 dark:bg-amber-900/10 border-amber-300 dark:border-amber-700' : ''">
                                        <input type="checkbox" :value="res.id" x-model="resourceFilterIds" class="rounded border-surface-300 text-amber-500 focus:ring-amber-500/20">
                                        <span class="text-sm font-medium text-surface-700 dark:text-surface-200" x-text="res.name"></span>
                                        <span class="text-[10px] text-surface-400 ml-1" x-text="'(' + res.field_type + ', ' + (res.values?.length || 0) + ' values)'"></span>
                                    </label>
                                    <!-- Show values when this resource is selected -->
                                    <div x-show="resourceFilterIds.includes(String(res.id)) || resourceFilterIds.includes(res.id)" x-cloak class="ml-8 mt-1 mb-2">
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="val in (res.values || [])" :key="val.id">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-[11px] font-medium text-amber-700 dark:text-amber-300" x-text="val.name"></span>
                                            </template>
                                            <span x-show="!res.values || res.values.length === 0" class="text-[10px] text-surface-400 italic">No values defined for this resource</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <p x-show="!resources.length" class="text-xs text-surface-400">No resources defined. Create them in Resources section first.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ RIGHT COLUMN: Booking + Payment + Preview ═══ -->
        <div class="space-y-6">

            <!-- Booking Behavior -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="text-sm font-bold text-surface-800 dark:text-surface-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Booking Behavior
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-surface-100 dark:border-surface-800 hover:bg-surface-50 dark:hover:bg-surface-800/50 cursor-pointer transition-all">
                        <input type="checkbox" :checked="s.inline_booking === '1'" @change="s.inline_booking = $event.target.checked ? '1' : '0'" class="rounded border-surface-300 text-blue-500 focus:ring-blue-500/20 mt-0.5">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Inline Click-to-Book</span>
                            <p class="text-[10px] text-surface-400 mt-0.5">Allow visitors to book directly from the schedule via a popup modal instead of navigating to a detail page.</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-surface-100 dark:border-surface-800 hover:bg-surface-50 dark:hover:bg-surface-800/50 cursor-pointer transition-all">
                        <input type="checkbox" :checked="s.require_login === '1'" @change="s.require_login = $event.target.checked ? '1' : '0'" class="rounded border-surface-300 text-blue-500 focus:ring-blue-500/20 mt-0.5">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Require Login</span>
                            <p class="text-[10px] text-surface-400 mt-0.5">Visitors must be logged in before they can make a booking or payment.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="text-sm font-bold text-surface-800 dark:text-surface-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Accepted Payment Methods
                    </h2>
                    <p class="text-xs text-surface-400 mt-1">Choose which payment methods visitors can use when booking.</p>
                </div>
                <div class="p-6 space-y-3">
                    <template x-for="pm in allPaymentMethods" :key="pm.key">
                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                               :class="paymentMethods.includes(pm.key)
                                   ? 'bg-green-50 dark:bg-green-900/10 border-green-300 dark:border-green-800'
                                   : 'border-surface-100 dark:border-surface-800 hover:bg-surface-50 dark:hover:bg-surface-800/50'">
                            <input type="checkbox" :value="pm.key" x-model="paymentMethods" class="rounded border-surface-300 text-green-500 focus:ring-green-500/20 mt-0.5">
                            <div>
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-200 flex items-center gap-1.5">
                                    <span x-text="pm.icon"></span>
                                    <span x-text="pm.label"></span>
                                </span>
                                <p class="text-[10px] text-surface-400 mt-0.5" x-text="pm.desc"></p>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Live Preview -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft sticky top-6" x-data="{ previewTab: 'list' }">
                <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                    <h2 class="text-sm font-bold text-surface-800 dark:text-surface-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Live Preview
                    </h2>
                    <p class="text-xs text-surface-400 mt-1">See how your schedule looks in each view mode.</p>
                    <!-- View tabs -->
                    <div class="flex gap-1 mt-3 bg-surface-100 dark:bg-surface-800 rounded-lg p-0.5">
                        <template x-for="vt in [{k:'list',l:'List'},{k:'week',l:'Week'},{k:'day',l:'Day'},{k:'month',l:'Month'}]" :key="vt.k">
                            <button @click="previewTab = vt.k" class="flex-1 text-[10px] font-semibold py-1.5 rounded-md transition-all"
                                :class="previewTab === vt.k ? 'bg-white dark:bg-surface-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'"
                                x-text="vt.l"></button>
                        </template>
                    </div>
                </div>
                <div class="p-4">
                    <!-- Dark background wrapper to match client page -->
                    <div class="bg-[#0a1628] rounded-xl overflow-hidden">

                        <!-- ═══ LIST VIEW PREVIEW ═══ -->
                        <div x-show="previewTab === 'list'" class="p-3 space-y-3">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Today — Mar 28</div>
                            <template x-for="(mock, mi) in [
                                {name:'Intermediate Clinic',cat:'Clinics',catColor:'#6366f1',time:'9:00 AM',end:'10:30 AM',coach:'Coach Johnson',price:'25.00',spots:8,full:false,hot:true,early:false,dur:90},
                                {name:'Open Play',cat:'Open Play',catColor:'#d4af37',time:'11:00 AM',end:'12:30 PM',coach:'Staff',price:'0',spots:3,full:false,hot:false,early:true,dur:90},
                                {name:'Advanced Drills',cat:'Clinics',catColor:'#6366f1',time:'2:00 PM',end:'3:00 PM',coach:'Coach Martinez',price:'35.00',spots:0,full:true,hot:false,early:false,dur:60}
                            ]" :key="mi">
                                <div class="flex items-start gap-3 p-3 rounded-xl bg-[#111d33] border border-[#1e2d45] hover:border-[#d4af37]/30 transition-all">
                                    <div class="flex-shrink-0 w-16 text-center">
                                        <div x-show="s.show_time === '1'" class="text-[11px] font-bold text-[#d4af37]" x-text="mock.time"></div>
                                        <div x-show="s.show_time === '1'" class="text-[9px] text-slate-500" x-text="mock.end"></div>
                                        <div x-show="s.show_duration === '1'" class="text-[9px] text-slate-600 mt-0.5" x-text="mock.dur + ' min'"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <div x-show="s.show_title === '1'" class="text-xs font-bold text-white" x-text="mock.name"></div>
                                            <span x-show="s.show_hot_deal_badge === '1' && mock.hot" class="px-1 py-0.5 text-[8px] font-bold bg-red-500 text-white rounded-full">HOT</span>
                                            <span x-show="s.show_early_bird_badge === '1' && mock.early" class="px-1 py-0.5 text-[8px] font-bold bg-blue-500 text-white rounded-full">EARLY</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                            <span x-show="s.show_category === '1'" class="text-[9px] px-1.5 py-0.5 rounded-full font-medium text-white/80" :style="'background:' + mock.catColor" x-text="mock.cat"></span>
                                            <!-- Resources -->
                                            <template x-if="s.show_resources === '1'">
                                                <template x-for="res in resources.filter(r => cardResourceIds.includes(String(r.id)) || cardResourceIds.includes(r.id))" :key="'lr'+res.id+mi">
                                                    <span class="text-[9px] text-slate-500" x-text="res.name + ': ' + (res.values && res.values.length ? res.values.map(v => v.name).slice(0,2).join(', ') : '—')"></span>
                                                </template>
                                            </template>
                                            <span x-show="s.show_coach === '1' && mock.coach" class="text-[9px] text-slate-400" x-text="mock.coach"></span>
                                            <span x-show="s.show_courts === '1'" class="text-[9px] text-slate-500">Courts 1, 2</span>
                                        </div>
                                        <div x-show="s.show_description === '1'" class="text-[9px] text-slate-500 mt-1 line-clamp-1">Work on your dinking, volleys, and third-shot drops with certified pros.</div>
                                        <div x-show="s.show_session_number === '1'" class="text-[9px] text-slate-500 mt-0.5">Session 3 of 8</div>
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        <template x-if="s.show_price === '1'">
                                            <div>
                                                <div x-show="mock.price > 0" class="text-xs font-extrabold text-white" x-text="'$' + mock.price"></div>
                                                <div x-show="mock.price == 0" class="text-xs font-extrabold text-[#d4af37]">Free</div>
                                            </div>
                                        </template>
                                        <div x-show="s.show_spots === '1'" class="text-[9px] font-semibold mt-0.5" :class="mock.full ? 'text-red-400' : 'text-emerald-400'" x-text="mock.full ? 'Full' : mock.spots + ' spots'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- ═══ WEEK VIEW PREVIEW ═══ -->
                        <div x-show="previewTab === 'week'" class="p-3">
                            <div class="grid grid-cols-3 gap-1">
                                <template x-for="(day, di) in [{d:'Mon',n:'24'},{d:'Tue',n:'25'},{d:'Wed',n:'26'}]" :key="di">
                                    <div>
                                        <div class="text-center text-[9px] font-semibold text-slate-500 pb-1" x-text="day.d + ' ' + day.n"></div>
                                        <div class="space-y-1">
                                            <template x-for="(wm, wi) in [{name:'Clinic',cat:'Clinics',catColor:'#6366f1',time:'9:00 AM',coach:'Johnson',price:'25.00',spots:8,full:false},{name:'Open Play',cat:'Open Play',catColor:'#d4af37',time:'11 AM',coach:'Staff',price:'0',spots:3,full:false}].slice(0, di === 1 ? 1 : 2)" :key="wi">
                                                <div class="p-1.5 rounded-lg bg-[#111d33] border border-[#1e2d45]">
                                                    <div x-show="s.show_time === '1'" class="text-[8px] font-medium text-[#d4af37]" x-text="wm.time"></div>
                                                    <div x-show="s.show_title === '1'" class="text-[8px] font-bold text-white mt-0.5 line-clamp-1" x-text="wm.name"></div>
                                                    <div class="flex flex-wrap gap-0.5 mt-0.5">
                                                        <span x-show="s.show_category === '1'" class="text-[7px] px-1 py-0.5 rounded font-medium text-white/80" :style="'background:' + wm.catColor" x-text="wm.cat"></span>
                                                        <span x-show="s.show_spots === '1'" class="text-[7px] font-bold" :class="wm.full ? 'text-red-400' : 'text-emerald-400'" x-text="wm.full ? 'Full' : wm.spots + ' left'"></span>
                                                    </div>
                                                    <div x-show="s.show_coach === '1'" class="text-[7px] text-slate-400 mt-0.5" x-text="wm.coach"></div>
                                                    <div x-show="s.show_price === '1' && wm.price > 0" class="text-[8px] font-bold text-white mt-0.5" x-text="'$' + wm.price"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- ═══ DAY VIEW PREVIEW ═══ -->
                        <div x-show="previewTab === 'day'" class="p-3 space-y-2">
                            <div class="text-[10px] font-bold text-white text-center">Friday, March 28</div>
                            <template x-for="(dm, dmi) in [
                                {name:'Morning Clinic',cat:'Clinics',catColor:'#6366f1',time:'9:00 AM',end:'10:30 AM',coach:'Coach Johnson',price:'25.00',spots:8,full:false,hot:true},
                                {name:'Open Play',cat:'Open Play',catColor:'#d4af37',time:'11:00 AM',end:'12:30 PM',coach:'Staff',price:'0',spots:12,full:false,hot:false},
                                {name:'League Night',cat:'Leagues',catColor:'#ef4444',time:'6:00 PM',end:'8:00 PM',coach:'Coach Martinez',price:'15.00',spots:0,full:true,hot:false}
                            ]" :key="dmi">
                                <div class="flex items-center gap-2 p-2.5 rounded-xl bg-[#111d33] border border-[#1e2d45]">
                                    <div x-show="s.show_time === '1'" class="text-[9px] font-bold text-[#d4af37] w-12 text-center flex-shrink-0" x-text="dm.time"></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1">
                                            <div x-show="s.show_title === '1'" class="text-[10px] font-bold text-white" x-text="dm.name"></div>
                                            <span x-show="s.show_hot_deal_badge === '1' && dm.hot" class="px-1 text-[7px] font-bold bg-red-500 text-white rounded-full">HOT</span>
                                        </div>
                                        <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                            <span x-show="s.show_category === '1'" class="text-[8px] px-1.5 py-0.5 rounded-full font-medium text-white/80" :style="'background:' + dm.catColor" x-text="dm.cat"></span>
                                            <span x-show="s.show_coach === '1'" class="text-[8px] text-slate-400" x-text="dm.coach"></span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        <div x-show="s.show_price === '1' && dm.price > 0" class="text-[9px] font-bold text-white" x-text="'$' + dm.price"></div>
                                        <div x-show="s.show_price === '1' && dm.price == 0" class="text-[9px] font-bold text-[#d4af37]">Free</div>
                                        <div x-show="s.show_spots === '1'" class="text-[8px] font-semibold" :class="dm.full ? 'text-red-400' : 'text-emerald-400'" x-text="dm.full ? 'Full' : dm.spots + ' left'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- ═══ MONTH VIEW PREVIEW ═══ -->
                        <div x-show="previewTab === 'month'" class="p-3">
                            <div class="text-[10px] font-bold text-white text-center mb-2">March 2026</div>
                            <div class="grid grid-cols-7 gap-px text-center">
                                <template x-for="dh in ['M','T','W','T','F','S','S']" :key="dh">
                                    <div class="text-[7px] font-semibold text-slate-500 py-0.5" x-text="dh"></div>
                                </template>
                                <template x-for="(cell, ci) in [
                                    {n:'',cls:[]},{n:'',cls:[]},{n:'',cls:[]},{n:'',cls:[]},{n:'',cls:[]},{n:'',cls:[]},{n:1,cls:[]},
                                    {n:2,cls:[]},{n:3,cls:['Clinic']},{n:4,cls:[]},{n:5,cls:['Open Play']},{n:6,cls:['Clinic','League']},{n:7,cls:['Open Play']},{n:8,cls:[]},
                                    {n:9,cls:[]},{n:10,cls:['Clinic']},{n:11,cls:['League']},{n:12,cls:[]},{n:13,cls:['Clinic']},{n:14,cls:[]},{n:15,cls:[]},
                                    {n:16,cls:[]},{n:17,cls:['Clinic']},{n:18,cls:[]},{n:19,cls:['Open Play']},{n:20,cls:['Clinic','League']},{n:21,cls:[]},{n:22,cls:[]},
                                    {n:23,cls:[]},{n:24,cls:['Clinic']},{n:25,cls:['Open Play']},{n:26,cls:['Clinic']},{n:27,cls:['League']},{n:28,cls:['Clinic','Open Play']},{n:29,cls:[]},
                                    {n:30,cls:[]},{n:31,cls:['Clinic']},{n:'',cls:[]},{n:'',cls:[]},{n:'',cls:[]},{n:'',cls:[]},{n:'',cls:[]}
                                ]" :key="ci">
                                    <div class="min-h-[28px] p-0.5 border border-[#1e2d45]/50 rounded" :class="cell.n === 28 ? 'bg-[#1e2d45]' : ''">
                                        <div class="text-[7px] font-medium" :class="cell.n ? 'text-slate-400' : 'text-transparent'" x-text="cell.n || '.'"></div>
                                        <template x-for="(cn, cni) in cell.cls.slice(0,2)" :key="cni">
                                            <div class="text-[5px] font-medium rounded px-0.5 mt-0.5 truncate" :class="cn === 'Clinic' ? 'bg-indigo-500/30 text-indigo-300' : cn === 'League' ? 'bg-red-500/30 text-red-300' : 'bg-[#d4af37]/30 text-[#d4af37]'" x-text="cn"></div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    <!-- Settings summary -->
                    <div class="mt-3 p-3 rounded-xl bg-surface-50 dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700">
                        <div class="text-[10px] font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Active Settings</div>
                        <div class="flex flex-wrap gap-1">
                            <template x-for="field in cardFields.filter(f => s[f.key] === '1')" :key="'sum'+field.key">
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium" x-text="field.label"></span>
                            </template>
                            <span x-show="s.show_resources === '1'" class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">Resources</span>
                        </div>
                        <div class="mt-2 text-[10px] text-surface-500">
                            Default view: <span class="font-semibold text-surface-700 dark:text-surface-300" x-text="s.default_view || 'week'"></span>
                            · <span x-text="enabledViews.length"></span> views enabled
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function schedulePageSettings() {
    return {
        loading: true, saving: false, successMsg: '',
        s: {},
        enabledViews: [],
        paymentMethods: [],
        resourceFilterIds: [],
        cardResourceIds: [],
        resources: [],
        categories: [],
        previewUrl: '',

        allViews: [
            { key: 'month', label: 'Month', desc: 'Full monthly calendar grid' },
            { key: 'week', label: 'Week', desc: '7-day column view' },
            { key: 'today', label: 'Day', desc: 'Single day timeline' },
            { key: 'list', label: 'List', desc: 'Multi-day scrolling list' },
            { key: 'calendar', label: 'Calendar Only', desc: 'Clean calendar with minimal chrome' },
        ],

        cardFields: [
            { key: 'show_time', label: 'Session Time', desc: 'Start and end time range (e.g. 9:00 AM – 10:30 AM)' },
            { key: 'show_title', label: 'Session Title', desc: 'Name of the session type' },
            { key: 'show_category', label: 'Category Badge', desc: 'Color-coded category label' },
            { key: 'show_spots', label: 'Availability / Spots', desc: 'Remaining spots or "Full" indicator' },
            { key: 'show_price', label: 'Price', desc: 'Standard price or price range' },
            { key: 'show_coach', label: 'Coach / Facilitator', desc: 'Assigned coach name' },
            { key: 'show_description', label: 'Description', desc: 'Session description snippet' },
            { key: 'show_courts', label: 'Courts', desc: 'Assigned court names' },
            { key: 'show_duration', label: 'Duration', desc: 'Length in minutes' },
            { key: 'show_session_number', label: 'Series Session #', desc: 'For series: "Session 3 of 8"' },
            { key: 'show_hot_deal_badge', label: 'Hot Deal Badge', desc: 'Show special deal indicator' },
            { key: 'show_early_bird_badge', label: 'Early Bird Badge', desc: 'Show early-bird discount indicator' },
        ],

        allPaymentMethods: [
            { key: 'card', icon: '💳', label: 'Credit / Debit Card', desc: 'Secure card payment via Square' },
            { key: 'credit_code', icon: '🎫', label: 'Credit Code', desc: 'Apply a credit code balance' },
            { key: 'gift_certificate', icon: '🎁', label: 'Gift Certificate', desc: 'Redeem a gift certificate code' },
            { key: 'other', icon: '📋', label: 'Other / Pay at Facility', desc: 'Mark as pending — collect payment at the facility' },
        ],

        async load() {
            this.loading = true;
            try {
                const base = window.APP_BASE || '';

                const res = await authFetch(base + '/api/schedule-settings/preview');
                const json = await res.json();
                if (json.status === 'success') {
                    this.resources = json.data.resources || [];
                    this.categories = json.data.categories || [];
                    const raw = json.data.settings || {};

                    // Normalize boolean-like values to '1'/'0' strings
                    // (DB may store type=boolean which PHP casts to true/false)
                    const b = (v, d='1') => (v === true || v === 1 || v === '1' || v === 'true' || (v == null && d === '1')) ? '1' : '0';

                    // Normalize settings
                    this.s = {
                        page_title: raw.page_title || 'Class Schedule',
                        page_subtitle: raw.page_subtitle || 'Browse upcoming classes and reserve your spot.',
                        default_view: raw.default_view || 'week',
                        show_time: b(raw.show_time, '1'),
                        show_title: b(raw.show_title, '1'),
                        show_category: b(raw.show_category, '1'),
                        show_spots: b(raw.show_spots, '1'),
                        show_price: b(raw.show_price, '1'),
                        show_coach: b(raw.show_coach, '0'),
                        show_description: b(raw.show_description, '0'),
                        show_courts: b(raw.show_courts, '0'),
                        show_duration: b(raw.show_duration, '0'),
                        show_resources: b(raw.show_resources, '0'),
                        show_session_number: b(raw.show_session_number, '0'),
                        show_hot_deal_badge: b(raw.show_hot_deal_badge, '1'),
                        show_early_bird_badge: b(raw.show_early_bird_badge, '1'),
                        show_category_filter: b(raw.show_category_filter, '1'),
                        show_resource_filters: b(raw.show_resource_filters, '0'),
                        inline_booking: b(raw.inline_booking, '1'),
                        require_login: b(raw.require_login, '0'),
                        color_scheme: raw.color_scheme || 'default',
                    };

                    // Parse JSON arrays
                    try { this.enabledViews = typeof raw.enabled_views === 'string' ? JSON.parse(raw.enabled_views) : (raw.enabled_views || ['month','week','today','list','calendar']); } catch(e) { this.enabledViews = ['month','week','today','list','calendar']; }
                    try { this.paymentMethods = typeof raw.payment_methods === 'string' ? JSON.parse(raw.payment_methods) : (raw.payment_methods || ['card','credit_code','gift_certificate']); } catch(e) { this.paymentMethods = ['card','credit_code','gift_certificate']; }
                    try { this.resourceFilterIds = typeof raw.resource_filter_ids === 'string' ? JSON.parse(raw.resource_filter_ids) : (raw.resource_filter_ids || []); } catch(e) { this.resourceFilterIds = []; }
                    try { this.cardResourceIds = typeof raw.card_resource_ids === 'string' ? JSON.parse(raw.card_resource_ids) : (raw.card_resource_ids || []); } catch(e) { this.cardResourceIds = []; }

                    this.previewUrl = base + '/schedule';
                }
            } catch(e) { console.error('Failed to load schedule settings', e); }
            this.loading = false;
        },

        async save() {
            this.saving = true;
            try {
                const base = window.APP_BASE || '';
                const payload = {
                    ...this.s,
                    enabled_views: JSON.stringify(this.enabledViews),
                    payment_methods: JSON.stringify(this.paymentMethods),
                    resource_filter_ids: JSON.stringify(this.resourceFilterIds.map(Number)),
                    card_resource_ids: JSON.stringify(this.cardResourceIds.map(Number)),
                };
                const res = await authFetch(base + '/api/schedule-settings', {
                    method: 'PUT',
                    body: JSON.stringify(payload),
                });
                const json = await res.json();
                if (json.status === 'success') {
                    this.successMsg = 'Settings saved successfully!';
                    setTimeout(() => this.successMsg = '', 3000);
                } else {
                    alert(json.message || 'Failed to save settings');
                }
            } catch(e) { alert('Network error. Please try again.'); }
            this.saving = false;
        },
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
