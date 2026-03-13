<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/courts'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Courts\\CourtController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Courts\\CourtController', 'show']);
        $router->post('/', ['App\\Modules\\Courts\\CourtController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Courts\\CourtController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\Courts\\CourtController', 'destroy']);
    });
};
