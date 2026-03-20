<?php
$title = 'Dashboard';
$breadcrumbs = [];

ob_start();
?>
<!-- ─── Quick Actions Bar ─── -->
<div x-data="dashboardActions()" x-init="init()" class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft px-5 py-4">
    <div class="flex items-center gap-3">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-md shadow-primary-500/20">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-surface-900 dark:text-white" x-text="orgName || 'Your Organization'"></p>
            <p class="text-xs text-surface-400">Admin Dashboard</p>
        </div>
    </div>
    <a x-show="clientUrl" :href="clientUrl" target="_blank" rel="noopener"
       class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 px-4 py-2 text-sm font-semibold text-surface-700 dark:text-surface-200 hover:border-primary-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253M3.157 7.582A8.959 8.959 0 003 12c0 .778.099 1.533.284 2.253"/></svg>
        View Client Site
        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
    </a>
</div>

<!-- ─── Stats Cards ─── -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">

    <!-- Total Users -->
    <div class="group relative overflow-hidden rounded-2xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 shadow-soft hover:shadow-medium transition-all">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-blue-500/5 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Total Users</p>
                <h4 class="mt-2 text-3xl font-bold text-surface-900 dark:text-white tracking-tight stat-skeleton" id="stat-users">—</h4>
                <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-blue-600 dark:text-blue-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    Registered
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
                <h4 class="mt-2 text-3xl font-bold text-surface-900 dark:text-white tracking-tight stat-skeleton" id="stat-facilities">—</h4>
                <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                    Locations
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
                <h4 class="mt-2 text-3xl font-bold text-surface-900 dark:text-white tracking-tight stat-skeleton" id="stat-courts">—</h4>
                <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-violet-600 dark:text-violet-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.25 14.25h13.5"/></svg>
                    Pickleball courts
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 shadow-lg shadow-violet-500/20 group-hover:shadow-violet-500/30 transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Players -->
    <div class="group relative overflow-hidden rounded-2xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 shadow-soft hover:shadow-medium transition-all">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-teal-500/5 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Players</p>
                <h4 class="mt-2 text-3xl font-bold text-surface-900 dark:text-white tracking-tight stat-skeleton" id="stat-players">—</h4>
                <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-teal-600 dark:text-teal-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    Profiles
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 shadow-lg shadow-teal-500/20 group-hover:shadow-teal-500/30 transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"/>
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
                <h4 class="mt-2 text-3xl font-bold text-surface-900 dark:text-white tracking-tight stat-skeleton" id="stat-revenue">—</h4>
                <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
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

<!-- ─── Charts Row ─── -->
<div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
    <!-- Revenue Chart -->
    <div class="rounded-2xl border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900 shadow-soft">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h5 class="text-base font-bold text-surface-900 dark:text-white">Revenue Overview</h5>
                <p class="text-xs text-surface-400 mt-0.5">Completed payments over time</p>
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
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h5 class="text-base font-bold text-surface-900 dark:text-white">New Registrations</h5>
                <p class="text-xs text-surface-400 mt-0.5">User sign-ups — last 7 days</p>
            </div>
            <div id="users-chart-total" class="px-2.5 py-1 rounded-lg bg-violet-500/10 text-violet-600 dark:text-violet-400 text-xs font-semibold hidden">
                <span id="users-chart-total-val">0</span> total
            </div>
        </div>
        <div style="position: relative; height: 280px;">
            <canvas id="usersChart"></canvas>
        </div>
    </div>
</div>

<!-- ─── Recent Activity ─── -->
<div class="mt-6 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
    <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30 flex items-start justify-between gap-4">
        <div>
            <h5 class="text-base font-bold text-surface-900 dark:text-white">Recent Activity</h5>
            <p class="text-xs text-surface-400 mt-0.5">
                A live audit trail of every action taken in your organization — who did what, and when.
                Track creates, updates, deletions, logins, and more across all modules.
            </p>
        </div>
        <a href="<?= ($baseUrl ?? '') ?>/admin/audit-logs"
           class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-primary-600 dark:text-primary-400 bg-primary-500/10 hover:bg-primary-500/20 transition-colors">
            View all
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
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
                    <td colspan="4" class="px-6 py-10 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-7 h-7 animate-spin text-primary-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span class="text-sm text-surface-400">Loading activity...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function dashboardActions() {
    return {
        orgName: '',
        clientUrl: '',
        async init() {
            try {
                const json = await getMe();
                const user = json.data || json;
                const org = user.organization || null;
                if (!org) return;
                this.orgName = org.name || '';
                const slug = org.slug || '';
                if (!slug) return;
                const hostname = window.location.hostname; // e.g. admin.k2pickleball.local
                const baseDomain = hostname.replace(/^admin\./, '');
                this.clientUrl = window.location.protocol + '//' + slug + '.' + baseDomain;
            } catch (_) {}
        }
    };
}

(function () {
    'use strict';

    let revenueChart = null;
    let usersChart   = null;
    const isDark     = document.documentElement.classList.contains('dark');
    const gridColor  = isDark ? 'rgba(148,163,184,0.06)' : 'rgba(0,0,0,0.04)';
    const textColor  = isDark ? '#94a3b8' : '#64748b';
    const tipOpts    = {
        backgroundColor : isDark ? '#1e293b' : '#fff',
        titleColor      : isDark ? '#fff'    : '#0f172a',
        bodyColor       : isDark ? '#cbd5e1' : '#475569',
        borderColor     : isDark ? '#334155' : '#e2e8f0',
        borderWidth     : 1, padding: 12, cornerRadius: 12, boxPadding: 4, displayColors: false,
    };

    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.color       = textColor;

    /* ─── Initialise charts (empty) ─── */
    function initCharts() {
        const revCtx = document.getElementById('revenueChart');
        if (revCtx) {
            const grad = revCtx.getContext('2d').createLinearGradient(0, 0, 0, 280);
            grad.addColorStop(0, 'rgba(99,102,241,0.18)');
            grad.addColorStop(1, 'rgba(99,102,241,0)');

            revenueChart = new Chart(revCtx, {
                type: 'line',
                data: { labels: [], datasets: [{
                    label: 'Revenue ($)',
                    data: [],
                    borderColor: '#6366f1', backgroundColor: grad,
                    fill: true, tension: 0.4, borderWidth: 2.5,
                    pointRadius: 0, pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff', pointHoverBorderWidth: 3,
                }]},
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false },
                        tooltip: { ...tipOpts, callbacks: { label: ctx => '$' + parseFloat(ctx.raw || 0).toFixed(2) } }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false },
                             ticks: { padding: 8, font: { size: 11 }, callback: v => '$' + v } },
                        x: { grid: { display: false }, border: { display: false },
                             ticks: { font: { size: 11 }, maxTicksLimit: 10 } },
                    },
                    interaction: { intersect: false, mode: 'index' },
                },
            });
        }

        const usrCtx = document.getElementById('usersChart');
        if (usrCtx) {
            usersChart = new Chart(usrCtx, {
                type: 'bar',
                data: { labels: [], datasets: [{
                    label: 'New Users',
                    data: [],
                    backgroundColor: 'rgba(139,92,246,0.75)',
                    hoverBackgroundColor: '#8b5cf6',
                    borderRadius: 8, borderSkipped: false, maxBarThickness: 44,
                }]},
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: tipOpts },
                    scales: {
                        y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false },
                             ticks: { padding: 8, font: { size: 11 }, precision: 0, stepSize: 1 } },
                        x: { grid: { display: false }, border: { display: false },
                             ticks: { font: { size: 11 } } },
                    },
                },
            });
        }
    }

    /* ─── Stats cards ─── */
    async function loadStats() {
        try {
            const [uRes, fRes, cRes, pRes, plRes] = await Promise.all([
                authFetch(APP_BASE + '/api/users?per_page=1').then(r => r.json()),
                authFetch(APP_BASE + '/api/facilities?per_page=1').then(r => r.json()),
                authFetch(APP_BASE + '/api/courts?per_page=1').then(r => r.json()),
                authFetch(APP_BASE + '/api/payments?per_page=500').then(r => r.json()),
                authFetch(APP_BASE + '/api/players?per_page=1').then(r => r.json()),
            ]);

            setText('stat-users',      (uRes.meta?.total  ?? 0).toLocaleString());
            setText('stat-facilities', (fRes.meta?.total  ?? 0).toLocaleString());
            setText('stat-courts',     (cRes.meta?.total  ?? 0).toLocaleString());
            setText('stat-players',    (plRes.meta?.total ?? 0).toLocaleString());

            /* revenue = completed payments this calendar month, stored in cents */
            const now        = new Date();
            const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
            const revenue    = (pRes.data || [])
                .filter(p => p.status === 'completed' && new Date(p.created_at) >= monthStart)
                .reduce((s, p) => s + (parseInt(p.amount) || 0), 0);
            setText('stat-revenue', '$' + (revenue / 100).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            document.querySelectorAll('.stat-skeleton').forEach(el => el.classList.remove('stat-skeleton'));
        } catch (e) {
            console.error('[dashboard] stats error:', e);
        }
    }

    /* ─── Charts ─── */
    async function loadCharts(days) {
        days = parseInt(days, 10);
        try {
            const [pRes, uRes] = await Promise.all([
                authFetch(APP_BASE + '/api/payments?per_page=500').then(r => r.json()),
                authFetch(APP_BASE + '/api/users?per_page=500').then(r => r.json()),
            ]);

            const now = new Date();

            /* Revenue: N-day date keys + human labels */
            const revKeys   = [];
            const revLabels = [];
            for (let i = days - 1; i >= 0; i--) {
                const d = new Date(now);
                d.setDate(d.getDate() - i);
                revKeys.push(d.toISOString().slice(0, 10));
                if (days <= 14) {
                    revLabels.push(d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }));
                } else if (days <= 31) {
                    revLabels.push(d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                } else {
                    /* 90-day: label every 7th tick to avoid clutter */
                    revLabels.push(i % 7 === 0 ? d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : '');
                }
            }
            const revByDay = Object.fromEntries(revKeys.map(k => [k, 0]));
            (pRes.data || []).forEach(p => {
                if (p.status !== 'completed') return;
                const k = (p.created_at || '').slice(0, 10);
                if (k in revByDay) revByDay[k] += (parseInt(p.amount) || 0) / 100;
            });
            if (revenueChart) {
                revenueChart.data.labels = revLabels;
                revenueChart.data.datasets[0].data = revKeys.map(k => revByDay[k]);
                revenueChart.update();
            }

            /* Users: always last 7 days */
            const usrKeys   = [];
            const usrLabels = [];
            for (let i = 6; i >= 0; i--) {
                const d = new Date(now);
                d.setDate(d.getDate() - i);
                usrKeys.push(d.toISOString().slice(0, 10));
                usrLabels.push(d.toLocaleDateString('en-US', { weekday: 'short' }));
            }
            const usrByDay = Object.fromEntries(usrKeys.map(k => [k, 0]));
            (uRes.data || []).forEach(u => {
                const k = (u.created_at || '').slice(0, 10);
                if (k in usrByDay) usrByDay[k]++;
            });
            const weekTotal = Object.values(usrByDay).reduce((s, v) => s + v, 0);
            const totalEl = document.getElementById('users-chart-total');
            if (totalEl) {
                document.getElementById('users-chart-total-val').textContent = weekTotal;
                totalEl.classList.toggle('hidden', weekTotal === 0);
            }
            if (usersChart) {
                usersChart.data.labels = usrLabels;
                usersChart.data.datasets[0].data = usrKeys.map(k => usrByDay[k]);
                usersChart.update();
            }
        } catch (e) {
            console.error('[dashboard] charts error:', e);
        }
    }

    /* ─── Activity table ─── */
    async function loadActivity() {
        const tbody = document.getElementById('activity-table-body');
        try {
            const res  = await authFetch(APP_BASE + '/api/audit-logs?per_page=15').then(r => r.json());
            const logs = res.data || [];

            if (!logs.length) {
                tbody.innerHTML = `
                <tr><td colspan="4" class="px-6 py-10 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-9 h-9 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.25 2.25v.894m-9.75 0a2.25 2.25 0 00-2.25 2.25v12a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75z"/>
                        </svg>
                        <p class="text-sm font-medium text-surface-500">No activity recorded yet</p>
                        <p class="text-xs text-surface-400">Actions like creating facilities, managing users, processing<br>payments, and more will appear here in real time.</p>
                    </div>
                </td></tr>`;
                return;
            }

            tbody.innerHTML = logs.map(log => {
                const name    = log.first_name
                    ? (log.first_name + ' ' + (log.last_name || '')).trim()
                    : (log.email || 'System');
                const initials = name === 'System'
                    ? 'SY'
                    : name.split(' ').filter(Boolean).map(w => w[0]).join('').substring(0, 2).toUpperCase();
                const color   = avatarColor(name);
                const emailTd = (log.email && name !== log.email)
                    ? `<p class="text-xs text-surface-400 truncate max-w-[150px]">${esc(log.email)}</p>`
                    : '';

                return `<tr class="hover:bg-surface-50/60 dark:hover:bg-surface-800/20 transition-colors">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-8 flex-shrink-0 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:${color}">${esc(initials)}</span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-surface-800 dark:text-surface-200 truncate">${esc(name)}</p>
                                ${emailTd}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5">${actionBadge(log.action)}</td>
                    <td class="px-6 py-3.5">${entityCell(log.entity_type, log.entity_id)}</td>
                    <td class="px-6 py-3.5 text-xs text-surface-400 whitespace-nowrap" title="${esc(log.created_at || '')}">${esc(timeAgo(log.created_at))}</td>
                </tr>`;
            }).join('');

        } catch (e) {
            console.error('[dashboard] activity error:', e);
            tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-8 text-center text-sm text-surface-400">Could not load recent activity.</td></tr>`;
        }
    }

    /* ─── Helpers ─── */
    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function esc(s) {
        if (!s && s !== 0) return '';
        return String(s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function avatarColor(str) {
        const palette = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444','#ec4899','#3b82f6','#14b8a6'];
        let h = 0;
        for (let i = 0; i < str.length; i++) h = str.charCodeAt(i) + ((h << 5) - h);
        return palette[Math.abs(h) % palette.length];
    }

    function actionBadge(action) {
        const map = {
            create          : ['Created',          'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'],
            update          : ['Updated',          'bg-blue-500/10 text-blue-600 dark:text-blue-400'],
            delete          : ['Deleted',          'bg-red-500/10 text-red-500 dark:text-red-400'],
            login           : ['Logged In',        'bg-purple-500/10 text-purple-600 dark:text-purple-400'],
            logout          : ['Logged Out',       'bg-surface-100 dark:bg-surface-800/60 text-surface-500'],
            register        : ['Registered',       'bg-teal-500/10 text-teal-600 dark:text-teal-400'],
            password_change : ['Password Changed', 'bg-amber-500/10 text-amber-600 dark:text-amber-400'],
            password_reset  : ['Password Reset',   'bg-amber-500/10 text-amber-600 dark:text-amber-400'],
            payment         : ['Payment',          'bg-green-500/10 text-green-600 dark:text-green-400'],
            refund          : ['Refund',           'bg-orange-500/10 text-orange-600 dark:text-orange-400'],
            subscribe       : ['Subscribed',       'bg-teal-500/10 text-teal-600 dark:text-teal-400'],
            cancel          : ['Cancelled',        'bg-red-500/10 text-red-500 dark:text-red-400'],
            upload          : ['Uploaded',         'bg-sky-500/10 text-sky-600 dark:text-sky-400'],
        };
        const a   = (action || '').toLowerCase();
        const [label, cls] = map[a] || [
            a ? (a.charAt(0).toUpperCase() + a.slice(1).replace(/_/g, ' ')) : 'Unknown',
            'bg-surface-100 dark:bg-surface-800 text-surface-500'
        ];
        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${cls}">${esc(label)}</span>`;
    }

    function entityCell(type, id) {
        if (!type) return '<span class="text-surface-300 dark:text-surface-600 text-sm">—</span>';
        const labelMap = {
            user: 'User', facility: 'Facility', court: 'Court', player: 'Player',
            subscription: 'Subscription', payment: 'Payment', invoice: 'Invoice',
            session: 'Session', waiver: 'Waiver', role: 'Role', resource: 'Resource',
            category: 'Category', api_token: 'API Token', notification: 'Notification',
            setting: 'Setting', discount: 'Discount', gift_certificate: 'Gift Certificate',
            credit_code: 'Credit Code',
        };
        const t     = (type || '').toLowerCase();
        const label = labelMap[t] || (t.charAt(0).toUpperCase() + t.slice(1).replace(/_/g, ' '));
        const idTag = id ? `<span class="ml-1 text-xs text-surface-400 dark:text-surface-500">#${esc(String(id))}</span>` : '';
        return `<span class="text-sm text-surface-700 dark:text-surface-300">${esc(label)}${idTag}</span>`;
    }

    function timeAgo(dateStr) {
        if (!dateStr) return '—';
        const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
        if (diff <    60) return 'Just now';
        if (diff <  3600) return Math.floor(diff / 60) + ' min ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        if (diff < 172800) return 'Yesterday';
        if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
        const d = new Date(dateStr);
        const now = new Date();
        return d.toLocaleDateString('en-US', {
            month: 'short', day: 'numeric',
            ...(d.getFullYear() !== now.getFullYear() ? { year: 'numeric' } : {}),
        });
    }

    /* ─── Boot ─── */
    document.addEventListener('DOMContentLoaded', function () {
        initCharts();
        loadStats();
        loadCharts('30');
        loadActivity();

        document.getElementById('revenue-period').addEventListener('change', function () {
            loadCharts(this.value);
        });
    });
}());
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
?>
