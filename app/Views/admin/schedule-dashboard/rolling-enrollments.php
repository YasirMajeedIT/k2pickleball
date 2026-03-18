<!-- Rolling Enrollments Tab -->
<div x-data="rollingEnrollments()" x-effect="
    const dash = $el.closest('[x-data]')._x_dataStack?.find(d => Array.isArray(d.facilities));
    if (dash && dash.facilityId && dash.facilityId !== facilityId) { facilityId = dash.facilityId; loadGroups(); }
">
    <!-- Header -->
    <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-bold text-surface-800 dark:text-white">Rolling Enrollments</h3>
            <p class="text-xs text-surface-400 mt-0.5">View and manage rolling session package enrollments across all session types.</p>
        </div>
        <div class="flex items-center gap-3">
            <select x-model="statusFilter" x-on:change="loadGroups()"
                    class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="partially_cancelled">Partially Cancelled</option>
                <option value="fully_cancelled">Fully Cancelled</option>
            </select>
            <button x-on:click="loadGroups()" class="inline-flex items-center gap-1.5 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-12 text-center">
        <div class="inline-block h-8 w-8 rounded-full border-[3px] border-surface-200 border-t-primary-500 animate-spin"></div>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && groups.length === 0" class="py-12 text-center">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-surface-100 dark:bg-surface-800 mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <p class="text-surface-500 text-sm">No rolling enrollments found for this facility.</p>
        <p class="text-surface-400 text-xs mt-1">Rolling enrollments are created when players book multi-week rolling session packages.</p>
    </div>

    <!-- Groups Table -->
    <div x-show="!loading && groups.length > 0" class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-surface-100 dark:border-surface-800 bg-surface-50/60 dark:bg-surface-800/40">
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Player</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Session Type</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-surface-500">Weeks</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-surface-500">Price</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-surface-500">Sessions</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-surface-500">Status</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500 hidden lg:table-cell">Start Date</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500 hidden md:table-cell">Payment</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-surface-500 w-32">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="g in filteredGroups" :key="g.id">
                        <tr class="border-b border-surface-100 dark:border-surface-800 last:border-0 hover:bg-surface-50/70 dark:hover:bg-surface-800/30 transition-colors">
                            <!-- Player -->
                            <td class="px-4 py-3">
                                <div class="font-medium text-surface-800 dark:text-surface-100" x-text="(g.player_first_name || '') + ' ' + (g.player_last_name || '') || 'Unknown'"></div>
                                <div class="text-xs text-surface-400" x-text="g.player_email || ''"></div>
                            </td>
                            <!-- Session Type -->
                            <td class="px-4 py-3 text-surface-600 dark:text-surface-300" x-text="g.session_type_name"></td>
                            <!-- Weeks -->
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-lg bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400" x-text="g.rolling_weeks + ' wk'"></span>
                            </td>
                            <!-- Price -->
                            <td class="px-4 py-3 text-right">
                                <div class="font-semibold text-surface-800 dark:text-surface-100" x-text="'$' + parseFloat(g.total_price).toFixed(2)"></div>
                                <div class="text-xs text-surface-400" x-text="'$' + parseFloat(g.per_session_price).toFixed(2) + '/session'"></div>
                            </td>
                            <!-- Active/Cancelled Sessions -->
                            <td class="px-4 py-3 text-center">
                                <span class="text-emerald-600 dark:text-emerald-400 font-medium" x-text="g.active_sessions"></span>
                                <span class="text-surface-300 dark:text-surface-600">/</span>
                                <span class="text-surface-400" x-text="(parseInt(g.active_sessions) + parseInt(g.cancelled_sessions))"></span>
                                <template x-if="parseInt(g.cancelled_sessions) > 0">
                                    <span class="text-xs text-red-400 ml-1" x-text="'(' + g.cancelled_sessions + ' cancelled)'"></span>
                                </template>
                            </td>
                            <!-- Status -->
                            <td class="px-4 py-3 text-center">
                                <span :class="{
                                    'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400': g.status === 'active',
                                    'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400': g.status === 'partially_cancelled',
                                    'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400': g.status === 'fully_cancelled'
                                }" class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold capitalize"
                                   x-text="g.status.replace('_', ' ')"></span>
                            </td>
                            <!-- Start Date -->
                            <td class="px-4 py-3 hidden lg:table-cell text-surface-600 dark:text-surface-300 text-xs" x-text="g.first_class_date ? new Date(g.first_class_date).toLocaleDateString() : '—'"></td>
                            <!-- Payment Method -->
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span :class="{
                                    'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': g.payment_method === 'card',
                                    'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400': g.payment_method === 'terminal',
                                    'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-300': g.payment_method === 'manual',
                                    'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400': g.payment_method === 'free'
                                }" class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-medium capitalize"
                                   x-text="g.payment_method === 'terminal' ? '📟 Terminal' : g.payment_method"></span>
                            </td>
                            <!-- Actions -->
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button x-on:click="viewDetails(g)" title="View Details"
                                            class="rounded-lg p-1.5 text-surface-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <template x-if="g.status !== 'fully_cancelled'">
                                        <button x-on:click="openCancel(g)" title="Cancel Sessions"
                                                class="rounded-lg p-1.5 text-surface-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="detailGroup" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" x-on:keydown.escape.window="detailGroup = null">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" x-on:click="detailGroup = null"></div>
        <div class="relative w-full max-w-2xl max-h-[85vh] overflow-y-auto rounded-2xl bg-white dark:bg-surface-900 shadow-xl border border-surface-200 dark:border-surface-800 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-surface-800 dark:text-white">Enrollment Details</h3>
                <button x-on:click="detailGroup = null" class="rounded-lg p-1.5 text-surface-400 hover:text-surface-600 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="detailGroup">
                <div>
                    <!-- Summary cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                        <div class="rounded-xl bg-surface-50 dark:bg-surface-800 p-3 text-center">
                            <p class="text-xs text-surface-400 mb-1">Weeks</p>
                            <p class="text-lg font-bold text-surface-800 dark:text-white" x-text="detailGroup.rolling_weeks"></p>
                        </div>
                        <div class="rounded-xl bg-surface-50 dark:bg-surface-800 p-3 text-center">
                            <p class="text-xs text-surface-400 mb-1">Total</p>
                            <p class="text-lg font-bold text-surface-800 dark:text-white" x-text="'$' + parseFloat(detailGroup.total_price).toFixed(2)"></p>
                        </div>
                        <div class="rounded-xl bg-surface-50 dark:bg-surface-800 p-3 text-center">
                            <p class="text-xs text-surface-400 mb-1">Active</p>
                            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400" x-text="detailAttendees.filter(a => a.status !== 'cancelled').length"></p>
                        </div>
                        <div class="rounded-xl bg-surface-50 dark:bg-surface-800 p-3 text-center">
                            <p class="text-xs text-surface-400 mb-1">Cancelled</p>
                            <p class="text-lg font-bold text-red-500" x-text="detailAttendees.filter(a => a.status === 'cancelled').length"></p>
                        </div>
                    </div>

                    <!-- Attendees (individual sessions) -->
                    <h4 class="text-sm font-semibold text-surface-700 dark:text-surface-200 mb-3">Enrolled Sessions</h4>
                    <div x-show="detailLoading" class="py-6 text-center">
                        <div class="inline-block h-6 w-6 rounded-full border-2 border-surface-200 border-t-primary-500 animate-spin"></div>
                    </div>
                    <div x-show="!detailLoading" class="space-y-2 max-h-64 overflow-y-auto">
                        <template x-for="att in detailAttendees" :key="att.id">
                            <div class="flex items-center justify-between rounded-xl border border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30 px-4 py-2.5">
                                <div>
                                    <span class="text-sm font-medium text-surface-700 dark:text-surface-200" x-text="att.scheduled_at ? new Date(att.scheduled_at).toLocaleDateString(undefined, {weekday:'short', month:'short', day:'numeric', year:'numeric'}) : 'N/A'"></span>
                                    <span class="text-xs text-surface-400 ml-2" x-text="att.scheduled_at ? new Date(att.scheduled_at).toLocaleTimeString(undefined, {hour:'numeric', minute:'2-digit'}) : ''"></span>
                                </div>
                                <span :class="{
                                    'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400': att.status === 'registered' || att.status === 'confirmed',
                                    'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': att.status === 'attended',
                                    'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400': att.status === 'cancelled',
                                    'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400': att.status === 'waitlisted'
                                }" class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold capitalize" x-text="att.status"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div x-show="cancelGroup" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" x-on:keydown.escape.window="cancelGroup = null">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" x-on:click="cancelGroup = null"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-surface-900 shadow-xl border border-surface-200 dark:border-surface-800 p-6">
            <h3 class="text-lg font-bold text-surface-800 dark:text-white mb-4">Cancel Rolling Enrollment</h3>
            <template x-if="cancelGroup">
                <div>
                    <p class="text-sm text-surface-600 dark:text-surface-300 mb-4">
                        Cancel sessions for <strong x-text="(cancelGroup.player_first_name || '') + ' ' + (cancelGroup.player_last_name || '')"></strong>
                        in <strong x-text="cancelGroup.session_type_name"></strong>?
                    </p>

                    <div class="space-y-3 mb-5">
                        <label class="flex items-start gap-3 rounded-xl border border-surface-200 dark:border-surface-700 p-3 cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-800/50 transition-colors">
                            <input type="radio" x-model="cancelScope" value="remaining" class="mt-0.5 text-primary-600">
                            <div>
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Cancel remaining sessions</span>
                                <p class="text-xs text-surface-400 mt-0.5">Cancel all future sessions in this enrollment group.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 rounded-xl border border-surface-200 dark:border-surface-700 p-3 cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-800/50 transition-colors">
                            <input type="radio" x-model="cancelScope" value="all" class="mt-0.5 text-primary-600">
                            <div>
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-200">Cancel all sessions</span>
                                <p class="text-xs text-surface-400 mt-0.5">Cancel every session in this group, including past ones.</p>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button x-on:click="cancelGroup = null" class="rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-2 text-sm text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                            Never Mind
                        </button>
                        <button x-on:click="confirmCancel()" :disabled="cancelling"
                                class="rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-2 text-sm font-semibold text-white hover:from-red-600 hover:to-red-700 shadow-soft transition-all disabled:opacity-50">
                            <span x-show="!cancelling">Cancel Sessions</span>
                            <span x-show="cancelling" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Cancelling...
                            </span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function rollingEnrollments() {
    const baseApi = '<?= ($baseUrl ?? '') ?>';

    return {
        facilityId: '',
        loading: false,
        groups: [],
        statusFilter: '',
        detailGroup: null,
        detailAttendees: [],
        detailLoading: false,
        cancelGroup: null,
        cancelScope: 'remaining',
        cancelling: false,

        get filteredGroups() {
            if (!this.statusFilter) return this.groups;
            return this.groups.filter(g => g.status === this.statusFilter);
        },

        async loadGroups() {
            if (!this.facilityId) return;
            this.loading = true;
            try {
                const res = await authFetch(baseApi + '/api/session-types/booking-groups?facility_id=' + this.facilityId);
                const json = await res.json();
                this.groups = json.data || [];
            } catch (e) {
                console.error('Failed to load booking groups', e);
                this.groups = [];
            } finally {
                this.loading = false;
            }
        },

        async viewDetails(group) {
            this.detailGroup = group;
            this.detailAttendees = [];
            this.detailLoading = true;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + group.session_type_id + '/booking-groups/' + group.id);
                const json = await res.json();
                this.detailAttendees = json.data?.attendees || [];
            } catch (e) {
                console.error('Failed to load group details', e);
            } finally {
                this.detailLoading = false;
            }
        },

        openCancel(group) {
            this.cancelGroup = group;
            this.cancelScope = 'remaining';
        },

        async confirmCancel() {
            if (!this.cancelGroup) return;
            this.cancelling = true;
            try {
                const res = await authFetch(baseApi + '/api/session-types/' + this.cancelGroup.session_type_id + '/booking-groups/' + this.cancelGroup.id + '/cancel', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ scope: this.cancelScope })
                });
                const json = await res.json();
                if (res.ok) {
                    this.showToast(json.message || 'Sessions cancelled successfully', 'success');
                    this.cancelGroup = null;
                    await this.loadGroups();
                } else {
                    this.showToast(json.message || 'Failed to cancel', 'error');
                }
            } catch (e) {
                this.showToast('Error: ' + (e.message || e), 'error');
            } finally {
                this.cancelling = false;
            }
        },

        showToast(msg, type) {
            if (typeof window.showToast === 'function') {
                window.showToast(msg, type);
            } else {
                if (type === 'error') console.error(msg);
                else console.log(msg);
            }
        }
    };
}
</script>
