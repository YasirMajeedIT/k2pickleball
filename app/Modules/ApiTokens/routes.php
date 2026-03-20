<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/api-tokens'], function (Router $router) {
        $router->group(['permission' => 'api_tokens.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\ApiTokens\\ApiTokenController', 'index']);
            $router->get('/{id}', ['App\\Modules\\ApiTokens\\ApiTokenController', 'show']);
        });
        $router->group(['permission' => 'api_tokens.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\ApiTokens\\ApiTokenController', 'store']);
        });
        $router->group(['permission' => 'api_tokens.revoke'], function (Router $router) {
            $router->post('/{id}/revoke', ['App\\Modules\\ApiTokens\\ApiTokenController', 'revoke']);
            $router->post('/revoke-all', ['App\\Modules\\ApiTokens\\ApiTokenController', 'revokeAll']);
        });
    });
};
