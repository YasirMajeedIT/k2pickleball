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
</div>

<script>
function subscriptionPage() {
    return {
        currentSub: null,
        plans: [],
        annual: false,
        loading: true,
        changingPlan: false,
        message: '',
        messageType: 'success',
        init() {
            const token = localStorage.getItem('access_token');
            if (!token) return;
            const headers = { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' };

            // Fetch current subscription & plans in parallel
            Promise.all([
                fetch(APP_BASE + '/api/subscriptions', { headers }).then(r => r.json()),
                fetch(APP_BASE + '/api/plans', { headers }).then(r => r.json())
            ]).then(([subData, planData]) => {
                const subs = subData.data || subData;
                if (Array.isArray(subs) && subs.length > 0) this.currentSub = subs[0];
                else if (subs && subs.id) this.currentSub = subs;

                const p = planData.data || planData;
                this.plans = (Array.isArray(p) ? p : []).map(pl => ({
                    ...pl,
                    price_monthly: parseFloat(pl.price_monthly) || 0,
                    price_yearly: parseFloat(pl.price_yearly) || 0,
                }));
                this.loading = false;
            }).catch(() => { this.loading = false; });
        },
        async changePlan(plan) {
            if (!confirm(`Are you sure you want to switch to the ${plan.name} plan?`)) return;
            this.changingPlan = true;
            this.message = '';
            const token = localStorage.getItem('access_token');
            try {
                if (this.currentSub) {
                    // Update existing subscription
                    const res = await fetch(APP_BASE + '/api/subscriptions/' + this.currentSub.id, {
                        method: 'PUT',
                        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ plan_id: plan.id, billing_cycle: this.annual ? 'yearly' : 'monthly' })
                    });
                    if (res.ok) {
                        this.message = 'Plan updated successfully!';
                        this.messageType = 'success';
                    } else {
                        const err = await res.json();
                        this.message = err.error || err.message || 'Failed to update plan.';
                        this.messageType = 'error';
                    }
                } else {
                    // Create new subscription
                    const user = JSON.parse(localStorage.getItem('user') || '{}');
                    const res = await fetch(APP_BASE + '/api/subscriptions', {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            organization_id: user.organization_id,
                            plan_id: plan.id,
                            billing_cycle: this.annual ? 'yearly' : 'monthly'
                        })
                    });
                    if (res.ok) {
                        this.message = 'Subscription created successfully!';
                        this.messageType = 'success';
                    } else {
                        const err = await res.json();
                        this.message = err.error || err.message || 'Failed to create subscription.';
                        this.messageType = 'error';
                    }
                }
                this.init(); // Refresh data
            } catch (e) {
                this.message = 'Network error. Please try again.';
                this.messageType = 'error';
            }
            this.changingPlan = false;
        }
    }
}
</script>
