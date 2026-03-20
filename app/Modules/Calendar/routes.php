<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/calendar', 'permission' => 'schedule.view'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Calendar\\CalendarController', 'index']);
        $router->get('/categories', ['App\\Modules\\Calendar\\CalendarController', 'categories']);
    });
};
