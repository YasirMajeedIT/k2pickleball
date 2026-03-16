<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/discounts'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Discounts\\DiscountController', 'index']);
        $router->get('/{id}', ['App\\Modules\\Discounts\\DiscountController', 'show']);
        $router->post('/', ['App\\Modules\\Discounts\\DiscountController', 'store']);
        $router->put('/{id}', ['App\\Modules\\Discounts\\DiscountController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\Discounts\\DiscountController', 'destroy']);
        $router->post('/validate-coupon', ['App\\Modules\\Discounts\\DiscountController', 'validateCoupon']);
    });
};
