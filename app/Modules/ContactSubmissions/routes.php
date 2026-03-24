<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\ContactSubmissions\ContactSubmissionController;

return function (Router $router): void {
    // Public — submit contact form (no auth required)
    $router->post('/api/contact', [ContactSubmissionController::class, 'submit']);

    // Platform admin — manage contact submissions
    $router->get('/api/platform/contact-submissions', [ContactSubmissionController::class, 'index']);
    $router->get('/api/platform/contact-submissions/stats', [ContactSubmissionController::class, 'stats']);
    $router->get('/api/platform/contact-submissions/{id}', [ContactSubmissionController::class, 'show']);
    $router->patch('/api/platform/contact-submissions/{id}/status', [ContactSubmissionController::class, 'updateStatus']);
    $router->delete('/api/platform/contact-submissions/{id}', [ContactSubmissionController::class, 'destroy']);
};
