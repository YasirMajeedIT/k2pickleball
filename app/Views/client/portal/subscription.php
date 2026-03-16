<?php $pageTitle = 'Subscription — K2 Portal'; ?>

<div x-data="subscriptionPage()" x-init="init()">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Subscription</h1>
        <p class="mt-1 text-surface-400">Manage your plan and billing cycle.</p>
    </div>

    <!-- Current Plan -->
    <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30 mb-8">
        <h2 class="text-lg font-semibold text-white mb-4">Current Plan</h2>

        <!-- Loading -->
        <div x-show="loading" class="animate-pulse space-y-4">
            <div class="h-6 bg-surface-800 rounded w-32"></div>
            <div class="h-10 bg-surface-800 rounded w-24"></div>
            <div class="h-4 bg-surface-800 rounded w-48"></div>
        </div>

        <template x-if="!loading && currentSub">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-2xl font-bold text-white" x-text="currentSub.plan_name"></span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="{
                        'bg-brand-500/10 text-brand-400': currentSub.status === 'active',
                        'bg-amber-500/10 text-amber-400': currentSub.status === 'pending',
                        'bg-red-500/10 text-red-400': currentSub.status === 'cancelled'
                    }" x-text="currentSub.status"></span>
                </div>
                <div class="flex items-baseline gap-1 mb-1">
                    <span class="text-4xl font-extrabold text-white" x-text="'$' + Math.round(currentSub.price || 0)"></span>
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

    <!-- Available Plans -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-white">Available Plans</h2>
            <div class="flex items-center gap-3">
                <span class="text-sm" :class="!annual ? 'text-white font-medium' : 'text-surface-400'">Monthly</span>
                <button @click="annual = !annual" class="relative h-6 w-11 rounded-full transition-colors" :class="annual ? 'bg-brand-600' : 'bg-surface-700'">
                    <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform" :class="annual ? 'translate-x-5' : ''"></span>
                </button>
                <span class="text-sm" :class="annual ? 'text-white font-medium' : 'text-surface-400'">Annual</span>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <template x-for="plan in plans" :key="plan.id">
                <div class="rounded-2xl border p-6 transition-all" :class="currentSub && currentSub.plan_id == plan.id ? 'border-brand-500/40 bg-brand-500/5' : 'border-surface-800/60 bg-surface-900/30'">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-lg font-bold text-white" x-text="plan.name"></h3>
                        <span x-show="currentSub && currentSub.plan_id == plan.id" class="px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-400 text-xs font-medium">Current</span>
                    </div>
                    <p class="text-sm text-surface-400 mb-4" x-text="plan.description"></p>
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-3xl font-extrabold text-white" x-text="'$' + (annual ? Math.round(plan.price_yearly || 0) : Math.round(plan.price_monthly || 0))"></span>
                        <template x-if="plan.price_monthly > 0">
                            <span class="text-sm text-surface-400" x-text="annual ? '/year' : '/month'"></span>
                        </template>
                        <template x-if="plan.price_monthly == 0">
                            <span class="text-sm text-surface-400">forever</span>
                        </template>
                    </div>
                    <button
                        @click="changePlan(plan)"
                        :disabled="(currentSub && currentSub.plan_id == plan.id) || changingPlan"
                        class="w-full py-2.5 rounded-xl text-sm font-semibold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                        :class="currentSub && currentSub.plan_id == plan.id ? 'bg-surface-800 text-surface-400' : 'bg-brand-600 hover:bg-brand-500 text-white'"
                        x-text="currentSub && currentSub.plan_id == plan.id ? 'Current Plan' : (plan.price_monthly == 0 ? 'Downgrade' : 'Upgrade')">
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="message" x-transition class="mb-6 p-4 rounded-xl text-sm" :class="messageType === 'success' ? 'bg-brand-500/10 border border-brand-500/20 text-brand-400' : 'bg-red-500/10 border border-red-500/20 text-red-400'" x-text="message"></div>

    <!-- Payment Modal -->
    <div x-show="showPayment" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="showPayment=false">
        <div class="w-full max-w-md bg-surface-900 rounded-2xl border border-surface-800/60 p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-white">Upgrade to <span class="text-brand-400" x-text="selectedPlan?.name"></span></h3>
                <button @click="showPayment=false;paying=false;payError=''" class="text-surface-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <!-- Order summary -->
            <div class="mb-5 p-4 bg-surface-800/50 rounded-xl space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-surface-400">Plan</span>
                    <span class="text-white font-medium" x-text="selectedPlan?.name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-400">Billing</span>
                    <span class="text-white" x-text="annual ? 'Annual' : 'Monthly'"></span>
                </div>
                <div class="flex justify-between border-t border-surface-700 pt-2 mt-2 font-bold">
                    <span>Total due today</span>
                    <span class="text-brand-400" x-text="'$' + (annual ? (selectedPlan?.price_yearly||0).toFixed(2) : (selectedPlan?.price_monthly||0).toFixed(2))"></span>
                </div>
            </div>
            <!-- Square Card Input -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-medium text-surface-400">Card Details</label>
                    <span class="text-xs text-surface-500">Powered by Square</span>
                </div>
                <div id="sq-upgrade-card" class="min-h-[52px] px-3 py-2 rounded-xl bg-surface-800/50 border border-surface-700/60"></div>
            </div>
            <p x-show="payError" class="text-red-400 text-xs mb-3" x-text="payError"></p>
            <button @click="submitUpgrade()" :disabled="paying || !sqCardReady" class="w-full py-3.5 font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 rounded-xl transition-all">
                <span x-show="!paying">Pay &amp; Upgrade</span>
                <span x-show="paying" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                    Processing...
                </span>
            </button>
        </div>
    </div>
</div>

<?php
$sqAppId = htmlspecialchars($_ENV['SQUARE_APPLICATION_ID'] ?? '', ENT_QUOTES);
$sqLocId = htmlspecialchars($_ENV['SQUARE_LOCATION_ID'] ?? '', ENT_QUOTES);
$sqEnv   = ($_ENV['SQUARE_ENVIRONMENT'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox';
?>
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
        // Payment state
        showPayment: false,
        selectedPlan: null,
        sqPayments: null,
        sqCard: null,
        sqCardReady: false,
        paying: false,
        payError: '',

        async init() {
            // Preload Square SDK if configured
            if (SQ_APP_ID && !window.Square) {
                const s = document.createElement('script');
                s.src = `https://${SQ_ENV === 'production' ? 'web' : 'sandbox.web'}.squarecdn.com/v1/square.js`;
                document.head.appendChild(s);
            }

            // Fetch current subscription & plans in parallel
            Promise.all([
                authFetch(APP_BASE + '/api/subscriptions/current').then(r => r.json()),
                authFetch(APP_BASE + '/api/plans').then(r => r.json())
            ]).then(([subData, planData]) => {
                const sub = subData.data || subData;
                if (sub && sub.id) {
                    this.currentSub = sub;
                    // Populate price from embedded plan
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
                    price_yearly: parseFloat(pl.price_yearly) || 0,
                }));
                this.loading = false;
            }).catch(() => { this.loading = false; });
        },

        async initSquareCard() {
            if (!SQ_APP_ID) return;
            // Wait for Square to load
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
                        '.input-container.is-focus': { borderColor: '#10b981' },
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

        async changePlan(plan) {
            if (this.currentSub && this.currentSub.plan_id == plan.id) return;
            this.message = '';

            // Paid plan upgrade — collect payment first
            if (plan.price_monthly > 0) {
                this.selectedPlan = plan;
                this.showPayment = true;
                this.paying = false;
                this.payError = '';
                this.$nextTick(() => this.initSquareCard());
                return;
            }

            // Free plan — downgrade without payment
            if (!confirm(`Downgrade to the ${plan.name} plan? Some features may be disabled.`)) return;
            this.changingPlan = true;
            try {
                const endpoint = this.currentSub
                    ? `${APP_BASE}/api/subscriptions/${this.currentSub.id}/change-plan`
                    : `${APP_BASE}/api/subscriptions`;
                const method = this.currentSub ? 'PUT' : 'POST';
                const body = this.currentSub
                    ? JSON.stringify({ plan_id: plan.id, billing_cycle: 'monthly' })
                    : JSON.stringify({ organization_id: (JSON.parse(localStorage.getItem('user') || '{}')).organization_id, plan_id: plan.id, billing_cycle: 'monthly' });
                const res = await authFetch(endpoint, { method, body });
                if (res.ok) { this.message = 'Plan changed successfully.'; this.messageType = 'success'; this.init(); }
                else { const e = await res.json(); this.message = e.message || 'Failed to change plan.'; this.messageType = 'error'; }
            } catch { this.message = 'Network error.'; this.messageType = 'error'; }
            this.changingPlan = false;
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

                // Payment success — create or update subscription
                const endpoint = this.currentSub
                    ? `${APP_BASE}/api/subscriptions/${this.currentSub.id}/change-plan`
                    : `${APP_BASE}/api/subscriptions`;
                const method = this.currentSub ? 'PUT' : 'POST';
                const body = this.currentSub
                    ? JSON.stringify({ plan_id: this.selectedPlan.id, billing_cycle: this.annual ? 'yearly' : 'monthly' })
                    : JSON.stringify({ organization_id: (JSON.parse(localStorage.getItem('user') || '{}')).organization_id, plan_id: this.selectedPlan.id, billing_cycle: this.annual ? 'yearly' : 'monthly' });
                const subRes = await authFetch(endpoint, { method, body });

                this.showPayment = false;
                this.sqCard = null; this.sqCardReady = false;
                if (subRes.ok) {
                    this.message = `Successfully upgraded to ${this.selectedPlan.name}! Welcome aboard.`;
                    this.messageType = 'success';
                } else {
                    this.message = 'Payment received but subscription update failed. Please contact support.';
                    this.messageType = 'error';
                }
                this.init();
            } catch { this.payError = 'Network error. Please try again.'; }
            this.paying = false;
        }
    }
}
</script>
