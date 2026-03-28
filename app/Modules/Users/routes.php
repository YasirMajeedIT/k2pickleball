<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/users'], function (Router $router) {
        $router->group(['permission' => 'users.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Users\\UserController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Users\\UserController', 'show']);
        });
        $router->group(['permission' => 'users.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Users\\UserController', 'store']);
        });
        $router->group(['permission' => 'users.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Users\\UserController', 'update']);
        });
        $router->group(['permission' => 'users.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Users\\UserController', 'destroy']);
        });
        $router->group(['permission' => 'users.create'], function (Router $router) {
            $router->post('/{id}/resend-invite', ['App\\Modules\\Users\\UserController', 'resendInvite']);
        });
        $router->group(['permission' => 'roles.assign'], function (Router $router) {
            $router->post('/{id}/roles', ['App\\Modules\\Users\\UserController', 'assignRole']);
            $router->delete('/{id}/roles/{roleId}', ['App\\Modules\\Users\\UserController', 'removeRole']);
        });
    });
};
