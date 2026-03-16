<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/players'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Players\\PlayerController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Players\\PlayerController', 'show']);
        $router->post('/', ['App\\Modules\\Players\\PlayerController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Players\\PlayerController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\Players\\PlayerController', 'destroy']);
        $router->post('/{id}/avatar', ['App\\Modules\\Players\\PlayerController', 'uploadAvatar']);
        $router->delete('/{id}/avatar', ['App\\Modules\\Players\\PlayerController', 'deleteAvatar']);
    });
};
