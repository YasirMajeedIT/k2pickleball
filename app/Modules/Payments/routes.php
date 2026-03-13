<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/payments'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Payments\\PaymentController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Payments\\PaymentController', 'show']);
        $router->post('/', ['App\\Modules\\Payments\\PaymentController', 'charge']);
        $router->post('/{id}/refund', ['App\\Modules\\Payments\\PaymentController', 'refund']);
    });

    // Payment Methods
    $router->get('/api/payment-methods', ['App\\Modules\\Payments\\PaymentController', 'paymentMethods']);
    $router->post('/api/payment-methods', ['App\\Modules\\Payments\\PaymentController', 'storePaymentMethod']);
    $router->delete('/api/payment-methods/{id}', ['App\\Modules\\Payments\\PaymentController', 'deletePaymentMethod']);

    // Transactions
    $router->get('/api/transactions', ['App\\Modules\\Payments\\PaymentController', 'transactions']);

    // Square Webhook
    $router->post('/api/webhooks/square', ['App\\Modules\\Payments\\WebhookController', 'handle']);
};
