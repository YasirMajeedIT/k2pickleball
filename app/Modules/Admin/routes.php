<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\Admin\AdminController;

/**
 * Admin panel HTML routes.
 */
return function (Router $router): void {
    // Admin panel HTML routes (no API middleware - these serve rendered views)
    $router->get('/admin/login', [AdminController::class, 'handleRequest']);
    $router->get('/admin', [AdminController::class, 'handleRequest']);
    $router->get('/admin/dashboard', [AdminController::class, 'handleRequest']);

    // Module index pages
    $router->get('/admin/facilities', [AdminController::class, 'handleRequest']);
    $router->get('/admin/courts', [AdminController::class, 'handleRequest']);
    $router->get('/admin/users', [AdminController::class, 'handleRequest']);
    $router->get('/admin/roles', [AdminController::class, 'handleRequest']);
    $router->get('/admin/subscriptions', [AdminController::class, 'handleRequest']);
    $router->get('/admin/payments', [AdminController::class, 'handleRequest']);
    $router->get('/admin/notifications', [AdminController::class, 'handleRequest']);
    $router->get('/admin/files', [AdminController::class, 'handleRequest']);
    $router->get('/admin/api-tokens', [AdminController::class, 'handleRequest']);
    $router->get('/admin/audit-logs', [AdminController::class, 'handleRequest']);
    $router->get('/admin/settings', [AdminController::class, 'handleRequest']);

    // Create pages
    $router->get('/admin/facilities/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/courts/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/users/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/roles/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/api-tokens/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/files/upload', [AdminController::class, 'handleRequest']);

    // Edit pages (with {id} param)
    $router->get('/admin/facilities/{id}', [AdminController::class, 'handleRequest']);
    $router->get('/admin/facilities/{id}/edit', [AdminController::class, 'handleRequest']);
    $router->get('/admin/courts/{id}/edit', [AdminController::class, 'handleRequest']);
    $router->get('/admin/users/{id}/edit', [AdminController::class, 'handleRequest']);
    $router->get('/admin/roles/{id}/edit', [AdminController::class, 'handleRequest']);
};
