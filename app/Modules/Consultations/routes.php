<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\Consultations\ConsultationController;

return function (Router $router): void {
    // Public endpoint — no auth required (listed in AuthMiddleware PUBLIC_ROUTES)
    $router->post('/api/consultations', [ConsultationController::class, 'submit']);

    // Platform admin endpoints
    $router->get('/api/platform/consultations', [ConsultationController::class, 'index']);
    $router->get('/api/platform/consultations/stats', [ConsultationController::class, 'stats']);
    $router->get('/api/platform/consultations/{id}', [ConsultationController::class, 'show']);
    $router->patch('/api/platform/consultations/{id}/status', [ConsultationController::class, 'updateStatus']);
    $router->delete('/api/platform/consultations/{id}', [ConsultationController::class, 'destroy']);
};
