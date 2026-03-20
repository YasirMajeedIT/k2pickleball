<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    // Public plan endpoints (no auth/permission required — used by pricing page)
    $router->get('/api/plans', ['App\\Modules\\Subscriptions\\SubscriptionController', 'plans']);
    $router->get('/api/plans/{id}', ['App\\Modules\\Subscriptions\\SubscriptionController', 'showPlan']);

    $router->group(['permission' => 'subscriptions.view'], function (Router $router) {
        $router->get('/api/invoices', ['App\\Modules\\Subscriptions\\SubscriptionController', 'invoices']);
    });
    $router->group(['permission' => 'subscriptions.manage'], function (Router $router) {
        $router->post('/api/plans', ['App\\Modules\\Subscriptions\\SubscriptionController', 'storePlan']);
        $router->put('/api/plans/{id}', ['App\\Modules\\Subscriptions\\SubscriptionController', 'updatePlan']);
        $router->delete('/api/plans/{id}', ['App\\Modules\\Subscriptions\\SubscriptionController', 'destroyPlan']);
    });

    $router->group(['prefix' => '/api/subscriptions'], function (Router $router) {
        $router->group(['permission' => 'subscriptions.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Subscriptions\\SubscriptionController', 'index']);
            $router->get('/current', ['App\\Modules\\Subscriptions\\SubscriptionController', 'current']);
            $router->get('/{id}', ['App\\Modules\\Subscriptions\\SubscriptionController', 'show']);
        });
        $router->group(['permission' => 'subscriptions.manage'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Subscriptions\\SubscriptionController', 'subscribe']);
            $router->post('/create-for-org', ['App\\Modules\\Subscriptions\\SubscriptionController', 'subscribeForOrg']);
            $router->post('/{id}/cancel', ['App\\Modules\\Subscriptions\\SubscriptionController', 'cancel']);
            $router->put('/{id}/change-plan', ['App\\Modules\\Subscriptions\\SubscriptionController', 'changePlan']);
            $router->post('/{id}/reactivate', ['App\\Modules\\Subscriptions\\SubscriptionController', 'reactivate']);
        });
    });
};
