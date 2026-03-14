<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\Client\ClientController;

/**
 * Client-facing website routes.
 * Marketing pages, auth, and customer portal.
 */
return function (Router $router): void {
    // Marketing pages
    $router->get('/', [ClientController::class, 'handleRequest']);
    $router->get('/product', [ClientController::class, 'handleRequest']);
    $router->get('/about', [ClientController::class, 'handleRequest']);
    $router->get('/contact', [ClientController::class, 'handleRequest']);
    $router->get('/demo', [ClientController::class, 'handleRequest']);
    $router->get('/pricing', [ClientController::class, 'handleRequest']);

    // Auth pages
    $router->get('/login', [ClientController::class, 'handleRequest']);
    $router->get('/register', [ClientController::class, 'handleRequest']);
    $router->get('/forgot-password', [ClientController::class, 'handleRequest']);
    $router->get('/reset-password', [ClientController::class, 'handleRequest']);

    // Customer portal (auth checked client-side via JS)
    $router->get('/portal', [ClientController::class, 'handleRequest']);
    $router->get('/portal/subscription', [ClientController::class, 'handleRequest']);
    $router->get('/portal/invoices', [ClientController::class, 'handleRequest']);
    $router->get('/portal/settings', [ClientController::class, 'handleRequest']);
};
