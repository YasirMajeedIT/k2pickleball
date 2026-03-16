<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/labels'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Labels\\LabelController', 'index']);
        $router->post('/', ['App\\Modules\\Labels\\LabelController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Labels\\LabelController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\Labels\\LabelController', 'destroy']);
    });
};
