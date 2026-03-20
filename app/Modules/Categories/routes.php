<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/categories'], function (Router $router) {
        $router->group(['permission' => 'categories.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Categories\\CategoryController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Categories\\CategoryController', 'show']);
        });
        $router->group(['permission' => 'categories.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Categories\\CategoryController', 'store']);
        });
        $router->group(['permission' => 'categories.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Categories\\CategoryController', 'update']);
        });
        $router->group(['permission' => 'categories.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Categories\\CategoryController', 'destroy']);
        });
    });
};
