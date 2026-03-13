<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/roles'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Roles\\RoleController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Roles\\RoleController', 'show']);
        $router->post('/', ['App\\Modules\\Roles\\RoleController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Roles\\RoleController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\Roles\\RoleController', 'destroy']);
    });

    $router->get('/api/permissions', ['App\\Modules\\Roles\\RoleController', 'permissions']);
};
