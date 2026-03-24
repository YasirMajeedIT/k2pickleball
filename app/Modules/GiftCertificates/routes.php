<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/gift-certificates'], function (Router $router) {
        // recordUsage is called during booking payment flow — no permission guard
        $router->post('/{id}/usages', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'recordUsage']);

        $router->group(['permission' => 'gift_certificates.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'index']);
            $router->get('/{id}', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'show']);
        });
        $router->group(['permission' => 'gift_certificates.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'store']);
        });
        $router->group(['permission' => 'gift_certificates.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'update']);
        });
        $router->group(['permission' => 'gift_certificates.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\GiftCertificates\\GiftCertificateController', 'destroy']);
        });
    });
};
