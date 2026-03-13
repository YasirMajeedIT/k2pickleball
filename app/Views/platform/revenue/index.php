<?php
$title = 'Revenue';
$breadcrumbs = [['label' => 'Revenue']];

ob_start();
?>
<div x-data="revenueView()" x-init="init()" class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="rounded-2xl bg-white dark:bg-surface-800/60 p-5 shadow-soft border border-surface-200/60 dark:border-surface-700/50">
            <p class="text-sm font-medium text-surface-500">Total Revenue</p>
            <h3 class="mt-1 text-3xl font-bold text-surface-900 dark:text-white" x-text="'$' + formatMoney(data.totalRevenue)"></h3>
            <p class="mt-2 text-xs text-surface-400">All time</p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-surface-800/60 p-5 shadow-soft border border-surface-200/60 dark:border-surface-700/50">
            <p class="text-sm font-medium text-surface-500">This Month</p>
            <h3 class="mt-1 text-3xl font-bold text-surface-900 dark:text-white" x-text="'$' + formatMoney(currentMonth)"></h3>
            <p class="mt-2 text-xs text-surface-400" x-text="currentMonthLabel"></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-surface-800/60 p-5 shadow-soft border border-surface-200/60 dark:border-surface-700/50">
            <p class="text-sm font-medium text-surface-500">Last Month</p>
            <h3 class="mt-1 text-3xl font-bold text-surface-900 dark:text-white" x-text="'$' + formatMoney(lastMonth)"></h3>
            <p class="mt-2 text-xs" :class="growth >= 0 ? 'text-emerald-600' : 'text-red-500'" x-text="(growth >= 0 ? '+' : '') + growth + '% growth'"></p>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="rounded-2xl bg-white dark:bg-surface-800/60 p-6 shadow-soft border border-surface-200/60 dark:border-surface-700/50">
        <h3 class="text-base font-semibold text-surface-900 dark:text-white mb-4">Monthly Revenue (Last 12 Months)</h3>
        <div class="relative h-72">
            <canvas id="revenueLineChart"></canvas>
        </div>
    </div>

    <!-- Revenue by Plan & Top Orgs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white dark:bg-surface-800/60 p-6 shadow-soft border border-surface-200/60 dark:border-surface-700/50">
            <h3 class="text-base font-semibold text-surface-900 dark:text-white mb-4">Revenue by Plan</h3>
            <template x-if="data.byPlan && data.byPlan.length">
                <div class="space-y-3">
                    <template x-for="p in data.byPlan" :key="p.plan_name">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-primary-500"></div>
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-300" x-text="p.plan_name"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-surface-900 dark:text-white" x-text="'$' + formatMoney(p.total_revenue)"></span>
                                <span class="text-xs text-surface-400 ml-2" x-text="p.invoice_count + ' invoices'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="!data.byPlan || data.byPlan.length === 0">
                <p class="text-sm text-surface-400 py-6 text-center">No revenue data yet</p>
            </template>
        </div>

        <div class="rounded-2xl bg-white dark:bg-surface-800/60 p-6 shadow-soft border border-surface-200/60 dark:border-surface-700/50">
            <h3 class="text-base font-semibold text-surface-900 dark:text-white mb-4">Top Organizations by Revenue</h3>
            <template x-if="data.topOrganizations && data.topOrganizations.length">
                <div class="space-y-3">
                    <template x-for="(org, idx) in data.topOrganizations" :key="org.name">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-surface-100 dark:bg-surface-700 text-xs font-bold text-surface-600 dark:text-surface-300" x-text="idx + 1"></span>
                                <span class="text-sm font-medium text-surface-700 dark:text-surface-300" x-text="org.name"></span>
                            </div>
                            <span class="text-sm font-bold text-surface-900 dark:text-white" x-text="'$' + formatMoney(org.total_revenue)"></span>
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="!data.topOrganizations || data.topOrganizations.length === 0">
                <p class="text-sm text-surface-400 py-6 text-center">No revenue data yet</p>
            </template>
        </div>
    </div>
</div>

<script>
function revenueView() {
    const token = localStorage.getItem('access_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };
    const isDark = document.documentElement.classList.contains('dark');

    return {
        data: {},
        currentMonth: 0,
        lastMonth: 0,
        currentMonthLabel: '',
        growth: 0,

        formatMoney(val) {
            return parseFloat(val || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        async init() {
            try {
                const res = await fetch(APP_BASE + '/api/platform/revenue', { headers });
                if (res.status === 401) { window.location.href = APP_BASE + '/admin/login'; return; }
                const json = await res.json();
                this.data = json.data || {};

                const monthly = this.data.monthly || [];
                if (monthly.length >= 1) {
                    this.currentMonth = monthly[monthly.length - 1].revenue;
                    this.currentMonthLabel = monthly[monthly.length - 1].month;
                }
                if (monthly.length >= 2) {
                    this.lastMonth = monthly[monthly.length - 2].revenue;
                    this.growth = this.lastMonth > 0 ? Math.round(((this.currentMonth - this.lastMonth) / this.lastMonth) * 100) : 0;
                }

                this.$nextTick(() => this.renderChart());
            } catch (e) { console.error(e); }
        },

        renderChart() {
            const monthly = this.data.monthly || [];
            const ctx = document.getElementById('revenueLineChart');
            if (!ctx) return;

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 288);
            gradient.addColorStop(0, 'rgba(168,85,247,0.15)');
            gradient.addColorStop(1, 'rgba(168,85,247,0)');

            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(148,163,184,0.08)' : 'rgba(148,163,184,0.15)';

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthly.map(m => m.month),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: monthly.map(m => m.revenue),
                        borderColor: '#a855f7',
                        backgroundColor: gradient,
                        fill: true, tension: 0.4, borderWidth: 2.5,
                        pointRadius: 3, pointHoverRadius: 6,
                        pointBackgroundColor: '#a855f7', pointHoverBackgroundColor: '#a855f7'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: { family: 'Inter', size: 11 }, callback: v => '$' + v } },
                        x: { grid: { display: false }, ticks: { color: textColor, font: { family: 'Inter', size: 11 } } }
                    }
                }
            });
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
