<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/players'], function (Router $router) {
        $router->group(['permission' => 'players.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Players\\PlayerController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Players\\PlayerController', 'show']);
        });
        $router->group(['permission' => 'players.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Players\\PlayerController', 'store']);
        });
        $router->group(['permission' => 'players.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Players\\PlayerController', 'update']);
            $router->post('/{id}/avatar', ['App\\Modules\\Players\\PlayerController', 'uploadAvatar']);
            $router->delete('/{id}/avatar', ['App\\Modules\\Players\\PlayerController', 'deleteAvatar']);
        });
        $router->group(['permission' => 'players.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Players\\PlayerController', 'destroy']);
        });
    });
};
