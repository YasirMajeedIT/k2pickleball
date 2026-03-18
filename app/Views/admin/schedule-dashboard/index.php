<?php
$title = 'Schedule Dashboard';
$breadcrumbs = [['label' => 'Schedule Dashboard']];

ob_start();
?>
<div x-data="scheduleDashboard()" x-init="init()">

    <!-- Facility Selector -->
    <div class="mb-6 rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Select Facility</h3>
                    <p class="text-xs text-surface-400">Choose a facility to manage its schedule</p>
                </div>
            </div>
            <div class="flex-1 sm:max-w-xs">
                <select x-model="facilityId" x-on:change="onFacilityChange()"
                        class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:text-white">
                    <option value="">-- Select Facility --</option>
                    <template x-for="f in facilities" :key="f.id">
                        <option :value="String(f.id)" x-text="f.name"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div x-show="facilityId" x-cloak>
        <div class="mb-6 border-b border-surface-200 dark:border-surface-800">
            <nav class="flex gap-1 overflow-x-auto pb-px">
                <template x-for="tab in visibleTabs" :key="tab.id">
                    <button x-on:click="switchTab(tab.id)"
                            :class="activeTab === tab.id
                                ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                                : 'border-transparent text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 hover:border-surface-300'"
                            class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors"
                            x-text="tab.label">
                    </button>
                </template>
            </nav>
        </div>

        <!-- Tab: Session Details -->
        <div x-show="activeTab === 'session-details'" x-cloak>
            <?php include __DIR__ . '/session-details-list.php'; ?>
        </div>

        <!-- Tab: Session Types List -->
        <div x-show="activeTab === 'session-types'" x-cloak>
            <?php include __DIR__ . '/session-types-list.php'; ?>
        </div>

        <!-- Tab: Add Session Type -->
        <div x-show="activeTab === 'add-session-type'" x-cloak>
            <?php include __DIR__ . '/add-session-type.php'; ?>
        </div>

        <!-- Tab: Edit Session Type (shown when editing) -->
        <div x-show="activeTab === 'edit-session-type'" x-cloak>
            <?php include __DIR__ . '/edit-session-type.php'; ?>
        </div>

        <!-- Tab: Master Schedule -->
        <div x-show="activeTab === 'master-schedule'" x-cloak>
            <?php include __DIR__ . '/master-schedule.php'; ?>
        </div>

    </div>

    <!-- No facility selected -->
    <div x-show="!facilityId" class="mt-10 text-center">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-surface-100 dark:bg-surface-800 mb-4">
            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <p class="text-surface-500 text-sm">Select a facility above to manage its schedule.</p>
    </div>
</div>

<script>
function scheduleDashboard() {
    const baseApi = '<?= ($baseUrl ?? '') ?>';

    // Valid tab IDs for validation
    const VALID_TABS = ['session-details', 'session-types', 'add-session-type', 'edit-session-type', 'master-schedule'];

    function readHash() {
        const hash = window.location.hash.slice(1);
        if (!hash) return {};
        try {
            const p = new URLSearchParams(hash);
            return { tab: p.get('tab') || null, facility: p.get('facility') || null, edit: p.get('edit') || null };
        } catch (e) { return {}; }
    }

    const savedState = readHash();

    // Also check URL query params (e.g. ?facility_id=21, ?facility=21, or ?fid=21)
    const urlParams = new URLSearchParams(window.location.search);
    const queryFacility = urlParams.get('facility_id') || urlParams.get('facility') || urlParams.get('fid') || '';

    // Use localStorage as fallback for facility selection
    const storedFacility = localStorage.getItem('k2_last_facility') || '';
    const initialFacility = savedState.facility || queryFacility || storedFacility || '';

    // Determine initial editing state from hash
    const initialEditId = (savedState.tab === 'edit-session-type' && savedState.edit) ? savedState.edit : null;
    // Only allow edit-session-type tab if we have an edit ID
    let initialTab = savedState.tab || 'session-details';
    if (initialTab === 'edit-session-type' && !initialEditId) initialTab = 'session-types';

    return {
        facilityId: '',
        _initialFacility: initialFacility,
        facilities: [],
        activeTab: (initialTab && VALID_TABS.includes(initialTab)) ? initialTab : 'session-details',
        tabs: [
            { id: 'session-details',  label: 'Session Details',  alwaysVisible: true },
            { id: 'session-types',    label: 'Session Types',    alwaysVisible: true },
            { id: 'add-session-type', label: 'Add Session Type', alwaysVisible: true },
            { id: 'edit-session-type',label: 'Edit Session Type',alwaysVisible: false },
            { id: 'master-schedule',  label: 'Master Schedule',  alwaysVisible: true },
        ],
        editingSessionTypeId: initialEditId,

        get visibleTabs() {
            return this.tabs.filter(t => t.alwaysVisible || (t.id === 'edit-session-type' && this.editingSessionTypeId));
        },

        async init() {
            await this.loadFacilities();
            // Watch for changes and persist to hash
            this.$watch('activeTab', (val) => this.updateHash());
            this.$watch('facilityId', (val) => this.updateHash());
        },

        updateHash() {
            const params = new URLSearchParams();
            if (this.facilityId) params.set('facility', this.facilityId);
            if (this.activeTab && this.activeTab !== 'session-details') params.set('tab', this.activeTab);
            if (this.activeTab === 'edit-session-type' && this.editingSessionTypeId) params.set('edit', this.editingSessionTypeId);
            const str = params.toString();
            window.history.replaceState(null, '', str ? '#' + str : window.location.pathname + window.location.search);
        },

        async loadFacilities() {
            try {
                const res = await authFetch(baseApi + '/api/facilities?per_page=100');
                const json = await res.json();
                this.facilities = json.data || [];
                // Wait for Alpine to render <option> elements from x-for
                await new Promise(r => setTimeout(r, 0));
                // Restore desired facility now that options exist in the DOM
                const desired = this._initialFacility;
                if (desired && this.facilities.find(f => String(f.id) === String(desired))) {
                    this.facilityId = String(desired);
                } else if (this.facilities.length > 0) {
                    this.facilityId = String(this.facilities[0].id);
                }
                if (this.facilityId) {
                    localStorage.setItem('k2_last_facility', this.facilityId);
                }
            } catch (e) { console.error('Failed to load facilities', e); }
        },

        onFacilityChange() {
            // Save to localStorage and reset tab when user manually switches facility
            if (this.facilityId) {
                localStorage.setItem('k2_last_facility', this.facilityId);
            }
            this.editingSessionTypeId = null;
            this.activeTab = 'session-details';
        },

        switchTab(tabId) {
            this.activeTab = tabId;
        },

        startEdit(id) {
            this.editingSessionTypeId = id;
            this.activeTab = 'edit-session-type';
            this.updateHash();
        },

        doneEditing() {
            this.editingSessionTypeId = null;
            this.activeTab = 'session-types';
            this.updateHash();
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
