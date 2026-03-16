<?php

declare(strict_types=1);

use App\Core\Http\Router;

/**
 * Master route registration file.
 * Loads all module route files.
 */
return function (Router $router): void {
    // Health check
    $router->get('/api/health', function () {
        return \App\Core\Http\Response::json([
            'status' => 'ok',
            'timestamp' => date('c'),
            'version' => \App\Core\Services\Config::get('app.version', '1.0.0'),
        ]);
    });

    // Load module routes
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
        'Admin',
        'Platform',
        'Client',
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
};
