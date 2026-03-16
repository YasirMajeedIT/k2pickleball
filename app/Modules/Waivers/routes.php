<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/waivers'], function (Router $router) {
        $router->get('/active', ['App\\Modules\\Waivers\\WaiverController', 'active']);
        $router->get('/', ['App\\Modules\\Waivers\\WaiverController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Waivers\\WaiverController', 'show']);
        $router->post('/', ['App\\Modules\\Waivers\\WaiverController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Waivers\\WaiverController', 'update']);
        $router->post('/{id}/activate', ['App\\Modules\\Waivers\\WaiverController', 'activate']);
        $router->delete('/{id}', ['App\\Modules\\Waivers\\WaiverController', 'destroy']);
    });
};
