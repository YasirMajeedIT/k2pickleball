<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/discounts'], function (Router $router) {
        // validate-coupon is used during checkout — no permission guard needed
        $router->post('/validate-coupon', ['App\\Modules\\Discounts\\DiscountController', 'validateCoupon']);

        $router->group(['permission' => 'discounts.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Discounts\\DiscountController', 'index']);
            $router->get('/{id}', ['App\\Modules\\Discounts\\DiscountController', 'show']);
        });
        $router->group(['permission' => 'discounts.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Discounts\\DiscountController', 'store']);
        });
        $router->group(['permission' => 'discounts.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\Discounts\\DiscountController', 'update']);
        });
        $router->group(['permission' => 'discounts.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Discounts\\DiscountController', 'destroy']);
        });
    });
};
