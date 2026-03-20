<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\SiteSettings\SiteSettingsController;

return function (Router $router): void {
    // Public — check site status / verify password (used by maintenance gate)
    $router->get('/api/site-status', [SiteSettingsController::class, 'status']);
    $router->post('/api/site-verify-password', [SiteSettingsController::class, 'verifyPassword']);

    // Platform admin — manage site settings
    $router->get('/api/platform/site-settings', [SiteSettingsController::class, 'index']);
    $router->put('/api/platform/site-settings', [SiteSettingsController::class, 'update']);
};
