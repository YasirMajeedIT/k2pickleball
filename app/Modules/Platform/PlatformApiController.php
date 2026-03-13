<?php

declare(strict_types=1);

namespace App\Modules\Platform;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;

final class PlatformApiController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * GET /api/platform/stats
     */
    public function stats(Request $request): Response
    {
        $orgCount = $this->db->fetch("SELECT COUNT(*) as cnt FROM organizations");
        $activeOrgCount = $this->db->fetch("SELECT COUNT(*) as cnt FROM organizations WHERE status = 'active'");
        $userCount = $this->db->fetch("SELECT COUNT(*) as cnt FROM users");
        $activeSubCount = $this->db->fetch("SELECT COUNT(*) as cnt FROM subscriptions WHERE status = 'active'");

        $monthStart = date('Y-m-01');
        $newOrgsThisMonth = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM organizations WHERE created_at >= ?",
            [$monthStart]
        );

        $monthlyRevenue = $this->db->fetch(
            "SELECT COALESCE(SUM(total), 0) as total FROM invoices WHERE status = 'paid' AND paid_at >= ?",
            [$monthStart]
        );

        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
        $lastMonthEnd = date('Y-m-01');
        $lastMonthRevenue = $this->db->fetch(
            "SELECT COALESCE(SUM(total), 0) as total FROM invoices WHERE status = 'paid' AND paid_at >= ? AND paid_at < ?",
            [$lastMonthStart, $lastMonthEnd]
        );

        $lastRev = (float) ($lastMonthRevenue['total'] ?? 0);
        $curRev = (float) ($monthlyRevenue['total'] ?? 0);
        $revenueGrowth = $lastRev > 0 ? round((($curRev - $lastRev) / $lastRev) * 100, 1) : 0;

        // Plan distribution
        $planDist = $this->db->fetchAll(
            "SELECT p.name, COUNT(s.id) as count
             FROM plans p
             LEFT JOIN subscriptions s ON s.plan_id = p.id AND s.status = 'active'
             GROUP BY p.id, p.name
             ORDER BY p.sort_order ASC"
        );

        // Monthly revenue for last 6 months
        $revenueHistory = [];
        for ($i = 5; $i >= 0; $i--) {
            $mStart = date('Y-m-01', strtotime("-{$i} months"));
            $mEnd = date('Y-m-01', strtotime((-$i + 1) . ' months'));
            $rev = $this->db->fetch(
                "SELECT COALESCE(SUM(total), 0) as total FROM invoices WHERE status = 'paid' AND paid_at >= ? AND paid_at < ?",
                [$mStart, $mEnd]
            );
            $revenueHistory[] = [
                'month' => date('M Y', strtotime($mStart)),
                'revenue' => (float) ($rev['total'] ?? 0),
            ];
        }

        // Cancelled subs this month (churn)
        $cancelledThisMonth = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM subscriptions WHERE status = 'cancelled' AND cancelled_at >= ?",
            [$monthStart]
        );
        $totalSubs = (int) ($activeSubCount['cnt'] ?? 0) + (int) ($cancelledThisMonth['cnt'] ?? 0);
        $churnRate = $totalSubs > 0 ? round(((int) $cancelledThisMonth['cnt'] / $totalSubs) * 100, 1) : 0;

        return $this->success([
            'organizations' => (int) ($orgCount['cnt'] ?? 0),
            'activeOrganizations' => (int) ($activeOrgCount['cnt'] ?? 0),
            'totalUsers' => (int) ($userCount['cnt'] ?? 0),
            'activeSubscriptions' => (int) ($activeSubCount['cnt'] ?? 0),
            'newOrgsThisMonth' => (int) ($newOrgsThisMonth['cnt'] ?? 0),
            'monthlyRevenue' => number_format($curRev, 2, '.', ''),
            'revenueGrowth' => $revenueGrowth,
            'churnRate' => $churnRate . '%',
            'planDistribution' => $planDist,
            'revenueHistory' => $revenueHistory,
        ]);
    }

    /**
     * GET /api/platform/users — all users across all organizations
     */
    public function users(Request $request): Response
    {
        $page = max(1, (int) ($request->input('page', 1)));
        $perPage = min(100, max(1, (int) ($request->input('per_page', 20))));
        $search = trim($request->input('search', ''));
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR o.name LIKE ?";
            $params = ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"];
        }

        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM users u LEFT JOIN organizations o ON u.organization_id = o.id {$where}",
            $params
        );

        $data = $this->db->fetchAll(
            "SELECT u.*, o.name as organization_name
             FROM users u
             LEFT JOIN organizations o ON u.organization_id = o.id
             {$where}
             ORDER BY u.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return $this->paginated($data, (int) ($total['cnt'] ?? 0), $page, $perPage);
    }

    /**
     * GET /api/platform/audit-logs — all audit logs across all organizations
     */
    public function auditLogs(Request $request): Response
    {
        $page = max(1, (int) ($request->input('page', 1)));
        $perPage = min(100, max(1, (int) ($request->input('per_page', 20))));
        $search = trim($request->input('search', ''));
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE a.action LIKE ? OR a.entity_type LIKE ? OR o.name LIKE ?";
            $params = ["%{$search}%", "%{$search}%", "%{$search}%"];
        }

        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM activity_logs a LEFT JOIN organizations o ON a.organization_id = o.id {$where}",
            $params
        );

        $data = $this->db->fetchAll(
            "SELECT a.*, o.name as organization_name, u.first_name, u.last_name, u.email as user_email
             FROM activity_logs a
             LEFT JOIN organizations o ON a.organization_id = o.id
             LEFT JOIN users u ON a.user_id = u.id
             {$where}
             ORDER BY a.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return $this->paginated($data, (int) ($total['cnt'] ?? 0), $page, $perPage);
    }

    /**
     * GET /api/platform/revenue — revenue breakdown
     */
    public function revenue(Request $request): Response
    {
        // Monthly revenue for last 12 months
        $monthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $mStart = date('Y-m-01', strtotime("-{$i} months"));
            $mEnd = date('Y-m-01', strtotime((-$i + 1) . ' months'));
            $rev = $this->db->fetch(
                "SELECT COALESCE(SUM(total), 0) as total, COUNT(*) as invoice_count FROM invoices WHERE status = 'paid' AND paid_at >= ? AND paid_at < ?",
                [$mStart, $mEnd]
            );
            $monthly[] = [
                'month' => date('M Y', strtotime($mStart)),
                'revenue' => (float) ($rev['total'] ?? 0),
                'invoices' => (int) ($rev['invoice_count'] ?? 0),
            ];
        }

        // Revenue by plan
        $byPlan = $this->db->fetchAll(
            "SELECT p.name as plan_name, COALESCE(SUM(i.total), 0) as total_revenue, COUNT(i.id) as invoice_count
             FROM plans p
             LEFT JOIN subscriptions s ON s.plan_id = p.id
             LEFT JOIN invoices i ON i.subscription_id = s.id AND i.status = 'paid'
             GROUP BY p.id, p.name
             ORDER BY total_revenue DESC"
        );

        // Top organizations by revenue
        $topOrgs = $this->db->fetchAll(
            "SELECT o.name, COALESCE(SUM(i.total), 0) as total_revenue
             FROM organizations o
             LEFT JOIN invoices i ON i.organization_id = o.id AND i.status = 'paid'
             GROUP BY o.id, o.name
             ORDER BY total_revenue DESC
             LIMIT 10"
        );

        $totalRevenue = $this->db->fetch("SELECT COALESCE(SUM(total), 0) as total FROM invoices WHERE status = 'paid'");

        return $this->success([
            'totalRevenue' => (float) ($totalRevenue['total'] ?? 0),
            'monthly' => $monthly,
            'byPlan' => $byPlan,
            'topOrganizations' => $topOrgs,
        ]);
    }

    /**
     * GET /api/platform/settings — system-wide settings
     */
    public function settings(Request $request): Response
    {
        $settings = $this->db->fetchAll("SELECT * FROM settings WHERE organization_id IS NULL ORDER BY `group` ASC, `key` ASC");
        return $this->success($settings);
    }

    /**
     * PUT /api/platform/settings
     */
    public function updateSettings(Request $request): Response
    {
        $items = $request->input('settings', []);
        if (!is_array($items)) {
            return $this->error('Invalid settings data');
        }

        foreach ($items as $item) {
            if (!isset($item['key'], $item['value'])) continue;
            $existing = $this->db->fetch(
                "SELECT id FROM settings WHERE `key` = ? AND organization_id IS NULL",
                [$item['key']]
            );
            if ($existing) {
                $this->db->update('settings', [
                    'value' => $item['value'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ], ['id' => $existing['id']]);
            } else {
                $this->db->insert('settings', [
                    'key' => $item['key'],
                    'value' => $item['value'],
                    'group' => $item['group'] ?? 'general',
                    'type' => $item['type'] ?? 'string',
                    'organization_id' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $settings = $this->db->fetchAll("SELECT * FROM settings WHERE organization_id IS NULL ORDER BY `group` ASC, `key` ASC");
        return $this->success($settings, 'Settings updated');
    }
}
