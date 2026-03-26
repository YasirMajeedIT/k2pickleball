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
                        <select x-model="form.facility_id" required class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
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

        <!-- Category Benefits -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden mb-6">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white tracking-tight">Category Benefits</h3>
                <p class="text-sm text-surface-400 mt-0.5">Select which categories are included or discounted for this plan</p>
            </div>
            <div class="px-6 py-6">
                <template x-if="categories.length === 0">
                    <p class="text-sm text-surface-400 italic">No categories found. Create categories first.</p>
                </template>
                <div class="space-y-3">
                    <template x-for="cat in categories" :key="cat.id">
                        <div class="flex flex-wrap items-center gap-4 rounded-xl border border-surface-100 dark:border-surface-800 px-4 py-3 hover:bg-surface-50/50 dark:hover:bg-surface-800/30 transition-colors">
                            <div class="flex-1 min-w-[140px]">
                                <span class="text-sm font-medium text-surface-800 dark:text-surface-200" x-text="cat.name"></span>
                            </div>
                            <div class="flex items-center gap-6">
                                <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                    <input type="checkbox" :value="cat.id" x-model="includedCategories" class="rounded border-surface-300 text-green-500 focus:ring-green-500 dark:border-surface-600 dark:bg-surface-800">
                                    <span class="text-green-600 dark:text-green-400 font-medium">Included</span>
                                </label>
                                <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                    <input type="checkbox" :value="cat.id" x-model="discountedCategories" class="rounded border-surface-300 text-amber-500 focus:ring-amber-500 dark:border-surface-600 dark:bg-surface-800">
                                    <span class="text-amber-600 dark:text-amber-400 font-medium">Discounted</span>
                                </label>
                                <template x-if="discountedCategories.includes(String(cat.id)) || discountedCategories.includes(cat.id)">
                                    <div class="flex items-center gap-2">
                                        <input type="number" :data-cat-discount="cat.id" min="1" max="100" placeholder="% off" class="w-20 rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-2.5 py-1.5 text-xs text-center dark:text-white">
                                    </div>
                                </template>
                            </div>
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
                <template x-if="sessionTypes.length === 0">
                    <p class="text-sm text-surface-400 italic">No session types found. Create session types first.</p>
                </template>
                <div class="space-y-3">
                    <template x-for="st in sessionTypes" :key="st.id">
                        <div class="flex flex-wrap items-center gap-4 rounded-xl border border-surface-100 dark:border-surface-800 px-4 py-3 hover:bg-surface-50/50 dark:hover:bg-surface-800/30 transition-colors">
                            <div class="flex-1 min-w-[140px]">
                                <span class="text-sm font-medium text-surface-800 dark:text-surface-200" x-text="st.name"></span>
                            </div>
                            <div class="flex items-center gap-6">
                                <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                    <input type="checkbox" :value="st.id" x-model="includedSessionTypes" class="rounded border-surface-300 text-green-500 focus:ring-green-500 dark:border-surface-600 dark:bg-surface-800">
                                    <span class="text-green-600 dark:text-green-400 font-medium">Included</span>
                                </label>
                                <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                                    <input type="checkbox" :value="st.id" x-model="discountedSessionTypes" class="rounded border-surface-300 text-amber-500 focus:ring-amber-500 dark:border-surface-600 dark:bg-surface-800">
                                    <span class="text-amber-600 dark:text-amber-400 font-medium">Discounted</span>
                                </label>
                                <template x-if="discountedSessionTypes.includes(String(st.id)) || discountedSessionTypes.includes(st.id)">
                                    <div class="flex items-center gap-2">
                                        <input type="number" :data-st-discount="st.id" min="1" max="100" placeholder="% off" class="w-20 rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-2.5 py-1.5 text-xs text-center dark:text-white">
                                    </div>
                                </template>
                            </div>
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
            is_taxable: false, color: '#6366f1', max_members: '',
        },
        facilities: [],
        categories: [],
        sessionTypes: [],
        includedCategories: [],
        discountedCategories: [],
        includedSessionTypes: [],
        discountedSessionTypes: [],
        errors: {},
        submitting: false,
        async init() {
            await Promise.all([this.loadFacilities(), this.loadCategories(), this.loadSessionTypes()]);
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
            try {
                const res = await authFetch(APP_BASE + '/api/session-types');
                const json = await res.json();
                this.sessionTypes = json.data || [];
            } catch (e) { console.error('Failed to load session types', e); }
        },
        buildBenefitPayload() {
            const included_categories = this.includedCategories.map(id => ({ category_id: parseInt(id) }));
            const discounted_categories = this.discountedCategories.map(id => {
                const el = document.querySelector(`[data-cat-discount="${id}"]`);
                return { category_id: parseInt(id), discount_percentage: parseFloat(el?.value || 0) };
            });
            const included_session_types = this.includedSessionTypes.map(id => ({ session_type_id: parseInt(id) }));
            const discounted_session_types = this.discountedSessionTypes.map(id => {
                const el = document.querySelector(`[data-st-discount="${id}"]`);
                return { session_type_id: parseInt(id), discount_percentage: parseFloat(el?.value || 0) };
            });
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
