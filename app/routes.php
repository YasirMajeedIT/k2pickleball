<?php

declare(strict_types=1);

use App\Core\Http\Router;

/**
 * Master route registration file.
 * Loads all module route files.
 */
return function (Router $router): void {
    // Serve uploaded storage files (storage dir is outside public docroot)
    $router->get('/storage/{path:.+}', function (\App\Core\Http\Request $request, string $path) {
        $storageBase = K2_ROOT . '/storage/';
        $realBase = realpath($storageBase);
        if ($realBase === false) {
            return \App\Core\Http\Response::notFound('Storage directory not found');
        }
        // Security: prevent path traversal
        $requestedPath = $storageBase . ltrim($path, '/');
        $realPath = realpath($requestedPath);
        if ($realPath === false || !str_starts_with($realPath, $realBase) || !is_file($realPath)) {
            return \App\Core\Http\Response::notFound('File not found');
        }
        $mime = mime_content_type($realPath) ?: 'application/octet-stream';
        $size = filesize($realPath);
        $content = file_get_contents($realPath);
        return new \App\Core\Http\Response($content, 200, [
            'Content-Type'   => $mime,
            'Content-Length' => (string) $size,
            'Cache-Control'  => 'public, max-age=86400',
        ]);
    });

    // Health check
    $router->get('/api/health', function () {
        return \App\Core\Http\Response::json([
            'status' => 'ok',
            'timestamp' => date('c'),
            'version' => \App\Core\Services\Config::get('app.version', '1.0.0'),
        ]);
    });

    // Determine if this is a tenant subdomain (organization-branded site).
    // System subdomains: platform, admin, api, www, '' (no subdomain) → load Client routes.
    // Any other subdomain → load Tenant routes (org public website).
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseDomain = \App\Core\Services\Config::get('app.domains.base', 'localhost');
    $subdomain = '';
    if (str_ends_with($host, '.' . $baseDomain)) {
        $subdomain = substr($host, 0, -(strlen($baseDomain) + 1));
    }
    $systemSubdomains = ['platform', 'admin', 'api', 'www', ''];
    $isTenant = !in_array($subdomain, $systemSubdomains, true);

    // Redirect admin subdomain root to admin panel login
    if ($subdomain === 'admin') {
        $router->get('/', function () {
            header('Location: /admin/login');
            exit;
        });
    }

    // Redirect platform subdomain root to platform panel
    if ($subdomain === 'platform') {
        $router->get('/', function () {
            header('Location: /platform/login');
            exit;
        });
    }

    // Load module routes (API modules load for all panels)
    $modules = [
        'Auth',
        'Organizations',
        'Facilities',
        'Courts',
        'Users',
        'Roles',
        'Subscriptions',
        'Payments',
        'Notifications',
        'Files',
        'ApiTokens',
        'AuditLogs',
        'Settings',
        'Categories',
        'Resources',
        'Players',
        'CreditCodes',
        'GiftCertificates',
        'Discounts',
        'Waivers',
        'SessionDetails',
        'SessionTypes',
        'Calendar',
        'Labels',
        'Admin',
        'Platform',
        'Extensions',
        'Memberships',
        'Consultations',
        'ContactSubmissions',
        'SiteSettings',
    ];

    foreach ($modules as $module) {
        $routeFile = __DIR__ . '/Modules/' . $module . '/routes.php';
        if (file_exists($routeFile)) {
            $loadRoutes = require $routeFile;
            if (is_callable($loadRoutes)) {
                $loadRoutes($router);
            }
        }
    }

    // Load either Tenant (org public site) or Client (SaaS marketing site) routes
    $frontendModule = $isTenant ? 'Tenant' : 'Client';
    $routeFile = __DIR__ . '/Modules/' . $frontendModule . '/routes.php';
    if (file_exists($routeFile)) {
        $loadRoutes = require $routeFile;
        if (is_callable($loadRoutes)) {
            $loadRoutes($router);
        }
    }
};
