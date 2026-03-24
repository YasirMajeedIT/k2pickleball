<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/payments'], function (Router $router) {
        $router->group(['permission' => 'payments.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Payments\\PaymentController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Payments\\PaymentController', 'show']);
        });
        $router->group(['permission' => 'payments.process'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Payments\\PaymentController', 'charge']);
        });
        $router->group(['permission' => 'payments.refund'], function (Router $router) {
            $router->post('/{id}/refund', ['App\\Modules\\Payments\\PaymentController', 'refund']);
        });
    });

    // Payment Methods
    $router->group(['permission' => 'payments.view'], function (Router $router) {
        $router->get('/api/payment-methods', ['App\\Modules\\Payments\\PaymentController', 'paymentMethods']);
        $router->get('/api/transactions', ['App\\Modules\\Payments\\PaymentController', 'transactions']);
    });
    $router->group(['permission' => 'payments.process'], function (Router $router) {
        $router->post('/api/payment-methods', ['App\\Modules\\Payments\\PaymentController', 'storePaymentMethod']);
    });
    $router->group(['permission' => 'payments.refund'], function (Router $router) {
        $router->delete('/api/payment-methods/{id}', ['App\\Modules\\Payments\\PaymentController', 'deletePaymentMethod']);
    });

    // Square Webhook (no auth/permission — external webhook endpoint)
    $router->post('/api/webhooks/square', ['App\\Modules\\Payments\\WebhookController', 'handle']);
};
