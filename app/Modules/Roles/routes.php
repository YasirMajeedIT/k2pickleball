<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/roles'], function (Router $router) {
        $router->group(['permission' => 'roles.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Roles\\RoleController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Roles\\RoleController', 'show']);
        });
        $router->group(['permission' => 'roles.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Roles\\RoleController', 'store']);
        });
        $router->group(['permission' => 'roles.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Roles\\RoleController', 'update']);
        });
        $router->group(['permission' => 'roles.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Roles\\RoleController', 'destroy']);
        });
    });

    $router->group(['permission' => 'roles.view'], function (Router $router) {
        $router->get('/api/permissions', ['App\\Modules\\Roles\\RoleController', 'permissions']);
    });
};
