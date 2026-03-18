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

    // Extensions
    $router->get('/admin/extensions', [AdminController::class, 'handleRequest']);

    // Categories
    $router->get('/admin/categories', [AdminController::class, 'handleRequest']);
    $router->get('/admin/categories/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/categories/{id}/edit', [AdminController::class, 'handleRequest']);

    // Resources
    $router->get('/admin/resources', [AdminController::class, 'handleRequest']);
    $router->get('/admin/resources/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/resources/{id}', [AdminController::class, 'handleRequest']);
    $router->get('/admin/resources/{id}/edit', [AdminController::class, 'handleRequest']);

    // Schedule Dashboard
    $router->get('/admin/schedule-dashboard', [AdminController::class, 'handleRequest']);
    $router->get('/admin/schedule-dashboard/session-types', [AdminController::class, 'handleRequest']);
    $router->get('/admin/schedule-dashboard/session-types/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/schedule-dashboard/session-types/{id}/edit', [AdminController::class, 'handleRequest']);
    $router->get('/admin/schedule-dashboard/sessions', [AdminController::class, 'handleRequest']);

    // Players
    $router->get('/admin/players', [AdminController::class, 'handleRequest']);
    $router->get('/admin/players/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/players/{id}', [AdminController::class, 'handleRequest']);
    $router->get('/admin/players/{id}/edit', [AdminController::class, 'handleRequest']);

    // Credit Codes
    $router->get('/admin/credit-codes', [AdminController::class, 'handleRequest']);
    $router->get('/admin/credit-codes/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/credit-codes/{id}', [AdminController::class, 'handleRequest']);
    $router->get('/admin/credit-codes/{id}/edit', [AdminController::class, 'handleRequest']);

    // Gift Certificates
    $router->get('/admin/gift-certificates', [AdminController::class, 'handleRequest']);
    $router->get('/admin/gift-certificates/create', [AdminController::class, 'handleRequest']);
    $router->get('/admin/gift-certificates/{id}', [AdminController::class, 'handleRequest']);
    $router->get('/admin/gift-certificates/{id}/edit', [AdminController::class, 'handleRequest']);

    // Discounts
    $router->get('/admin/discounts', [AdminController::class, 'handleRequest']);

    // Waivers
    $router->get('/admin/waivers', [AdminController::class, 'handleRequest']);

    // My Account pages
    $router->get('/admin/account', [AdminController::class, 'handleRequest']);
    $router->get('/admin/my-subscription', [AdminController::class, 'handleRequest']);
    $router->get('/admin/my-invoices', [AdminController::class, 'handleRequest']);

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
    $router->get('/admin/courts/{id}', [AdminController::class, 'handleRequest']);
    $router->get('/admin/courts/{id}/edit', [AdminController::class, 'handleRequest']);
    $router->get('/admin/users/{id}', [AdminController::class, 'handleRequest']);
    $router->get('/admin/users/{id}/edit', [AdminController::class, 'handleRequest']);
    $router->get('/admin/roles/{id}', [AdminController::class, 'handleRequest']);
    $router->get('/admin/roles/{id}/edit', [AdminController::class, 'handleRequest']);
};
