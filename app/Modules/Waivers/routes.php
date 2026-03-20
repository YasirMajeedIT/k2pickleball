<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/waivers'], function (Router $router) {
        // /active is used during the booking flow — accessible to any authenticated user
        $router->get('/active', ['App\\Modules\\Waivers\\WaiverController', 'active']);

        $router->group(['permission' => 'waivers.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Waivers\\WaiverController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Waivers\\WaiverController', 'show']);
        });
        $router->group(['permission' => 'waivers.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Waivers\\WaiverController', 'store']);
        });
        $router->group(['permission' => 'waivers.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Waivers\\WaiverController', 'update']);
            $router->post('/{id}/activate', ['App\\Modules\\Waivers\\WaiverController', 'activate']);
        });
        $router->group(['permission' => 'waivers.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Waivers\\WaiverController', 'destroy']);
        });
    });
};
