<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/users'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Users\\UserController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Users\\UserController', 'show']);
        $router->post('/', ['App\\Modules\\Users\\UserController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Users\\UserController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\Users\\UserController', 'destroy']);
        $router->post('/{id}/roles', ['App\\Modules\\Users\\UserController', 'assignRole']);
        $router->delete('/{id}/roles/{roleId}', ['App\\Modules\\Users\\UserController', 'removeRole']);
    });
};
