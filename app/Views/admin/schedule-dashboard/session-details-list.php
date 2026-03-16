<!-- Session Details List Tab -->
<style>
    .sd-quill-editor .ql-container { border-radius: 0 0 0.75rem 0.75rem; min-height: 160px; font-size: 0.875rem; }
    .sd-quill-editor .ql-toolbar { border-radius: 0.75rem 0.75rem 0 0; }
    .sd-quill-editor .ql-editor { min-height: 160px; }
    .dark .sd-quill-editor .ql-toolbar { background: #1e293b; border-color: #334155; }
    .dark .sd-quill-editor .ql-container { background: #1e293b; border-color: #334155; color: #e2e8f0; }
    .dark .sd-quill-editor .ql-toolbar button .ql-stroke { stroke: #94a3b8; }
    .dark .sd-quill-editor .ql-toolbar button .ql-fill { fill: #94a3b8; }
    .dark .sd-quill-editor .ql-toolbar button:hover .ql-stroke { stroke: #e2e8f0; }
    .dark .sd-quill-editor .ql-toolbar .ql-picker-label { color: #94a3b8; }
    .dark .sd-quill-editor .ql-toolbar .ql-picker-options { background: #1e293b; border-color: #334155; }
    .sd-image-drop { border: 2px dashed; transition: all 0.2s; }
    .sd-image-drop.dragover { border-color: #6366f1; background: rgba(99,102,241,0.05); }
</style>

<div x-data="sessionDetailsList()" x-effect="
    const dash = $el.closest('[x-data]')._x_dataStack?.find(d => Array.isArray(d.facilities));
    if (dash && dash.facilityId && !dataLoaded) { facilityId = dash.facilityId; loadData(); }
    if (dash && dash.facilityId && dash.facilityId !== facilityId) { facilityId = dash.facilityId; dataLoaded = false; loadData(); }
">
    <!-- ===== LIST VIEW ===== -->
    <div x-show="mode === 'list'">
        <!-- Header -->
        <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-surface-800 dark:text-white">Session Details</h3>
                <p class="text-xs text-surface-400 mt-0.5">Manage your session names, descriptions, and featured images.</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="text" x-model="search" x-on:input.debounce.300ms="loadData()" placeholder="Search..."
                       class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white w-48">
                <button x-on:click="openAddForm()"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Session
                </button>
            </div>
        </div>

        <!-- Category filter pills -->
        <div x-show="categories.length > 0" class="mb-4 flex flex-wrap gap-2">
            <button x-on:click="filterCategory = ''; loadData()"
                    :class="!filterCategory ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 border-surface-200 dark:border-surface-700'"
                    class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors">All</button>
            <template x-for="cat in categories" :key="cat.id">
                <button x-on:click="filterCategory = cat.id; loadData()"
                        :class="filterCategory == cat.id ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 border-surface-200 dark:border-surface-700'"
                        class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors"
                        x-text="cat.name"></button>
            </template>
        </div>

        <!-- Loading -->
        <div x-show="loading" class="py-12 text-center">
            <div class="inline-block h-8 w-8 rounded-full border-[3px] border-surface-200 border-t-primary-500 animate-spin"></div>
        </div>

        <!-- Session Table -->
        <div x-show="!loading" class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-surface-100 dark:border-surface-800 bg-surface-50/60 dark:bg-surface-800/40">
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500 w-16">Image</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Session</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500 hidden sm:table-cell">Category</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500 hidden md:table-cell">Facilities</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-surface-500 w-24">Status</th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-surface-500 w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(sd, index) in sessions" :key="sd.id">
                            <tr class="border-b border-surface-100 dark:border-surface-800 last:border-0 hover:bg-surface-50/70 dark:hover:bg-surface-800/30 transition-colors">
                                <!-- Thumbnail -->
                                <td class="px-4 py-3">
                                    <div class="w-14 h-9 rounded-lg overflow-hidden bg-surface-100 dark:bg-surface-800 flex items-center justify-center flex-shrink-0">
                                        <template x-if="sd.picture">
                                            <img :src="baseApi + sd.picture" :alt="sd.session_name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!sd.picture">
                                            <svg class="w-5 h-5 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </template>
                                    </div>
                                </td>
                                <!-- Session Name + Tagline -->
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-surface-800 dark:text-white leading-snug" x-text="sd.session_name"></p>
                                    <p x-show="sd.session_tagline" class="text-xs text-surface-400 dark:text-surface-500 mt-0.5 line-clamp-1" x-text="sd.session_tagline"></p>
                                    <!-- Category shown inline on mobile -->
                                    <span class="sm:hidden inline-flex items-center mt-1 rounded-full bg-primary-50 dark:bg-primary-500/10 px-2 py-0.5 text-[10px] font-semibold text-primary-600 dark:text-primary-400"
                                          x-text="getCategoryName(sd.category_id)"></span>
                                </td>
                                <!-- Category (desktop) -->
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    <span class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-500/10 px-2.5 py-1 text-xs font-semibold text-primary-600 dark:text-primary-400"
                                          x-text="getCategoryName(sd.category_id)"></span>
                                </td>
                                <!-- Facilities -->
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="fid in (sd.facility_ids || [])" :key="fid">
                                            <span class="inline-flex items-center rounded-full bg-surface-100 dark:bg-surface-800 px-2 py-0.5 text-[10px] font-medium text-surface-600 dark:text-surface-400"
                                                  x-text="getFacilityName(fid)"></span>
                                        </template>
                                        <span x-show="!sd.facility_ids || sd.facility_ids.length === 0"
                                              class="text-[10px] text-surface-400 italic">None</span>
                                    </div>
                                </td>
                                <!-- Status -->
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold uppercase"
                                          :class="sd.is_active == 1
                                              ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                              : 'bg-surface-100 dark:bg-surface-800 text-surface-500 dark:text-surface-400'"
                                          x-text="sd.is_active == 1 ? 'Active' : 'Inactive'"></span>
                                </td>
                                <!-- Actions -->
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <button x-on:click="openEditForm(sd)"
                                                class="rounded-lg p-1.5 text-surface-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button x-on:click="deleteSession(sd.id)"
                                                class="rounded-lg p-1.5 text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty state row -->
                        <template x-if="sessions.length === 0">
                            <tr>
                                <td colspan="6" class="px-4 py-16 text-center">
                                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-100 dark:bg-surface-800 mb-3">
                                        <svg class="w-7 h-7 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-semibold text-surface-700 dark:text-surface-200 mb-1">No sessions yet</h4>
                                    <p class="text-xs text-surface-400 mb-4">Create your first session detail to get started.</p>
                                    <button x-on:click="openAddForm()"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Add First Session
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Table footer: row count -->
            <div x-show="sessions.length > 0" class="border-t border-surface-100 dark:border-surface-800 px-4 py-2.5 bg-surface-50/40 dark:bg-surface-800/20">
                <p class="text-xs text-surface-400">
                    Showing <span class="font-semibold text-surface-600 dark:text-surface-300" x-text="sessions.length"></span>
                    <span x-text="sessions.length === 1 ? 'session' : 'sessions'"></span>
                </p>
            </div>
        </div>
    </div>

    <!-- ===== ADD / EDIT FORM VIEW ===== -->
    <div x-show="mode === 'form'" x-cloak>
        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <!-- Form Header -->
            <div class="flex items-center justify-between border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg shadow-sm"
                         :class="editingId ? 'bg-gradient-to-br from-amber-500 to-amber-600' : 'bg-gradient-to-br from-primary-500 to-primary-600'">
                        <svg x-show="!editingId" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <svg x-show="editingId" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100" x-text="editingId ? 'Edit Session Detail' : 'Add New Session Detail'"></h3>
                </div>
                <button type="button" x-on:click="closeForm()"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-surface-500 hover:text-surface-700 dark:hover:text-surface-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to List
                </button>
            </div>

            <!-- Form Body -->
            <form x-on:submit.prevent="saveSession()" class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Main Fields -->
                    <div class="lg:col-span-2 space-y-5">

                        <!-- Category + Session Name row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Category <span class="text-red-500">*</span></label>
                                <select x-model="form.category_id" required
                                        class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                                    <option value="">Select Category</option>
                                    <template x-for="cat in categories" :key="cat.id">
                                        <option :value="cat.id" x-text="cat.name"></option>
                                    </template>
                                </select>
                                <a :href="baseApi + '/admin/categories'" target="_blank"
                                   class="inline-flex items-center gap-1 mt-1.5 text-xs text-primary-500 hover:text-primary-600 font-medium transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add New Category
                                </a>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Session Name <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.session_name" required placeholder="e.g. Saturday Morning Round Robin"
                                       class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                            </div>
                        </div>

                        <!-- Tagline -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Tagline</label>
                            <input type="text" x-model="form.session_tagline" placeholder="A short catchy tagline for players..."
                                   class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                            <p class="mt-1 text-[11px] text-surface-400">Brief subtitle shown to players (max 500 characters)</p>
                        </div>

                        <!-- Description with Quill Editor -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Description</label>
                            <div class="sd-quill-editor rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                                <div x-ref="quillEditor"></div>
                            </div>
                            <p class="mt-1 text-[11px] text-surface-400">Use the toolbar to format text, add links, lists, etc.</p>
                        </div>

                        <!-- Active Toggle -->
                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-surface-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-surface-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-surface-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-surface-600 peer-checked:bg-primary-600"></div>
                            </label>
                            <div>
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-200" x-text="form.is_active ? 'Active' : 'Inactive'"></span>
                                <p class="text-[11px] text-surface-400">Active sessions are available for linking to session types</p>
                            </div>
                        </div>

                        <!-- Facility Assignment -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Assign to Facilities</label>
                            <p class="text-[11px] text-surface-400 mb-3">Select the facilities where this session should be available.</p>
                            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/20 p-3 space-y-2 max-h-48 overflow-y-auto">
                                <!-- Select All -->
                                <label x-show="allFacilities.length > 1" class="flex items-center gap-2.5 cursor-pointer pb-2 mb-2 border-b border-surface-200 dark:border-surface-700">
                                    <input type="checkbox"
                                           :checked="form.facility_ids.length === allFacilities.length && allFacilities.length > 0"
                                           x-on:change="form.facility_ids = $event.target.checked ? allFacilities.map(f => String(f.id)) : []"
                                           class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm font-semibold text-surface-700 dark:text-surface-200">Select All</span>
                                </label>
                                <template x-for="fac in allFacilities" :key="fac.id">
                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="checkbox" :value="String(fac.id)" x-model="form.facility_ids"
                                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-surface-600 dark:text-surface-300" x-text="fac.name"></span>
                                    </label>
                                </template>
                                <p x-show="allFacilities.length === 0" class="text-xs text-surface-400 italic py-2 text-center">No facilities found</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Featured Image -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Featured Image</label>
                            <p class="text-[11px] text-surface-400 mb-3">Recommended: 1200×630px (16:9 ratio). Works great on desktop and mobile.</p>

                            <!-- Image Preview / Upload Area -->
                            <div class="relative">
                                <!-- Existing image preview -->
                                <div x-show="imagePreview || form.picture" class="relative rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700">
                                    <div class="aspect-[16/9] bg-surface-100 dark:bg-surface-800">
                                        <img :src="imagePreview || (form.picture ? baseApi + form.picture : '')"
                                             class="w-full h-full object-cover" alt="Featured image preview">
                                    </div>
                                    <div class="absolute inset-0 bg-black/0 hover:bg-black/40 transition-colors flex items-center justify-center opacity-0 hover:opacity-100">
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer inline-flex items-center gap-1.5 rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-surface-700 hover:bg-white transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                Replace
                                                <input type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" x-on:change="onImageSelect($event)">
                                            </label>
                                            <button type="button" x-on:click="removeImage()"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-red-500/90 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-600 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Drop zone (when no image) -->
                                <div x-show="!imagePreview && !form.picture"
                                     class="sd-image-drop rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-800/50 aspect-[16/9] flex flex-col items-center justify-center cursor-pointer hover:border-primary-400 hover:bg-primary-50/30 dark:hover:bg-primary-500/5 transition-colors"
                                     :class="{ 'dragover': isDragOver }"
                                     x-on:click="$refs.imageInput.click()"
                                     x-on:dragover.prevent="isDragOver = true"
                                     x-on:dragleave.prevent="isDragOver = false"
                                     x-on:drop.prevent="isDragOver = false; onImageDrop($event)">
                                    <svg class="w-10 h-10 text-surface-300 dark:text-surface-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Click to upload or drag & drop</p>
                                    <p class="text-[10px] text-surface-400 mt-0.5">JPEG, PNG, GIF, WebP &middot; Max 5MB</p>
                                </div>
                                <input type="file" x-ref="imageInput" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" x-on:change="onImageSelect($event)">
                            </div>

                            <!-- Upload progress -->
                            <div x-show="uploadingImage" x-cloak class="mt-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-surface-200 dark:bg-surface-700 overflow-hidden">
                                        <div class="h-full bg-primary-500 rounded-full animate-pulse" style="width: 70%"></div>
                                    </div>
                                    <span class="text-[10px] text-surface-400">Uploading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center gap-3 pt-6 mt-6 border-t border-surface-100 dark:border-surface-800">
                    <button type="submit" :disabled="saving || !form.session_name || !form.category_id"
                            class="inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold text-white shadow-soft hover:shadow-medium transition-all disabled:opacity-50"
                            :class="editingId ? 'bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800' : 'bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800'">
                        <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <svg x-show="!saving && !editingId" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <svg x-show="!saving && editingId" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="saving ? 'Saving...' : (editingId ? 'Update Session Detail' : 'Create Session Detail')"></span>
                    </button>
                    <button type="button" x-on:click="closeForm()"
                            class="rounded-xl border border-surface-200 dark:border-surface-700 px-6 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function sessionDetailsList() {
    const baseApi = '<?= ($baseUrl ?? '') ?>';
    return {
        sessions: [],
        categories: [],
        loading: false,
        saving: false,
        search: '',
        filterCategory: '',
        facilityId: null,
        mode: 'list',
        editingId: null,
        baseApi: baseApi,
        dataLoaded: false,
        allFacilities: [],
        quillInstance: null,
        imagePreview: null,
        imageFile: null,
        uploadingImage: false,
        isDragOver: false,

        form: {
            category_id: '',
            session_name: '',
            session_tagline: '',
            description: '',
            picture: '',
            is_active: true,
            facility_ids: [],
        },

        async loadData() {
            this.loading = true;
            this.dataLoaded = true;
            try {
                const [catRes, sesRes, facRes] = await Promise.all([
                    authFetch(baseApi + '/api/categories?per_page=100'),
                    authFetch(baseApi + '/api/session-details?per_page=100'
                        + (this.filterCategory ? '&category_id=' + this.filterCategory : '')
                        + (this.search ? '&search=' + encodeURIComponent(this.search) : '')),
                    authFetch(baseApi + '/api/facilities?per_page=100'),
                ]);
                const catJson = await catRes.json();
                const sesJson = await sesRes.json();
                const facJson = await facRes.json();
                this.categories = catJson.data || [];
                this.sessions = sesJson.data || [];
                this.allFacilities = facJson.data || [];
            } catch (e) { console.error('Failed to load session details', e); }
            this.loading = false;
        },

        getCategoryName(catId) {
            if (!catId) return 'Uncategorized';
            const cat = this.categories.find(c => c.id == catId);
            return cat ? cat.name : 'Category #' + catId;
        },

        getFacilityName(facId) {
            const fac = this.allFacilities.find(f => f.id == facId);
            return fac ? fac.name : 'Facility #' + facId;
        },

        resetForm() {
            this.form = { category_id: '', session_name: '', session_tagline: '', description: '', picture: '', is_active: true, facility_ids: [] };
            this.imagePreview = null;
            this.imageFile = null;
            if (this.quillInstance) {
                this.quillInstance.root.innerHTML = '';
            }
        },

        initQuill(content) {
            this.$nextTick(() => {
                const editorEl = this.$refs.quillEditor;
                if (!editorEl) return;

                // Quill 2.0 inserts .ql-toolbar as a sibling BEFORE the container.
                // We must remove it and reset the container or we get double toolbars.
                const wrapper = editorEl.parentElement;
                wrapper?.querySelectorAll('.ql-toolbar').forEach(el => el.remove());
                editorEl.innerHTML = '';
                editorEl.className = '';
                this.quillInstance = null;

                this.quillInstance = new Quill(editorEl, {
                    theme: 'snow',
                    placeholder: 'Write a detailed description for players...',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link'],
                            ['blockquote'],
                            ['clean']
                        ]
                    }
                });

                if (content) {
                    this.quillInstance.root.innerHTML = content;
                }

                this.quillInstance.on('text-change', () => {
                    const html = this.quillInstance.root.innerHTML;
                    this.form.description = (html === '<p><br></p>') ? '' : html;
                });
            });
        },

        openAddForm() {
            this.editingId = null;
            this.resetForm();
            this.mode = 'form';
            this.initQuill('');
        },

        openEditForm(sd) {
            this.editingId = sd.id;
            this.form = {
                category_id: sd.category_id ? String(sd.category_id) : '',
                session_name: sd.session_name || '',
                session_tagline: sd.session_tagline || '',
                description: sd.description || '',
                picture: sd.picture || '',
                is_active: sd.is_active == 1,
                facility_ids: (sd.facility_ids || []).map(String),
            };
            this.imagePreview = null;
            this.imageFile = null;
            this.mode = 'form';
            this.initQuill(sd.description || '');
        },

        closeForm() {
            this.mode = 'list';
            this.editingId = null;
            this.resetForm();
        },

        onImageSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.handleImageFile(file);
            event.target.value = '';
        },

        onImageDrop(event) {
            const file = event.dataTransfer.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Please drop an image file', type: 'error' } }));
                return;
            }
            this.handleImageFile(file);
        },

        handleImageFile(file) {
            if (file.size > 5 * 1024 * 1024) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Image must be under 5MB', type: 'error' } }));
                return;
            }
            const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowed.includes(file.type)) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Only JPEG, PNG, GIF, WebP images allowed', type: 'error' } }));
                return;
            }
            this.imageFile = file;
            const reader = new FileReader();
            reader.onload = (e) => { this.imagePreview = e.target.result; };
            reader.readAsDataURL(file);
        },

        removeImage() {
            this.imagePreview = null;
            this.imageFile = null;
            this.form.picture = '';
        },

        async uploadImage(sessionId) {
            if (!this.imageFile) return;
            this.uploadingImage = true;
            try {
                const formData = new FormData();
                formData.append('picture', this.imageFile);
                const res = await authFetch(baseApi + '/api/session-details/' + sessionId + '/picture', {
                    method: 'POST',
                    body: formData,
                });
                const json = await res.json();
                if (!res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Image upload failed', type: 'error' } }));
                }
            } catch (e) {
                console.error('Image upload failed', e);
            }
            this.uploadingImage = false;
        },

        async removeServerImage(sessionId) {
            try {
                await authFetch(baseApi + '/api/session-details/' + sessionId + '/picture', { method: 'DELETE' });
            } catch (e) { console.error('Remove image failed', e); }
        },

        async saveSession() {
            this.saving = true;
            try {
                // Grab description from Quill
                if (this.quillInstance) {
                    const html = this.quillInstance.root.innerHTML;
                    this.form.description = (html === '<p><br></p>') ? '' : html;
                }

                const payload = {
                    category_id: parseInt(this.form.category_id),
                    session_name: this.form.session_name,
                    session_tagline: this.form.session_tagline,
                    description: this.form.description,
                    is_active: this.form.is_active,
                    facility_ids: this.form.facility_ids.map(Number),
                };

                const url = this.editingId
                    ? baseApi + '/api/session-details/' + this.editingId
                    : baseApi + '/api/session-details';
                const method = this.editingId ? 'PUT' : 'POST';

                const res = await authFetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();

                if (res.ok) {
                    const recordId = this.editingId || json.data?.id || json.id;

                    // Handle image upload/removal
                    if (this.imageFile && recordId) {
                        await this.uploadImage(recordId);
                    } else if (!this.form.picture && this.editingId && !this.imageFile) {
                        // User removed the image
                        await this.removeServerImage(this.editingId);
                    }

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: this.editingId ? 'Session detail updated!' : 'Session detail created!', type: 'success' }
                    }));
                    this.closeForm();
                    this.loadData();
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: json.message || 'Validation failed', type: 'error' }
                    }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
            this.saving = false;
        },

        async deleteSession(id) {
            if (!confirm('Delete this session detail? Session types linked to it will be unlinked.')) return;
            try {
                const res = await authFetch(baseApi + '/api/session-details/' + id, { method: 'DELETE' });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Session detail deleted', type: 'success' } }));
                    this.loadData();
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
        }
    };
}
</script>
