<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/resources'], function (Router $router) {
        $router->group(['permission' => 'resources.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Resources\\ResourceController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Resources\\ResourceController', 'show']);
        });
        $router->group(['permission' => 'resources.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Resources\\ResourceController', 'store']);
            $router->post('/{id}/values', ['App\\Modules\\Resources\\ResourceController', 'storeValue']);
        });
        $router->group(['permission' => 'resources.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Resources\\ResourceController', 'update']);
            $router->put('/{id}/values/{valueId}', ['App\\Modules\\Resources\\ResourceController', 'updateValue']);
        });
        $router->group(['permission' => 'resources.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Resources\\ResourceController', 'destroy']);
            $router->delete('/{id}/values/{valueId}', ['App\\Modules\\Resources\\ResourceController', 'destroyValue']);
        });
    });
};
