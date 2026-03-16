<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/gift-certificates'], function (Router $router) {
        $router->get('/', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'index']);
        $router->get('/{id}', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'show']);
        $router->post('/', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'store']);
        $router->put('/{id}', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'update']);
        $router->delete('/{id}', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'destroy']);
        $router->post('/{id}/usages', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'recordUsage']);
    });
};
