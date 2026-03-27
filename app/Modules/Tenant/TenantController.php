<?php

declare(strict_types=1);

namespace App\Modules\Tenant;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;

/**
 * Serves tenant-branded HTML pages for the organization's public website.
 * Accessed via org subdomain (e.g., tbpb.k2pickleball.local).
 *
 * URL structure:
 *   /                          → Home page
 *   /sessions                  → Session programs listing
 *   /schedule                  → Public calendar
 *   /schedule/{classId}        → Class detail / booking
 *   /facilities                → Locations listing
 *   /facilities/{slug}         → Location detail
 *   /about                     → About the club
 *   /contact                   → Contact page
 *   /login                     → Player login
 *   /register                  → Player registration
 *   /forgot-password           → Password reset
 *   /dashboard                 → Player dashboard (auth required)
 *   /dashboard/bookings        → My bookings
 *   /dashboard/profile         → Profile settings
 */
class TenantController extends Controller
{
    private Connection $db;

    /** View file mapping */
    private array $viewMap = [
        // Public pages
        'home'              => 'tenant/home.php',
        'sessions'          => 'tenant/sessions.php',
        'schedule'          => 'tenant/schedule.php',
        'facilities'        => 'tenant/facilities.php',
        'about'             => 'tenant/about.php',
        'contact'           => 'tenant/contact.php',
        'book-court'        => 'tenant/book-court.php',
        'memberships'       => 'tenant/memberships.php',

        // Player auth
        'login'             => 'tenant/auth/login.php',
        'register'          => 'tenant/auth/register.php',
        'forgot-password'   => 'tenant/auth/forgot-password.php',
        'reset-password'    => 'tenant/auth/reset-password.php',

        // Player dashboard
        'dashboard'             => 'tenant/dashboard/index.php',
        'dashboard/bookings'    => 'tenant/dashboard/bookings.php',
        'dashboard/profile'     => 'tenant/dashboard/profile.php',
        'dashboard/notifications' => 'tenant/dashboard/notifications.php',
    ];

    /** Auth pages (standalone, no layout) */
    private const AUTH_VIEWS = [
        'tenant/auth/login.php',
        'tenant/auth/register.php',
        'tenant/auth/forgot-password.php',
        'tenant/auth/reset-password.php',
    ];

    /** Dashboard pages (uses dashboard layout) */
    private const DASHBOARD_VIEWS = [
        'tenant/dashboard/index.php',
        'tenant/dashboard/bookings.php',
        'tenant/dashboard/profile.php',
        'tenant/dashboard/notifications.php',
    ];

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function handleRequest(Request $request): Response
    {
        $path = trim($request->path(), '/');
        if ($path === '' || $path === null) {
            $path = 'home';
        }

        if (!isset($this->viewMap[$path])) {
            return Response::html($this->renderNotFound(), 404);
        }

        // Load org context for all pages
        $org = $this->loadOrganization($request);
        if (!$org) {
            return Response::html($this->renderNotFound(), 404);
        }

        return $this->renderView($this->viewMap[$path], [
            'org' => $org,
            'branding' => $org['branding'] ?? [],
        ]);
    }

    /**
     * Handle schedule/{id} detail route (dynamic segment).
     */
    public function classDetail(Request $request, int $id): Response
    {
        $org = $this->loadOrganization($request);
        if (!$org) {
            return Response::html($this->renderNotFound(), 404);
        }

        return $this->renderView('tenant/schedule-detail.php', [
            'org' => $org,
            'branding' => $org['branding'] ?? [],
            'classId' => $id,
        ]);
    }

    /**
     * Handle facilities/{slug} pages.
     */
    public function facilityDetail(Request $request, string $slug): Response
    {
        $org = $this->loadOrganization($request);
        if (!$org) {
            return Response::html($this->renderNotFound(), 404);
        }

        return $this->renderView('tenant/facility-detail.php', [
            'org' => $org,
            'branding' => $org['branding'] ?? [],
            'facilitySlug' => $slug,
        ]);
    }

    /**
     * Handle /schedule/category/{slug} — dynamic category schedule page.
     */
    public function categoryPage(Request $request, string $slug): Response
    {
        $org = $this->loadOrganization($request);
        if (!$org) {
            return Response::html($this->renderNotFound(), 404);
        }

        return $this->renderView('tenant/category-schedule.php', [
            'org' => $org,
            'branding' => $org['branding'] ?? [],
            'categorySlug' => $slug,
        ]);
    }

    /**
     * Handle /p/{slug} — custom page.
     */
    public function customPage(Request $request, string $slug): Response
    {
        $org = $this->loadOrganization($request);
        if (!$org) {
            return Response::html($this->renderNotFound(), 404);
        }

        return $this->renderView('tenant/custom-page.php', [
            'org' => $org,
            'branding' => $org['branding'] ?? [],
            'pageSlug' => $slug,
        ]);
    }

    /**
     * Handle /forms/{slug} — custom form.
     */
    public function customForm(Request $request, string $slug): Response
    {
        $org = $this->loadOrganization($request);
        if (!$org) {
            return Response::html($this->renderNotFound(), 404);
        }

        return $this->renderView('tenant/custom-form.php', [
            'org' => $org,
            'branding' => $org['branding'] ?? [],
            'formSlug' => $slug,
        ]);
    }

    /* ── private helpers ── */

    private function loadOrganization(Request $request): ?array
    {
        $orgId = $request->organizationId();
        if (!$orgId) {
            return null;
        }

        $org = $this->db->fetch(
            "SELECT `id`, `name`, `slug`, `email`, `phone`, `timezone`, `currency`, `settings`
             FROM `organizations` WHERE `id` = ?",
            [$orgId]
        );

        if (!$org) {
            return null;
        }

        $org['settings'] = json_decode($org['settings'] ?? '{}', true) ?: [];

        // Load branding
        $rows = $this->db->fetchAll(
            "SELECT `key_name`, `value` FROM `settings`
             WHERE `organization_id` = ? AND `group_name` = 'branding'",
            [$orgId]
        );
        $org['branding'] = [];
        foreach ($rows as $r) {
            $org['branding'][$r['key_name']] = $r['value'];
        }

        // Load facilities for location switcher
        $org['facilities'] = $this->db->fetchAll(
            "SELECT `id`, `name`, `slug`, `city`, `state` FROM `facilities`
             WHERE `organization_id` = ? AND `status` = 'active' ORDER BY `name`",
            [$orgId]
        );

        // Load system categories (e.g. Book a Court) for nav/footer names
        $systemCats = $this->db->fetchAll(
            "SELECT `system_slug`, `name`, `is_active` FROM `categories`
             WHERE `organization_id` = ? AND `is_system` = 1",
            [$orgId]
        );
        $org['system_categories'] = [];
        foreach ($systemCats as $sc) {
            $org['system_categories'][$sc['system_slug']] = [
                'name' => $sc['name'],
                'is_active' => (bool) $sc['is_active'],
            ];
        }

        return $org;
    }

    private function renderView(string $viewFile, array $params = []): Response
    {
        $viewPath = dirname(__DIR__, 2) . '/Views/' . $viewFile;
        if (!file_exists($viewPath)) {
            return Response::html($this->renderNotFound(), 404);
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = rtrim(dirname(dirname($scriptName)), '/\\');
        $params['baseUrl'] = ($basePath === '' || $basePath === '.') ? '' : $basePath;

        // Determine layout
        if (in_array($viewFile, self::AUTH_VIEWS, true)) {
            // Standalone auth pages
            extract($params);
            ob_start();
            include $viewPath;
            return Response::html(ob_get_clean());
        }

        if (in_array($viewFile, self::DASHBOARD_VIEWS, true)) {
            // Dashboard layout
            $params['contentView'] = $viewFile;
            $layoutPath = dirname(__DIR__, 2) . '/Views/layouts/tenant-dashboard.php';
            extract($params);
            ob_start();
            include $layoutPath;
            return Response::html(ob_get_clean());
        }

        // Public layout
        $params['contentView'] = $viewFile;
        $layoutPath = dirname(__DIR__, 2) . '/Views/layouts/tenant.php';
        extract($params);
        ob_start();
        include $layoutPath;
        return Response::html(ob_get_clean());
    }

    private function renderNotFound(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Page Not Found</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-white flex items-center justify-center min-h-screen">
<div class="text-center px-6">
  <div class="text-8xl font-extrabold text-gray-200">404</div>
  <p class="mt-4 text-xl font-semibold text-gray-800">Page not found</p>
  <p class="mt-2 text-gray-500">The page you're looking for doesn't exist or has been moved.</p>
  <a href="/" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700 transition">
    ← Back to Home
  </a>
</div>
</body></html>
HTML;
    }
}
