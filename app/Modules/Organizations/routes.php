<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/organizations'], function (Router $router) {
        $router->group(['permission' => 'organizations.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Organizations\\OrganizationController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Organizations\\OrganizationController', 'show']);
            $router->get('/{id}/details', ['App\\Modules\\Organizations\\OrganizationController', 'details']);
        });
        $router->group(['permission' => 'organizations.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Organizations\\OrganizationController', 'store']);
        });
        $router->group(['permission' => 'organizations.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Organizations\\OrganizationController', 'update']);
            $router->patch('/{id}/status', ['App\\Modules\\Organizations\\OrganizationController', 'updateStatus']);
        });
        $router->group(['permission' => 'organizations.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Organizations\\OrganizationController', 'destroy']);
        });
    });
};
