<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/session-details'], function (Router $router) {
        $router->group(['permission' => 'session_details.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\SessionDetails\\SessionDetailController', 'index']);
            $router->get('/by-category', ['App\\Modules\\SessionDetails\\SessionDetailController', 'byCategory']);
            $router->get('/{id}', ['App\\Modules\\SessionDetails\\SessionDetailController', 'show']);
        });
        $router->group(['permission' => 'session_details.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\SessionDetails\\SessionDetailController', 'store']);
        });
        $router->group(['permission' => 'session_details.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\SessionDetails\\SessionDetailController', 'update']);
            $router->post('/{id}/picture', ['App\\Modules\\SessionDetails\\SessionDetailController', 'uploadPicture']);
            $router->delete('/{id}/picture', ['App\\Modules\\SessionDetails\\SessionDetailController', 'removePicture']);
        });
        $router->group(['permission' => 'session_details.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\SessionDetails\\SessionDetailController', 'destroy']);
        });
    });
};
