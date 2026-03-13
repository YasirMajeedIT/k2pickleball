<?php
$title = 'Facility Details';
$breadcrumbs = [
    ['label' => 'Facilities', 'url' => ($baseUrl ?? '') . '/admin/facilities'],
    ['label' => 'View'],
];
$backUrl  = ($baseUrl ?? '') . '/admin/facilities';
$editUrl  = ($baseUrl ?? '') . '/admin/facilities/' . ($id ?? '') . '/edit';
$apiUrl   = ($baseUrl ?? '') . '/api/facilities/' . ($id ?? '');

ob_start();
?>
<div x-data="facilityShow()" x-init="init()">

    <!-- Header bar -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-surface-900 dark:text-white" x-text="facility.name || 'Facility Details'"></h2>
            <p class="mt-0.5 text-sm text-surface-500" x-text="facility.city && facility.state ? facility.city + ', ' + facility.state : ''"></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($backUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-soft">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <a href="<?= htmlspecialchars($editUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Facility
            </a>
        </div>
    </div>

    <!-- Loading state -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-20 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
        <p class="text-sm text-surface-400">Loading facility...</p>
    </div>

    <!-- Content -->
    <div x-show="!loading" class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Left column: main info -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Basic Info Card -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Basic Information</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Facility Name</dt>
                            <dd class="mt-1 text-sm font-medium text-surface-800 dark:text-surface-100" x-text="facility.name || '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Slug</dt>
                            <dd class="mt-1 text-sm font-mono text-surface-600 dark:text-surface-300" x-text="facility.slug || '—'"></dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Status</dt>
                            <dd class="mt-1">
                                <span x-html="statusBadge(facility.status)"></span>
                            </dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Timezone</dt>
                            <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="facility.timezone || '—'"></dd>
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Description</dt>
                        <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300 leading-relaxed" x-text="facility.description || '—'"></dd>
                    </div>
                </div>
            </div>

            <!-- Address Card -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Address</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Street Address</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="facility.address_line1 || '—'"></dd>
                        <dd class="text-sm text-surface-500" x-show="facility.address_line2" x-text="facility.address_line2"></dd>
                    </div>
                    <div class="grid grid-cols-3 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">City</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="facility.city || '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">State</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="facility.state || '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">ZIP</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="facility.zip || '—'"></dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Country</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="facility.country || '—'"></dd>
                        </div>
                        <div class="px-6 py-4 border-l border-surface-100 dark:border-surface-800">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Phone</dt>
                            <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="facility.phone || '—'"></dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courts Card -->
            <div x-show="facility.courts && facility.courts.length > 0"
                 class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Courts</h3>
                    <span class="ml-auto inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-500/10 px-2.5 py-0.5 text-xs font-semibold text-primary-600 dark:text-primary-400"
                          x-text="facility.courts ? facility.courts.length : 0"></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-surface-50 dark:bg-surface-800/30">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-500">Name</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-500">Surface</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-500">Indoor</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                            <template x-for="court in (facility.courts || [])" :key="court.id">
                                <tr class="hover:bg-primary-50/20 dark:hover:bg-surface-800/40 transition-colors">
                                    <td class="px-5 py-3 font-medium text-surface-700 dark:text-surface-200" x-text="court.name"></td>
                                    <td class="px-5 py-3 text-surface-500 capitalize" x-text="court.surface_type || '—'"></td>
                                    <td class="px-5 py-3 text-surface-500" x-text="court.is_indoor ? 'Indoor' : 'Outdoor'"></td>
                                    <td class="px-5 py-3">
                                        <span x-html="statusBadge(court.status)"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right column: meta info -->
        <div class="space-y-6">

            <!-- Contact Card -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Contact</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Email</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300 break-all" x-text="facility.email || '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Phone</dt>
                        <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="facility.phone || '—'"></dd>
                    </div>
                </div>
            </div>

            <!-- Meta Card -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-surface-400 to-surface-500 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Details</h3>
                </div>
                <div class="divide-y divide-surface-100 dark:divide-surface-800">
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Facility ID</dt>
                        <dd class="mt-1 font-mono text-xs text-surface-500" x-text="facility.id || '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Created</dt>
                        <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="facility.created_at ? new Date(facility.created_at).toLocaleDateString() : '—'"></dd>
                    </div>
                    <div class="px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Updated</dt>
                        <dd class="mt-1 text-sm text-surface-600 dark:text-surface-300" x-text="facility.updated_at ? new Date(facility.updated_at).toLocaleDateString() : '—'"></dd>
                    </div>
                </div>
            </div>

            <!-- Amenities Card -->
            <div x-show="amenities.length > 0"
                 class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
                <div class="flex items-center gap-3 border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-800 dark:text-surface-100">Amenities</h3>
                </div>
                <div class="px-6 py-4 flex flex-wrap gap-2">
                    <template x-for="item in amenities" :key="item">
                        <span class="inline-flex items-center rounded-full bg-violet-50 dark:bg-violet-500/10 px-3 py-1 text-xs font-medium text-violet-700 dark:text-violet-400 capitalize" x-text="item"></span>
                    </template>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function facilityShow() {
    const token = localStorage.getItem('access_token');

    return {
        facility: {},
        amenities: [],
        loading: true,
        async init() {
            try {
                const res = await fetch('<?= htmlspecialchars($apiUrl) ?>', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (res.status === 401) { window.location.href = APP_BASE + '/admin/login'; return; }
                const json = await res.json();
                if (json.data) {
                    this.facility = json.data;
                    const settings = json.data.settings;
                    if (settings && Array.isArray(settings.amenities)) {
                        this.amenities = settings.amenities;
                    } else if (settings && typeof settings.amenities === 'string') {
                        try { this.amenities = JSON.parse(settings.amenities); } catch(e) {}
                    }
                }
            } catch (e) {
                console.error('Facility fetch failed', e);
            } finally {
                this.loading = false;
            }
        },
        statusBadge(status) {
            const map = {
                active:      'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                inactive:    'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400',
                maintenance: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
            };
            const cls = map[status] || 'bg-surface-100 text-surface-600';
            return `<span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize ${cls}">${status || '—'}</span>`;
        },
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
