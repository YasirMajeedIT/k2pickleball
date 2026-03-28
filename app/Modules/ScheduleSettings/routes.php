<?php

declare(strict_types=1);

use App\Core\Http\Router;
use App\Modules\ScheduleSettings\ScheduleSettingsController;

/**
 * Schedule page settings API routes (admin).
 */
return function (Router $router): void {
    $router->group(['prefix' => '/api/schedule-settings', 'permission' => 'settings.view'], function (Router $router) {
        $router->get('/',        [ScheduleSettingsController::class, 'index']);
        $router->get('/preview', [ScheduleSettingsController::class, 'preview']);
    });
    $router->group(['prefix' => '/api/schedule-settings', 'permission' => 'settings.update'], function (Router $router) {
        $router->put('/', [ScheduleSettingsController::class, 'update']);
    });
};
