<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/credit-codes'], function (Router $router) {
        // recordUsage is called during booking payment flow — no permission guard
        $router->post('/{id}/usages', ['App\\Modules\\CreditCodes\\CreditCodeController', 'recordUsage']);

        $router->group(['permission' => 'credit_codes.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\CreditCodes\\CreditCodeController', 'index']);
            $router->get('/{id}', ['App\\Modules\\CreditCodes\\CreditCodeController', 'show']);
        });
        $router->group(['permission' => 'credit_codes.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\CreditCodes\\CreditCodeController', 'store']);
        });
        $router->group(['permission' => 'credit_codes.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\CreditCodes\\CreditCodeController', 'update']);
        });
        $router->group(['permission' => 'credit_codes.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\CreditCodes\\CreditCodeController', 'destroy']);
        });
    });
};
