<?php
$title = 'Create Membership Plan';
$breadcrumbs = [['label' => 'Membership Plans', 'url' => ($baseUrl ?? '') . '/admin/membership-plans'], ['label' => 'Create']];
$apiBase = $baseUrl ?? '';

ob_start();
?>
<div x-data="membershipPlanForm()" x-init="init()" class="mx-auto max-w-4xl space-y-6">
    <form @submit.prevent="submitForm()">
        <!-- Plan Details -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden mb-6">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white tracking-tight">Plan Details</h3>
                <p class="text-sm text-surface-400 mt-0.5">Define the membership plan details and pricing</p>
            </div>
            <div class="space-y-5 px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Plan Name <span class="text-red-400">*</span></label>
                        <input type="text" x-model="form.name" required placeholder="e.g. Gold Membership" class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                        <template x-if="errors.name"><p class="mt-1 text-xs text-red-500" x-text="errors.name[0]"></p></template>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Facility <span class="text-red-400">*</span></label>
                        <select x-model="form.facility_id" @change="loadSessionTypes()" required class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                            <option value="">Select Facility...</option>
                            <template x-for="f in facilities" :key="f.id">
                                <option :value="f.id" x-text="f.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Description</label>
                    <textarea x-model="form.description" rows="3" placeholder="Plan description visible to players..." class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Duration <span class="text-red-400">*</span></label>
                        <select x-model="form.duration_type" required class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                            <option value="monthly">Monthly (1 Month)</option>
                            <option value="3months">Quarterly (3 Months)</option>
                            <option value="6months">Semi-Annual (6 Months)</option>
                            <option value="12months">Annual (12 Months)</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div x-show="form.duration_type === 'custom'">
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Custom Duration (months)</label>
                        <input type="number" x-model="form.duration_value" min="1" class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Renewal Type</label>
                        <select x-model="form.renewal_type" class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                            <option value="auto">Auto-Renew</option>
                            <option value="manual">Manual Renewal</option>
                            <option value="none">No Renewal</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Price ($) <span class="text-red-400">*</span></label>
                        <input type="number" x-model="form.price" required step="0.01" min="0" placeholder="0.00" class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Setup Fee ($)</label>
                        <input type="number" x-model="form.setup_fee" step="0.01" min="0" placeholder="0.00" class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Max Members</label>
                        <input type="number" x-model="form.max_members" min="1" placeholder="Unlimited" class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Plan Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="form.color" class="h-10 w-14 rounded-lg border border-surface-200 dark:border-surface-700 cursor-pointer">
                            <span class="text-xs text-surface-400" x-text="form.color"></span>
                        </div>
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="inline-flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" x-model="form.is_taxable" class="peer sr-only">
                                <div class="h-5 w-9 rounded-full bg-surface-200 peer-checked:bg-primary-500 dark:bg-surface-700 transition-colors"></div>
                                <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transform peer-checked:translate-x-4 transition-transform"></div>
                            </div>
                            <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Taxable</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Renewal Pricing -->
        <div x-show="form.renewal_type !== 'none'" class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden mb-6">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white tracking-tight">Renewal Pricing</h3>
                <p class="text-sm text-surface-400 mt-0.5">Configure how pricing works when memberships renew</p>
            </div>
            <div class="space-y-5 px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Renewal Price ($)</label>
                        <input type="number" x-model="form.renewal_price" step="0.01" min="0" placeholder="Same as plan price" class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                        <p class="mt-1 text-xs text-surface-400">Leave blank to use the current plan price on renewal</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">Renewal Price Policy</label>
                        <select x-model="form.renewal_price_policy" class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                            <option value="current_price">Current Price — Always charge the latest plan price</option>
                            <option value="locked_price">Locked Price — Grandfather existing members at their signup price</option>
                        </select>
                    </div>
                </div>
                <div class="rounded-xl bg-blue-50 dark:bg-blue-500/5 border border-blue-100 dark:border-blue-800/30 p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            <template x-if="form.renewal_price_policy === 'current_price'">
                                <p><strong>Current Price:</strong> When the plan price changes, all renewing members will be charged the new price. No grandfathering.</p>
                            </template>
                            <template x-if="form.renewal_price_policy === 'locked_price'">
                                <p><strong>Locked Price:</strong> Existing members keep the price they originally signed up at, even if the plan price increases. New members pay the current price.</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Benefits -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden mb-6">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white tracking-tight">Category Benefits</h3>
                <p class="text-sm text-surface-400 mt-0.5">Select which categories are included or discounted — a category can be one or the other, not both</p>
            </div>
            <div class="px-6 py-6">
                <template x-if="categories.length === 0">
                    <p class="text-sm text-surface-400 italic">No categories found. Create categories first.</p>
                </template>
                <div class="space-y-4">
                    <template x-for="cat in categories" :key="cat.id">
                        <div class="rounded-xl border border-surface-100 dark:border-surface-800 overflow-hidden hover:border-surface-200 dark:hover:border-surface-700 transition-colors">
                            <div class="flex flex-wrap items-center gap-4 px-4 py-3 bg-surface-50/30 dark:bg-surface-800/20">
                                <div class="flex-1 min-w-[140px]">
                                    <span class="text-sm font-medium text-surface-800 dark:text-surface-200" x-text="cat.name"></span>
                                </div>
                                <div class="flex items-center gap-6">
                                    <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                        <input type="checkbox"
                                            :checked="catBenefits[cat.id]?.type === 'included'"
                                            @change="toggleCatBenefit(cat.id, 'included', $event.target.checked)"
                                            class="rounded border-surface-300 text-green-500 focus:ring-green-500 dark:border-surface-600 dark:bg-surface-800">
                                        <span class="text-green-600 dark:text-green-400 font-medium">Included</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                        <input type="checkbox"
                                            :checked="catBenefits[cat.id]?.type === 'discounted'"
                                            @change="toggleCatBenefit(cat.id, 'discounted', $event.target.checked)"
                                            class="rounded border-surface-300 text-amber-500 focus:ring-amber-500 dark:border-surface-600 dark:bg-surface-800">
                                        <span class="text-amber-600 dark:text-amber-400 font-medium">Discounted</span>
                                    </label>
                                </div>
                            </div>
                            <!-- Included options: price override, usage limit, usage period -->
                            <template x-if="catBenefits[cat.id]?.type === 'included'">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-4 py-3 bg-green-50/50 dark:bg-green-500/5 border-t border-green-100 dark:border-green-800/20">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-green-700 dark:text-green-400">Price Override ($)</label>
                                        <input type="number" step="0.01" min="0" placeholder="Plan price"
                                            :value="catBenefits[cat.id]?.price || ''"
                                            @input="catBenefits[cat.id].price = $event.target.value"
                                            class="w-full rounded-lg border border-green-200 dark:border-green-800/40 bg-white dark:bg-surface-800 px-3 py-2 text-xs dark:text-white">
                                        <p class="mt-0.5 text-[10px] text-surface-400">Custom price for this category</p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-green-700 dark:text-green-400">Usage Limit</label>
                                        <input type="number" min="1" placeholder="Unlimited"
                                            :value="catBenefits[cat.id]?.usage_limit || ''"
                                            @input="catBenefits[cat.id].usage_limit = $event.target.value"
                                            class="w-full rounded-lg border border-green-200 dark:border-green-800/40 bg-white dark:bg-surface-800 px-3 py-2 text-xs dark:text-white">
                                        <p class="mt-0.5 text-[10px] text-surface-400">Max uses per period</p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-green-700 dark:text-green-400">Usage Period</label>
                                        <select :value="catBenefits[cat.id]?.usage_period || 'unlimited'"
                                            @change="catBenefits[cat.id].usage_period = $event.target.value"
                                            class="w-full rounded-lg border border-green-200 dark:border-green-800/40 bg-white dark:bg-surface-800 px-3 py-2 text-xs dark:text-white">
                                            <option value="unlimited">Unlimited</option>
                                            <option value="day">Per Day</option>
                                            <option value="week">Per Week</option>
                                            <option value="month">Per Month</option>
                                        </select>
                                    </div>
                                </div>
                            </template>
                            <!-- Discounted options: discount percentage -->
                            <template x-if="catBenefits[cat.id]?.type === 'discounted'">
                                <div class="px-4 py-3 bg-amber-50/50 dark:bg-amber-500/5 border-t border-amber-100 dark:border-amber-800/20">
                                    <div class="max-w-[200px]">
                                        <label class="mb-1 block text-xs font-medium text-amber-700 dark:text-amber-400">Discount (%)</label>
                                        <input type="number" min="1" max="100" placeholder="% off"
                                            :value="catBenefits[cat.id]?.discount_percentage || ''"
                                            @input="catBenefits[cat.id].discount_percentage = $event.target.value"
                                            class="w-full rounded-lg border border-amber-200 dark:border-amber-800/40 bg-white dark:bg-surface-800 px-3 py-2 text-xs dark:text-white">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Session Type Benefits -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden mb-6">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white tracking-tight">Session Type Benefits</h3>
                <p class="text-sm text-surface-400 mt-0.5">Select which session types are included or discounted</p>
            </div>
            <div class="px-6 py-6">
                <template x-if="!form.facility_id">
                    <p class="text-sm text-surface-400 italic">Please select a facility first to see available session types.</p>
                </template>
                <template x-if="form.facility_id && sessionTypes.length === 0">
                    <p class="text-sm text-surface-400 italic">No session types found for this facility.</p>
                </template>
                <div class="space-y-4">
                    <template x-for="st in sessionTypes" :key="st.id">
                        <div class="rounded-xl border border-surface-100 dark:border-surface-800 overflow-hidden hover:border-surface-200 dark:hover:border-surface-700 transition-colors">
                            <div class="flex flex-wrap items-center gap-4 px-4 py-3 bg-surface-50/30 dark:bg-surface-800/20">
                                <div class="flex-1 min-w-[140px]">
                                    <span class="text-sm font-medium text-surface-800 dark:text-surface-200" x-text="st.name || st.title"></span>
                                    <template x-if="st.standard_price">
                                        <span class="ml-2 text-xs text-surface-400" x-text="'(Standard: $' + parseFloat(st.standard_price).toFixed(2) + ')'"></span>
                                    </template>
                                </div>
                                <div class="flex items-center gap-6">
                                    <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                        <input type="checkbox"
                                            :checked="stBenefits[st.id]?.type === 'included'"
                                            @change="toggleStBenefit(st.id, 'included', $event.target.checked)"
                                            class="rounded border-surface-300 text-green-500 focus:ring-green-500 dark:border-surface-600 dark:bg-surface-800">
                                        <span class="text-green-600 dark:text-green-400 font-medium">Included</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                        <input type="checkbox"
                                            :checked="stBenefits[st.id]?.type === 'discounted'"
                                            @change="toggleStBenefit(st.id, 'discounted', $event.target.checked)"
                                            class="rounded border-surface-300 text-amber-500 focus:ring-amber-500 dark:border-surface-600 dark:bg-surface-800">
                                        <span class="text-amber-600 dark:text-amber-400 font-medium">Discounted</span>
                                    </label>
                                </div>
                            </div>
                            <!-- Included options -->
                            <template x-if="stBenefits[st.id]?.type === 'included'">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-4 py-3 bg-green-50/50 dark:bg-green-500/5 border-t border-green-100 dark:border-green-800/20">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-green-700 dark:text-green-400">Price Override ($)</label>
                                        <input type="number" step="0.01" min="0" placeholder="Standard price"
                                            :value="stBenefits[st.id]?.price || ''"
                                            @input="stBenefits[st.id].price = $event.target.value"
                                            class="w-full rounded-lg border border-green-200 dark:border-green-800/40 bg-white dark:bg-surface-800 px-3 py-2 text-xs dark:text-white">
                                        <p class="mt-0.5 text-[10px] text-surface-400">Custom member price</p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-green-700 dark:text-green-400">Usage Limit</label>
                                        <input type="number" min="1" placeholder="Unlimited"
                                            :value="stBenefits[st.id]?.usage_limit || ''"
                                            @input="stBenefits[st.id].usage_limit = $event.target.value"
                                            class="w-full rounded-lg border border-green-200 dark:border-green-800/40 bg-white dark:bg-surface-800 px-3 py-2 text-xs dark:text-white">
                                        <p class="mt-0.5 text-[10px] text-surface-400">Max uses per period</p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-green-700 dark:text-green-400">Usage Period</label>
                                        <select :value="stBenefits[st.id]?.usage_period || 'unlimited'"
                                            @change="stBenefits[st.id].usage_period = $event.target.value"
                                            class="w-full rounded-lg border border-green-200 dark:border-green-800/40 bg-white dark:bg-surface-800 px-3 py-2 text-xs dark:text-white">
                                            <option value="unlimited">Unlimited</option>
                                            <option value="day">Per Day</option>
                                            <option value="week">Per Week</option>
                                            <option value="month">Per Month</option>
                                        </select>
                                    </div>
                                </div>
                            </template>
                            <!-- Discounted options -->
                            <template x-if="stBenefits[st.id]?.type === 'discounted'">
                                <div class="px-4 py-3 bg-amber-50/50 dark:bg-amber-500/5 border-t border-amber-100 dark:border-amber-800/20">
                                    <div class="max-w-[200px]">
                                        <label class="mb-1 block text-xs font-medium text-amber-700 dark:text-amber-400">Discount (%)</label>
                                        <input type="number" min="1" max="100" placeholder="% off"
                                            :value="stBenefits[st.id]?.discount_percentage || ''"
                                            @input="stBenefits[st.id].discount_percentage = $event.target.value"
                                            class="w-full rounded-lg border border-amber-200 dark:border-amber-800/40 bg-white dark:bg-surface-800 px-3 py-2 text-xs dark:text-white">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="<?= ($baseUrl ?? '') . '/admin/membership-plans' ?>" class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-5 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-soft">Cancel</a>
            <button type="submit" :disabled="submitting" class="rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-2.5 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 disabled:opacity-50 shadow-soft transition-all">
                <span x-show="!submitting">Create Membership Plan</span>
                <span x-show="submitting" class="flex items-center gap-2"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Creating...</span>
            </button>
        </div>
    </form>
</div>

<script>
function membershipPlanForm() {
    return {
        form: {
            name: '', facility_id: '', description: '',
            duration_type: 'monthly', duration_value: '',
            price: '', setup_fee: '', renewal_type: 'auto',
            renewal_price: '', renewal_price_policy: 'current_price',
            is_taxable: false, color: '#6366f1', max_members: '',
        },
        facilities: [],
        categories: [],
        sessionTypes: [],
        catBenefits: {},   // { [catId]: { type:'included'|'discounted', price:'', usage_limit:'', usage_period:'unlimited', discount_percentage:'' } }
        stBenefits: {},    // same structure with session_type_id
        errors: {},
        submitting: false,

        async init() {
            await Promise.all([this.loadFacilities(), this.loadCategories()]);
        },

        async loadFacilities() {
            try {
                const res = await authFetch(APP_BASE + '/api/facilities');
                const json = await res.json();
                this.facilities = json.data || [];
            } catch (e) { console.error('Failed to load facilities', e); }
        },

        async loadCategories() {
            try {
                const res = await authFetch(APP_BASE + '/api/categories');
                const json = await res.json();
                this.categories = json.data || [];
            } catch (e) { console.error('Failed to load categories', e); }
        },

        async loadSessionTypes() {
            this.sessionTypes = [];
            this.stBenefits = {};
            if (!this.form.facility_id) return;
            try {
                const res = await authFetch(APP_BASE + '/api/session-types?facility_id=' + this.form.facility_id);
                const json = await res.json();
                this.sessionTypes = json.data || [];
            } catch (e) { console.error('Failed to load session types', e); }
        },

        toggleCatBenefit(catId, type, checked) {
            if (checked) {
                this.catBenefits[catId] = { type, price: '', usage_limit: '', usage_period: 'unlimited', discount_percentage: '' };
            } else if (this.catBenefits[catId]?.type === type) {
                delete this.catBenefits[catId];
            }
        },

        toggleStBenefit(stId, type, checked) {
            if (checked) {
                this.stBenefits[stId] = { type, price: '', usage_limit: '', usage_period: 'unlimited', discount_percentage: '' };
            } else if (this.stBenefits[stId]?.type === type) {
                delete this.stBenefits[stId];
            }
        },

        buildBenefitPayload() {
            const included_categories = [];
            const discounted_categories = [];
            for (const [id, b] of Object.entries(this.catBenefits)) {
                if (b.type === 'included') {
                    const item = { category_id: parseInt(id) };
                    if (b.price !== '' && b.price !== null) item.price = parseFloat(b.price);
                    if (b.usage_limit !== '' && b.usage_limit !== null) item.usage_limit = parseInt(b.usage_limit);
                    if (b.usage_period && b.usage_period !== 'unlimited') item.usage_period = b.usage_period;
                    included_categories.push(item);
                } else if (b.type === 'discounted') {
                    discounted_categories.push({ category_id: parseInt(id), discount_percentage: parseFloat(b.discount_percentage || 0) });
                }
            }
            const included_session_types = [];
            const discounted_session_types = [];
            for (const [id, b] of Object.entries(this.stBenefits)) {
                if (b.type === 'included') {
                    const item = { session_type_id: parseInt(id) };
                    if (b.price !== '' && b.price !== null) item.price = parseFloat(b.price);
                    if (b.usage_limit !== '' && b.usage_limit !== null) item.usage_limit = parseInt(b.usage_limit);
                    if (b.usage_period && b.usage_period !== 'unlimited') item.usage_period = b.usage_period;
                    included_session_types.push(item);
                } else if (b.type === 'discounted') {
                    discounted_session_types.push({ session_type_id: parseInt(id), discount_percentage: parseFloat(b.discount_percentage || 0) });
                }
            }
            return { included_categories, discounted_categories, included_session_types, discounted_session_types };
        },

        async submitForm() {
            this.submitting = true; this.errors = {};
            try {
                const benefits = this.buildBenefitPayload();
                const body = new FormData();
                Object.keys(this.form).forEach(k => {
                    let v = this.form[k];
                    if (typeof v === 'boolean') v = v ? '1' : '0';
                    if (v !== '' && v !== null) body.append(k, v);
                });
                body.append('included_categories', JSON.stringify(benefits.included_categories));
                body.append('discounted_categories', JSON.stringify(benefits.discounted_categories));
                body.append('included_session_types', JSON.stringify(benefits.included_session_types));
                body.append('discounted_session_types', JSON.stringify(benefits.discounted_session_types));

                const res = await authFetch(APP_BASE + '/api/membership-plans', { method: 'POST', body });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Membership plan created', type: 'success' } }));
                    setTimeout(() => window.location.href = '<?= ($baseUrl ?? '') ?>/admin/membership-plans', 600);
                } else {
                    this.errors = json.errors || {};
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Validation failed', type: 'error' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
            this.submitting = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
