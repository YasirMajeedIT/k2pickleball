<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/files'], function (Router $router) {
        $router->group(['permission' => 'files.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\Files\\FileController', 'index']);
            $router->get('/usage', ['App\\Modules\\Files\\FileController', 'usage']);
            $router->get('/{id}', ['App\\Modules\\Files\\FileController', 'show']);
        });
        $router->group(['permission' => 'files.upload'], function (Router $router) {
            $router->post('/', ['App\\Modules\\Files\\FileController', 'upload']);
        });
        $router->group(['permission' => 'files.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\Files\\FileController', 'destroy']);
        });
    });
};
