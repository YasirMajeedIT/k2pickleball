<?php
$title = 'My Subscription';
$breadcrumbs = [['label' => 'My Subscription']];
ob_start();

$sqAppId = htmlspecialchars($_ENV['SQUARE_APPLICATION_ID'] ?? '', ENT_QUOTES);
$sqLocId = htmlspecialchars($_ENV['SQUARE_LOCATION_ID'] ?? '', ENT_QUOTES);
$sqEnv   = ($_ENV['SQUARE_ENVIRONMENT'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox';
?>

<div x-data="subscriptionPage()" x-init="init()">
    <!-- Current Plan -->
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-8">
        <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30">
            <h5 class="text-base font-bold text-surface-900 dark:text-white">Current Plan</h5>
            <p class="text-xs text-surface-400 mt-0.5">Your active subscription details</p>
        </div>
        <div class="p-6">
            <!-- Loading -->
            <div x-show="loading" class="animate-pulse space-y-4">
                <div class="h-6 bg-surface-200 dark:bg-surface-800 rounded w-32"></div>
                <div class="h-10 bg-surface-200 dark:bg-surface-800 rounded w-24"></div>
                <div class="h-4 bg-surface-200 dark:bg-surface-800 rounded w-48"></div>
            </div>

            <template x-if="!loading && currentSub">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl font-bold text-surface-900 dark:text-white" x-text="currentSub.plan_name"></span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="{
                            'bg-emerald-500/10 text-emerald-500': currentSub.status === 'active',
                            'bg-amber-500/10 text-amber-500': currentSub.status === 'pending',
                            'bg-red-500/10 text-red-500': currentSub.status === 'cancelled'
                        }" x-text="currentSub.status"></span>
                    </div>
                    <div class="flex items-baseline gap-1 mb-1">
                        <span class="text-4xl font-extrabold text-surface-900 dark:text-white" x-text="'$' + parseFloat(currentSub.price || 0).toFixed(2)"></span>
                        <span class="text-surface-400" x-text="'/' + (currentSub.billing_cycle || 'month')"></span>
                    </div>
                    <div class="text-sm text-surface-500" x-show="currentSub.current_period_end">
                        Next billing: <span x-text="new Date(currentSub.current_period_end).toLocaleDateString()"></span>
                    </div>
                </div>
            </template>
            <template x-if="!loading && !currentSub">
                <div class="text-surface-400">
                    <p>No active subscription. Choose a plan below to get started.</p>
                </div>
            </template>
        </div>
    </div>

    <!-- Available Plans -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h5 class="text-base font-bold text-surface-900 dark:text-white">Available Plans</h5>
            <div class="flex items-center gap-3">
                <span class="text-sm" :class="!annual ? 'text-surface-900 dark:text-white font-medium' : 'text-surface-400'">Monthly</span>
                <button @click="annual = !annual" class="relative h-6 w-11 rounded-full transition-colors" :class="annual ? 'bg-primary-600' : 'bg-surface-300 dark:bg-surface-700'">
                    <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform" :class="annual ? 'translate-x-5' : ''"></span>
                </button>
                <span class="text-sm" :class="annual ? 'text-surface-900 dark:text-white font-medium' : 'text-surface-400'">Annual
                    <span class="ml-1 text-xs font-semibold text-emerald-500">Save ~17%</span>
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <template x-for="plan in plans" :key="plan.id">
                <div class="rounded-2xl border p-6 transition-all flex flex-col"
                     :class="isCurrent(plan) ? 'border-primary-500/40 bg-primary-500/5 dark:bg-primary-500/5 ring-1 ring-primary-500/30' : 'border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900'">

                    <!-- Plan header -->
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-lg font-bold text-surface-900 dark:text-white" x-text="plan.name"></h3>
                        <span x-show="isCurrent(plan)" class="px-2 py-0.5 rounded-full bg-primary-500/10 text-primary-500 text-xs font-semibold">Current</span>
                    </div>
                    <p class="text-sm text-surface-400 mb-4" x-text="plan.description"></p>

                    <!-- Price -->
                    <div class="flex items-baseline gap-1 mb-5">
                        <span class="text-3xl font-extrabold text-surface-900 dark:text-white"
                              x-text="'$' + (annual ? Math.round(plan.price_yearly || 0) : (plan.price_monthly > 0 ? parseFloat(plan.price_monthly).toFixed(2).replace(/\.00$/, '') : '0'))"></span>
                        <template x-if="plan.price_monthly > 0">
                            <span class="text-sm text-surface-400" x-text="annual ? '/year' : '/month'"></span>
                        </template>
                        <template x-if="plan.price_monthly == 0">
                            <span class="text-sm text-surface-400">forever</span>
                        </template>
                    </div>

                    <!-- Limits -->
                    <div class="flex flex-wrap gap-2 mb-5">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-100 dark:bg-surface-800 rounded-lg text-xs font-medium text-surface-600 dark:text-surface-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span x-text="plan.max_facilities ? plan.max_facilities + ' facilit' + (plan.max_facilities == 1 ? 'y' : 'ies') : 'Unlimited facilities'"></span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-100 dark:bg-surface-800 rounded-lg text-xs font-medium text-surface-600 dark:text-surface-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-text="plan.max_users ? plan.max_users + ' user' + (plan.max_users == 1 ? '' : 's') : 'Unlimited users'"></span>
                        </span>
                    </div>

                    <!-- Features -->
                    <ul class="space-y-2 mb-6 flex-1">
                        <template x-for="feat in getPlanFeatureLabels(plan)" :key="feat">
                            <li class="flex items-start gap-2 text-sm text-surface-600 dark:text-surface-400">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="feat"></span>
                            </li>
                        </template>
                    </ul>

                    <!-- Action button -->
                    <template x-if="isCurrent(plan)">
                        <button disabled class="w-full py-2.5 rounded-xl text-sm font-semibold bg-surface-100 dark:bg-surface-800 text-surface-400 cursor-not-allowed">Current Plan</button>
                    </template>
                    <template x-if="!isCurrent(plan)">
                        <button
                            @click="changePlan(plan)"
                            :disabled="changingPlan || loading"
                            class="w-full py-2.5 rounded-xl text-sm font-semibold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="getPlanAction(plan).cls"
                            x-text="getPlanAction(plan).label">
                        </button>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="message" x-transition class="mb-6 p-4 rounded-xl text-sm"
         :class="messageType === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-500' : 'bg-red-500/10 border border-red-500/20 text-red-400'"
         x-text="message"></div>

    <!-- Downgrade Confirmation Modal -->
    <div x-show="showDowngradeModal" x-transition.opacity class="fixed inset-0 z-[9999] bg-surface-950/60 backdrop-blur-sm flex items-center justify-center p-4" x-cloak @click.self="showDowngradeModal=false">
        <div x-show="showDowngradeModal" x-transition class="w-full max-w-md bg-white dark:bg-surface-900 rounded-2xl border border-surface-200 dark:border-surface-800 p-6 shadow-2xl">
            <div class="text-center mb-5">
                <div class="w-14 h-14 mx-auto rounded-full bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-surface-900 dark:text-white">Downgrade to <span class="text-amber-500" x-text="selectedPlan?.name"></span>?</h3>
                <p class="text-sm text-surface-400 mt-1">This change takes effect immediately.</p>
            </div>

            <!-- Lost features -->
            <template x-if="getLostFeatures(selectedPlan).length > 0">
                <div class="mb-4 p-4 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-200 dark:border-amber-500/30">
                    <p class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wide mb-2">Features you'll lose</p>
                    <ul class="space-y-1">
                        <template x-for="feat in getLostFeatures(selectedPlan)" :key="feat">
                            <li class="flex items-center gap-2 text-sm text-amber-700 dark:text-amber-400">
                                <svg class="w-3.5 h-3.5 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span x-text="feat"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </template>

            <p class="text-sm text-surface-600 dark:text-surface-400 mb-6">
                Any data associated with higher-tier features may become inaccessible. This action cannot be undone automatically — you will need to re-subscribe to restore access.
            </p>

            <div class="flex gap-3">
                <button @click="showDowngradeModal=false" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-surface-600 dark:text-surface-300 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">Cancel</button>
                <button @click="confirmDowngrade()" :disabled="changingPlan" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-amber-500 hover:bg-amber-400 disabled:opacity-50 transition-colors">
                    <span x-show="!changingPlan">Confirm Downgrade</span>
                    <span x-show="changingPlan" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Downgrading...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div x-show="showPayment" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="showPayment=false">
        <div class="w-full max-w-md bg-white dark:bg-surface-900 rounded-2xl border border-surface-200 dark:border-surface-800 p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white">
                    <span x-text="currentSub ? 'Upgrade to' : 'Subscribe to'"></span>
                    <span class="text-primary-500" x-text="' ' + (selectedPlan?.name ?? '')"></span>
                </h3>
                <button @click="showPayment=false;paying=false;payError=''" class="text-surface-400 hover:text-surface-600 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <!-- Order summary -->
            <div class="mb-5 p-4 bg-surface-50 dark:bg-surface-800/50 rounded-xl space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-surface-400">Plan</span>
                    <span class="text-surface-900 dark:text-white font-medium" x-text="selectedPlan?.name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-400">Billing</span>
                    <span class="text-surface-900 dark:text-white" x-text="annual ? 'Annual' : 'Monthly'"></span>
                </div>
                <div class="flex justify-between border-t border-surface-200 dark:border-surface-700 pt-2 mt-2 font-bold">
                    <span class="text-surface-900 dark:text-white">Total due today</span>
                    <span class="text-primary-500" x-text="'$' + (annual ? (selectedPlan?.price_yearly||0).toFixed(2) : (selectedPlan?.price_monthly||0).toFixed(2))"></span>
                </div>
            </div>
            <!-- Square Card Input -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-medium text-surface-400">Card Details</label>
                    <span class="text-xs text-surface-500">Powered by Square</span>
                </div>
                <div id="sq-upgrade-card" class="min-h-[52px] px-3 py-2 rounded-xl bg-surface-50 dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60"></div>
            </div>
            <p x-show="payError" class="text-red-500 text-xs mb-3" x-text="payError"></p>
            <button @click="submitUpgrade()" :disabled="paying || !sqCardReady" class="w-full py-3.5 font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 rounded-xl shadow-soft transition-all">
                <span x-show="!paying">Pay &amp; Upgrade</span>
                <span x-show="paying" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                    Processing...
                </span>
            </button>
        </div>
    </div>
</div>

<script>
const SQ_APP_ID = '<?= $sqAppId ?>';
const SQ_LOC_ID = '<?= $sqLocId ?>';
const SQ_ENV    = '<?= $sqEnv ?>';

function subscriptionPage() {
    return {
        currentSub: null,
        plans: [],
        annual: false,
        loading: true,
        changingPlan: false,
        message: '',
        messageType: 'success',
        // Payment modal (upgrade)
        showPayment: false,
        selectedPlan: null,
        sqPayments: null,
        sqCard: null,
        sqCardReady: false,
        paying: false,
        payError: '',
        // Downgrade modal
        showDowngradeModal: false,

        featureLabels: {
            'basic_scheduling':         'Basic Scheduling',
            'court_management':         'Court Management',
            'player_profiles':          'Player Profiles',
            'online_payments':          'Online Payments',
            'tournament_management':    'Tournament Management',
            'email_notifications':      'Email Notifications',
            'reports_analytics':        'Reports & Analytics',
            'api_access':               'API Access',
            'custom_branding':          'Custom Branding',
            'priority_support':         'Priority Support',
            'sla_guarantee':            'SLA Guarantee',
            'dedicated_account_manager':'Dedicated Account Manager',
            'custom_integrations':      'Custom Integrations',
        },

        async init() {
            if (SQ_APP_ID && !window.Square) {
                const s = document.createElement('script');
                s.src = `https://${SQ_ENV === 'production' ? 'web' : 'sandbox.web'}.squarecdn.com/v1/square.js`;
                document.head.appendChild(s);
            }

            try {
                const [subData, planData] = await Promise.all([
                    authFetch(APP_BASE + '/api/subscriptions/current').then(r => r.json()),
                    authFetch(APP_BASE + '/api/plans?per_page=50').then(r => r.json()),
                ]);

                const sub = subData.data || subData;
                if (sub && sub.id) {
                    this.currentSub = sub;
                    if (sub.plan) {
                        this.currentSub.plan_name = sub.plan.name || '';
                        this.currentSub.price = sub.billing_cycle === 'yearly'
                            ? parseFloat(sub.plan.price_yearly || 0)
                            : parseFloat(sub.plan.price_monthly || 0);
                    }
                }

                const p = planData.data || planData;
                this.plans = (Array.isArray(p) ? p : []).map(pl => ({
                    ...pl,
                    price_monthly: parseFloat(pl.price_monthly) || 0,
                    price_yearly:  parseFloat(pl.price_yearly)  || 0,
                    sort_order:    parseInt(pl.sort_order)       || 0,
                }));
            } catch (e) { console.error(e); }
            this.loading = false;
        },

        // --- Helpers ---

        isCurrent(plan) {
            return this.currentSub && this.currentSub.plan_id == plan.id;
        },

        getPlanTier(plan) {
            return plan ? (plan.sort_order || 0) : 0;
        },

        parseRawFeatures(plan) {
            if (!plan || !plan.features) return [];
            try {
                return typeof plan.features === 'string' ? JSON.parse(plan.features) : (plan.features || []);
            } catch { return []; }
        },

        getPlanFeatureLabels(plan) {
            return this.parseRawFeatures(plan).map(k => this.featureLabels[k] || k.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
        },

        getLostFeatures(newPlan) {
            if (!newPlan || !this.currentSub) return [];
            const currentPlan = this.plans.find(p => p.id == this.currentSub.plan_id)
                             || this.currentSub.plan;
            if (!currentPlan) return [];
            const currentFeats = new Set(this.parseRawFeatures(currentPlan));
            const newFeats     = new Set(this.parseRawFeatures(newPlan));
            return [...currentFeats]
                .filter(f => !newFeats.has(f))
                .map(k => this.featureLabels[k] || k.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
        },

        getPlanAction(plan) {
            if (this.isCurrent(plan)) return { label: 'Current Plan', cls: 'bg-surface-100 dark:bg-surface-800 text-surface-400 cursor-not-allowed' };

            const currentPlan = this.currentSub
                ? (this.plans.find(p => p.id == this.currentSub.plan_id) || this.currentSub.plan)
                : null;

            const currentTier = this.getPlanTier(currentPlan);
            const newTier     = this.getPlanTier(plan);

            if (!this.currentSub || newTier > currentTier) {
                return { label: plan.price_monthly > 0 ? 'Upgrade' : 'Get Started', cls: 'bg-primary-600 hover:bg-primary-500 text-white shadow-soft' };
            }
            if (newTier < currentTier) {
                return { label: 'Downgrade', cls: 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-500/40' };
            }
            return { label: 'Switch', cls: 'bg-primary-600 hover:bg-primary-500 text-white shadow-soft' };
        },

        // --- Actions ---

        async changePlan(plan) {
            if (this.isCurrent(plan) || this.changingPlan) return;
            this.message = '';

            const action = this.getPlanAction(plan);

            if (action.label === 'Downgrade') {
                this.selectedPlan = plan;
                this.showDowngradeModal = true;
                return;
            }

            // Upgrade / new subscription — needs payment for paid plans
            if (plan.price_monthly > 0) {
                this.selectedPlan = plan;
                this.showPayment = true;
                this.paying = false;
                this.payError = '';
                this.$nextTick(() => this.initSquareCard());
                return;
            }

            // Free plan (shouldn't occur as upgrade, but handle)
            if (!confirm(`Subscribe to the ${plan.name} plan?`)) return;
            this.changingPlan = true;
            try {
                const res = await this._doChangePlan(plan.id, 'monthly');
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: `Subscribed to ${plan.name}.` } }));
                    await this.init();
                } else {
                    const e = await res.json();
                    this.message = e.message || 'Failed to change plan.';
                    this.messageType = 'error';
                }
            } catch { this.message = 'Network error.'; this.messageType = 'error'; }
            this.changingPlan = false;
        },

        async confirmDowngrade() {
            this.changingPlan = true;
            try {
                const cycle = this.selectedPlan.price_monthly > 0 ? (this.annual ? 'yearly' : 'monthly') : 'monthly';
                const res = await this._doChangePlan(this.selectedPlan.id, cycle);
                this.showDowngradeModal = false;
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: `Downgraded to ${this.selectedPlan.name} plan.` } }));
                    await this.init();
                } else {
                    const e = await res.json();
                    this.message = e.message || 'Failed to downgrade.';
                    this.messageType = 'error';
                }
            } catch { this.message = 'Network error.'; this.messageType = 'error'; }
            this.changingPlan = false;
        },

        async _doChangePlan(planId, cycle) {
            if (this.currentSub) {
                return authFetch(`${APP_BASE}/api/subscriptions/${this.currentSub.id}/change-plan`, {
                    method: 'PUT',
                    body: JSON.stringify({ plan_id: planId, billing_cycle: cycle }),
                });
            }
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            return authFetch(`${APP_BASE}/api/subscriptions`, {
                method: 'POST',
                body: JSON.stringify({ organization_id: user.organization_id, plan_id: planId, billing_cycle: cycle }),
            });
        },

        async initSquareCard() {
            if (!SQ_APP_ID) return;
            let attempts = 0;
            while (!window.Square && attempts < 20) {
                await new Promise(r => setTimeout(r, 200));
                attempts++;
            }
            if (!window.Square) { console.warn('Square SDK not loaded'); return; }
            if (this.sqCard) { await this.sqCard.attach('#sq-upgrade-card'); this.sqCardReady = true; return; }
            try {
                this.sqPayments = Square.payments(SQ_APP_ID, SQ_LOC_ID);
                this.sqCard = await this.sqPayments.card({
                    style: {
                        '.input-container': { borderColor: '#334155', borderRadius: '12px' },
                        '.input-container.is-focus': { borderColor: '#6366f1' },
                        '.message-text': { color: '#f87171' },
                        '.message-icon': { color: '#f87171' },
                        input: { backgroundColor: '#0f172a', color: '#e2e8f0', fontSize: '15px' },
                        'input.is-focus': { backgroundColor: '#0f172a' },
                        'input::placeholder': { color: '#475569' },
                    }
                });
                await this.sqCard.attach('#sq-upgrade-card');
                this.sqCardReady = true;
            } catch(e) { console.warn('Square init:', e); }
        },

        async submitUpgrade() {
            if (!this.sqCard || !this.sqCardReady) { this.payError = 'Payment form not loaded. Please try again.'; return; }
            this.payError = ''; this.paying = true;
            try {
                const tokenResult = await this.sqCard.tokenize();
                if (tokenResult.status !== 'OK') {
                    this.payError = (tokenResult.errors || []).map(e => e.message).join(', ') || 'Card error. Please check your details.';
                    this.paying = false; return;
                }
                const price = this.annual ? (this.selectedPlan.price_yearly || 0) : (this.selectedPlan.price_monthly || 0);
                const payRes = await authFetch(APP_BASE + '/api/payments', {
                    method: 'POST',
                    body: JSON.stringify({ source_id: tokenResult.token, amount: Math.round(price * 100), currency: 'USD', description: `${this.selectedPlan.name} subscription` })
                });
                const payData = await payRes.json();
                if (!payRes.ok) { this.payError = payData.message || 'Payment failed. Please try a different card.'; this.paying = false; return; }

                const cycle = this.annual ? 'yearly' : 'monthly';
                const subRes = await this._doChangePlan(this.selectedPlan.id, cycle);
                this.showPayment = false;
                this.sqCard = null; this.sqCardReady = false;
                if (subRes.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: `Successfully upgraded to ${this.selectedPlan.name}!` } }));
                } else {
                    this.message = 'Payment received but subscription update failed. Please contact support.';
                    this.messageType = 'error';
                }
                await this.init();
            } catch { this.payError = 'Network error. Please try again.'; }
            this.paying = false;
        }
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
