<?php
$title = 'Platform Dashboard';
$breadcrumbs = [['label' => 'Dashboard']];

ob_start();
?>
<div x-data="platformDashboard()" x-init="loadStats()" class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Organizations -->
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-surface-800/60 p-5 shadow-soft border border-surface-200/60 dark:border-surface-700/50 hover:shadow-medium transition-all">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-primary-500/5 to-transparent rounded-bl-[3rem]"></div>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Organizations</p>
                    <h3 class="mt-1 text-3xl font-bold tracking-tight text-surface-900 dark:text-white" x-text="stats.organizations || '—'"></h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 shadow-lg shadow-primary-500/20">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-xs font-medium text-emerald-600"><span x-text="stats.newOrgsThisMonth || 0"></span> new this month</p>
        </div>

        <!-- Active Subscriptions -->
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-surface-800/60 p-5 shadow-soft border border-surface-200/60 dark:border-surface-700/50 hover:shadow-medium transition-all">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-500/5 to-transparent rounded-bl-[3rem]"></div>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Active Subscriptions</p>
                    <h3 class="mt-1 text-3xl font-bold tracking-tight text-surface-900 dark:text-white" x-text="stats.activeSubscriptions || '—'"></h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-lg shadow-emerald-500/20">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-xs font-medium text-surface-500"><span x-text="stats.churnRate || '0%'"></span> churn rate</p>
        </div>

        <!-- Monthly Revenue -->
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-surface-800/60 p-5 shadow-soft border border-surface-200/60 dark:border-surface-700/50 hover:shadow-medium transition-all">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-500/5 to-transparent rounded-bl-[3rem]"></div>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Monthly Revenue</p>
                    <h3 class="mt-1 text-3xl font-bold tracking-tight text-surface-900 dark:text-white" x-text="'$' + (stats.monthlyRevenue || '0')"></h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg shadow-blue-500/20">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-xs font-medium" :class="stats.revenueGrowth >= 0 ? 'text-emerald-600' : 'text-red-500'">
                <span x-text="(stats.revenueGrowth >= 0 ? '+' : '') + (stats.revenueGrowth || 0) + '%'"></span> vs last month
            </p>
        </div>

        <!-- Total Users -->
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-surface-800/60 p-5 shadow-soft border border-surface-200/60 dark:border-surface-700/50 hover:shadow-medium transition-all">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-amber-500/5 to-transparent rounded-bl-[3rem]"></div>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Total Users</p>
                    <h3 class="mt-1 text-3xl font-bold tracking-tight text-surface-900 dark:text-white" x-text="stats.totalUsers || '—'"></h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 shadow-lg shadow-amber-500/20">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-xs font-medium text-surface-500">Across all organizations</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white dark:bg-surface-800/60 p-6 shadow-soft border border-surface-200/60 dark:border-surface-700/50">
            <h3 class="text-base font-semibold text-surface-900 dark:text-white mb-4">Revenue Overview</h3>
            <canvas id="revenueChart" height="250"></canvas>
        </div>
        <div class="rounded-2xl bg-white dark:bg-surface-800/60 p-6 shadow-soft border border-surface-200/60 dark:border-surface-700/50">
            <h3 class="text-base font-semibold text-surface-900 dark:text-white mb-4">Plan Distribution</h3>
            <canvas id="planChart" height="250"></canvas>
        </div>
    </div>

    <!-- Recent Organizations Table -->
    <div class="rounded-2xl bg-white dark:bg-surface-800/60 shadow-soft border border-surface-200/60 dark:border-surface-700/50 overflow-hidden">
        <div class="flex items-center justify-between border-b border-surface-200 dark:border-surface-700/50 px-6 py-4">
            <div>
                <h3 class="text-base font-semibold text-surface-900 dark:text-white">Recent Organizations</h3>
                <p class="text-xs text-surface-500 mt-0.5">Latest organizations on the platform</p>
            </div>
            <a href="<?= ($baseUrl ?? '') . '/platform/organizations' ?>" class="inline-flex items-center gap-1 text-sm font-medium text-primary-500 hover:text-primary-600 transition-colors">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-surface-50/50 dark:bg-surface-800/80">
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Name</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Plan</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Users</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Status</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-surface-500">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 dark:divide-surface-700/50">
                    <template x-for="org in recentOrgs" :key="org.id">
                        <tr class="hover:bg-primary-50/30 dark:hover:bg-primary-500/5 transition-colors">
                            <td class="px-6 py-4">
                                <a :href="APP_BASE + '/platform/organizations/' + org.id" class="text-sm font-semibold text-primary-600 hover:text-primary-700" x-text="org.name"></a>
                            </td>
                            <td class="px-6 py-4 text-sm text-surface-600 dark:text-surface-400" x-text="org.plan || 'Free'"></td>
                            <td class="px-6 py-4 text-sm text-surface-600 dark:text-surface-400" x-text="org.user_count || 0"></td>
                            <td class="px-6 py-4">
                                <span :class="org.status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-surface-100 text-surface-600 border-surface-200 dark:bg-surface-700 dark:text-surface-400 dark:border-surface-600'"
                                    class="inline-block rounded-lg border px-2.5 py-0.5 text-xs font-medium" x-text="org.status"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-surface-500" x-text="org.created_at ? new Date(org.created_at).toLocaleDateString() : '-'"></td>
                        </tr>
                    </template>
                    <template x-if="recentOrgs.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-10 w-10 text-surface-300 dark:text-surface-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                                <p class="text-sm font-medium text-surface-500">No organizations yet</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function platformDashboard() {
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };
    const isDark = document.documentElement.classList.contains('dark');

    return {
        stats: {},
        recentOrgs: [],

        async loadStats() {
            try {
                const [orgsRes] = await Promise.all([
                    fetch(APP_BASE + '/api/organizations', { headers })
                ]);
                const orgsJson = await orgsRes.json();
                const orgs = orgsJson.data || [];

                this.stats = {
                    organizations: orgs.length,
                    activeSubscriptions: orgs.filter(o => o.status === 'active').length,
                    monthlyRevenue: '0.00',
                    totalUsers: 0,
                    newOrgsThisMonth: 0,
                    revenueGrowth: 0,
                    churnRate: '0%'
                };
                this.recentOrgs = orgs.slice(0, 10);
            } catch (e) { console.error(e); }

            this.$nextTick(() => { this.initCharts(); });
        },

        initCharts() {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const now = new Date();
            const last6 = [];
            for (let i = 5; i >= 0; i--) {
                const d = new Date(now.getFullYear(), now.getMonth() - i);
                last6.push(months[d.getMonth()]);
            }

            const gridColor = isDark ? 'rgba(148,163,184,0.08)' : 'rgba(148,163,184,0.15)';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            // Revenue Chart
            const revCtx = document.getElementById('revenueChart');
            if (revCtx) {
                const gradient = revCtx.getContext('2d').createLinearGradient(0, 0, 0, 250);
                gradient.addColorStop(0, 'rgba(168,85,247,0.15)');
                gradient.addColorStop(1, 'rgba(168,85,247,0)');
                new Chart(revCtx, {
                    type: 'line',
                    data: {
                        labels: last6,
                        datasets: [{ label: 'Revenue ($)', data: [0, 0, 0, 0, 0, 0], borderColor: '#a855f7', backgroundColor: gradient, fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#a855f7' }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { backgroundColor: isDark ? '#1e293b' : '#fff', titleColor: textColor, bodyColor: isDark ? '#e2e8f0' : '#0f172a', borderColor: isDark ? '#334155' : '#e2e8f0', borderWidth: 1, padding: 12, cornerRadius: 12, displayColors: false } },
                        scales: { y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: { family: 'Inter', size: 11 } } }, x: { grid: { display: false }, ticks: { color: textColor, font: { family: 'Inter', size: 11 } } } }
                    }
                });
            }

            // Plan Distribution Chart
            const planCtx = document.getElementById('planChart');
            if (planCtx) {
                new Chart(planCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Free', 'Starter', 'Professional', 'Enterprise'],
                        datasets: [{ data: [this.stats.organizations || 1, 0, 0, 0], backgroundColor: ['#94a3b8', '#3b82f6', '#a855f7', '#f59e0b'], borderWidth: 0, hoverOffset: 6 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '65%',
                        plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 16, font: { family: 'Inter', size: 12 }, usePointStyle: true, pointStyle: 'circle' } }, tooltip: { backgroundColor: isDark ? '#1e293b' : '#fff', titleColor: textColor, bodyColor: isDark ? '#e2e8f0' : '#0f172a', borderColor: isDark ? '#334155' : '#e2e8f0', borderWidth: 1, padding: 12, cornerRadius: 12 } }
                    }
                });
            }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/platform.php';
?>
