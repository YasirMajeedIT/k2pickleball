<!-- Add Session Type Tab -->
<div x-data="addSessionType()" x-effect="
    const dash = $el.closest('[x-data]')._x_dataStack?.find(d => Array.isArray(d.facilities));
    if (dash && dash.facilityId && dash.facilityId !== facilityId) { facilityId = dash.facilityId; loadFormData(); }
">
    <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
        <!-- Header -->
        <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <h3 class="font-semibold text-surface-800 dark:text-surface-100">Add Session Type</h3>
        </div>

        <div class="p-6">
            <!-- Loading -->
            <div x-show="loadingForm" class="py-8 text-center">
                <div class="inline-block h-8 w-8 rounded-full border-[3px] border-surface-200 border-t-primary-500 animate-spin"></div>
                <p class="mt-2 text-sm text-surface-400">Loading form data...</p>
            </div>

            <form x-show="!loadingForm" x-on:submit.prevent="submitForm()" class="space-y-6">

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
                        <!-- Toggle: pick from session details or type manually -->
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
                                    <p class="mt-1 text-xs text-surface-400">Selecting a session will set the title automatically.</p>
                                </div>
                            </template>
                            <template x-if="!loadingSessions && categorySessions.length === 0 && form.category_id">
                                <div class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 rounded-lg p-2.5">
                                    No session details found for this category. You can
                                    <button type="button" x-on:click="sessionNameMode = 'manual'" class="underline font-semibold">enter a name manually</button>
                                    or create sessions in the Session Details tab first.
                                </div>
                            </template>
                            <template x-if="!form.category_id">
                                <p class="text-xs text-surface-400 italic">Select a category first to see available sessions.</p>
                            </template>
                        </div>

                        <!-- Manual entry -->
                        <div x-show="sessionNameMode === 'manual'">
                            <input type="text" x-model="form.title" placeholder="e.g. Saturday Morning Round Robin"
                                   class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                        </div>
                    </div>

                    <!-- Show resolved title -->
                    <div x-show="form.title" class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-surface-600 dark:text-surface-300">Title: <strong x-text="form.title"></strong></span>
                    </div>
                </div>

                <!-- ===== INTERNAL TITLE ===== -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-surface-500 mb-1.5">Internal Title</label>
                    <input type="text" x-model="form.internal_title" maxlength="255" placeholder="Internal title for staff reference..."
                           class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                    <p class="mt-1 text-xs text-surface-400">Only visible to staff, not shown to players.</p>
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
                                <!-- Checkbox Group -->
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
                                <!-- Select Box -->
                                <template x-if="resource.field_type === 'selectbox'">
                                    <select x-on:change="toggleResourceSelect($event, resource)"
                                            class="w-full sm:w-64 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                                        <option value="">Select...</option>
                                        <template x-for="val in resource.values" :key="val.id">
                                            <option :value="val.id" :selected="form.resource_value_ids.includes(String(val.id))" x-text="val.name"></option>
                                        </template>
                                    </select>
                                </template>
                                <!-- Radio -->
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

                    <!-- Standard Price (always shown) -->
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1.5">Standard Price ($)</label>
                        <input type="number" x-model="form.standard_price" min="0" step="0.01" placeholder="0.00"
                               class="w-full sm:w-48 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                    </div>

                    <!-- Pricing Mode -->
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

                    <!-- Time-Based Pricing Rules -->
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

                    <!-- User-Based Pricing Rules -->
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

                    <!-- Rolling Prices (only for series_rolling) -->
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
                            <input type="radio" name="access" :value="0" x-model.number="form.private" class="border-surface-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-surface-600 dark:text-surface-300">Public</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access" :value="1" x-model.number="form.private" class="border-surface-300 text-primary-600 focus:ring-primary-500">
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

                    <!-- Fields list -->
                    <div class="space-y-2">
                        <template x-for="(ff, idx) in formFields" :key="ff._localId">
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
                                    <button type="button" x-on:click="deleteLocalField(ff._localId)" title="Delete"
                                            class="rounded-lg p-1.5 text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <template x-if="formFields.length === 0">
                            <div class="py-6 text-center rounded-xl border border-dashed border-surface-200 dark:border-surface-700">
                                <svg class="w-10 h-10 mx-auto text-surface-300 dark:text-surface-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm text-surface-400">No custom fields defined yet.</p>
                                <p class="text-xs text-surface-400 mt-1">Click "Add Field" to create registration form fields.</p>
                            </div>
                        </template>
                    </div>
                </div>
                <!-- END REGISTRATION FORM FIELDS SECTION -->

                <!-- ===== SUBMIT ===== -->
                <div class="flex items-center gap-3 pt-4 border-t border-surface-100 dark:border-surface-800">
                    <button type="submit" :disabled="submitting || !form.title"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-2.5 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all disabled:opacity-50">
                        <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="submitting ? 'Saving...' : 'Create Session Type'"></span>
                    </button>
                </div>
            </form>

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

                        <!-- Field Name -->
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
                        <button type="button" x-on:click="saveLocalField()" :disabled="fieldModal.saving"
                                class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700 transition-colors disabled:opacity-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="fieldModal.editingId ? 'Update Field' : 'Add Field'"></span>
                        </button>
                        <button type="button" x-on:click="fieldModal.show = false"
                                class="rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            <!-- END FORM FIELD MODAL -->
        </div>
    </div>
</div>

<script>
function addSessionType() {
    const baseApi = '<?= ($baseUrl ?? '') ?>';
    return {
        facilityId: null,
        resources: [],
        categories: [],
        categorySessions: [],
        resourcesLoaded: false,
        loadingForm: true,
        loadingSessions: false,
        submitting: false,
        errors: {},
        showFormFields: false,
        showSettings: false,
        settings: {},
        noSessionName: false,
        sessionNameMode: 'pick',
        baseApi: baseApi,

        // Custom form fields (local, synced after creation)
        formFields: [],
        _localIdCounter: 0,
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

        init() {
            this.$nextTick(() => {
                const dash = this.$el.closest('[x-data]')?._x_dataStack?.find(d => Array.isArray(d.facilities));
                if (dash && dash.facilityId) {
                    this.facilityId = dash.facilityId;
                    this.loadFormData();
                } else {
                    this.loadingForm = false;
                }
            });
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
            this.loadingForm = true;
            try {
                const [resRes, catRes] = await Promise.all([
                    authFetch(baseApi + '/api/session-types/resources'),
                    authFetch(baseApi + '/api/categories?per_page=100'),
                ]);
                const resJson = await resRes.json();
                const catJson = await catRes.json();
                this.resources = resJson.data || [];
                this.categories = catJson.data || [];
                this.resourcesLoaded = true;
            } catch (e) { console.error('Failed to load form data', e); }
            this.loadingForm = false;
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

        // ===== LOCAL FORM FIELD MANAGEMENT =====

        openFieldModal(field = null) {
            this.fieldModal.show = true;
            this.fieldModal.errors = [];
            this.fieldModal.saving = false;
            if (field) {
                this.fieldModal.editingId = field._localId;
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

        getFieldTypeLabel(type) {
            const found = this.fieldTypes.find(ft => ft.value === type);
            return found ? found.label : type;
        },

        saveLocalField() {
            const fm = this.fieldModal;
            fm.errors = [];
            if (!fm.field_label.trim()) { fm.errors = ['Field label is required.']; return; }
            if (!fm.field_name.trim()) { fm.errors = ['Field name is required.']; return; }

            const fieldData = {
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

            // Check uniqueness
            const duplicate = this.formFields.find(f => f.field_name === fieldData.field_name && f._localId !== fm.editingId);
            if (duplicate) { fm.errors = ['A field with this name already exists.']; return; }

            if (fm.editingId) {
                const idx = this.formFields.findIndex(f => f._localId === fm.editingId);
                if (idx >= 0) {
                    fieldData._localId = fm.editingId;
                    this.formFields[idx] = fieldData;
                }
            } else {
                this._localIdCounter++;
                fieldData._localId = this._localIdCounter;
                this.formFields.push(fieldData);
            }

            fm.show = false;
        },

        deleteLocalField(localId) {
            this.formFields = this.formFields.filter(f => f._localId !== localId);
        },

        async syncFormFieldsToServer(sessionTypeId) {
            if (!this.showFormFields || this.formFields.length === 0) return;
            try {
                const fields = this.formFields.map((f, idx) => ({
                    field_label: f.field_label,
                    field_name: f.field_name,
                    field_type: f.field_type,
                    field_options: f.field_options,
                    placeholder: f.placeholder,
                    is_required: f.is_required,
                    sort_order: f.sort_order ?? idx,
                }));
                await authFetch(baseApi + '/api/session-types/' + sessionTypeId + '/form-fields/sync', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ fields: fields })
                });
            } catch (e) { console.error('Failed to sync form fields', e); }
        },

        // ===== END LOCAL FORM FIELD MANAGEMENT =====

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

                // Filter pricing rules by mode
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

                // Filter rolling prices
                payload.rolling_prices = (payload.rolling_prices || []).filter(r => r.number_of_weeks !== '' && r.price !== '').map(r => ({
                    number_of_weeks: parseInt(r.number_of_weeks), price: parseFloat(r.price)
                }));

                payload.settings = this.settings;

                const res = await authFetch(baseApi + '/api/session-types', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (res.ok) {
                    const newId = json.data?.id;
                    // Sync form fields if any were added
                    if (newId) await this.syncFormFieldsToServer(newId);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Session type created! You can now schedule classes.', type: 'success' } }));
                    this.resetForm();
                    const dash = this.$el.closest('[x-data]')?._x_dataStack?.find(d => d.activeTab !== undefined);
                    if (dash && newId) {
                        dash.startEdit(newId);
                    } else if (dash) {
                        dash.activeTab = 'session-types';
                    }
                } else {
                    this.errors = json.errors || {};
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Validation failed', type: 'error' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
            this.submitting = false;
        },

        resetForm() {
            this.form = {
                category_id: '', session_id: '', title: '', internal_title: '', session_type: 'class',
                capacity: '', duration: '', standard_price: '', pricing_mode: 'single',
                is_active: true, private: 0,
                resource_value_ids: [], resource_input_values: [], pricing_rules: [], rolling_prices: [],
            };
            this.showFormFields = false;
            this.showSettings = false;
            this.settings = {};
            this.formFields = [];
            this._localIdCounter = 0;
            this.noSessionName = false;
            this.sessionNameMode = 'pick';
            this.categorySessions = [];
            this.errors = {};
        }
    };
}
</script>
