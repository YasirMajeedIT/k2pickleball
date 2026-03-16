<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/credit-codes'], function (Router $router) {
        $router->get('/', ['App\\Modules\\CreditCodes\\CreditCodeController', 'index']);
        $router->get('/{id}', ['App\\Modules\\CreditCodes\\CreditCodeController', 'show']);
        $router->post('/', ['App\\Modules\\CreditCodes\\CreditCodeController', 'store']);
        $router->put('/{id}', ['App\\Modules\\CreditCodes\\CreditCodeController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\CreditCodes\\CreditCodeController', 'destroy']);
        $router->post('/{id}/usages', ['App\\Modules\\CreditCodes\\CreditCodeController', 'recordUsage']);
    });
};
