<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\Platform\PlatformController;
use App\Modules\Platform\PlatformApiController;

/**
 * Platform (Super Admin) panel HTML routes.
 */
return function (Router $router): void {
    // Auth pages (standalone — no sidebar layout)
    $router->get('/platform/login', [PlatformController::class, 'handleRequest']);

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
    $router->get('/platform/system-users/create', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/system-users/{id}', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/invoices', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/extensions', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/announcements', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/system-settings', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/audit-logs', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/consultations', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/contact-submissions', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/site-settings', [PlatformController::class, 'handleRequest']);

    // Platform API endpoints — Stats & Dashboard
    $router->get('/api/platform/stats', [PlatformApiController::class, 'stats']);
    $router->get('/api/platform/revenue', [PlatformApiController::class, 'revenue']);

    // Platform API — Users
    $router->get('/api/platform/users', [PlatformApiController::class, 'users']);
    $router->get('/api/platform/users/{id}', [PlatformApiController::class, 'userDetail']);
    $router->patch('/api/platform/users/{id}/status', [PlatformApiController::class, 'updateUserStatus']);

    // Platform API — Invoices
    $router->get('/api/platform/invoices', [PlatformApiController::class, 'invoices']);

    // Platform API — Extensions
    $router->get('/api/platform/extensions', [PlatformApiController::class, 'extensions']);
    $router->post('/api/platform/extensions', [PlatformApiController::class, 'createExtension']);
    $router->put('/api/platform/extensions/{id}', [PlatformApiController::class, 'updateExtension']);
    $router->delete('/api/platform/extensions/{id}', [PlatformApiController::class, 'deleteExtension']);

    // Platform API — Org Extensions (install/uninstall)
    $router->get('/api/platform/organizations/{id}/extensions', [PlatformApiController::class, 'orgExtensions']);
    $router->post('/api/platform/organizations/{id}/extensions', [PlatformApiController::class, 'installExtension']);
    $router->delete('/api/platform/organizations/{id}/extensions/{extId}', [PlatformApiController::class, 'uninstallExtension']);

    // Platform API — Announcements
    $router->get('/api/platform/announcements', [PlatformApiController::class, 'announcements']);
    $router->post('/api/platform/announcements', [PlatformApiController::class, 'createAnnouncement']);
    $router->put('/api/platform/announcements/{id}', [PlatformApiController::class, 'updateAnnouncement']);
    $router->delete('/api/platform/announcements/{id}', [PlatformApiController::class, 'deleteAnnouncement']);

    // Platform API — Impersonation
    $router->post('/api/platform/impersonate/{id}', [PlatformApiController::class, 'impersonate']);
    $router->post('/api/platform/organizations/{id}/impersonate', [PlatformApiController::class, 'impersonateOrgOwner']);

    // Platform API — Active Announcements (admin-facing)
    $router->get('/api/announcements/active', [PlatformApiController::class, 'activeAnnouncements']);

    // Platform API — Settings & Audit
    $router->get('/api/platform/settings', [PlatformApiController::class, 'settings']);
    $router->put('/api/platform/settings', [PlatformApiController::class, 'updateSettings']);
    $router->get('/api/platform/audit-logs', [PlatformApiController::class, 'auditLogs']);
};
