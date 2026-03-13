<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/facilities'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Facilities\\FacilityController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Facilities\\FacilityController', 'show']);
        $router->post('/', ['App\\Modules\\Facilities\\FacilityController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Facilities\\FacilityController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\Facilities\\FacilityController', 'destroy']);
    });
};
