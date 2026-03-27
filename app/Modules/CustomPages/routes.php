<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {

    /* ── Admin API (auth + permission required) ── */
    $router->group(['prefix' => '/api/custom-pages'], function (Router $router) {
        $router->group(['permission' => 'settings.view'], function (Router $router) {
            $router->get('/',     ['App\\Modules\\CustomPages\\CustomPageController', 'index']);
            $router->get('/{id}', ['App\\Modules\\CustomPages\\CustomPageController', 'show']);
        });
        $router->group(['permission' => 'settings.update'], function (Router $router) {
            $router->post('/',        ['App\\Modules\\CustomPages\\CustomPageController', 'store']);
            $router->put('/{id}',     ['App\\Modules\\CustomPages\\CustomPageController', 'update']);
            $router->delete('/{id}',  ['App\\Modules\\CustomPages\\CustomPageController', 'destroy']);
        });
    });

    /* ── Public API (no auth) ── */
    $router->group(['prefix' => '/api/public/pages'], function (Router $router) {
        $router->get('/',       ['App\\Modules\\CustomPages\\CustomPageController', 'publicIndex']);
        $router->get('/{slug}', ['App\\Modules\\CustomPages\\CustomPageController', 'publicShow']);
    });
};
