<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    // ─── Extension management (org admin) ─────────────────────────────
    $router->group(['prefix' => '/api/extensions'], function (Router $router) {
        $router->group(['permission' => 'extensions.view'], function (Router $router) {
            $router->get('/catalog', ['App\\Modules\\Extensions\\ExtensionController', 'catalog']);
            $router->get('/check/{slug}', ['App\\Modules\\Extensions\\ExtensionController', 'check']);
            $router->get('/{slug}/settings', ['App\\Modules\\Extensions\\ExtensionController', 'settings']);
        });
        $router->group(['permission' => 'extensions.install'], function (Router $router) {
            $router->post('/{slug}/install', ['App\\Modules\\Extensions\\ExtensionController', 'install']);
            $router->post('/{slug}/uninstall', ['App\\Modules\\Extensions\\ExtensionController', 'uninstall']);
        });
        $router->group(['permission' => 'extensions.configure'], function (Router $router) {
            $router->put('/{slug}/settings', ['App\\Modules\\Extensions\\ExtensionController', 'updateSettings']);
            $router->put('/{slug}/facilities/{facilityId}/settings', ['App\\Modules\\Extensions\\ExtensionController', 'updateFacilitySettings']);
        });
    });

    // ─── Square Terminal POS extension routes ─────────────────────────
    $terminalRoutes = __DIR__ . '/SquareTerminal/routes.php';
    if (file_exists($terminalRoutes)) {
        $loadTerminalRoutes = require $terminalRoutes;
        if (is_callable($loadTerminalRoutes)) {
            $loadTerminalRoutes($router);
        }
    }
};
