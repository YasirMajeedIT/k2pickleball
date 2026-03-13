<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/settings'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Settings\\SettingsController', 'index']);
        $router->get('/{group}', ['App\\Modules\\Settings\\SettingsController', 'group']);
        $router->put('/{group}', ['App\\Modules\\Settings\\SettingsController', 'update']);
        $router->delete('/{group}/{key}', ['App\\Modules\\Settings\\SettingsController', 'destroy']);
    });
};
