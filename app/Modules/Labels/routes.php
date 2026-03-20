<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/labels'], function (Router $router) {
        $router->group(['permission' => 'labels.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Labels\\LabelController', 'index']);
        });
        $router->group(['permission' => 'labels.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Labels\\LabelController', 'store']);
        });
        $router->group(['permission' => 'labels.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Labels\\LabelController', 'update']);
        });
        $router->group(['permission' => 'labels.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Labels\\LabelController', 'destroy']);
        });
    });
};
