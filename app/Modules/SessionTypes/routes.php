<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/session-types'], function (Router $router) {
        $router->get('/', ['App\\Modules\\SessionTypes\\SessionTypeController', 'index']);
        $router->get('/resources', ['App\\Modules\\SessionTypes\\SessionTypeController', 'resources']);
        $router->get('/{id}', ['App\\Modules\\SessionTypes\\SessionTypeController', 'show']);
        $router->post('/', ['App\\Modules\\SessionTypes\\SessionTypeController', 'store']);
        $router->put('/{id}', ['App\\Modules\\SessionTypes\\SessionTypeController', 'update']);
        $router->put('/{id}/spots', ['App\\Modules\\SessionTypes\\SessionTypeController', 'updateSpots']);
        $router->delete('/{id}', ['App\\Modules\\SessionTypes\\SessionTypeController', 'destroy']);
        $router->post('/{id}/copy', ['App\\Modules\\SessionTypes\\SessionTypeController', 'copy']);

        // Classes (scheduled instances under a session type)
        $router->get('/{id}/classes', ['App\\Modules\\SessionTypes\\ClassController', 'index']);
        $router->post('/{id}/classes', ['App\\Modules\\SessionTypes\\ClassController', 'store']);
        $router->put('/{id}/classes/{classId}', ['App\\Modules\\SessionTypes\\ClassController', 'update']);
        $router->delete('/{id}/classes/{classId}', ['App\\Modules\\SessionTypes\\ClassController', 'destroy']);
        $router->post('/{id}/classes/bulk-delete', ['App\\Modules\\SessionTypes\\ClassController', 'bulkDestroy']);

        // Form fields (custom registration fields for a session type)
        $router->get('/{id}/form-fields', ['App\\Modules\\SessionTypes\\FormFieldController', 'index']);
        $router->post('/{id}/form-fields', ['App\\Modules\\SessionTypes\\FormFieldController', 'store']);
        $router->put('/{id}/form-fields/sync', ['App\\Modules\\SessionTypes\\FormFieldController', 'sync']);
        $router->put('/{id}/form-fields/{fieldId}', ['App\\Modules\\SessionTypes\\FormFieldController', 'update']);
        $router->delete('/{id}/form-fields/{fieldId}', ['App\\Modules\\SessionTypes\\FormFieldController', 'destroy']);

        // Class notes
        $router->get('/{id}/classes/{classId}/notes', ['App\\Modules\\SessionTypes\\ClassNotesController', 'index']);
        $router->post('/{id}/classes/{classId}/notes', ['App\\Modules\\SessionTypes\\ClassNotesController', 'store']);
        $router->delete('/{id}/classes/{classId}/notes/{noteId}', ['App\\Modules\\SessionTypes\\ClassNotesController', 'destroy']);

        // Class court assignments
        $router->get('/{id}/classes/{classId}/courts', ['App\\Modules\\SessionTypes\\ClassCourtsController', 'index']);
        $router->put('/{id}/classes/{classId}/courts', ['App\\Modules\\SessionTypes\\ClassCourtsController', 'sync']);

        // Class attendees
        $router->get('/{id}/classes/{classId}/attendees', ['App\\Modules\\SessionTypes\\ClassAttendeeController', 'index']);
        $router->post('/{id}/classes/{classId}/attendees', ['App\\Modules\\SessionTypes\\ClassAttendeeController', 'store']);
        $router->put('/{id}/classes/{classId}/attendees/{attendeeId}', ['App\\Modules\\SessionTypes\\ClassAttendeeController', 'update']);
        $router->delete('/{id}/classes/{classId}/attendees/{attendeeId}', ['App\\Modules\\SessionTypes\\ClassAttendeeController', 'destroy']);

        // Hot deals
        $router->get('/{id}/classes/{classId}/hot-deal', ['App\\Modules\\SessionTypes\\HotDealController', 'show']);
        $router->put('/{id}/classes/{classId}/hot-deal', ['App\\Modules\\SessionTypes\\HotDealController', 'upsert']);
        $router->delete('/{id}/classes/{classId}/hot-deal', ['App\\Modules\\SessionTypes\\HotDealController', 'destroy']);

        // Early bird
        $router->get('/{id}/classes/{classId}/early-bird', ['App\\Modules\\SessionTypes\\EarlyBirdController', 'show']);
        $router->put('/{id}/classes/{classId}/early-bird', ['App\\Modules\\SessionTypes\\EarlyBirdController', 'upsert']);
        $router->delete('/{id}/classes/{classId}/early-bird', ['App\\Modules\\SessionTypes\\EarlyBirdController', 'destroy']);

        // Feedback
        $router->get('/{id}/classes/{classId}/feedback', ['App\\Modules\\SessionTypes\\FeedbackController', 'show']);
        $router->put('/{id}/classes/{classId}/feedback', ['App\\Modules\\SessionTypes\\FeedbackController', 'upsert']);
        $router->post('/{id}/classes/{classId}/feedback/send', ['App\\Modules\\SessionTypes\\FeedbackController', 'send']);
    });
};
