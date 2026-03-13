<?php
$title = 'Dashboard';
$breadcrumbs = [];

ob_start();
?>
<!-- Stats Cards Row -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
    <!-- Total Users -->
    <div class="group relative overflow-hidden rounded-2xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 shadow-soft hover:shadow-medium transition-all">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-blue-500/5 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Total Users</p>
                <h4 class="mt-2 text-3xl font-bold text-surface-900 dark:text-white tracking-tight" id="stat-users">0</h4>
                <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Active
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/30 transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Facilities -->
    <div class="group relative overflow-hidden rounded-2xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 shadow-soft hover:shadow-medium transition-all">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-emerald-500/5 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Facilities</p>
                <h4 class="mt-2 text-3xl font-bold text-surface-900 dark:text-white tracking-tight" id="stat-facilities">0</h4>
                <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                    Operational
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/20 group-hover:shadow-emerald-500/30 transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Courts -->
    <div class="group relative overflow-hidden rounded-2xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 shadow-soft hover:shadow-medium transition-all">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-violet-500/5 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Courts</p>
                <h4 class="mt-2 text-3xl font-bold text-surface-900 dark:text-white tracking-tight" id="stat-courts">0</h4>
                <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-violet-600 dark:text-violet-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.25 14.25h13.5"/></svg>
                    Available
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 shadow-lg shadow-violet-500/20 group-hover:shadow-violet-500/30 transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Revenue -->
    <div class="group relative overflow-hidden rounded-2xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 shadow-soft hover:shadow-medium transition-all">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-amber-500/5 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Revenue</p>
                <h4 class="mt-2 text-3xl font-bold text-surface-900 dark:text-white tracking-tight" id="stat-revenue">$0</h4>
                <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-surface-400 dark:text-surface-500">
                    This month
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg shadow-amber-500/20 group-hover:shadow-amber-500/30 transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
    <!-- Revenue Chart -->
    <div class="rounded-2xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 shadow-soft">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h5 class="text-base font-bold text-surface-900 dark:text-white">Revenue Overview</h5>
                <p class="text-xs text-surface-400 mt-0.5">Track your income trends</p>
            </div>
            <select id="revenue-period" class="rounded-xl border border-surface-200 bg-surface-50 px-3 py-1.5 text-xs font-medium dark:border-surface-700 dark:bg-surface-800 dark:text-surface-300 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                <option value="7">Last 7 days</option>
                <option value="30" selected>Last 30 days</option>
                <option value="90">Last 90 days</option>
            </select>
        </div>
        <div style="position: relative; height: 280px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- User Registrations Chart -->
    <div class="rounded-2xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 shadow-soft">
        <div class="mb-5">
            <h5 class="text-base font-bold text-surface-900 dark:text-white">User Registrations</h5>
            <p class="text-xs text-surface-400 mt-0.5">New signups this week</p>
        </div>
        <div style="position: relative; height: 280px;">
            <canvas id="usersChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="mt-6 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
    <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30">
        <h5 class="text-base font-bold text-surface-900 dark:text-white">Recent Activity</h5>
        <p class="text-xs text-surface-400 mt-0.5">Latest actions in your organization</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-50 dark:bg-surface-800/50">
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">User</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Action</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Entity</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Time</th>
                </tr>
            </thead>
            <tbody id="activity-table-body" class="divide-y divide-surface-100 dark:divide-surface-800">
                <tr>
                    <td class="px-6 py-8 text-center text-surface-400" colspan="4">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-8 h-8 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm">Loading activity...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148,163,184,0.06)' : 'rgba(0,0,0,0.04)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.color = textColor;

    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const gradient = revenueCtx.getContext('2d').createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(99,102,241,0.15)');
        gradient.addColorStop(1, 'rgba(99,102,241,0)');

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [0,0,0,0,0,0,0],
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { backgroundColor: isDark ? '#1e293b' : '#fff', titleColor: isDark ? '#fff' : '#0f172a', bodyColor: isDark ? '#cbd5e1' : '#475569', borderColor: isDark ? '#334155' : '#e2e8f0', borderWidth: 1, padding: 12, cornerRadius: 12, boxPadding: 4, displayColors: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { padding: 8, font: { size: 11 } }, border: { display: false } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } }, border: { display: false } }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });
    }

    const usersCtx = document.getElementById('usersChart');
    if (usersCtx) {
        new Chart(usersCtx, {
            type: 'bar',
            data: {
                labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                datasets: [{
                    label: 'New Users',
                    data: [0,0,0,0,0,0,0],
                    backgroundColor: 'rgba(139,92,246,0.8)',
                    hoverBackgroundColor: '#8b5cf6',
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { backgroundColor: isDark ? '#1e293b' : '#fff', titleColor: isDark ? '#fff' : '#0f172a', bodyColor: isDark ? '#cbd5e1' : '#475569', borderColor: isDark ? '#334155' : '#e2e8f0', borderWidth: 1, padding: 12, cornerRadius: 12, boxPadding: 4, displayColors: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { padding: 8, font: { size: 11 }, stepSize: 1 }, border: { display: false } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } }, border: { display: false } }
                }
            }
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
?>
