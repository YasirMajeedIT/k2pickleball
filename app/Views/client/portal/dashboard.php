<?php $pageTitle = 'Dashboard — K2 Portal'; ?>

<div x-data="portalDashboard()" x-init="init()">
    <!-- Welcome header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Welcome back<span x-show="userName" x-text="', ' + userName"></span></h1>
        <p class="mt-1 text-surface-400">Here's an overview of your organization.</p>
    </div>

    <!-- Stats cards -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="p-5 rounded-2xl border border-surface-800/60 bg-surface-900/30">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-brand-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white" x-text="stats.facilities">—</div>
                    <div class="text-xs text-surface-400">Facilities</div>
                </div>
            </div>
        </div>
        <div class="p-5 rounded-2xl border border-surface-800/60 bg-surface-900/30">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a9 9 0 01-9 9m0 0H3"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white" x-text="stats.courts">—</div>
                    <div class="text-xs text-surface-400">Courts</div>
                </div>
            </div>
        </div>
        <div class="p-5 rounded-2xl border border-surface-800/60 bg-surface-900/30">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white" x-text="stats.users">—</div>
                    <div class="text-xs text-surface-400">Users</div>
                </div>
            </div>
        </div>
        <div class="p-5 rounded-2xl border border-surface-800/60 bg-surface-900/30">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white" x-text="stats.bookings">—</div>
                    <div class="text-xs text-surface-400">Bookings Today</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Plan Card -->
    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30">
            <h2 class="text-lg font-semibold text-white mb-4">Current Plan</h2>
            <template x-if="subscription">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xl font-bold text-white" x-text="subscription.plan_name"></span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="subscription.status === 'active' ? 'bg-brand-500/10 text-brand-400' : 'bg-amber-500/10 text-amber-400'" x-text="subscription.status"></span>
                    </div>
                    <div class="flex items-baseline gap-1 mb-4">
                        <span class="text-3xl font-extrabold text-white" x-text="'$' + Math.round(subscription.price || 0)"></span>
                        <span class="text-sm text-surface-400" x-text="'/' + (subscription.billing_cycle || 'month')"></span>
                    </div>
                    <div class="flex gap-3">
                        <a :href="APP_BASE + '/portal/subscription'" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">
                            Manage Plan
                        </a>
                    </div>
                </div>
            </template>
            <template x-if="!subscription">
                <div class="text-surface-400 text-sm">
                    <p>No active subscription found.</p>
                    <a :href="APP_BASE + '/portal/subscription'" class="mt-2 inline-flex items-center gap-1 text-brand-400 hover:text-brand-300 font-medium">
                        Choose a plan →
                    </a>
                </div>
            </template>
        </div>

        <!-- Quick Links -->
        <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30">
            <h2 class="text-lg font-semibold text-white mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a :href="APP_BASE + '/admin'" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-800/40 transition-colors group">
                    <div class="h-8 w-8 rounded-lg bg-indigo-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-white group-hover:text-brand-400 transition-colors">Admin Panel</div>
                        <div class="text-xs text-surface-500">Manage your facility</div>
                    </div>
                </a>
                <a :href="APP_BASE + '/portal/invoices'" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-800/40 transition-colors group">
                    <div class="h-8 w-8 rounded-lg bg-brand-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-white group-hover:text-brand-400 transition-colors">View Invoices</div>
                        <div class="text-xs text-surface-500">Billing history</div>
                    </div>
                </a>
                <a :href="APP_BASE + '/portal/settings'" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-800/40 transition-colors group">
                    <div class="h-8 w-8 rounded-lg bg-surface-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-white group-hover:text-brand-400 transition-colors">Settings</div>
                        <div class="text-xs text-surface-500">Account preferences</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity placeholder -->
    <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30">
        <h2 class="text-lg font-semibold text-white mb-4">Recent Activity</h2>
        <div class="text-center py-8 text-surface-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-surface-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm">Activity feed will appear here as you use the platform.</p>
            <a :href="APP_BASE + '/admin'" class="mt-2 inline-flex items-center gap-1 text-sm text-brand-400 hover:text-brand-300 font-medium">
                Go to Admin Panel →
            </a>
        </div>
    </div>
</div>

<script>
function portalDashboard() {
    return {
        userName: '',
        stats: { facilities: '—', courts: '—', users: '—', bookings: '—' },
        subscription: null,
        init() {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            this.userName = user.first_name || '';

            // Fetch subscription info
            authFetch(APP_BASE + '/api/subscriptions/current')
                .then(r => r.json())
                .then(data => {
                    const sub = data.data || data;
                    if (sub && sub.id) {
                        this.subscription = sub;
                        if (sub.plan) {
                            this.subscription.plan_name = sub.plan.name || '';
                            this.subscription.price = sub.billing_cycle === 'yearly'
                                ? parseFloat(sub.plan.price_yearly || 0)
                                : parseFloat(sub.plan.price_monthly || 0);
                        }
                    }
                })
                .catch(() => {});
        }
    }
}
</script>
