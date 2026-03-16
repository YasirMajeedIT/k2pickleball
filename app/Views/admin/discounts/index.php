<?php
$title = 'Discounts';
$breadcrumbs = [['label' => 'Packages & Discounts'], ['label' => 'Discounts']];
ob_start();
?>

<div x-data="discountsPage()" x-init="init()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Discount Rules</h3>
            <p class="text-sm text-surface-500 mt-0.5">Manage discounts, coupon codes, and promotions for your session types</p>
        </div>
        <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 rounded-xl shadow-soft transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Discount
        </button>
    </div>

    <!-- Filters -->
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-6 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <select x-model="filters.facility_id" @change="loadDiscounts()" class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
                    <option value="">All Facilities</option>
                    <template x-for="f in facilities" :key="f.id">
                        <option :value="f.id" x-text="f.name"></option>
                    </template>
                </select>
            </div>
            <div class="flex-1">
                <input type="text" x-model="filters.search" @input.debounce.400ms="loadDiscounts()" placeholder="Search discounts..." class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white placeholder:text-surface-400 focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="space-y-4">
        <template x-for="i in 3" :key="i">
            <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 p-5 animate-pulse">
                <div class="flex justify-between">
                    <div class="h-5 w-48 bg-surface-200 dark:bg-surface-800 rounded-lg"></div>
                    <div class="h-5 w-20 bg-surface-200 dark:bg-surface-800 rounded-lg"></div>
                </div>
                <div class="h-4 w-32 bg-surface-100 dark:bg-surface-800/50 rounded mt-3"></div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <template x-if="!loading && discounts.length === 0">
        <div class="rounded-2xl border-2 border-dashed border-surface-200 dark:border-surface-700 p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-surface-300 dark:text-surface-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6h.008v.008H6V6z"/></svg>
            <h4 class="text-lg font-semibold text-surface-700 dark:text-surface-300 mb-1">No Discounts Yet</h4>
            <p class="text-sm text-surface-500 mb-4">Create your first discount rule to offer promotions to your players.</p>
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 rounded-xl shadow-soft transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Discount
            </button>
        </div>
    </template>

    <!-- Discounts Table -->
    <div x-show="!loading && discounts.length > 0" class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                <thead class="bg-surface-50/80 dark:bg-surface-800/50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Category</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Coupon Code</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Discount</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Validity</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Usage</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-surface-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                    <template x-for="d in discounts" :key="d.id">
                        <tr class="hover:bg-surface-50/50 dark:hover:bg-surface-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold text-surface-800 dark:text-white" x-text="d.name"></p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm text-surface-600 dark:text-surface-400" x-text="d.discount_category || '-'"></span>
                            </td>
                            <td class="px-5 py-4">
                                <template x-if="d.coupon_code">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-primary-50 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400 text-xs font-mono font-bold tracking-wider" x-text="d.coupon_code"></span>
                                </template>
                                <template x-if="!d.coupon_code">
                                    <span class="text-sm text-surface-400">—</span>
                                </template>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm font-semibold" :class="d.discount_type === 'percent' ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'" x-text="d.discount_type === 'percent' ? d.discount_value + '%' : '$' + parseFloat(d.discount_value).toFixed(2)"></span>
                                <span class="text-xs text-surface-400 ml-1" x-text="d.discount_type === 'percent' ? 'off' : 'off'"></span>
                            </td>
                            <td class="px-5 py-4">
                                <template x-if="d.valid_from || d.valid_to">
                                    <div class="text-xs text-surface-500">
                                        <span x-show="d.valid_from" x-text="new Date(d.valid_from).toLocaleDateString('en-US', {month:'short',day:'numeric'})"></span>
                                        <span x-show="d.valid_from && d.valid_to"> – </span>
                                        <span x-show="d.valid_to" x-text="new Date(d.valid_to).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'})"></span>
                                    </div>
                                </template>
                                <template x-if="!d.valid_from && !d.valid_to">
                                    <span class="text-xs text-surface-400">No limit</span>
                                </template>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm text-surface-600 dark:text-surface-400" x-text="d.used_count + (d.usage_limit ? ' / ' + d.usage_limit : ' / ∞')"></span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      :class="d.is_active == 1 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-surface-200 text-surface-500 dark:bg-surface-700 dark:text-surface-400'"
                                      x-text="d.is_active == 1 ? 'Active' : 'Inactive'"></span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal(d)" class="p-1.5 rounded-lg text-surface-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="deleteDiscount(d.id)" class="p-1.5 rounded-lg text-surface-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Delete">
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
                <button @click="page > 1 && (page--, loadDiscounts())" :disabled="page <= 1" class="px-3 py-1 text-xs font-medium rounded-lg border border-surface-200 dark:border-surface-700 text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-800 disabled:opacity-40 transition-colors">Prev</button>
                <button @click="page < totalPages && (page++, loadDiscounts())" :disabled="page >= totalPages" class="px-3 py-1 text-xs font-medium rounded-lg border border-surface-200 dark:border-surface-700 text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-800 disabled:opacity-40 transition-colors">Next</button>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-[9998] bg-surface-950/60 backdrop-blur-sm" @click.self="showModal = false" x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showModal" x-transition
                 class="relative w-full max-w-2xl bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-surface-200 dark:border-surface-800 overflow-hidden max-h-[90vh] flex flex-col"
                 @click.stop>
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-bold text-surface-900 dark:text-white" x-text="editingId ? 'Edit Discount Rule' : 'Create Discount Rule'"></h4>
                        <button @click="showModal = false" class="text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto flex-1">
                    <form @submit.prevent="saveDiscount()" class="space-y-5">
                        <!-- Row: Name & Category -->
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Discount Name <span class="text-red-400">*</span></label>
                                <input type="text" x-model="form.name" required class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none" placeholder="Early Bird Discount">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Category</label>
                                <select x-model="form.discount_category" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
                                    <option value="">Select Category</option>
                                    <option value="early_bird">Early Bird</option>
                                    <option value="seasonal">Seasonal</option>
                                    <option value="loyalty">Loyalty</option>
                                    <option value="referral">Referral</option>
                                    <option value="bulk">Bulk / Group</option>
                                    <option value="promotional">Promotional</option>
                                    <option value="student">Student</option>
                                    <option value="senior">Senior</option>
                                    <option value="military">Military</option>
                                    <option value="member">Member</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row: Facility -->
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Facility</label>
                            <select x-model="form.facility_id" @change="onModalFacilityChange()" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
                                <option value="">All Facilities</option>
                                <template x-for="f in facilities" :key="f.id">
                                    <option :value="f.id" x-text="f.name"></option>
                                </template>
                            </select>
                            <p class="mt-1 text-xs text-surface-400">Leave blank to apply to all facilities</p>
                        </div>

                        <!-- Row: Coupon Code -->
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Coupon Code</label>
                            <input type="text" x-model="form.coupon_code" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white font-mono uppercase tracking-wider focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none" placeholder="SUMMER2024" maxlength="50">
                            <p class="mt-1 text-xs text-surface-400">Optional. Players will enter this code at checkout to apply the discount.</p>
                        </div>

                        <!-- Row: Discount Type & Value -->
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Discount Type <span class="text-red-400">*</span></label>
                                <select x-model="form.discount_type" required class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
                                    <option value="percent">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount ($)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Discount Value <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-surface-400" x-text="form.discount_type === 'percent' ? '%' : '$'"></span>
                                    <input type="number" x-model="form.discount_value" required step="0.01" min="0" :max="form.discount_type === 'percent' ? 100 : 99999" class="w-full pl-8 pr-4 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Row: Valid From & Valid To -->
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Valid From</label>
                                <div class="relative">
                                    <input type="text" x-ref="validFrom" placeholder="YYYY-MM-DD" readonly class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none cursor-pointer">
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Valid To</label>
                                <div class="relative">
                                    <input type="text" x-ref="validTo" placeholder="YYYY-MM-DD" readonly class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none cursor-pointer">
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Row: Usage Limit & Status -->
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Usage Limit</label>
                                <input type="number" x-model="form.usage_limit" min="0" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none" placeholder="Unlimited">
                                <p class="mt-1 text-xs text-surface-400">Leave empty for unlimited usage</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Status</label>
                                <label class="flex items-center gap-3 cursor-pointer mt-2">
                                    <div class="relative">
                                        <input type="checkbox" x-model="form.is_active" class="peer sr-only">
                                        <div class="h-5 w-9 rounded-full bg-surface-200 peer-checked:bg-primary-500 dark:bg-surface-700 transition-colors"></div>
                                        <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transform peer-checked:translate-x-4 transition-transform"></div>
                                    </div>
                                    <span class="text-sm text-surface-600 dark:text-surface-400" x-text="form.is_active ? 'Active' : 'Inactive'"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Session Types Assignment -->
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Apply to Session Types</label>
                            <p class="text-xs text-surface-400 mb-3">Select which session types this discount applies to. Leave unchecked to apply to all.</p>
                            <div x-show="modalSessionTypesLoading" class="flex items-center gap-2 py-3">
                                <div class="h-4 w-4 rounded-full border-2 border-primary-300 border-t-primary-600 animate-spin"></div>
                                <span class="text-xs text-surface-400">Loading session types...</span>
                            </div>
                            <div x-show="!modalSessionTypesLoading && modalSessionTypes.length === 0" class="text-sm text-surface-400 italic py-2">No session types available for this facility</div>
                            <div x-show="!modalSessionTypesLoading && modalSessionTypes.length > 0" class="grid sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                                <template x-for="st in modalSessionTypes" :key="st.id">
                                    <label class="flex items-center gap-2.5 cursor-pointer group rounded-lg px-3 py-2 hover:bg-surface-50 dark:hover:bg-surface-800/50 transition-colors">
                                        <input type="checkbox" :value="String(st.id)" x-model="form.session_type_ids"
                                               class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500/20 dark:border-surface-600 dark:bg-surface-700">
                                        <div class="leading-tight">
                                            <span class="text-sm text-surface-700 dark:text-surface-300" x-text="st.title"></span>
                                            <span class="block text-[10px] text-surface-400" x-text="st.session_type || ''"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30 flex items-center justify-end gap-3 flex-shrink-0">
                    <button @click="showModal = false" class="px-5 py-2.5 text-sm font-semibold text-surface-600 dark:text-surface-300 border border-surface-200 dark:border-surface-700 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">Cancel</button>
                    <button @click="saveDiscount()" :disabled="saving" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 rounded-xl shadow-soft disabled:opacity-50 transition-all">
                        <template x-if="saving">
                            <div class="h-4 w-4 rounded-full border-2 border-white/30 border-t-white animate-spin"></div>
                        </template>
                        <span x-text="editingId ? 'Update' : 'Create'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function discountsPage() {
    return {
        discounts: [],
        facilities: [],
        modalSessionTypes: [],
        modalSessionTypesLoading: false,
        loading: true,
        saving: false,
        showModal: false,
        editingId: null,
        page: 1,
        total: 0,
        totalPages: 1,
        filters: { facility_id: '', search: '' },
        form: {
            name: '',
            discount_category: '',
            facility_id: '',
            coupon_code: '',
            discount_type: 'percent',
            discount_value: '',
            valid_from: '',
            valid_to: '',
            usage_limit: '',
            is_active: true,
            session_type_ids: [],
        },
        async init() {
            await Promise.all([
                this.loadFacilities(),
                this.loadDiscounts(),
            ]);
        },
        async loadFacilities() {
            try {
                const res = await authFetch(APP_BASE + '/api/facilities');
                const json = await res.json();
                this.facilities = json.data || [];
                // Auto-select if only one facility
                if (this.facilities.length === 1) {
                    this.filters.facility_id = this.facilities[0].id;
                    this.loadDiscounts();
                }
            } catch (e) { console.error(e); }
        },
        async loadModalSessionTypes() {
            this.modalSessionTypesLoading = true;
            this.modalSessionTypes = [];
            try {
                // Fetch all org session types — no facility filter so NULL-facility types are included
                const res = await authFetch(APP_BASE + '/api/session-types?per_page=100');
                const json = await res.json();
                this.modalSessionTypes = json.data || [];
            } catch (e) { console.error(e); }
            this.modalSessionTypesLoading = false;
        },
        initDatePickers() {
            const self = this;
            ['validFrom', 'validTo'].forEach(refName => {
                const el = self.$refs[refName];
                if (!el) return;
                if (el._flatpickr) el._flatpickr.destroy();
                const fp = flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    onChange(selectedDates, dateStr) {
                        if (refName === 'validFrom') self.form.valid_from = dateStr;
                        else self.form.valid_to = dateStr;
                    },
                });
                const val = refName === 'validFrom' ? self.form.valid_from : self.form.valid_to;
                if (val) fp.setDate(val, false);
            });
        },
        onModalFacilityChange() {
            this.form.session_type_ids = [];
            this.loadModalSessionTypes();
        },
        async loadDiscounts() {
            this.loading = true;
            try {
                let url = APP_BASE + '/api/discounts?page=' + this.page + '&per_page=20';
                if (this.filters.facility_id) url += '&facility_id=' + this.filters.facility_id;
                if (this.filters.search) url += '&search=' + encodeURIComponent(this.filters.search);
                const res = await authFetch(url);
                const json = await res.json();
                this.discounts = json.data || [];
                this.total = json.meta?.total || json.total || 0;
                this.totalPages = json.meta?.last_page || Math.ceil(this.total / 20) || 1;
            } catch (e) { console.error(e); }
            this.loading = false;
        },
        resetForm() {
            const defaultFacility = this.filters.facility_id || (this.facilities.length === 1 ? this.facilities[0].id : '');
            this.form = {
                name: '', discount_category: '', facility_id: defaultFacility, coupon_code: '',
                discount_type: 'percent', discount_value: '', valid_from: '', valid_to: '',
                usage_limit: '', is_active: true, session_type_ids: [],
            };
        },
        openCreateModal() {
            this.editingId = null;
            this.resetForm();
            this.showModal = true;
            this.$nextTick(() => {
                this.loadModalSessionTypes();
                this.initDatePickers();
            });
        },
        openEditModal(d) {
            this.editingId = d.id;
            this.form = {
                name: d.name || '',
                discount_category: d.discount_category || '',
                facility_id: d.facility_id || '',
                coupon_code: d.coupon_code || '',
                discount_type: d.discount_type || 'percent',
                discount_value: d.discount_value || '',
                valid_from: d.valid_from || '',
                valid_to: d.valid_to || '',
                usage_limit: d.usage_limit || '',
                is_active: d.is_active == 1,
                session_type_ids: (d.session_type_ids || []).map(String),
            };
            this.showModal = true;
            this.$nextTick(() => {
                this.loadModalSessionTypes();
                this.initDatePickers();
            });
        },
        async saveDiscount() {
            if (!this.form.name || !this.form.discount_type || !this.form.discount_value) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please fill in required fields.' } }));
                return;
            }
            this.saving = true;
            try {
                const body = {
                    ...this.form,
                    facility_id: this.form.facility_id || null,
                    usage_limit: this.form.usage_limit ? parseInt(this.form.usage_limit) : null,
                    valid_from: this.form.valid_from || null,
                    valid_to: this.form.valid_to || null,
                    session_type_ids: this.form.session_type_ids.map(Number).filter(Boolean),
                };

                let url = APP_BASE + '/api/discounts';
                let method = 'POST';
                if (this.editingId) {
                    url += '/' + this.editingId;
                    method = 'PUT';
                }

                const res = await authFetch(url, { method, body: JSON.stringify(body) });
                const json = await res.json();

                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: this.editingId ? 'Discount updated' : 'Discount created' } }));
                    this.showModal = false;
                    await this.loadDiscounts();
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: json.message || 'Failed to save discount.' } }));
                }
            } catch (e) {
                console.error(e);
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Network error.' } }));
            }
            this.saving = false;
        },
        async deleteDiscount(id) {
            if (!confirm('Are you sure you want to delete this discount rule?')) return;
            try {
                const res = await authFetch(APP_BASE + '/api/discounts/' + id, { method: 'DELETE' });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Discount deleted.' } }));
                    await this.loadDiscounts();
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
