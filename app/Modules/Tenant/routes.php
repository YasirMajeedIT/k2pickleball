<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\Tenant\TenantController;
use App\Modules\Tenant\PublicApiController;

/**
 * Tenant-facing routes.
 *
 * These are registered for organization subdomains — the TenantResolver
 * middleware sets panel_type='tenant' when a non-system subdomain is detected.
 * The Application boot checks panel_type and loads this route file accordingly.
 *
 * Public API: /api/public/*   — JSON data for the frontend
 * Pages:     /*               — HTML pages served by TenantController
 */
return function (Router $router): void {

    /* ─── Public API (no auth) ─── */
    $router->group(['prefix' => '/api/public'], function (Router $router) {
        $router->get('/org',                    [PublicApiController::class, 'organization']);
        $router->get('/facilities',             [PublicApiController::class, 'facilities']);
        $router->get('/facilities/{slug}',      [PublicApiController::class, 'facility']);
        $router->get('/categories',             [PublicApiController::class, 'categories']);
        $router->get('/court-category',          [PublicApiController::class, 'courtCategory']);
        $router->get('/sessions',               [PublicApiController::class, 'sessions']);
        $router->get('/sessions/{id}',          [PublicApiController::class, 'sessionDetail']);
        $router->get('/schedule',               [PublicApiController::class, 'schedule']);
        $router->get('/classes/{id}',           [PublicApiController::class, 'classDetail']);
        $router->get('/courts/availability',    [PublicApiController::class, 'courtAvailability']);
        $router->post('/courts/book',           [PublicApiController::class, 'bookCourt']);

        // Schedule page settings & inline booking (038+)
        $router->get('/schedule-settings',      [PublicApiController::class, 'scheduleSettings']);
        $router->get('/validate-credit-code',   [PublicApiController::class, 'validateCreditCode']);
        $router->get('/validate-gift-code',     [PublicApiController::class, 'validateGiftCode']);
        $router->post('/book-class/{sessionTypeId}/{classId}', [PublicApiController::class, 'bookClass']);

        // New endpoints (035+)
        $router->get('/navigation',             [PublicApiController::class, 'navigation']);
        $router->get('/membership-plans',       [PublicApiController::class, 'membershipPlans']);
        $router->get('/theme',                  [PublicApiController::class, 'theme']);
        $router->get('/category/{slug}',        [PublicApiController::class, 'categoryBySlug']);
    });

    /* ─── HTML pages ─── */
    // Public pages
    $router->get('/',                   [TenantController::class, 'handleRequest']);
    $router->get('/sessions',           [TenantController::class, 'handleRequest']);
    $router->get('/schedule',           [TenantController::class, 'handleRequest']);
    $router->get('/schedule/{id}',      [TenantController::class, 'classDetail']);
    $router->get('/schedule/category/{slug}', [TenantController::class, 'categoryPage']);
    $router->get('/facilities',         [TenantController::class, 'handleRequest']);
    $router->get('/facilities/{slug}',  [TenantController::class, 'facilityDetail']);
    $router->get('/about',              [TenantController::class, 'handleRequest']);
    $router->get('/contact',            [TenantController::class, 'handleRequest']);
    $router->get('/book-court',         [TenantController::class, 'handleRequest']);
    $router->get('/memberships',        [TenantController::class, 'handleRequest']);

    // Custom content
    $router->get('/p/{slug}',            [TenantController::class, 'customPage']);
    $router->get('/forms/{slug}',        [TenantController::class, 'customForm']);

    // Player auth
    $router->get('/login',              [TenantController::class, 'handleRequest']);
    $router->get('/register',           [TenantController::class, 'handleRequest']);
    $router->get('/forgot-password',    [TenantController::class, 'handleRequest']);
    $router->get('/reset-password',     [TenantController::class, 'handleRequest']);
    $router->get('/accept-invite',      [TenantController::class, 'handleRequest']);

    // Player dashboard (auth guard on client-side)
    $router->get('/dashboard',              [TenantController::class, 'handleRequest']);
    $router->get('/dashboard/bookings',     [TenantController::class, 'handleRequest']);
    $router->get('/dashboard/profile',      [TenantController::class, 'handleRequest']);
    $router->get('/dashboard/notifications',[TenantController::class, 'handleRequest']);
};
