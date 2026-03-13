<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/notifications'], function (Router $router) {
        $router->get('/', ['App\\Modules\\Notifications\\NotificationController', 'index']);
        $router->get('/unread-count', ['App\\Modules\\Notifications\\NotificationController', 'unreadCount']);
        $router->get('/{id}', ['App\\Modules\\Notifications\\NotificationController', 'show']);
        $router->post('/{id}/read', ['App\\Modules\\Notifications\\NotificationController', 'markAsRead']);
        $router->post('/read-all', ['App\\Modules\\Notifications\\NotificationController', 'markAllAsRead']);
        $router->delete('/{id}', ['App\\Modules\\Notifications\\NotificationController', 'destroy']);
    });
};
