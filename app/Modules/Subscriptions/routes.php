<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    // Plans (public-ish)
    $router->get('/api/plans', ['App\\Modules\\Subscriptions\\SubscriptionController', 'plans']);
    $router->get('/api/plans/{id}', ['App\\Modules\\Subscriptions\\SubscriptionController', 'showPlan']);
    $router->post('/api/plans', ['App\\Modules\\Subscriptions\\SubscriptionController', 'storePlan']);
    $router->put('/api/plans/{id}', ['App\\Modules\\Subscriptions\\SubscriptionController', 'updatePlan']);
    $router->delete('/api/plans/{id}', ['App\\Modules\\Subscriptions\\SubscriptionController', 'destroyPlan']);

    $router->group(['prefix' => '/api/subscriptions'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Subscriptions\\SubscriptionController', 'index']);
        $router->get('/current', ['App\\Modules\\Subscriptions\\SubscriptionController', 'current']);
        $router->get('/{id}', ['App\\Modules\\Subscriptions\\SubscriptionController', 'show']);
        $router->post('/', ['App\\Modules\\Subscriptions\\SubscriptionController', 'subscribe']);
        $router->post('/{id}/cancel', ['App\\Modules\\Subscriptions\\SubscriptionController', 'cancel']);
    });

    $router->get('/api/invoices', ['App\\Modules\\Subscriptions\\SubscriptionController', 'invoices']);
};
