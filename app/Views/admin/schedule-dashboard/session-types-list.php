<!-- Session Types List Tab -->
<div x-data="sessionTypesList()" x-effect="
    const dash = $el.closest('[x-data]')._x_dataStack?.find(d => Array.isArray(d.facilities));
    if (dash && dash.facilityId && dash.facilityId !== facilityId) { facilityId = dash.facilityId; loadSessionTypes(); }
">
    <!-- Header -->
    <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-bold text-surface-800 dark:text-white">Session Types</h3>
            <p class="text-xs text-surface-400 mt-0.5">Manage your session types grouped by category.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" x-model="search" x-on:input.debounce.300ms="loadSessionTypes()" placeholder="Search..."
                   class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white w-48">
        </div>
    </div>

    <!-- Category filter pills -->
    <div x-show="uniqueCategories.length > 0" class="mb-4 flex flex-wrap gap-2">
        <button x-on:click="filterCategory = ''"
                :class="!filterCategory ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 border-surface-200 dark:border-surface-700 hover:border-primary-300'"
                class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors">
            All
            <span class="ml-1 opacity-70" x-text="'(' + sessionTypes.length + ')'"></span>
        </button>
        <template x-for="cat in uniqueCategories" :key="cat.id">
            <button x-on:click="filterCategory = String(cat.id)"
                    :class="filterCategory === String(cat.id) ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 border-surface-200 dark:border-surface-700 hover:border-primary-300'"
                    class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors">
                <span x-text="cat.name"></span>
                <span class="ml-1 opacity-70" x-text="'(' + cat.count + ')'"></span>
            </button>
        </template>
        <button x-show="unassignedTypes.length > 0" x-on:click="filterCategory = 'unassigned'"
                :class="filterCategory === 'unassigned' ? 'bg-surface-600 text-white border-surface-600' : 'bg-white dark:bg-surface-800 text-surface-500 dark:text-surface-400 border-surface-200 dark:border-surface-700 hover:border-surface-400'"
                class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors">
            Unassigned
            <span class="ml-1 opacity-70" x-text="'(' + unassignedTypes.length + ')'"></span>
        </button>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-12 text-center">
        <div class="inline-block h-8 w-8 rounded-full border-[3px] border-surface-200 border-t-primary-500 animate-spin"></div>
    </div>

    <!-- Grouped List -->
    <div x-show="!loading" class="space-y-6">

        <!-- Categorized groups -->
        <template x-for="group in displayGroups" :key="group.id">
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <!-- Category header -->
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-5 py-3 bg-surface-50/60 dark:bg-surface-800/40">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 text-white text-xs font-bold shadow-sm"
                         x-text="group.name.charAt(0).toUpperCase()"></div>
                    <h4 class="text-sm font-bold text-surface-800 dark:text-white" x-text="group.name"></h4>
                    <span class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-500/10 px-2 py-0.5 text-[10px] font-bold text-primary-600 dark:text-primary-400"
                          x-text="group.items.length + (group.items.length === 1 ? ' type' : ' types')"></span>
                </div>

                <!-- Session type rows -->
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <template x-for="(st, idx) in group.items" :key="st.id">
                        <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-surface-50/70 dark:hover:bg-surface-800/30 transition-colors">
                            <!-- Serial number circle -->
                            <div class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-surface-100 dark:bg-surface-800 text-xs font-bold text-surface-500 dark:text-surface-400"
                                 x-text="idx + 1"></div>

                            <!-- Main info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h5 class="text-sm font-semibold text-surface-800 dark:text-white truncate" x-text="st.title"></h5>
                                    <!-- Status badge -->
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                                          :class="st.is_active ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-50 text-red-500 dark:bg-red-500/10 dark:text-red-400'"
                                          x-text="st.is_active ? 'Active' : 'Inactive'"></span>
                                    <!-- Type badge -->
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                                          :class="{
                                              'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400': st.session_type === 'class',
                                              'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400': st.session_type === 'series',
                                              'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400': st.session_type === 'series_rolling',
                                          }"
                                          x-text="({class:'Single',series:'Multi-Week',series_rolling:'Rolling'})[st.session_type] || st.session_type"></span>
                                    <!-- Scheduled classes count -->
                                    <span x-show="st.classes_count > 0" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span x-text="st.classes_count + ' class' + (st.classes_count > 1 ? 'es' : '')"></span>
                                    </span>
                                    <!-- No classes badge -->
                                    <span x-show="!st.classes_count || st.classes_count === 0" class="inline-flex items-center rounded-full bg-surface-100 dark:bg-surface-800 px-2 py-0.5 text-[10px] font-medium text-surface-400">
                                        No classes
                                    </span>
                                </div>
                                <!-- Meta line -->
                                <div class="mt-1 flex items-center gap-3 text-xs text-surface-400 flex-wrap">
                                    <span x-show="st.capacity" class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span x-text="st.capacity + ' spots'"></span>
                                    </span>
                                    <span x-show="st.duration" class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span x-text="st.duration + ' min'"></span>
                                    </span>
                                    <!-- Pricing display -->
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span x-text="formatPrice(st)"></span>
                                    </span>
                                    <span x-show="st.internal_title" class="text-surface-300">|</span>
                                    <span x-show="st.internal_title" class="italic text-surface-400" x-text="st.internal_title"></span>
                                </div>
                            </div>

                            <!-- Action buttons -->
                            <div class="flex items-center gap-0.5 flex-shrink-0">
                                <button x-on:click="copySessionType(st.id)" title="Duplicate"
                                        class="rounded-lg p-2 text-surface-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                                <button x-on:click="openSpotsModal(st)" title="Update Spots"
                                        class="rounded-lg p-2 text-surface-400 hover:text-violet-500 hover:bg-violet-50 dark:hover:bg-violet-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                                <button x-on:click="$el.closest('[x-data]')._x_dataStack.find(d => d.startEdit)?.startEdit(st.id)" title="Edit"
                                        class="rounded-lg p-2 text-surface-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button x-on:click="deleteSessionType(st.id)" title="Delete"
                                        class="rounded-lg p-2 text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Unassigned session types (no scheduled classes) -->
        <template x-if="displayUnassigned.length > 0">
            <div class="rounded-2xl border border-dashed border-surface-300 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/20 overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-200 dark:border-surface-700 px-5 py-3 bg-surface-100/60 dark:bg-surface-800/50">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-surface-300 dark:bg-surface-700 text-white text-xs font-bold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-surface-600 dark:text-surface-300">Unscheduled Session Types</h4>
                    <span class="inline-flex items-center rounded-full bg-surface-200 dark:bg-surface-700 px-2 py-0.5 text-[10px] font-bold text-surface-500 dark:text-surface-400"
                          x-text="displayUnassigned.length + ' type' + (displayUnassigned.length > 1 ? 's' : '')"></span>
                    <p class="text-[10px] text-surface-400 ml-auto">These have no scheduled classes yet</p>
                </div>
                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                    <template x-for="(st, idx) in displayUnassigned" :key="st.id">
                        <div class="flex items-center gap-4 px-5 py-3 hover:bg-white dark:hover:bg-surface-800/40 transition-colors">
                            <div class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-full border-2 border-dashed border-surface-300 dark:border-surface-600 text-xs font-bold text-surface-400"
                                 x-text="idx + 1"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h5 class="text-sm font-semibold text-surface-700 dark:text-surface-200 truncate" x-text="st.title"></h5>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                                          :class="st.is_active ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-50 text-red-500 dark:bg-red-500/10 dark:text-red-400'"
                                          x-text="st.is_active ? 'Active' : 'Inactive'"></span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                                          :class="{
                                              'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400': st.session_type === 'class',
                                              'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400': st.session_type === 'series',
                                              'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400': st.session_type === 'series_rolling',
                                          }"
                                          x-text="({class:'Single',series:'Multi-Week',series_rolling:'Rolling'})[st.session_type] || st.session_type"></span>
                                    <span x-show="st.category_name" class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-500/10 px-2 py-0.5 text-[10px] font-medium text-primary-500 dark:text-primary-400"
                                          x-text="st.category_name"></span>
                                </div>
                                <div class="mt-1 flex items-center gap-3 text-xs text-surface-400 flex-wrap">
                                    <span x-show="st.capacity" x-text="st.capacity + ' spots'"></span>
                                    <span x-show="st.duration" x-text="st.duration + ' min'"></span>
                                    <span x-text="formatPrice(st)"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5 flex-shrink-0">
                                <button x-on:click="copySessionType(st.id)" title="Duplicate"
                                        class="rounded-lg p-2 text-surface-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                                <button x-on:click="openSpotsModal(st)" title="Update Spots"
                                        class="rounded-lg p-2 text-surface-400 hover:text-violet-500 hover:bg-violet-50 dark:hover:bg-violet-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                                <button x-on:click="$el.closest('[x-data]')._x_dataStack.find(d => d.startEdit)?.startEdit(st.id)" title="Edit"
                                        class="rounded-lg p-2 text-surface-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button x-on:click="deleteSessionType(st.id)" title="Delete"
                                        class="rounded-lg p-2 text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Empty state -->
        <template x-if="!loading && sessionTypes.length === 0">
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft py-16 text-center">
                <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-100 dark:bg-surface-800 mb-3">
                    <svg class="w-7 h-7 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h4 class="text-sm font-semibold text-surface-700 dark:text-surface-200 mb-1">No session types found</h4>
                <p class="text-xs text-surface-400 mb-4">Click the "Add Session Type" tab to create one.</p>
            </div>
        </template>

        <!-- Summary footer -->
        <div x-show="sessionTypes.length > 0" class="text-xs text-surface-400 px-1 flex items-center gap-4">
            <span>Total: <span class="font-semibold text-surface-600 dark:text-surface-300" x-text="sessionTypes.length"></span> session types</span>
            <span x-show="scheduledCount > 0" class="text-emerald-500">
                <span class="font-semibold" x-text="scheduledCount"></span> with classes
            </span>
            <span x-show="unassignedTypes.length > 0" class="text-surface-400">
                <span class="font-semibold" x-text="unassignedTypes.length"></span> unscheduled
            </span>
        </div>
    </div>

    <!-- Spots Modal -->
    <div x-show="spotsModal.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.4)">
        <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-800 w-full max-w-md p-6" @click.outside="spotsModal.show = false">
            <h3 class="text-lg font-bold text-surface-800 dark:text-white mb-4">Update Spots / Capacity</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Current Capacity</label>
                    <input type="number" x-model="spotsModal.capacity" min="1"
                           class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                </div>
                <label class="flex items-center gap-3 cursor-pointer rounded-xl border border-amber-200 dark:border-amber-500/30 bg-amber-50/50 dark:bg-amber-500/5 p-3">
                    <input type="checkbox" x-model="spotsModal.applyToAll" class="rounded border-surface-300 text-amber-600 focus:ring-amber-500">
                    <div>
                        <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Apply to all scheduled classes</span>
                        <p class="text-xs text-surface-400">This will update spots on all existing classes for this session type</p>
                    </div>
                </label>
            </div>
            <div class="flex items-center gap-3 mt-5">
                <button x-on:click="saveSpots()" :disabled="spotsModal.saving"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition-colors disabled:opacity-50">
                    <span x-text="spotsModal.saving ? 'Saving...' : 'Update Spots'"></span>
                </button>
                <button x-on:click="spotsModal.show = false"
                        class="rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function sessionTypesList() {
    const baseApi = '<?= ($baseUrl ?? '') ?>';
    return {
        sessionTypes: [],
        loading: false,
        search: '',
        facilityId: null,
        filterCategory: '',
        spotsModal: { show: false, sessionTypeId: null, capacity: '', applyToAll: false, saving: false },

        // --- Computed-like getters ---

        get uniqueCategories() {
            const map = {};
            this.sessionTypes.forEach(st => {
                if (st.category_id && st.category_name) {
                    if (!map[st.category_id]) {
                        map[st.category_id] = { id: st.category_id, name: st.category_name, count: 0 };
                    }
                    map[st.category_id].count++;
                }
            });
            return Object.values(map).sort((a, b) => a.name.localeCompare(b.name));
        },

        get scheduledTypes() {
            return this.sessionTypes.filter(st => st.classes_count > 0);
        },

        get unassignedTypes() {
            return this.sessionTypes.filter(st => !st.classes_count || st.classes_count === 0);
        },

        get scheduledCount() {
            return this.scheduledTypes.length;
        },

        /**
         * Build category-grouped arrays for the main list (scheduled + those with a category).
         * Unscheduled items without filtering show at the bottom in a separate section.
         */
        get displayGroups() {
            let items = this.sessionTypes;

            // If filtering by a specific category
            if (this.filterCategory && this.filterCategory !== 'unassigned') {
                items = items.filter(st => String(st.category_id) === this.filterCategory);
            } else if (this.filterCategory === 'unassigned') {
                return []; // Only show unassigned section
            }

            // Separate items with classes vs without
            const withClasses = items.filter(st => st.classes_count > 0);
            const withoutClasses = items.filter(st => !st.classes_count || st.classes_count === 0);

            // Group items with classes by category
            const groupMap = {};
            withClasses.forEach(st => {
                const catId = st.category_id || 0;
                const catName = st.category_name || 'Uncategorized';
                if (!groupMap[catId]) {
                    groupMap[catId] = { id: catId, name: catName, items: [] };
                }
                groupMap[catId].items.push(st);
            });

            // Also include without-classes items in their category groups IF a category filter is active
            if (this.filterCategory) {
                withoutClasses.forEach(st => {
                    const catId = st.category_id || 0;
                    const catName = st.category_name || 'Uncategorized';
                    if (!groupMap[catId]) {
                        groupMap[catId] = { id: catId, name: catName, items: [] };
                    }
                    groupMap[catId].items.push(st);
                });
            }

            return Object.values(groupMap).sort((a, b) => a.name.localeCompare(b.name));
        },

        get displayUnassigned() {
            if (this.filterCategory === 'unassigned') {
                return this.unassignedTypes;
            }
            if (this.filterCategory) {
                return []; // Specific category filter — don't show unassigned section
            }
            return this.unassignedTypes;
        },

        // --- Price formatting ---

        formatPrice(st) {
            // For rolling types, show range from rolling prices
            if (st.session_type === 'series_rolling' && st.rolling_prices && st.rolling_prices.length > 0) {
                const prices = st.rolling_prices.map(r => parseFloat(r.price)).sort((a, b) => a - b);
                if (prices.length === 1) {
                    return '$' + prices[0].toFixed(2) + ' / ' + st.rolling_prices[0].number_of_weeks + 'wk';
                }
                return '$' + prices[0].toFixed(2) + ' – $' + prices[prices.length - 1].toFixed(2);
            }
            // Standard price
            if (st.standard_price) {
                return '$' + parseFloat(st.standard_price).toFixed(2);
            }
            return 'No price';
        },

        // --- Data loading ---

        async loadSessionTypes() {
            if (!this.facilityId) { this.sessionTypes = []; return; }
            this.loading = true;
            try {
                let url = baseApi + '/api/session-types?facility_id=' + this.facilityId + '&per_page=100';
                if (this.search) url += '&search=' + encodeURIComponent(this.search);
                const res = await authFetch(url);
                const json = await res.json();
                this.sessionTypes = json.data || [];
            } catch (e) { console.error('Failed to load session types', e); }
            this.loading = false;
        },

        // --- Copy / Duplicate ---

        async copySessionType(id) {
            if (!confirm('Duplicate this session type? (Settings and pricing will be copied, scheduled classes will not.)')) return;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + id + '/copy', { method: 'POST' });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Session type duplicated!', type: 'success' } }));
                    this.loadSessionTypes();
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed to copy', type: 'error' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
        },

        // --- Spots modal ---

        openSpotsModal(st) {
            this.spotsModal = { show: true, sessionTypeId: st.id, capacity: st.capacity || '', applyToAll: false, saving: false };
        },

        async saveSpots() {
            this.spotsModal.saving = true;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + this.spotsModal.sessionTypeId + '/spots', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ capacity: parseInt(this.spotsModal.capacity), apply_to_all_classes: this.spotsModal.applyToAll })
                });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Spots updated', type: 'success' } }));
                    this.spotsModal.show = false;
                    this.loadSessionTypes();
                }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Error', type: 'error' } })); }
            this.spotsModal.saving = false;
        },

        // --- Delete ---

        async deleteSessionType(id) {
            if (!confirm('Delete this session type and all its scheduled classes?')) return;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + id, { method: 'DELETE' });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Session type deleted', type: 'success' } }));
                    this.loadSessionTypes();
                }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
        }
    };
}
</script>
