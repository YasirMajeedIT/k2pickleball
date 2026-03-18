<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/square-terminal'], function (Router $router) {
        $router->get('/status', ['App\\Modules\\Extensions\\SquareTerminal\\SquareTerminalController', 'status']);
        $router->get('/devices', ['App\\Modules\\Extensions\\SquareTerminal\\SquareTerminalController', 'listDevices']);
        $router->post('/devices/pair', ['App\\Modules\\Extensions\\SquareTerminal\\SquareTerminalController', 'pairDevice']);
        $router->post('/checkout', ['App\\Modules\\Extensions\\SquareTerminal\\SquareTerminalController', 'createCheckout']);
        $router->get('/checkout/{checkoutId}', ['App\\Modules\\Extensions\\SquareTerminal\\SquareTerminalController', 'checkoutStatus']);
        $router->post('/checkout/{checkoutId}/cancel', ['App\\Modules\\Extensions\\SquareTerminal\\SquareTerminalController', 'cancelCheckout']);
    });
};
