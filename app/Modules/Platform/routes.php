<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\Platform\PlatformController;

/**
 * Platform (Super Admin) panel HTML routes.
 */
return function (Router $router): void {
    $router->get('/platform', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/organizations', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/plans', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/subscriptions', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/revenue', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/system-users', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/system-settings', [PlatformController::class, 'handleRequest']);
    $router->get('/platform/audit-logs', [PlatformController::class, 'handleRequest']);
};
