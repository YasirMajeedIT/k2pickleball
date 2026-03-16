<?php
$title = 'Waivers';
$breadcrumbs = [['label' => 'Legal'], ['label' => 'Waivers']];
ob_start();
?>

<div x-data="waiversPage()" x-init="init()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Waivers</h3>
            <p class="text-sm text-surface-500 mt-0.5">Manage waiver versions. Only one waiver can be active at a time — activating one automatically expires all others.</p>
        </div>
        <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 rounded-xl shadow-soft transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Waiver
        </button>
    </div>

    <!-- Active Waiver Banner -->
    <template x-if="activeWaiver">
        <div class="mb-6 flex items-start gap-3 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 px-5 py-4">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">Active Waiver</p>
                <p class="text-sm text-emerald-700 dark:text-emerald-400 mt-0.5">
                    <span x-text="activeWaiver.title"></span>
                    <span class="mx-1.5 opacity-40">·</span>
                    <span class="font-mono">v<span x-text="activeWaiver.version"></span></span>
                    <template x-if="activeWaiver.effective_date">
                        <span> · Effective <span x-text="formatDate(activeWaiver.effective_date)"></span></span>
                    </template>
                </p>
            </div>
        </div>
    </template>

    <!-- Search -->
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-6 p-4">
        <input type="text" x-model="search" @input.debounce.400ms="page = 1; loadWaivers()" placeholder="Search waivers by title..." class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white placeholder:text-surface-400 focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
    </div>

    <!-- Loading skeleton -->
    <div x-show="loading" class="space-y-3">
        <template x-for="i in 3" :key="i">
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 p-5 animate-pulse">
                <div class="flex justify-between">
                    <div class="h-5 w-56 bg-surface-200 dark:bg-surface-700 rounded-lg"></div>
                    <div class="h-5 w-16 bg-surface-100 dark:bg-surface-800 rounded-full"></div>
                </div>
                <div class="h-4 w-40 bg-surface-100 dark:bg-surface-800 rounded mt-3"></div>
            </div>
        </template>
    </div>

    <!-- Empty state -->
    <template x-if="!loading && waivers.length === 0">
        <div class="rounded-2xl border-2 border-dashed border-surface-200 dark:border-surface-700 p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-surface-300 dark:text-surface-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            <h4 class="text-lg font-semibold text-surface-700 dark:text-surface-300 mb-1">No Waivers Yet</h4>
            <p class="text-sm text-surface-500 mb-4">Create your first waiver version to get started.</p>
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 rounded-xl shadow-soft transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Waiver
            </button>
        </div>
    </template>

    <!-- Table -->
    <div x-show="!loading && waivers.length > 0" class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                <thead class="bg-surface-50/80 dark:bg-surface-800/50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Title</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Version</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Effective Date</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Expiry Date</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-surface-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                    <template x-for="w in waivers" :key="w.id">
                        <tr class="hover:bg-surface-50/50 dark:hover:bg-surface-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold text-surface-800 dark:text-white" x-text="w.title"></p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-surface-100 dark:bg-surface-700 text-xs font-mono font-semibold text-surface-600 dark:text-surface-300" x-text="'v' + w.version"></span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm text-surface-600 dark:text-surface-400" x-text="w.effective_date ? formatDate(w.effective_date) : '—'"></span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm text-surface-600 dark:text-surface-400" x-text="w.expiry_date ? formatDate(w.expiry_date) : 'No expiry'"></span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                    :class="w.is_active == 1 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-surface-100 text-surface-500 dark:bg-surface-700 dark:text-surface-400'">
                                    <span class="h-1.5 w-1.5 rounded-full" :class="w.is_active == 1 ? 'bg-emerald-500' : 'bg-surface-400'"></span>
                                    <span x-text="w.is_active == 1 ? 'Active' : 'Inactive'"></span>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Activate button (only shown when inactive) -->
                                    <template x-if="w.is_active != 1">
                                        <button @click="activateWaiver(w.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20 transition-colors" title="Set as Active">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Activate
                                        </button>
                                    </template>
                                    <!-- View -->
                                    <button @click="viewWaiver(w)" class="p-1.5 rounded-lg text-surface-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-colors" title="View Content">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </button>
                                    <!-- Edit -->
                                    <button @click="openEditModal(w)" class="p-1.5 rounded-lg text-surface-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <!-- Delete -->
                                    <button @click="deleteWaiver(w)" class="p-1.5 rounded-lg text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div x-show="totalPages > 1" class="flex items-center justify-between px-5 py-3 border-t border-surface-100 dark:border-surface-800 bg-surface-50/30 dark:bg-surface-800/20">
            <p class="text-xs text-surface-500">Page <span x-text="page"></span> of <span x-text="totalPages"></span> (<span x-text="total"></span> total)</p>
            <div class="flex gap-1">
                <button @click="page > 1 && (page--, loadWaivers())" :disabled="page <= 1" class="px-3 py-1 text-xs font-medium rounded-lg border border-surface-200 dark:border-surface-700 text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-800 disabled:opacity-40 transition-colors">Prev</button>
                <button @click="page < totalPages && (page++, loadWaivers())" :disabled="page >= totalPages" class="px-3 py-1 text-xs font-medium rounded-lg border border-surface-200 dark:border-surface-700 text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-800 disabled:opacity-40 transition-colors">Next</button>
            </div>
        </div>
    </div>

    <!-- ===== Create / Edit Modal ===== -->
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-[9998] bg-surface-950/60 backdrop-blur-sm" @click.self="closeModal()" x-cloak>
        <div class="flex items-start justify-center min-h-screen p-4 pt-10">
            <div x-show="showModal" x-transition
                 class="relative w-full max-w-3xl bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-surface-200 dark:border-surface-800 flex flex-col max-h-[88vh]"
                 @click.stop>
                <!-- Header -->
                <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-bold text-surface-900 dark:text-white" x-text="editingId ? 'Edit Waiver' : 'Add New Waiver'"></h4>
                        <button @click="closeModal()" class="text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto flex-1 space-y-5">
                    <!-- Title & Version -->
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Title <span class="text-red-400">*</span></label>
                            <input type="text" x-model="form.title" required placeholder="e.g. K2 Pickleball Liability Waiver" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Version <span class="text-red-400">*</span></label>
                            <input type="text" x-model="form.version" required placeholder="1.0" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white font-mono focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
                        </div>
                    </div>

                    <!-- Effective & Expiry Dates -->
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Effective Date</label>
                            <div class="relative">
                                <input type="text" x-ref="effectiveDate" placeholder="YYYY-MM-DD HH:MM" readonly class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none cursor-pointer">
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Expiry Date <span class="text-xs text-surface-400">(optional)</span></label>
                            <div class="relative">
                                <input type="text" x-ref="expiryDate" placeholder="Leave blank for no expiry" readonly class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none cursor-pointer">
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Active Toggle -->
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer w-fit">
                            <div class="relative">
                                <input type="checkbox" x-model="form.is_active" class="peer sr-only">
                                <div class="h-5 w-9 rounded-full bg-surface-200 peer-checked:bg-primary-500 dark:bg-surface-700 transition-colors"></div>
                                <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transform peer-checked:translate-x-4 transition-transform"></div>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Set as Active Waiver</span>
                        </label>
                        <p x-show="form.is_active" class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                            ⚠ Activating this waiver will automatically deactivate all other waivers.
                        </p>
                    </div>

                    <!-- Content (Quill) -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Waiver Content <span class="text-red-400">*</span></label>
                        <div id="waiver-quill-wrapper" class="rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700">
                            <div id="waiver-quill-editor" style="min-height: 320px;"></div>
                        </div>
                        <p class="mt-1 text-xs text-surface-400">Use the toolbar above to format the waiver text.</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30 flex items-center justify-end gap-3 flex-shrink-0">
                    <button @click="closeModal()" class="px-5 py-2.5 text-sm font-semibold text-surface-600 dark:text-surface-300 border border-surface-200 dark:border-surface-700 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">Cancel</button>
                    <button @click="saveWaiver()" :disabled="saving" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 rounded-xl shadow-soft disabled:opacity-50 transition-all">
                        <template x-if="saving">
                            <div class="h-4 w-4 rounded-full border-2 border-white/30 border-t-white animate-spin"></div>
                        </template>
                        <span x-text="editingId ? 'Update Waiver' : 'Create Waiver'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== View Content Modal ===== -->
    <div x-show="showViewModal" x-transition.opacity class="fixed inset-0 z-[9999] bg-surface-950/60 backdrop-blur-sm" @click.self="showViewModal = false" x-cloak>
        <div class="flex items-start justify-center min-h-screen p-4 pt-10">
            <div x-show="showViewModal" x-transition
                 class="relative w-full max-w-3xl bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-surface-200 dark:border-surface-800 flex flex-col max-h-[88vh]"
                 @click.stop>
                <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30 flex-shrink-0 flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-bold text-surface-900 dark:text-white" x-text="viewingWaiver?.title"></h4>
                        <p class="text-xs text-surface-400 mt-0.5">
                            Version <span class="font-mono" x-text="viewingWaiver?.version"></span>
                            <template x-if="viewingWaiver?.effective_date">
                                <span> · Effective <span x-text="formatDate(viewingWaiver?.effective_date)"></span></span>
                            </template>
                        </p>
                    </div>
                    <button @click="showViewModal = false" class="text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    <div class="prose prose-sm dark:prose-invert max-w-none text-surface-700 dark:text-surface-300 whitespace-pre-wrap leading-relaxed text-sm" x-html="viewingWaiver?.content"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function waiversPage() {
    return {
        waivers: [],
        activeWaiver: null,
        loading: true,
        saving: false,
        showModal: false,
        showViewModal: false,
        editingId: null,
        viewingWaiver: null,
        search: '',
        page: 1,
        total: 0,
        totalPages: 1,
        quillEditor: null,
        form: {
            title: '',
            version: '',
            content: '',
            effective_date: '',
            expiry_date: '',
            is_active: false,
        },

        async init() {
            await Promise.all([this.loadWaivers(), this.loadActiveWaiver()]);
        },

        async loadWaivers() {
            this.loading = true;
            try {
                let url = APP_BASE + '/api/waivers?page=' + this.page + '&per_page=20';
                if (this.search) url += '&search=' + encodeURIComponent(this.search);
                const res = await authFetch(url);
                const json = await res.json();
                this.waivers = json.data || [];
                this.total = json.meta?.total || 0;
                this.totalPages = json.meta?.last_page || 1;
            } catch (e) { console.error(e); }
            this.loading = false;
        },

        async loadActiveWaiver() {
            try {
                const res = await authFetch(APP_BASE + '/api/waivers/active');
                const json = await res.json();
                this.activeWaiver = json.data || null;
            } catch (e) { console.error(e); }
        },

        formatDate(dt) {
            if (!dt) return '—';
            return new Date(dt).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },

        resetForm() {
            this.form = { title: '', version: '', content: '', effective_date: '', expiry_date: '', is_active: false };
        },

        initDatePickers() {
            const self = this;
            ['effectiveDate', 'expiryDate'].forEach(refName => {
                const el = self.$refs[refName];
                if (!el) return;
                if (el._flatpickr) el._flatpickr.destroy();
                const fp = flatpickr(el, {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    allowInput: false,
                    onChange(_, dateStr) {
                        if (refName === 'effectiveDate') self.form.effective_date = dateStr;
                        else self.form.expiry_date = dateStr;
                    },
                });
                const val = refName === 'effectiveDate' ? self.form.effective_date : self.form.expiry_date;
                if (val) fp.setDate(val, false);
            });
        },

        initQuill() {
            const self = this;
            self.quillEditor = null;
            // Reset the entire wrapper to remove any existing toolbar + editor DOM
            const wrapper = document.getElementById('waiver-quill-wrapper');
            if (!wrapper) return;
            wrapper.innerHTML = '<div id="waiver-quill-editor" style="min-height: 320px;"></div>';

            self.quillEditor = new Quill('#waiver-quill-editor', {
                theme: 'snow',
                placeholder: 'Enter the full waiver text here...',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ indent: '-1' }, { indent: '+1' }],
                        ['clean'],
                    ],
                },
            });

            if (self.form.content) {
                self.quillEditor.clipboard.dangerouslyPasteHTML(self.form.content);
            }
        },

        openCreateModal() {
            this.editingId = null;
            this.resetForm();
            this.showModal = true;
            this.$nextTick(() => {
                this.initDatePickers();
                this.initQuill();
            });
        },

        openEditModal(w) {
            this.editingId = w.id;
            this.form = {
                title: w.title || '',
                version: w.version || '',
                content: w.content || '',
                effective_date: w.effective_date || '',
                expiry_date: w.expiry_date || '',
                is_active: w.is_active == 1,
            };
            this.showModal = true;
            this.$nextTick(() => {
                this.initDatePickers();
                this.initQuill();
            });
        },

        closeModal() {
            this.showModal = false;
            if (this.quillEditor) {
                this.quillEditor = null;
            }
        },

        viewWaiver(w) {
            this.viewingWaiver = w;
            this.showViewModal = true;
        },

        async saveWaiver() {
            // Pull content from Quill
            if (this.quillEditor) {
                this.form.content = this.quillEditor.root.innerHTML;
            }

            if (!this.form.title.trim() || !this.form.version.trim() || !this.form.content.trim() || this.form.content === '<p><br></p>') {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Title, version, and content are required.' } }));
                return;
            }

            this.saving = true;
            try {
                const url = this.editingId
                    ? APP_BASE + '/api/waivers/' + this.editingId
                    : APP_BASE + '/api/waivers';
                const method = this.editingId ? 'PUT' : 'POST';

                const res = await authFetch(url, {
                    method,
                    body: JSON.stringify({
                        ...this.form,
                        effective_date: this.form.effective_date || null,
                        expiry_date: this.form.expiry_date || null,
                    }),
                });
                const json = await res.json();

                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: this.editingId ? 'Waiver updated.' : 'Waiver created.' } }));
                    this.closeModal();
                    await Promise.all([this.loadWaivers(), this.loadActiveWaiver()]);
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: json.message || 'Failed to save waiver.' } }));
                }
            } catch (e) {
                console.error(e);
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Network error.' } }));
            }
            this.saving = false;
        },

        async activateWaiver(id) {
            if (!confirm('Activate this waiver? All other waivers will be set to inactive.')) return;
            try {
                const res = await authFetch(APP_BASE + '/api/waivers/' + id + '/activate', { method: 'POST' });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Waiver activated successfully.' } }));
                    await Promise.all([this.loadWaivers(), this.loadActiveWaiver()]);
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: json.message || 'Failed to activate.' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Network error.' } }));
            }
        },

        async deleteWaiver(w) {
            const confirmMsg = w.is_active == 1
                ? 'This is the ACTIVE waiver. Deleting it will leave no active waiver. Are you sure?'
                : 'Are you sure you want to delete this waiver?';
            if (!confirm(confirmMsg)) return;
            try {
                const res = await authFetch(APP_BASE + '/api/waivers/' + w.id, { method: 'DELETE' });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Waiver deleted.' } }));
                    await Promise.all([this.loadWaivers(), this.loadActiveWaiver()]);
                } else {
                    const json = await res.json();
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: json.message || 'Failed to delete.' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Network error.' } }));
            }
        },
    };
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
