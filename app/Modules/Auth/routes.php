<?php

declare(strict_types=1);

use App\Core\Http\Router;

/**
 * Auth module routes.
 */
return function (Router $router): void {
    $router->group(['prefix' => '/api/auth'], function (Router $router) {
        // Public routes
        $router->post('/login', ['App\\Modules\\Auth\\AuthController', 'login']);
        $router->post('/register', ['App\\Modules\\Auth\\AuthController', 'register']);
        $router->post('/refresh', ['App\\Modules\\Auth\\AuthController', 'refresh']);
        $router->post('/forgot-password', ['App\\Modules\\Auth\\AuthController', 'forgotPassword']);
        $router->post('/reset-password', ['App\\Modules\\Auth\\AuthController', 'resetPassword']);

        // Authenticated routes
        $router->post('/logout', ['App\\Modules\\Auth\\AuthController', 'logout']);
        $router->post('/change-password', ['App\\Modules\\Auth\\AuthController', 'changePassword']);
        $router->get('/me', ['App\\Modules\\Auth\\AuthController', 'me']);
    });
};
