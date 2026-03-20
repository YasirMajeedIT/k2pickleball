<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/courts'], function (Router $router) {
        $router->group(['permission' => 'courts.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Courts\\CourtController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Courts\\CourtController', 'show']);
        });
        $router->group(['permission' => 'courts.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Courts\\CourtController', 'store']);
        });
        $router->group(['permission' => 'courts.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Courts\\CourtController', 'update']);
        });
        $router->group(['permission' => 'courts.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Courts\\CourtController', 'destroy']);
        });
    });
};
