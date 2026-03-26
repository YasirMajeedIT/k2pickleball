<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\Invoices\InvoiceController;

return function (Router $router): void {
    $router->group(['prefix' => '/api/booking-invoices'], function (Router $router): void {

        // Read endpoints
        $router->group(['permission' => 'invoices.view'], function (Router $r): void {
            $r->get('/',                               [InvoiceController::class, 'index']);
            $r->get('/session-types',                  [InvoiceController::class, 'sessionTypes']);
            $r->get('/{id}',                           [InvoiceController::class, 'show']);
            $r->get('/{id}/payments',                  [InvoiceController::class, 'getPayments']);
            $r->get('/{id}/session-type/{stId}/classes', [InvoiceController::class, 'classesForSessionType']);
        });

        // Create endpoint
        $router->group(['permission' => 'invoices.create'], function (Router $r): void {
            $r->post('/', [InvoiceController::class, 'store']);
        });

        // Update endpoint
        $router->group(['permission' => 'invoices.update'], function (Router $r): void {
            $r->put('/{id}', [InvoiceController::class, 'update']);
        });

        // Delete endpoint
        $router->group(['permission' => 'invoices.delete'], function (Router $r): void {
            $r->delete('/{id}', [InvoiceController::class, 'destroy']);
        });

        // Send & Pay endpoints
        $router->group(['permission' => 'invoices.pay'], function (Router $r): void {
            $r->post('/{id}/send', [InvoiceController::class, 'sendInvoice']);
            $r->post('/{id}/pay',  [InvoiceController::class, 'processPayment']);
        });
    });
};
