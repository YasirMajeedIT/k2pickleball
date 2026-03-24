<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/facilities'], function (Router $router) {
        $router->group(['permission' => 'facilities.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Facilities\\FacilityController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Facilities\\FacilityController', 'show']);
        });
        $router->group(['permission' => 'facilities.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Facilities\\FacilityController', 'store']);
        });
        $router->group(['permission' => 'facilities.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Facilities\\FacilityController', 'update']);
        });
        $router->group(['permission' => 'facilities.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Facilities\\FacilityController', 'destroy']);
        });
    });
};
