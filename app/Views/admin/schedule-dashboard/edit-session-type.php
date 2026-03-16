<!-- Edit Session Type Tab -->
<div x-data="editSessionType()" x-effect="
    const dash = $el.closest('[x-data]')._x_dataStack?.find(d => Array.isArray(d.facilities));
    if (dash && dash.facilityId && dash.facilityId !== facilityId) { facilityId = dash.facilityId; loadFormData(); }
    if (dash && dash.editingSessionTypeId && dash.editingSessionTypeId !== editId) { editId = dash.editingSessionTypeId; loadSessionType(); }
">
    <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Edit Session Type</h3>
                <span x-show="form.title" class="text-sm text-surface-400" x-text="'— ' + form.title"></span>
            </div>
            <button type="button" x-on:click="goBack()" class="inline-flex items-center gap-1.5 text-xs font-medium text-surface-500 hover:text-surface-700 dark:hover:text-surface-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to List
            </button>
        </div>

        <div class="p-6">
            <!-- Loading -->
            <div x-show="loadingData" class="py-8 text-center">
                <div class="inline-block h-8 w-8 rounded-full border-[3px] border-surface-200 border-t-primary-500 animate-spin"></div>
                <p class="mt-2 text-sm text-surface-400">Loading session type...</p>
            </div>

            <form x-show="!loadingData" x-on:submit.prevent="submitForm()" class="space-y-6">

                <!-- ===== CATEGORY ===== -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Category</label>
                    <select x-model="form.category_id" x-on:change="onCategoryChange()"
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

                <!-- ===== SESSION NAME SOURCE ===== -->
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/20 p-4 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-surface-500">Session Name</p>

                    <!-- No session name needed checkbox -->
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="noSessionName"
                               x-on:change="if(noSessionName) { form.session_id = ''; form.title = getSelectedCategoryName(); sessionNameMode = 'manual'; } else { form.title = ''; }"
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-surface-600 dark:text-surface-300">No session name needed <span class="text-xs text-surface-400">(e.g. Private Court Rental)</span></span>
                    </label>

                    <div x-show="!noSessionName" class="space-y-3">
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" value="pick" x-model="sessionNameMode" class="border-surface-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-surface-600 dark:text-surface-300">Pick from Session Details</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" value="manual" x-model="sessionNameMode" class="border-surface-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-surface-600 dark:text-surface-300">Enter Manually</span>
                            </label>
                        </div>

                        <!-- Pick from session details -->
                        <div x-show="sessionNameMode === 'pick'" class="space-y-2">
                            <template x-if="loadingSessions">
                                <p class="text-xs text-surface-400 italic">Loading sessions...</p>
                            </template>
                            <template x-if="!loadingSessions && categorySessions.length > 0">
                                <div>
                                    <select x-model="form.session_id" x-on:change="onSessionSelect()"
                                            class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                        <option value="">Select Session...</option>
                                        <template x-for="ses in categorySessions" :key="ses.id">
                                            <option :value="ses.id" x-text="ses.session_name"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>
                            <template x-if="!loadingSessions && categorySessions.length === 0 && form.category_id">
                                <div class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 rounded-lg p-2.5">
                                    No session details found for this category.
                                    <button type="button" x-on:click="sessionNameMode = 'manual'" class="underline font-semibold">Enter a name manually</button>.
                                </div>
                            </template>
                        </div>

                        <!-- Manual entry -->
                        <div x-show="sessionNameMode === 'manual'">
                            <input type="text" x-model="form.title" placeholder="e.g. Saturday Morning Round Robin"
                                   class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                        </div>
                    </div>

                    <div x-show="form.title" class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-surface-600 dark:text-surface-300">Title: <strong x-text="form.title"></strong></span>
                    </div>
                </div>

                <!-- ===== INTERNAL TITLE ===== -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Internal Title</label>
                    <input type="text" x-model="form.internal_title" placeholder="Internal title visible to staff only..."
                           class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                    <p class="mt-1 text-[11px] text-surface-400">Optional. Only visible to staff for internal reference.</p>
                </div>

                <!-- ===== ADDITIONAL SETTINGS ===== -->
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="showSettings" class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-semibold uppercase tracking-wider text-surface-500">Additional Settings</span>
                    </label>
                    <div x-show="showSettings" x-cloak class="mt-3 space-y-3 rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/20 p-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" :checked="settings.auto_reserve_partner === '1'" @change="settings.auto_reserve_partner = $event.target.checked ? '1' : '0'"
                                   class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Auto-Reserve Partner Slot</span>
                                <p class="text-xs text-surface-400">When a player books a slot, automatically reserve a second slot for their partner.</p>
                            </div>
                        </label>
                        <!-- Auto-Generate (series_rolling only) -->
                        <label x-show="form.session_type === 'series_rolling'" class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" :checked="settings.auto_generate === '1'" @change="settings.auto_generate = $event.target.checked ? '1' : '0'"
                                   class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Auto-Generate Sessions</span>
                                <p class="text-xs text-surface-400">Continuously generate future sessions to maintain the occurrence count. Requires a cron job.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- ===== RESOURCES ===== -->
                <template x-if="resources.length > 0">
                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500">Resources</p>
                        <template x-for="resource in resources" :key="resource.id">
                            <div class="rounded-xl border border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/20 p-4">
                                <p class="text-sm font-semibold text-surface-700 dark:text-surface-200 mb-2" x-text="resource.name"></p>
                                <template x-if="resource.field_type === 'checkbox'">
                                    <div class="space-y-2">
                                        <template x-for="val in resource.values" :key="val.id">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" :value="val.id" x-model="form.resource_value_ids"
                                                       class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                                <span class="text-sm text-surface-600 dark:text-surface-300" x-text="val.name"></span>
                                            </label>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="resource.field_type === 'selectbox'">
                                    <select x-on:change="toggleResourceSelect($event, resource)"
                                            class="w-full sm:w-64 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                        <option value="">Select...</option>
                                        <template x-for="val in resource.values" :key="val.id">
                                            <option :value="val.id" :selected="form.resource_value_ids.includes(String(val.id))" x-text="val.name"></option>
                                        </template>
                                    </select>
                                </template>
                                <template x-if="resource.field_type === 'radio'">
                                    <div class="space-y-2">
                                        <template x-for="val in resource.values" :key="val.id">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" :name="'resource_' + resource.id" :value="val.id"
                                                       x-on:change="toggleResourceRadio(val.id, resource)"
                                                       :checked="form.resource_value_ids.includes(String(val.id))"
                                                       class="border-surface-300 text-primary-600 focus:ring-primary-500">
                                                <span class="text-sm text-surface-600 dark:text-surface-300" x-text="val.name"></span>
                                            </label>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="resource.field_type === 'input'">
                                    <input type="text" :placeholder="'Enter ' + resource.name + '...'"
                                           :value="getResourceInputValue(resource.id)"
                                           x-on:input="setResourceInputValue(resource.id, $event.target.value)"
                                           class="w-full sm:w-64 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                </template>
                                <template x-if="resource.field_type !== 'input' && resource.values.length === 0">
                                    <p class="text-xs text-surface-400 italic">No values defined for this resource.</p>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- ===== SESSION TYPE ===== -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-2">Session Type</label>
                    <div class="space-y-2">
                        <label class="flex items-start gap-3 cursor-pointer rounded-xl border border-surface-200 dark:border-surface-700 p-3 hover:border-primary-300 transition-colors"
                               :class="form.session_type === 'class' ? 'border-primary-400 bg-primary-50/50 dark:bg-primary-500/5' : ''">
                            <input type="radio" name="session_type" value="class" x-model="form.session_type" class="mt-0.5 border-surface-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <span class="text-sm font-semibold text-surface-700 dark:text-surface-200">SINGLE SESSION</span>
                                <p class="text-xs text-surface-400">Clients can book one session at a time</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer rounded-xl border border-surface-200 dark:border-surface-700 p-3 hover:border-primary-300 transition-colors"
                               :class="form.session_type === 'series' ? 'border-primary-400 bg-primary-50/50 dark:bg-primary-500/5' : ''">
                            <input type="radio" name="session_type" value="series" x-model="form.session_type" class="mt-0.5 border-surface-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <span class="text-sm font-semibold text-surface-700 dark:text-surface-200">MULTI-WEEK (Traditional)</span>
                                <p class="text-xs text-surface-400">Clients must sign up for all sessions in the series</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer rounded-xl border border-surface-200 dark:border-surface-700 p-3 hover:border-primary-300 transition-colors"
                               :class="form.session_type === 'series_rolling' ? 'border-primary-400 bg-primary-50/50 dark:bg-primary-500/5' : ''">
                            <input type="radio" name="session_type" value="series_rolling" x-model="form.session_type" class="mt-0.5 border-surface-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <span class="text-sm font-semibold text-surface-700 dark:text-surface-200">MULTI-WEEK (Rolling Signups)</span>
                                <p class="text-xs text-surface-400">Clients can sign up anytime there is an opening</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- ===== CAPACITY & DURATION ===== -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Capacity</label>
                        <input type="number" x-model="form.capacity" min="1" placeholder="e.g. 16"
                               class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Duration (minutes)</label>
                        <input type="number" x-model="form.duration" min="1" placeholder="e.g. 90"
                               class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                    </div>
                </div>

                <!-- ===== PRICING ===== -->
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/20 p-4 space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-surface-500">Pricing</p>

                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1.5">Standard Price ($)</label>
                        <input type="number" x-model="form.standard_price" min="0" step="0.01" placeholder="0.00"
                               class="w-full sm:w-48 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-2">Pricing Mode</label>
                        <div class="flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" value="single" x-model="form.pricing_mode" class="border-surface-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-surface-600 dark:text-surface-300">Single Price</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" value="time_based" x-model="form.pricing_mode" class="border-surface-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-surface-600 dark:text-surface-300">Time-Based (Early Bird)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" value="user_based" x-model="form.pricing_mode" class="border-surface-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-surface-600 dark:text-surface-300">User-Based</span>
                            </label>
                        </div>
                    </div>

                    <!-- Time-Based -->
                    <div x-show="form.pricing_mode === 'time_based'" x-cloak class="space-y-3">
                        <p class="text-xs text-surface-400">Set different prices based on how many days before the event.</p>
                        <template x-for="(rule, idx) in form.pricing_rules" :key="idx">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] text-surface-400 mb-1">Days Before Event</label>
                                        <input type="number" x-model="rule.start_offset_days" min="0" placeholder="e.g. 7"
                                               class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-surface-400 mb-1">Price ($)</label>
                                        <input type="number" x-model="rule.price" min="0" step="0.01" placeholder="0.00"
                                               class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                    </div>
                                </div>
                                <button type="button" x-on:click="form.pricing_rules.splice(idx, 1)"
                                        class="rounded-lg p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors mt-4">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" x-on:click="form.pricing_rules.push({ pricing_type: 'time_based', start_offset_days: '', price: '' })"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-primary-500 hover:text-primary-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Time Rule
                        </button>
                    </div>

                    <!-- User-Based -->
                    <div x-show="form.pricing_mode === 'user_based'" x-cloak class="space-y-3">
                        <p class="text-xs text-surface-400">Set different prices based on number of registrations.</p>
                        <template x-for="(rule, idx) in form.pricing_rules" :key="idx">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] text-surface-400 mb-1">Up to # Users</label>
                                        <input type="number" x-model="rule.max_users" min="1" placeholder="e.g. 10"
                                               class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-surface-400 mb-1">Price ($)</label>
                                        <input type="number" x-model="rule.price" min="0" step="0.01" placeholder="0.00"
                                               class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                    </div>
                                </div>
                                <button type="button" x-on:click="form.pricing_rules.splice(idx, 1)"
                                        class="rounded-lg p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors mt-4">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" x-on:click="form.pricing_rules.push({ pricing_type: 'user_based', max_users: '', price: '' })"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-primary-500 hover:text-primary-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add User Rule
                        </button>
                    </div>

                    <!-- Rolling Prices -->
                    <div x-show="form.session_type === 'series_rolling'" x-cloak class="space-y-3 rounded-xl border border-indigo-200 dark:border-indigo-500/30 bg-indigo-50/30 dark:bg-indigo-500/5 p-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Rolling Pricing</p>
                        </div>
                        <p class="text-xs text-surface-400">Set different prices for different signup durations.</p>
                        <template x-for="(rp, idx) in form.rolling_prices" :key="idx">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] text-surface-400 mb-1"># of Weeks</label>
                                        <input type="number" x-model="rp.number_of_weeks" min="1" placeholder="e.g. 4"
                                               class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-surface-400 mb-1">Price ($)</label>
                                        <input type="number" x-model="rp.price" min="0" step="0.01" placeholder="0.00"
                                               class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                    </div>
                                </div>
                                <button type="button" x-on:click="form.rolling_prices.splice(idx, 1)"
                                        class="rounded-lg p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors mt-4">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" x-on:click="form.rolling_prices.push({ number_of_weeks: '', price: '' })"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-500 hover:text-indigo-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Rolling Price
                        </button>
                    </div>
                </div>

                <!-- ===== ACCESS ===== -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-2">Access</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="edit_access" :value="0" x-model.number="form.private" class="border-surface-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-surface-600 dark:text-surface-300">Public</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="edit_access" :value="1" x-model.number="form.private" class="border-surface-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-surface-600 dark:text-surface-300">Private</span>
                        </label>
                    </div>
                </div>

                <!-- ===== BOOLEAN TOGGLES ===== -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 cursor-pointer rounded-xl border border-surface-200 dark:border-surface-700 p-3">
                        <input type="checkbox" x-model="form.is_active" class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Active</span>
                            <p class="text-xs text-surface-400">Session type is bookable</p>
                        </div>
                    </label>
                </div>

                <!-- ===== REGISTRATION FORM FIELDS TOGGLE ===== -->
                <div>
                    <label class="flex items-center gap-3 cursor-pointer rounded-xl border border-surface-200 dark:border-surface-700 p-3"
                           :class="showFormFields ? 'border-violet-400 bg-violet-50/50 dark:bg-violet-500/5' : ''">
                        <input type="checkbox" x-model="showFormFields" class="rounded border-surface-300 text-violet-600 focus:ring-violet-500">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Custom Registration Fields</span>
                            <p class="text-xs text-surface-400">Add custom fields shown to clients during registration</p>
                        </div>
                    </label>
                </div>

                <!-- ===== REGISTRATION FORM FIELDS SECTION ===== -->
                <div x-show="showFormFields" x-transition x-cloak class="rounded-xl border border-violet-200 dark:border-violet-500/30 bg-violet-50/30 dark:bg-violet-500/5 p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 shadow-sm">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-surface-800 dark:text-surface-100">Registration Form Fields</h4>
                                <p class="text-xs text-surface-400">Define custom fields shown to clients during registration.</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-surface-100 dark:bg-surface-800 px-2 py-0.5 text-[11px] font-bold text-surface-500" x-text="formFields.length + ' fields'"></span>
                        </div>
                        <button type="button" x-on:click="openFieldModal()"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-violet-600 to-violet-700 px-4 py-2 text-xs font-semibold text-white hover:from-violet-700 hover:to-violet-800 shadow-sm transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Field
                        </button>
                    </div>

                    <!-- Loading -->
                    <div x-show="loadingFormFields" class="py-4 text-center">
                        <div class="inline-block h-6 w-6 rounded-full border-2 border-surface-200 border-t-violet-500 animate-spin"></div>
                    </div>

                    <!-- Fields list -->
                    <div x-show="!loadingFormFields" class="space-y-2">
                        <template x-for="(ff, idx) in formFields" :key="ff.id">
                            <div class="flex items-center justify-between rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800/50 px-4 py-3 hover:border-violet-200 dark:hover:border-violet-500/30 transition-colors group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-500/10 text-xs font-bold text-violet-600 dark:text-violet-400" x-text="idx + 1"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-surface-800 dark:text-surface-100" x-text="ff.field_label"></p>
                                        <div class="flex items-center gap-2 mt-0.5 text-xs text-surface-400">
                                            <span class="inline-flex items-center rounded-full bg-surface-100 dark:bg-surface-700 px-1.5 py-0.5 font-medium" x-text="getFieldTypeLabel(ff.field_type)"></span>
                                            <span x-show="ff.is_required" class="text-amber-500 font-semibold">Required</span>
                                            <span x-show="ff.field_options && ff.field_options.length > 0" x-text="ff.field_options.length + ' options'"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" x-on:click="openFieldModal(ff)" title="Edit"
                                            class="rounded-lg p-1.5 text-surface-400 hover:text-violet-600 hover:bg-violet-50 dark:hover:bg-violet-500/10 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" x-on:click="deleteFormField(ff.id)" title="Delete"
                                            class="rounded-lg p-1.5 text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <template x-if="formFields.length === 0 && !loadingFormFields">
                            <div class="py-6 text-center rounded-xl border border-dashed border-surface-200 dark:border-surface-700">
                                <svg class="w-10 h-10 mx-auto text-surface-300 dark:text-surface-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm text-surface-400">No custom fields defined yet.</p>
                                <p class="text-xs text-surface-400 mt-1">Click "Add Field" to create registration form fields for clients.</p>
                            </div>
                        </template>
                    </div>
                </div>
                <!-- END REGISTRATION FORM FIELDS SECTION -->

                <!-- ===== SUBMIT ===== -->
                <div class="flex items-center gap-3 pt-4 border-t border-surface-100 dark:border-surface-800">
                    <button type="submit" :disabled="submitting || !form.title"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-2.5 text-sm font-semibold text-white hover:from-amber-700 hover:to-amber-800 shadow-soft hover:shadow-medium transition-all disabled:opacity-50">
                        <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="submitting ? 'Saving...' : 'Update Session Type'"></span>
                    </button>
                    <button type="button" x-on:click="goBack()"
                            class="rounded-xl border border-surface-200 dark:border-surface-700 px-6 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>

            <!-- ===== SCHEDULED CLASSES SECTION ===== -->
            <div x-show="editId && !loadingData" class="mt-8 pt-6 border-t border-surface-200 dark:border-surface-800">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="font-semibold text-surface-800 dark:text-surface-100">Scheduled Classes</h4>
                        <span class="inline-flex items-center rounded-full bg-surface-100 dark:bg-surface-800 px-2 py-0.5 text-[11px] font-bold text-surface-500" x-text="classes.length + ' total'"></span>
                    </div>
                    <button type="button" x-on:click="openScheduleModal()"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-2 text-xs font-semibold text-white hover:from-emerald-700 hover:to-emerald-800 shadow-sm transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Schedule Session
                    </button>
                </div>

                <!-- Loading classes -->
                <div x-show="loadingClasses" class="py-4 text-center">
                    <div class="inline-block h-6 w-6 rounded-full border-2 border-surface-200 border-t-emerald-500 animate-spin"></div>
                </div>

                <!-- Classes list -->
                <div x-show="!loadingClasses">
                    <!-- Upcoming -->
                    <template x-if="upcomingClasses.length > 0">
                        <div class="mb-4">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-surface-400 mb-2">Upcoming Sessions</p>
                            <div class="space-y-1.5">
                                <template x-for="cls in upcomingClasses" :key="cls.id">
                                    <div class="flex items-center justify-between rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800/50 px-4 py-3 hover:border-emerald-200 dark:hover:border-emerald-500/30 transition-colors group">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="flex flex-col items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1.5 min-w-[52px]">
                                                <span class="text-[10px] font-bold uppercase text-emerald-600 dark:text-emerald-400" x-text="formatClassMonth(cls.scheduled_at)"></span>
                                                <span class="text-lg font-bold leading-tight text-emerald-700 dark:text-emerald-300" x-text="formatClassDay(cls.scheduled_at)"></span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-surface-800 dark:text-surface-100" x-text="formatClassTime(cls.scheduled_at)"></p>
                                                <div class="flex items-center gap-3 mt-0.5 text-xs text-surface-400">
                                                    <span x-text="'Spots: ' + cls.slots_available + '/' + cls.slots"></span>
                                                    <span x-show="cls.coach_first_name" x-text="'Coach: ' + (cls.coach_first_name || '') + ' ' + (cls.coach_last_name || '')"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button x-on:click="deleteClass(cls.id)" title="Delete"
                                                    class="rounded-lg p-1.5 text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Past -->
                    <template x-if="pastClasses.length > 0">
                        <div>
                            <button type="button" x-on:click="showPastClasses = !showPastClasses"
                                    class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 mb-2 transition-colors">
                                <svg class="w-3 h-3 transition-transform" :class="showPastClasses ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                <span x-text="'Past Sessions (' + pastClasses.length + ')'"></span>
                            </button>
                            <div x-show="showPastClasses" x-transition class="space-y-1.5">
                                <template x-for="cls in pastClasses" :key="cls.id">
                                    <div class="flex items-center justify-between rounded-xl border border-surface-100 dark:border-surface-700/50 bg-surface-50 dark:bg-surface-800/30 px-4 py-3 opacity-70">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="flex flex-col items-center justify-center rounded-lg bg-surface-100 dark:bg-surface-700/50 px-2.5 py-1.5 min-w-[52px]">
                                                <span class="text-[10px] font-bold uppercase text-surface-400" x-text="formatClassMonth(cls.scheduled_at)"></span>
                                                <span class="text-lg font-bold leading-tight text-surface-500" x-text="formatClassDay(cls.scheduled_at)"></span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-surface-500" x-text="formatClassTime(cls.scheduled_at)"></p>
                                                <div class="flex items-center gap-3 mt-0.5 text-xs text-surface-400">
                                                    <span x-text="'Spots: ' + cls.slots_available + '/' + cls.slots"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Empty state -->
                    <template x-if="classes.length === 0 && !loadingClasses">
                        <div class="py-6 text-center rounded-xl border border-dashed border-surface-200 dark:border-surface-700">
                            <svg class="w-10 h-10 mx-auto text-surface-300 dark:text-surface-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm text-surface-400">No classes scheduled yet.</p>
                            <p class="text-xs text-surface-400 mt-1">Click "Schedule Session" to add class dates.</p>
                        </div>
                    </template>
                </div>
            </div>
            <!-- END SCHEDULED CLASSES SECTION -->

            <!-- ===== FORM FIELD MODAL ===== -->
            <div x-show="fieldModal.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.4)" x-on:keydown.escape.window="fieldModal.show = false">
                <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-800 w-full max-w-lg p-6" x-on:click.outside="fieldModal.show = false">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-surface-800 dark:text-white" x-text="fieldModal.editingId ? 'Edit Field' : 'Add Field'"></h3>
                        <button type="button" x-on:click="fieldModal.show = false" class="rounded-lg p-1 text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Field Label -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Field Label</label>
                            <input type="text" x-model="fieldModal.field_label" x-on:input="autoFieldName()" placeholder="e.g. Skill Level"
                                   class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 dark:text-white">
                        </div>

                        <!-- Field Name (auto-generated) -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Field Name (system key)</label>
                            <input type="text" x-model="fieldModal.field_name" placeholder="e.g. skill_level"
                                   class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 dark:text-white font-mono text-xs">
                            <p class="mt-1 text-[11px] text-surface-400">Auto-generated from label. Only lowercase letters, numbers, and underscores.</p>
                        </div>

                        <!-- Field Type -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Field Type</label>
                            <select x-model="fieldModal.field_type"
                                    class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 dark:text-white">
                                <template x-for="ft in fieldTypes" :key="ft.value">
                                    <option :value="ft.value" x-text="ft.label"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Options (for select, checkbox, radio) -->
                        <div x-show="needsOptions(fieldModal.field_type)" x-transition>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Options</label>
                            <textarea x-model="fieldModal.field_options" rows="3" placeholder="Option 1, Option 2, Option 3"
                                      class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 dark:text-white"></textarea>
                            <p class="mt-1 text-[11px] text-surface-400">Separate options with commas.</p>
                        </div>

                        <!-- Placeholder -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Placeholder</label>
                            <input type="text" x-model="fieldModal.placeholder" placeholder="e.g. Enter your skill level..."
                                   class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 dark:text-white">
                        </div>

                        <!-- Required + Sort Order -->
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 cursor-pointer rounded-xl border border-surface-200 dark:border-surface-700 p-3">
                                <input type="checkbox" x-model="fieldModal.is_required" class="rounded border-surface-300 text-violet-600 focus:ring-violet-500">
                                <div>
                                    <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Required</span>
                                    <p class="text-xs text-surface-400">Must be filled</p>
                                </div>
                            </label>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Sort Order</label>
                                <input type="number" x-model.number="fieldModal.sort_order" min="0"
                                       class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 dark:text-white">
                            </div>
                        </div>

                        <!-- Errors -->
                        <template x-if="fieldModal.errors.length > 0">
                            <div class="rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 p-3">
                                <template x-for="(err, i) in fieldModal.errors" :key="i">
                                    <p class="text-xs text-red-600 dark:text-red-400" x-text="err"></p>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center gap-3 mt-5">
                        <button type="button" x-on:click="saveFormField()" :disabled="fieldModal.saving"
                                class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700 transition-colors disabled:opacity-50">
                            <svg x-show="!fieldModal.saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="fieldModal.saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="fieldModal.saving ? 'Saving...' : (fieldModal.editingId ? 'Update Field' : 'Add Field')"></span>
                        </button>
                        <button type="button" x-on:click="fieldModal.show = false"
                                class="rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            <!-- END FORM FIELD MODAL -->

            <!-- ===== SCHEDULE CLASS MODAL ===== -->
            <div x-show="scheduleModal.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.4)" x-on:keydown.escape.window="scheduleModal.show = false">
                <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-800 w-full max-w-lg p-6" x-on:click.outside="scheduleModal.show = false">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-surface-800 dark:text-white">Schedule Class</h3>
                        <button type="button" x-on:click="scheduleModal.show = false" class="rounded-lg p-1 text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Date -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Date</label>
                            <input type="text" x-ref="scheduleDate" x-model="scheduleModal.date" placeholder="Select date..."
                                   class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 dark:text-white">
                        </div>

                        <!-- Time -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Time</label>
                            <input type="text" x-model="scheduleModal.time" placeholder="e.g. 09:30 AM"
                                   x-on:blur="formatTimeInput()"
                                   class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 dark:text-white">
                            <p class="mt-1 text-xs text-surface-400">Enter time like 9:30 AM, 2:00 PM, or 14:00</p>
                        </div>

                        <!-- Recurring toggle -->
                        <label class="flex items-center gap-3 cursor-pointer rounded-xl border border-surface-200 dark:border-surface-700 p-3">
                            <input type="checkbox" x-model="scheduleModal.recurring" class="rounded border-surface-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Recurring</span>
                                <p class="text-xs text-surface-400">Generate multiple class dates automatically</p>
                            </div>
                        </label>

                        <!-- Recurring options -->
                        <div x-show="scheduleModal.recurring" x-transition class="space-y-3 rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/50 dark:bg-emerald-500/5 p-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Pattern</label>
                                    <select x-model="scheduleModal.recurrenceType" x-on:change="generateRecurringDates()"
                                            class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white">
                                        <option value="daily">Daily</option>
                                        <option value="weekdays">Weekdays (Mon–Fri)</option>
                                        <option value="weekends">Weekends (Sat–Sun)</option>
                                        <option value="every_other_day">Every Other Day</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="biweekly">Every 2 Weeks</option>
                                        <option value="triweekly">Every 3 Weeks</option>
                                        <option value="every_4_weeks">Every 4 Weeks</option>
                                        <option value="mon_wed_fri">Mon / Wed / Fri</option>
                                        <option value="tue_thu">Tue / Thu</option>
                                        <option value="mon_wed">Mon / Wed</option>
                                        <option value="tue_thu_sat">Tue / Thu / Sat</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="first_weekday_monthly">First [Same Weekday] Monthly</option>
                                        <option value="last_weekday_monthly">Last [Same Weekday] Monthly</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Occurrences</label>
                                    <input type="number" x-model.number="scheduleModal.recurrenceCount" min="2"
                                           x-on:change="generateRecurringDates()" x-on:input.debounce.500ms="generateRecurringDates()"
                                           class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white">
                                </div>
                            </div>
                            <button type="button" x-on:click="generateRecurringDates()"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Generate Dates
                            </button>

                            <!-- Generated dates preview -->
                            <div x-show="scheduleModal.generatedTimes.length > 0" class="max-h-48 overflow-y-auto space-y-1 mt-2">
                                <template x-for="(gt, idx) in scheduleModal.generatedTimes" :key="idx">
                                    <div class="flex items-center justify-between rounded-lg bg-white dark:bg-surface-800 px-3 py-1.5 text-xs border border-surface-200 dark:border-surface-700">
                                        <span class="text-surface-700 dark:text-surface-200" x-text="formatPreviewDate(gt.date, gt.time)"></span>
                                        <button type="button" x-on:click="scheduleModal.generatedTimes.splice(idx, 1)" class="text-surface-400 hover:text-red-500 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Errors -->
                        <template x-if="scheduleModal.errors.length > 0">
                            <div class="rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 p-3">
                                <template x-for="(err, i) in scheduleModal.errors" :key="i">
                                    <p class="text-xs text-red-600 dark:text-red-400" x-text="err"></p>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center gap-3 mt-5">
                        <button type="button" x-on:click="saveScheduledClasses()" :disabled="scheduleModal.saving"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors disabled:opacity-50">
                            <svg x-show="!scheduleModal.saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="scheduleModal.saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="scheduleModal.saving ? 'Scheduling...' : (scheduleModal.recurring && scheduleModal.generatedTimes.length > 0 ? 'Schedule ' + scheduleModal.generatedTimes.length + ' Classes' : 'Schedule Class')"></span>
                        </button>
                        <button type="button" x-on:click="scheduleModal.show = false"
                                class="rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            <!-- END SCHEDULE CLASS MODAL -->
        </div>
    </div>
</div>

<script>
function editSessionType() {
    const baseApi = '<?= ($baseUrl ?? '') ?>';
    return {
        facilityId: null,
        editId: null,
        resources: [],
        categories: [],
        categorySessions: [],
        loadingData: true,
        loadingSessions: false,
        submitting: false,
        errors: {},
        showFormFields: false,
        noSessionName: false,
        sessionNameMode: 'pick',
        baseApi: baseApi,

        // Custom form fields
        formFields: [],
        loadingFormFields: false,

        // Additional settings
        settings: {},
        showSettings: false,
        fieldTypes: [
            { value: 'text', label: 'Text' },
            { value: 'number', label: 'Number' },
            { value: 'email', label: 'Email' },
            { value: 'phone', label: 'Phone' },
            { value: 'date', label: 'Date' },
            { value: 'textarea', label: 'Textarea' },
            { value: 'select', label: 'Dropdown' },
            { value: 'checkbox', label: 'Checkbox' },
            { value: 'radio', label: 'Radio Buttons' },
            { value: 'toggle', label: 'Toggle (Yes/No)' },
        ],
        fieldModal: {
            show: false,
            editingId: null,
            field_label: '',
            field_name: '',
            field_type: 'text',
            field_options: '',
            placeholder: '',
            is_required: false,
            sort_order: 0,
            saving: false,
            errors: [],
        },

        // Classes
        classes: [],
        loadingClasses: false,
        showPastClasses: false,
        scheduleModal: {
            show: false,
            date: '',
            time: '',
            timeDisplay: '',
            recurring: false,
            recurrenceType: 'weekly',
            recurrenceCount: 4,
            generatedTimes: [],
            errors: [],
            saving: false,
        },

        get upcomingClasses() {
            const now = new Date();
            return this.classes.filter(c => new Date(c.scheduled_at) >= now);
        },
        get pastClasses() {
            const now = new Date();
            return this.classes.filter(c => new Date(c.scheduled_at) < now);
        },

        form: {
            category_id: '',
            session_id: '',
            title: '',
            internal_title: '',
            session_type: 'class',
            capacity: '',
            duration: '',
            standard_price: '',
            pricing_mode: 'single',
            is_active: true,
            private: 0,
            resource_value_ids: [],
            resource_input_values: [],
            pricing_rules: [],
            rolling_prices: [],
        },

        async loadFormData() {
            try {
                const [resRes, catRes] = await Promise.all([
                    authFetch(baseApi + '/api/session-types/resources'),
                    authFetch(baseApi + '/api/categories?per_page=100'),
                ]);
                const resJson = await resRes.json();
                const catJson = await catRes.json();
                this.resources = resJson.data || [];
                this.categories = catJson.data || [];
            } catch (e) { console.error('Failed to load form data', e); }
        },

        async loadSessionType() {
            if (!this.editId) return;
            this.loadingData = true;
            try {
                await this.loadFormData();
                const res = await authFetch(baseApi + '/api/session-types/' + this.editId);
                const json = await res.json();
                const d = json.data || json;
                this.form.category_id = d.category_id ? String(d.category_id) : '';
                this.form.session_id = d.session_id ? String(d.session_id) : '';
                this.form.title = d.title || '';
                this.form.internal_title = d.internal_title || '';
                this.form.session_type = d.session_type || 'class';
                this.form.capacity = d.capacity || '';
                this.form.duration = d.duration || '';
                this.form.standard_price = d.standard_price || '';
                this.form.pricing_mode = d.pricing_mode || 'single';
                this.form.is_active = d.is_active == 1 || d.is_active === true;
                this.form.private = d.private ? 1 : 0;

                // Settings
                this.settings = d.settings || {};
                if (Object.keys(this.settings).length > 0) this.showSettings = true;

                // Resource values
                if (d.resource_values && Array.isArray(d.resource_values)) {
                    this.form.resource_value_ids = d.resource_values.map(rv => String(rv.resource_value_id || rv.id));
                }

                // Resource input values
                if (d.resource_input_values && Array.isArray(d.resource_input_values)) {
                    this.form.resource_input_values = d.resource_input_values.map(iv => ({
                        resource_id: iv.resource_id,
                        value: iv.value || ''
                    }));
                }

                // Pricing rules
                if (d.pricing_rules && Array.isArray(d.pricing_rules)) {
                    this.form.pricing_rules = d.pricing_rules.map(r => ({
                        pricing_type: r.pricing_type || this.form.pricing_mode,
                        start_offset_days: r.start_offset_days || '',
                        max_users: r.max_users || '',
                        price: r.price || '',
                    }));
                }

                // Rolling prices
                if (d.rolling_prices && Array.isArray(d.rolling_prices)) {
                    this.form.rolling_prices = d.rolling_prices.map(r => ({
                        number_of_weeks: r.number_of_weeks || '',
                        price: r.price || '',
                    }));
                }

                // Auto-detect modes
                if (this.form.session_id) {
                    this.sessionNameMode = 'pick';
                    // Load category sessions so dropdown is populated
                    await this.loadCategorySessions();
                } else {
                    this.sessionNameMode = 'manual';
                }
            } catch (e) { console.error('Failed to load session type', e); }
            this.loadingData = false;
            // Load classes and form fields after session type is loaded
            this.loadClasses();
            this.loadFormFields();
        },

        getSelectedCategoryName() {
            if (!this.form.category_id) return '';
            const cat = this.categories.find(c => c.id == this.form.category_id);
            return cat ? cat.name : '';
        },

        async onCategoryChange() {
            this.form.session_id = '';
            this.categorySessions = [];
            if (this.noSessionName) {
                this.form.title = this.getSelectedCategoryName();
                return;
            }
            if (!this.form.category_id) return;
            await this.loadCategorySessions();
        },

        async loadCategorySessions() {
            if (!this.form.category_id) return;
            this.loadingSessions = true;
            try {
                const res = await authFetch(baseApi + '/api/session-details/by-category?category_id=' + this.form.category_id);
                const json = await res.json();
                this.categorySessions = json.data || [];
            } catch (e) { console.error('Failed to load sessions', e); }
            this.loadingSessions = false;
        },

        onSessionSelect() {
            if (!this.form.session_id) { this.form.title = ''; return; }
            const ses = this.categorySessions.find(s => s.id == this.form.session_id);
            if (ses) this.form.title = ses.session_name;
        },

        toggleResourceSelect(event, resource) {
            const val = event.target.value;
            const resValueIds = resource.values.map(v => String(v.id));
            this.form.resource_value_ids = this.form.resource_value_ids.filter(id => !resValueIds.includes(String(id)));
            if (val) this.form.resource_value_ids.push(val);
        },

        toggleResourceRadio(valId, resource) {
            const resValueIds = resource.values.map(v => String(v.id));
            this.form.resource_value_ids = this.form.resource_value_ids.filter(id => !resValueIds.includes(String(id)));
            this.form.resource_value_ids.push(String(valId));
        },

        getResourceInputValue(resourceId) {
            const entry = this.form.resource_input_values.find(iv => iv.resource_id == resourceId);
            return entry ? entry.value : '';
        },

        setResourceInputValue(resourceId, value) {
            const idx = this.form.resource_input_values.findIndex(iv => iv.resource_id == resourceId);
            if (idx >= 0) {
                this.form.resource_input_values[idx].value = value;
            } else {
                this.form.resource_input_values.push({ resource_id: resourceId, value: value });
            }
        },

        // ===== CLASS MANAGEMENT =====

        openScheduleModal() {
            this.scheduleModal.show = true;
            this.scheduleModal.date = '';
            this.scheduleModal.time = '';
            this.scheduleModal.timeDisplay = '';
            this.scheduleModal.recurring = false;
            this.scheduleModal.recurrenceType = 'weekly';
            this.scheduleModal.recurrenceCount = 4;
            this.scheduleModal.generatedTimes = [];
            this.scheduleModal.errors = [];
            // Init flatpickr on date input after modal renders
            this.$nextTick(() => {
                if (this.$refs.scheduleDate && !this.$refs.scheduleDate._flatpickr) {
                    flatpickr(this.$refs.scheduleDate, {
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'F j, Y',
                        minDate: 'today',
                        onChange: (selectedDates, dateStr) => {
                            this.scheduleModal.date = dateStr;
                        }
                    });
                } else if (this.$refs.scheduleDate && this.$refs.scheduleDate._flatpickr) {
                    this.$refs.scheduleDate._flatpickr.clear();
                }
            });
        },

        async loadClasses() {
            if (!this.editId) return;
            this.loadingClasses = true;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + this.editId + '/classes');
                const json = await res.json();
                this.classes = json.data || [];
            } catch (e) { console.error('Failed to load classes', e); }
            this.loadingClasses = false;
        },

        formatClassMonth(dt) {
            return new Date(dt).toLocaleDateString('en-US', { month: 'short' });
        },
        formatClassDay(dt) {
            return new Date(dt).getDate();
        },
        formatClassTime(dt) {
            const d = new Date(dt);
            const time = d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const day = d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            return time + ' — ' + day;
        },
        formatPreviewDate(date, time) {
            const d = new Date(date + 'T' + time);
            return d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })
                + ' at ' + d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        },

        parseTimeString(str) {
            if (!str) return null;
            str = str.trim();
            // Normalize: remove dots from a.m./p.m., collapse multiple spaces
            str = str.replace(/\.\s*/g, '').replace(/\s+/g, ' ');
            const upper = str.toUpperCase();
            // Match: "9:30 AM", "9:30AM", "9:30am", "09:30", "2:00 PM", "14:00", "9:00pm"
            let match = upper.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)?$/);
            if (!match) {
                // Try without minutes: "9AM", "9 PM", "14"
                match = upper.match(/^(\d{1,2})\s*(AM|PM)?$/);
                if (match) {
                    match = [match[0], match[1], '00', match[2]];
                }
            }
            if (!match) return null;
            let hours = parseInt(match[1]);
            const mins = parseInt(match[2]);
            const period = match[3];
            // Validate raw values
            if (mins < 0 || mins > 59) return null;
            if (period) {
                // With AM/PM: hours must be 1-12
                if (hours < 1 || hours > 12) return null;
                if (period === 'PM' && hours < 12) hours += 12;
                if (period === 'AM' && hours === 12) hours = 0;
            } else {
                // Without AM/PM: 24-hour format, must be 0-23
                if (hours < 0 || hours > 23) return null;
            }
            return String(hours).padStart(2, '0') + ':' + String(mins).padStart(2, '0');
        },

        formatTimeInput() {
            const raw = this.scheduleModal.time;
            if (!raw || !raw.trim()) return;
            const parsed = this.parseTimeString(raw);
            if (parsed) {
                // Show friendly 12-hour format in the input
                const [h, m] = parsed.split(':').map(Number);
                const period = h >= 12 ? 'PM' : 'AM';
                const displayHour = h === 0 ? 12 : (h > 12 ? h - 12 : h);
                const friendly = displayHour + ':' + String(m).padStart(2, '0') + ' ' + period;
                this.scheduleModal.time = friendly;
                this.scheduleModal.timeDisplay = parsed;
                this.scheduleModal.errors = [];
            } else {
                this.scheduleModal.errors = ['Invalid time format. Examples: 9:00 AM, 9:00am, 2:30 PM, 14:00'];
            }
        },

        generateRecurringDates() {
            const m = this.scheduleModal;
            // Parse the time input and ensure we have a 24h value
            if (m.time) {
                const parsed = this.parseTimeString(m.time);
                if (parsed) {
                    m.timeDisplay = parsed;
                } else {
                    m.errors = ['Invalid time format. Use formats like 9:30 AM, 2:00 PM, or 14:00'];
                    return;
                }
            }
            const time24 = m.timeDisplay || '';
            if (!m.date || !time24) {
                if (m.generatedTimes.length === 0 && m.date === '' && m.time === '') return;
                m.errors = ['Please set a start date and time first.'];
                return;
            }
            m.errors = [];
            m.generatedTimes = [];

            const start = new Date(m.date + 'T' + time24);
            const count = Math.max(m.recurrenceCount || 2, 2);
            const startDay = start.getDay(); // 0=Sun..6=Sat

            // Helper: get specific weekdays from start
            function getNextByWeekdays(base, allowedDays, needed) {
                const results = [];
                let cursor = new Date(base);
                if (allowedDays.includes(cursor.getDay())) results.push(new Date(cursor));
                while (results.length < needed) {
                    cursor.setDate(cursor.getDate() + 1);
                    if (allowedDays.includes(cursor.getDay())) results.push(new Date(cursor));
                }
                return results;
            }

            // Helper: nth weekday of month
            function nthWeekdayOfMonth(year, month, weekday, nth) {
                const d = new Date(year, month, 1);
                let count = 0;
                while (d.getMonth() === month) {
                    if (d.getDay() === weekday) {
                        count++;
                        if (count === nth) return new Date(d);
                    }
                    d.setDate(d.getDate() + 1);
                }
                return null;
            }

            // Helper: last weekday of month
            function lastWeekdayOfMonth(year, month, weekday) {
                const d = new Date(year, month + 1, 0); // last day of month
                while (d.getDay() !== weekday) d.setDate(d.getDate() - 1);
                return new Date(d);
            }

            let dates = [];

            // Specific-days-of-week patterns
            const dayPatterns = {
                'weekdays': [1, 2, 3, 4, 5],
                'weekends': [0, 6],
                'mon_wed_fri': [1, 3, 5],
                'tue_thu': [2, 4],
                'mon_wed': [1, 3],
                'tue_thu_sat': [2, 4, 6],
            };

            if (dayPatterns[m.recurrenceType]) {
                dates = getNextByWeekdays(start, dayPatterns[m.recurrenceType], count);
            } else if (m.recurrenceType === 'first_weekday_monthly') {
                // E.g. if start is a Tuesday, get the 1st Tuesday of each subsequent month
                const nthWeek = Math.ceil(start.getDate() / 7);
                for (let i = 0; i < count; i++) {
                    const targetMonth = start.getMonth() + i;
                    const targetYear = start.getFullYear() + Math.floor(targetMonth / 12);
                    const mo = targetMonth % 12;
                    const d = nthWeekdayOfMonth(targetYear, mo, startDay, nthWeek);
                    if (d) dates.push(d);
                }
            } else if (m.recurrenceType === 'last_weekday_monthly') {
                for (let i = 0; i < count; i++) {
                    const targetMonth = start.getMonth() + i;
                    const targetYear = start.getFullYear() + Math.floor(targetMonth / 12);
                    const mo = targetMonth % 12;
                    dates.push(lastWeekdayOfMonth(targetYear, mo, startDay));
                }
            } else {
                // Simple interval-based patterns
                const intervals = {
                    'daily': 1, 'every_other_day': 2,
                    'weekly': 7, 'biweekly': 14, 'triweekly': 21, 'every_4_weeks': 28,
                    'monthly': 0, // handled specially
                };
                for (let i = 0; i < count; i++) {
                    const d = new Date(start);
                    if (m.recurrenceType === 'monthly') {
                        d.setMonth(d.getMonth() + i);
                    } else {
                        d.setDate(d.getDate() + i * (intervals[m.recurrenceType] || 1));
                    }
                    dates.push(d);
                }
            }

            dates.forEach(d => {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                m.generatedTimes.push({ date: year + '-' + month + '-' + day, time: time24 });
            });
        },

        async saveScheduledClasses() {
            const m = this.scheduleModal;
            m.errors = [];

            // Validate and parse time input
            if (m.time) {
                const parsed = this.parseTimeString(m.time);
                if (parsed) {
                    m.timeDisplay = parsed;
                } else {
                    m.errors = ['Invalid time format. Use formats like 9:30 AM, 2:00 PM, or 14:00'];
                    return;
                }
            }
            const time24 = m.timeDisplay || '';

            // Auto-generate if recurring is on but dates not yet generated
            if (m.recurring && m.generatedTimes.length === 0 && m.date && time24) {
                this.generateRecurringDates();
            }

            let entries = [];
            if (m.recurring && m.generatedTimes.length > 0) {
                entries = m.generatedTimes;
            } else {
                if (!m.date || !time24) {
                    m.errors = ['Please set a date and time.'];
                    return;
                }
                entries = [{ date: m.date, time: time24 }];
            }

            m.saving = true;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + this.editId + '/classes', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ classes: entries })
                });
                const json = await res.json();
                if (res.ok) {
                    const created = json.data?.created || [];
                    const errs = json.data?.errors || [];
                    if (errs.length > 0) {
                        m.errors = errs;
                    }
                    if (created.length > 0) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: created.length + ' class(es) scheduled!', type: 'success' } }));
                        if (errs.length === 0) m.show = false;
                        await this.loadClasses();
                    } else if (errs.length === 0) {
                        m.errors = ['No classes were created.'];
                    }
                } else {
                    m.errors = [json.message || 'Failed to schedule classes'];
                }
            } catch (e) {
                m.errors = ['Network error'];
            }
            m.saving = false;
        },

        async deleteClass(classId) {
            if (!confirm('Delete this scheduled class?')) return;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + this.editId + '/classes/' + classId, { method: 'DELETE' });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Class deleted', type: 'success' } }));
                    await this.loadClasses();
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Error deleting class', type: 'error' } }));
            }
        },

        // ===== END CLASS MANAGEMENT =====

        // ===== CUSTOM FORM FIELDS =====

        async loadFormFields() {
            if (!this.editId) return;
            this.loadingFormFields = true;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + this.editId + '/form-fields');
                const json = await res.json();
                this.formFields = json.data || [];
                if (this.formFields.length > 0) this.showFormFields = true;
            } catch (e) { console.error('Failed to load form fields', e); }
            this.loadingFormFields = false;
        },

        openFieldModal(field = null) {
            this.fieldModal.show = true;
            this.fieldModal.errors = [];
            this.fieldModal.saving = false;
            if (field) {
                this.fieldModal.editingId = field.id;
                this.fieldModal.field_label = field.field_label || '';
                this.fieldModal.field_name = field.field_name || '';
                this.fieldModal.field_type = field.field_type || 'text';
                this.fieldModal.field_options = Array.isArray(field.field_options) ? field.field_options.join(', ') : '';
                this.fieldModal.placeholder = field.placeholder || '';
                this.fieldModal.is_required = !!field.is_required;
                this.fieldModal.sort_order = field.sort_order || 0;
            } else {
                this.fieldModal.editingId = null;
                this.fieldModal.field_label = '';
                this.fieldModal.field_name = '';
                this.fieldModal.field_type = 'text';
                this.fieldModal.field_options = '';
                this.fieldModal.placeholder = '';
                this.fieldModal.is_required = false;
                this.fieldModal.sort_order = this.formFields.length;
            }
        },

        autoFieldName() {
            if (!this.fieldModal.editingId) {
                this.fieldModal.field_name = this.fieldModal.field_label
                    .toLowerCase().trim()
                    .replace(/[^a-z0-9\s]/g, '')
                    .replace(/\s+/g, '_');
            }
        },

        needsOptions(type) {
            return ['select', 'checkbox', 'radio'].includes(type);
        },

        async saveFormField() {
            const fm = this.fieldModal;
            fm.errors = [];
            if (!fm.field_label.trim()) { fm.errors = ['Field label is required.']; return; }
            if (!fm.field_name.trim()) { fm.errors = ['Field name is required.']; return; }

            const payload = {
                field_label: fm.field_label.trim(),
                field_name: fm.field_name.trim().toLowerCase().replace(/[^a-z0-9_]/g, ''),
                field_type: fm.field_type,
                field_options: this.needsOptions(fm.field_type) && fm.field_options
                    ? fm.field_options.split(',').map(o => o.trim()).filter(o => o)
                    : [],
                placeholder: fm.placeholder.trim() || null,
                is_required: fm.is_required,
                sort_order: parseInt(fm.sort_order) || 0,
            };

            fm.saving = true;
            try {
                let url = baseApi + '/api/session-types/' + this.editId + '/form-fields';
                let method = 'POST';
                if (fm.editingId) {
                    url += '/' + fm.editingId;
                    method = 'PUT';
                }
                const res = await authFetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: fm.editingId ? 'Field updated!' : 'Field added!', type: 'success' } }));
                    fm.show = false;
                    await this.loadFormFields();
                } else {
                    fm.errors = [json.message || 'Failed to save field'];
                }
            } catch (e) {
                fm.errors = ['Network error'];
            }
            fm.saving = false;
        },

        async deleteFormField(fieldId) {
            if (!confirm('Delete this form field?')) return;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + this.editId + '/form-fields/' + fieldId, { method: 'DELETE' });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Field deleted', type: 'success' } }));
                    await this.loadFormFields();
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Error deleting field', type: 'error' } }));
            }
        },

        getFieldTypeLabel(type) {
            const found = this.fieldTypes.find(ft => ft.value === type);
            return found ? found.label : type;
        },

        // ===== END CUSTOM FORM FIELDS =====

        goBack() {
            const dash = this.$el.closest('[x-data]')?._x_dataStack?.find(d => d.activeTab !== undefined);
            if (dash) {
                dash.editingSessionTypeId = null;
                dash.activeTab = 'session-types';
            }
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};
            try {
                const payload = { ...this.form };
                payload.facility_id = this.facilityId;
                if (payload.capacity) payload.capacity = parseInt(payload.capacity);
                if (payload.duration) payload.duration = parseInt(payload.duration);
                if (payload.standard_price) payload.standard_price = parseFloat(payload.standard_price);
                if (payload.category_id) payload.category_id = parseInt(payload.category_id);
                if (payload.session_id) payload.session_id = parseInt(payload.session_id);

                if (payload.pricing_mode === 'time_based') {
                    payload.pricing_rules = payload.pricing_rules.filter(r => r.start_offset_days !== '' && r.price !== '').map(r => ({
                        pricing_type: 'time_based', start_offset_days: parseInt(r.start_offset_days), price: parseFloat(r.price)
                    }));
                } else if (payload.pricing_mode === 'user_based') {
                    payload.pricing_rules = payload.pricing_rules.filter(r => r.max_users !== '' && r.price !== '').map(r => ({
                        pricing_type: 'user_based', max_users: parseInt(r.max_users), price: parseFloat(r.price)
                    }));
                } else {
                    payload.pricing_rules = [];
                }

                payload.rolling_prices = (payload.rolling_prices || []).filter(r => r.number_of_weeks !== '' && r.price !== '').map(r => ({
                    number_of_weeks: parseInt(r.number_of_weeks), price: parseFloat(r.price)
                }));

                payload.settings = this.settings;

                const res = await authFetch(baseApi + '/api/session-types/' + this.editId, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Session type updated!', type: 'success' } }));
                    this.goBack();
                } else {
                    this.errors = json.errors || {};
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Validation failed', type: 'error' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
            this.submitting = false;
        },
    };
}
</script>
