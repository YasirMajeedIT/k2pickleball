<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\Platform\PlatformController;
use App\Modules\Platform\PlatformApiController;

/**
 * Platform (Super Admin) panel HTML routes.
 */
return function (Router $router): void {
    // HTML pages
    $router->get('/platform', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/organizations', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/organizations/create', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/organizations/{id}', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/organizations/{id}/edit', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/plans', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/plans/create', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/plans/{id}/edit', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/subscriptions', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/subscriptions/{id}', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/revenue', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/system-users', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/system-settings', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/audit-logs', [PlatformController::class, 'handleRequest']);

    // Platform API endpoints
    $router->get('/api/platform/stats', [PlatformApiController::class, 'stats']);
    $router->get('/api/platform/users', [PlatformApiController::class, 'users']);
    $router->get('/api/platform/audit-logs', [PlatformApiController::class, 'auditLogs']);
    $router->get('/api/platform/revenue', [PlatformApiController::class, 'revenue']);
    $router->get('/api/platform/settings', [PlatformApiController::class, 'settings']);
    $router->put('/api/platform/settings', [PlatformApiController::class, 'updateSettings']);
};
