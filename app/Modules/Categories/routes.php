<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/categories'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Categories\\CategoryController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Categories\\CategoryController', 'show']);
        $router->post('/', ['App\\Modules\\Categories\\CategoryController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Categories\\CategoryController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\Categories\\CategoryController', 'destroy']);
    });
};
