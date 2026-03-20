<?php

declare(strict_types=1);

namespace App\Modules\Platform;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Exceptions\NotFoundException;

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

    // =========================================================================
    // User Management
    // =========================================================================

    /**
     * GET /api/platform/users/{id}
     */
    public function userDetail(Request $request, int $id): Response
    {
        $user = $this->db->fetch(
            "SELECT u.*, o.name as organization_name, o.slug as organization_slug
             FROM users u LEFT JOIN organizations o ON u.organization_id = o.id
             WHERE u.id = ?",
            [$id]
        );
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        // Get user roles
        $user['roles'] = $this->db->fetchAll(
            "SELECT r.name, r.slug FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = ?",
            [$id]
        );

        unset($user['password_hash']);
        return $this->success($user);
    }

    /**
     * PATCH /api/platform/users/{id}/status
     */
    public function updateUserStatus(Request $request, int $id): Response
    {
        $user = $this->db->fetch("SELECT id, status FROM users WHERE id = ?", [$id]);
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        $data = Validator::validate($request->all(), [
            'status' => 'required|in:active,inactive,suspended,pending',
        ]);

        $this->db->update('users', [
            'status' => $data['status'],
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);

        $user = $this->db->fetch(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.status, o.name as organization_name
             FROM users u LEFT JOIN organizations o ON u.organization_id = o.id WHERE u.id = ?",
            [$id]
        );
        return $this->success($user, 'User status updated');
    }

    // =========================================================================
    // Invoices (cross-org)
    // =========================================================================

    /**
     * GET /api/platform/invoices
     */
    public function invoices(Request $request): Response
    {
        $page = max(1, (int) ($request->input('page', 1)));
        $perPage = min(100, max(1, (int) ($request->input('per_page', 20))));
        $search = trim($request->input('search', ''));
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE i.invoice_number LIKE ? OR o.name LIKE ?";
            $params = ["%{$search}%", "%{$search}%"];
        }

        $total = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM invoices i LEFT JOIN organizations o ON i.organization_id = o.id {$where}",
            $params
        );

        $data = $this->db->fetchAll(
            "SELECT i.*, o.name as organization_name
             FROM invoices i
             LEFT JOIN organizations o ON i.organization_id = o.id
             {$where}
             ORDER BY i.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return $this->paginated($data, (int) ($total['cnt'] ?? 0), $page, $perPage);
    }

    // =========================================================================
    // Extensions Management
    // =========================================================================

    /**
     * GET /api/platform/extensions
     */
    public function extensions(Request $request): Response
    {
        $page = max(1, (int) ($request->input('page', 1)));
        $perPage = min(100, max(1, (int) ($request->input('per_page', 20))));
        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM extensions");
        $data = $this->db->fetchAll(
            "SELECT e.*, (SELECT COUNT(*) FROM organization_extensions oe WHERE oe.extension_id = e.id AND oe.is_active = 1) as active_installs
             FROM extensions e ORDER BY e.sort_order ASC, e.name ASC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        return $this->paginated($data, (int) ($total['cnt'] ?? 0), $page, $perPage);
    }

    /**
     * POST /api/platform/extensions
     */
    public function createExtension(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|slug|max:100',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'price_monthly' => 'nullable|numeric',
            'price_yearly' => 'nullable|numeric',
            'is_active' => 'nullable|integer',
            'settings_schema' => 'nullable|json',
            'sort_order' => 'nullable|integer',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['slug'] = Sanitizer::slug($data['slug']);

        $existing = $this->db->fetch("SELECT id FROM extensions WHERE slug = ?", [$data['slug']]);
        if ($existing) {
            return $this->validationError(['slug' => ['Extension slug already exists']]);
        }

        $data['is_active'] = (int) ($data['is_active'] ?? 1);
        $data['price_monthly'] = (float) ($data['price_monthly'] ?? 0);
        $data['price_yearly'] = (float) ($data['price_yearly'] ?? 0);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->db->insert('extensions', $data);
        $ext = $this->db->fetch("SELECT * FROM extensions WHERE id = ?", [$id]);
        return $this->created($ext, 'Extension created');
    }

    /**
     * PUT /api/platform/extensions/{id}
     */
    public function updateExtension(Request $request, int $id): Response
    {
        $ext = $this->db->fetch("SELECT * FROM extensions WHERE id = ?", [$id]);
        if (!$ext) {
            throw new NotFoundException('Extension not found');
        }

        $data = Validator::validate($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|slug|max:100',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'price_monthly' => 'nullable|numeric',
            'price_yearly' => 'nullable|numeric',
            'is_active' => 'nullable|integer',
            'settings_schema' => 'nullable|json',
            'sort_order' => 'nullable|integer',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['slug'] = Sanitizer::slug($data['slug']);

        $dup = $this->db->fetch("SELECT id FROM extensions WHERE slug = ? AND id != ?", [$data['slug'], $id]);
        if ($dup) {
            return $this->validationError(['slug' => ['Extension slug already exists']]);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update('extensions', $data, ['id' => $id]);

        $ext = $this->db->fetch("SELECT * FROM extensions WHERE id = ?", [$id]);
        return $this->success($ext, 'Extension updated');
    }

    /**
     * DELETE /api/platform/extensions/{id}
     */
    public function deleteExtension(Request $request, int $id): Response
    {
        $ext = $this->db->fetch("SELECT * FROM extensions WHERE id = ?", [$id]);
        if (!$ext) {
            throw new NotFoundException('Extension not found');
        }

        $installs = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM organization_extensions WHERE extension_id = ? AND is_active = 1",
            [$id]
        );
        if ((int) ($installs['cnt'] ?? 0) > 0) {
            return $this->error('Cannot delete extension with active installations. Uninstall from all organizations first.', 409);
        }

        $this->db->delete('extensions', ['id' => $id]);
        return $this->success(null, 'Extension deleted');
    }

    /**
     * GET /api/platform/organizations/{id}/extensions
     */
    public function orgExtensions(Request $request, int $id): Response
    {
        $org = $this->db->fetch("SELECT id FROM organizations WHERE id = ?", [$id]);
        if (!$org) {
            throw new NotFoundException('Organization not found');
        }

        $installed = $this->db->fetchAll(
            "SELECT oe.*, e.name, e.slug, e.description, e.version, e.category, e.price_monthly, e.price_yearly
             FROM organization_extensions oe
             JOIN extensions e ON oe.extension_id = e.id
             WHERE oe.organization_id = ?
             ORDER BY e.name ASC",
            [$id]
        );

        return $this->success($installed);
    }

    /**
     * POST /api/platform/organizations/{id}/extensions — install extension for org
     */
    public function installExtension(Request $request, int $id): Response
    {
        $data = Validator::validate($request->all(), [
            'extension_id' => 'required|integer',
        ]);

        $org = $this->db->fetch("SELECT id FROM organizations WHERE id = ?", [$id]);
        if (!$org) {
            throw new NotFoundException('Organization not found');
        }

        $ext = $this->db->fetch("SELECT * FROM extensions WHERE id = ? AND is_active = 1", [$data['extension_id']]);
        if (!$ext) {
            throw new NotFoundException('Extension not found or inactive');
        }

        $existing = $this->db->fetch(
            "SELECT id, is_active FROM organization_extensions WHERE organization_id = ? AND extension_id = ?",
            [$id, $data['extension_id']]
        );

        if ($existing && $existing['is_active']) {
            return $this->error('Extension already installed for this organization', 409);
        }

        if ($existing) {
            $this->db->update('organization_extensions', [
                'is_active' => 1,
                'installed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $existing['id']]);
        } else {
            $this->db->insert('organization_extensions', [
                'organization_id' => $id,
                'extension_id' => $data['extension_id'],
                'is_active' => 1,
                'settings' => null,
                'installed_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->success(null, 'Extension installed for organization');
    }

    /**
     * DELETE /api/platform/organizations/{id}/extensions/{extId} — uninstall
     */
    public function uninstallExtension(Request $request, int $id, int $extId): Response
    {
        $row = $this->db->fetch(
            "SELECT id FROM organization_extensions WHERE organization_id = ? AND extension_id = ? AND is_active = 1",
            [$id, $extId]
        );
        if (!$row) {
            throw new NotFoundException('Extension not installed for this organization');
        }

        $this->db->update('organization_extensions', [
            'is_active' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $row['id']]);

        return $this->success(null, 'Extension uninstalled');
    }

    // =========================================================================
    // Announcements
    // =========================================================================

    /**
     * GET /api/platform/announcements
     */
    public function announcements(Request $request): Response
    {
        $page = max(1, (int) ($request->input('page', 1)));
        $perPage = min(100, max(1, (int) ($request->input('per_page', 20))));
        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM announcements");
        $data = $this->db->fetchAll(
            "SELECT * FROM announcements ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        return $this->paginated($data, (int) ($total['cnt'] ?? 0), $page, $perPage);
    }

    /**
     * POST /api/platform/announcements
     */
    public function createAnnouncement(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|in:info,warning,critical,maintenance',
            'target' => 'nullable|in:all,specific',
            'target_org_ids' => 'nullable|string',
            'starts_at' => 'nullable|string',
            'ends_at' => 'nullable|string',
            'is_active' => 'nullable|integer',
        ]);

        $data['title'] = Sanitizer::string($data['title']);
        $data['type'] = $data['type'] ?? 'info';
        $data['target'] = $data['target'] ?? 'all';
        $data['is_active'] = (int) ($data['is_active'] ?? 1);
        $data['created_by'] = $request->userId();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->db->insert('announcements', $data);
        $ann = $this->db->fetch("SELECT * FROM announcements WHERE id = ?", [$id]);
        return $this->created($ann, 'Announcement created');
    }

    /**
     * PUT /api/platform/announcements/{id}
     */
    public function updateAnnouncement(Request $request, int $id): Response
    {
        $ann = $this->db->fetch("SELECT * FROM announcements WHERE id = ?", [$id]);
        if (!$ann) {
            throw new NotFoundException('Announcement not found');
        }

        $data = Validator::validate($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|in:info,warning,critical,maintenance',
            'target' => 'nullable|in:all,specific',
            'target_org_ids' => 'nullable|string',
            'starts_at' => 'nullable|string',
            'ends_at' => 'nullable|string',
            'is_active' => 'nullable|integer',
        ]);

        $data['title'] = Sanitizer::string($data['title']);
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->update('announcements', $data, ['id' => $id]);
        $ann = $this->db->fetch("SELECT * FROM announcements WHERE id = ?", [$id]);
        return $this->success($ann, 'Announcement updated');
    }

    /**
     * DELETE /api/platform/announcements/{id}
     */
    public function deleteAnnouncement(Request $request, int $id): Response
    {
        $ann = $this->db->fetch("SELECT * FROM announcements WHERE id = ?", [$id]);
        if (!$ann) {
            throw new NotFoundException('Announcement not found');
        }

        $this->db->delete('announcements', ['id' => $id]);
        return $this->success(null, 'Announcement deleted');
    }

    // =========================================================================
    // Impersonation (Login As)
    // =========================================================================

    /**
     * POST /api/platform/impersonate/{userId}
     */
    public function impersonate(Request $request, int $userId): Response
    {
        $user = $this->db->fetch(
            "SELECT u.*, o.name as organization_name, o.slug as organization_slug
             FROM users u LEFT JOIN organizations o ON u.organization_id = o.id
             WHERE u.id = ?",
            [$userId]
        );
        if (!$user) {
            throw new NotFoundException('User not found');
        }

        // Get user's roles
        $roles = $this->db->fetchAll(
            "SELECT r.slug FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = ?",
            [$userId]
        );
        $rolesList = array_column($roles, 'slug');

        // Get user's permissions
        $permissions = $this->db->fetchAll(
            "SELECT DISTINCT p.slug FROM user_roles ur
             JOIN role_permissions rp ON rp.role_id = ur.role_id
             JOIN permissions p ON p.id = rp.permission_id
             WHERE ur.user_id = ?",
            [$userId]
        );
        $permsList = array_column($permissions, 'slug');

        // Generate impersonation token (short-lived, 1 hour)
        $secret = $_ENV['JWT_SECRET'] ?? 'default-secret';
        $payload = [
            'iss' => 'k2pickleball',
            'sub' => $userId,
            'org' => $user['organization_id'],
            'roles' => $rolesList,
            'permissions' => $permsList,
            'impersonated_by' => $request->userId(),
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $token = \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');

        // Log impersonation in audit trail
        $this->db->insert('activity_logs', [
            'organization_id' => $user['organization_id'],
            'user_id' => $request->userId(),
            'action' => 'impersonate',
            'entity_type' => 'user',
            'entity_id' => $userId,
            'description' => 'Super admin impersonated user: ' . $user['email'],
            'ip_address' => $request->ip(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->success([
            'access_token' => $token,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'organization_id' => $user['organization_id'],
                'organization_name' => $user['organization_name'],
            ],
        ], 'Impersonation token generated');
    }

    /**
     * POST /api/platform/organizations/{id}/impersonate
     * Find the org owner (or first active user) and impersonate them.
     */
    public function impersonateOrgOwner(Request $request, int $id): Response
    {
        $org = $this->db->fetch("SELECT id, name FROM organizations WHERE id = ?", [$id]);
        if (!$org) {
            throw new NotFoundException('Organization not found');
        }

        // Find org owner (user with org-owner role), fallback to first active user
        $owner = $this->db->fetch(
            "SELECT u.id FROM users u
             JOIN user_roles ur ON ur.user_id = u.id
             JOIN roles r ON r.id = ur.role_id
             WHERE u.organization_id = ? AND r.slug = 'org-owner' AND u.status = 'active'
             LIMIT 1",
            [$id]
        );

        if (!$owner) {
            $owner = $this->db->fetch(
                "SELECT id FROM users WHERE organization_id = ? AND status = 'active' ORDER BY id ASC LIMIT 1",
                [$id]
            );
        }

        if (!$owner) {
            return $this->error('No active users found for this organization', 404);
        }

        return $this->impersonate($request, (int) $owner['id']);
    }

    // =========================================================================
    // Create User (Platform)
    // =========================================================================

    /**
     * POST /api/platform/users
     */
    public function createUser(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'required|email|max:255',
            'password'        => 'required|string|min:8|max:255',
            'organization_id' => 'nullable|integer',
            'role_id'         => 'required|integer',
            'status'          => 'nullable|in:active,inactive,suspended,pending',
        ]);

        $data['first_name'] = Sanitizer::string($data['first_name']);
        $data['last_name'] = Sanitizer::string($data['last_name']);

        // Check email uniqueness
        $existing = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$data['email']]);
        if ($existing) {
            return $this->validationError(['email' => ['A user with this email already exists']]);
        }

        // Validate organization exists if provided
        $orgId = !empty($data['organization_id']) ? (int) $data['organization_id'] : null;
        if ($orgId) {
            $org = $this->db->fetch("SELECT id FROM organizations WHERE id = ?", [$orgId]);
            if (!$org) {
                return $this->validationError(['organization_id' => ['Organization not found']]);
            }
        }

        // Validate role exists
        $role = $this->db->fetch("SELECT id, slug FROM roles WHERE id = ?", [(int) $data['role_id']]);
        if (!$role) {
            return $this->validationError(['role_id' => ['Role not found']]);
        }

        // Super-admin role must not be linked to an org
        if ($role['slug'] === 'super-admin' && $orgId) {
            return $this->validationError(['role_id' => ['Super-admin users cannot belong to an organization']]);
        }

        // Non-super-admin roles need an org
        if ($role['slug'] !== 'super-admin' && !$orgId) {
            return $this->validationError(['organization_id' => ['Organization is required for this role']]);
        }

        $now = date('Y-m-d H:i:s');
        $userId = $this->db->insert('users', [
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'email'           => $data['email'],
            'password_hash'   => password_hash($data['password'], PASSWORD_BCRYPT),
            'organization_id' => $orgId,
            'status'          => $data['status'] ?? 'active',
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        // Assign role
        $this->db->insert('user_roles', [
            'user_id' => $userId,
            'role_id' => (int) $data['role_id'],
        ]);

        // Log action
        $this->db->insert('activity_logs', [
            'organization_id' => $orgId,
            'user_id'         => $request->userId(),
            'action'          => 'create',
            'entity_type'     => 'user',
            'entity_id'       => $userId,
            'description'     => 'Platform created user: ' . $data['email'] . ' with role: ' . $role['slug'],
            'ip_address'      => $request->ip(),
            'created_at'      => $now,
        ]);

        $user = $this->db->fetch(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.status, u.organization_id, o.name as organization_name
             FROM users u LEFT JOIN organizations o ON u.organization_id = o.id WHERE u.id = ?",
            [$userId]
        );

        return $this->created($user, 'User created successfully');
    }

    // =========================================================================
    // Active Announcements (for admin dashboard)
    // =========================================================================

    /**
     * GET /api/announcements/active
     * Returns active announcements visible to the current user's organization.
     */
    public function activeAnnouncements(Request $request): Response
    {
        $orgId = $request->orgId();
        $now = date('Y-m-d H:i:s');

        $rows = $this->db->fetchAll(
            "SELECT id, title, message, type, target, target_org_ids, starts_at, ends_at, created_at
             FROM announcements
             WHERE is_active = 1
               AND (starts_at IS NULL OR starts_at <= ?)
               AND (ends_at IS NULL OR ends_at >= ?)
             ORDER BY created_at DESC
             LIMIT 20",
            [$now, $now]
        );

        // Filter by target
        $result = [];
        foreach ($rows as $row) {
            if ($row['target'] === 'all') {
                unset($row['target_org_ids']);
                $result[] = $row;
            } elseif ($row['target'] === 'specific' && $orgId) {
                $targetOrgIds = array_map('intval', array_filter(explode(',', $row['target_org_ids'] ?? '')));
                if (in_array((int) $orgId, $targetOrgIds, true)) {
                    unset($row['target_org_ids']);
                    $result[] = $row;
                }
            }
        }

        return $this->success($result);
    }
}
