<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/resources'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Resources\\ResourceController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Resources\\ResourceController', 'show']);
        $router->post('/', ['App\\Modules\\Resources\\ResourceController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Resources\\ResourceController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\Resources\\ResourceController', 'destroy']);

        // Resource Values
        $router->post('/{id}/values', ['App\\Modules\\Resources\\ResourceController', 'storeValue']);
        $router->put('/{id}/values/{valueId}', ['App\\Modules\\Resources\\ResourceController', 'updateValue']);
        $router->delete('/{id}/values/{valueId}', ['App\\Modules\\Resources\\ResourceController', 'destroyValue']);
    });
};
