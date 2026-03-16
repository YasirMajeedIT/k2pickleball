<!-- Session Types List Tab -->
<div x-data="sessionTypesList()" x-effect="
    const dash = $el.closest('[x-data]')._x_dataStack?.find(d => Array.isArray(d.facilities));
    if (dash && dash.facilityId && dash.facilityId !== facilityId) { facilityId = dash.facilityId; loadSessionTypes(); }
">
    <!-- Header -->
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-bold text-surface-800 dark:text-white">Session Types</h3>
        <div class="flex items-center gap-3">
            <input type="text" x-model="search" x-on:input.debounce.300ms="loadSessionTypes()" placeholder="Search..."
                   class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white w-48">
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-12 text-center">
        <div class="inline-block h-8 w-8 rounded-full border-[3px] border-surface-200 border-t-primary-500 animate-spin"></div>
    </div>

    <!-- List -->
    <div x-show="!loading" class="space-y-3">
        <template x-for="st in sessionTypes" :key="st.id">
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft p-4 flex items-center justify-between hover:border-primary-200 dark:hover:border-primary-500/30 transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="text-sm font-semibold text-surface-800 dark:text-white truncate" x-text="st.title"></h4>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                              :class="st.is_active ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-400'"
                              x-text="st.is_active ? 'Active' : 'Inactive'"></span>
                        <span class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-500/10 px-2 py-0.5 text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase"
                              x-text="({class:'Single',series:'Multi-Week',series_rolling:'Rolling'})[st.session_type] || st.session_type"></span>
                        <span x-show="st.classes_count > 0" class="inline-flex items-center gap-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span x-text="st.classes_count"></span>
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-surface-400">
                        <span x-text="'Capacity: ' + (st.capacity || '–')"></span>
                        <span class="mx-1">|</span>
                        <span x-text="'Duration: ' + (st.duration ? st.duration + ' min' : '–')"></span>
                        <span class="mx-1">|</span>
                        <span x-text="'Price: ' + (st.standard_price ? '$' + parseFloat(st.standard_price).toFixed(2) : '–')"></span>
                    </p>
                </div>
                <div class="flex items-center gap-1 ml-4">
                    <!-- Spots button -->
                    <button x-on:click="openSpotsModal(st)"
                            class="rounded-lg p-2 text-surface-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-colors" title="Update Spots">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                    <button x-on:click="$el.closest('[x-data]')._x_dataStack.find(d => d.startEdit)?.startEdit(st.id)"
                            class="rounded-lg p-2 text-surface-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button x-on:click="deleteSessionType(st.id)"
                            class="rounded-lg p-2 text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </template>

        <template x-if="!loading && sessionTypes.length === 0">
            <div class="py-12 text-center">
                <p class="text-sm text-surface-400">No session types found. Click "Add Session Type" tab to create one.</p>
            </div>
        </template>
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
        spotsModal: { show: false, sessionTypeId: null, capacity: '', applyToAll: false, saving: false },

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

        async deleteSessionType(id) {
            if (!confirm('Delete this session type?')) return;
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
