<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    // Membership Plans API
    $router->group(['prefix' => '/api/membership-plans'], function (Router $router) {
        $router->group(['permission' => 'memberships.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Memberships\\MembershipPlanController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Memberships\\MembershipPlanController', 'show']);
        });
        $router->group(['permission' => 'memberships.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Memberships\\MembershipPlanController', 'store']);
        });
        $router->group(['permission' => 'memberships.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Memberships\\MembershipPlanController', 'update']);
            $router->patch('/{id}/toggle-status', ['App\\Modules\\Memberships\\MembershipPlanController', 'toggleStatus']);
            $router->post('/reorder', ['App\\Modules\\Memberships\\MembershipPlanController', 'reorder']);
        });
        $router->group(['permission' => 'memberships.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Memberships\\MembershipPlanController', 'destroy']);
        });
    });

    // Player Memberships API
    $router->group(['prefix' => '/api/memberships'], function (Router $router) {
        $router->group(['permission' => 'memberships.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Memberships\\MembershipPlanController', 'memberships']);
            $router->get('/{id}', ['App\\Modules\\Memberships\\MembershipPlanController', 'showMembership']);
        });
        $router->group(['permission' => 'memberships.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Memberships\\MembershipPlanController', 'assignMembership']);
        });
        $router->group(['permission' => 'memberships.update'], function (Router $router) {
            $router->patch('/{id}/cancel', ['App\\Modules\\Memberships\\MembershipPlanController', 'cancelMembership']);
        });
    });
};
