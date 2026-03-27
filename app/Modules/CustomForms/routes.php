<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {

    /* ── Admin API (auth + permission required) ── */
    $router->group(['prefix' => '/api/custom-forms'], function (Router $router) {
        $router->group(['permission' => 'settings.view'], function (Router $router) {
            $router->get('/',                                ['App\\Modules\\CustomForms\\CustomFormController', 'index']);
            $router->get('/{id}',                            ['App\\Modules\\CustomForms\\CustomFormController', 'show']);
            $router->get('/{id}/submissions',                ['App\\Modules\\CustomForms\\CustomFormController', 'submissions']);
            $router->get('/{id}/submissions/{subId}',        ['App\\Modules\\CustomForms\\CustomFormController', 'submissionDetail']);
        });
        $router->group(['permission' => 'settings.update'], function (Router $router) {
            $router->post('/',                               ['App\\Modules\\CustomForms\\CustomFormController', 'store']);
            $router->put('/{id}',                            ['App\\Modules\\CustomForms\\CustomFormController', 'update']);
            $router->delete('/{id}',                         ['App\\Modules\\CustomForms\\CustomFormController', 'destroy']);
            $router->patch('/{id}/submissions/{subId}/status',['App\\Modules\\CustomForms\\CustomFormController', 'updateSubmissionStatus']);
            $router->delete('/{id}/submissions/{subId}',     ['App\\Modules\\CustomForms\\CustomFormController', 'deleteSubmission']);
        });
    });

    /* ── Public API (no auth — for form rendering + submission) ── */
    $router->group(['prefix' => '/api/public/forms'], function (Router $router) {
        $router->get('/{slug}',        ['App\\Modules\\CustomForms\\CustomFormController', 'publicShow']);
        $router->post('/{slug}/submit',['App\\Modules\\CustomForms\\CustomFormController', 'submit']);
    });
};
