<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/navigation'], function (Router $router) {
        $router->group(['permission' => 'settings.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Navigation\\NavigationController', 'index']);
        });
        $router->group(['permission' => 'settings.update'], function (Router $router) {
            $router->put('/', ['App\\Modules\\Navigation\\NavigationController', 'sync']);
        });
    });
};
