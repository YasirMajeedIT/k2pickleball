<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/api-tokens'], function (Router $router) {
        $router->get('/', ['App\\Modules\\ApiTokens\\ApiTokenController', 'index']);
        $router->get('/{id}', ['App\\Modules\\ApiTokens\\ApiTokenController', 'show']);
        $router->post('/', ['App\\Modules\\ApiTokens\\ApiTokenController', 'store']);
        $router->post('/{id}/revoke', ['App\\Modules\\ApiTokens\\ApiTokenController', 'revoke']);
        $router->post('/revoke-all', ['App\\Modules\\ApiTokens\\ApiTokenController', 'revokeAll']);
    });
};
