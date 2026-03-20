<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['prefix' => '/api/session-types'], function (Router $router) {

        // ─── Booking-flow routes (no permission guard — any authenticated user) ──
        $router->post('/{id}/classes/{classId}/book', ['App\\Modules\\SessionTypes\\BookingController', 'book']);
        $router->post('/{id}/classes/{classId}/calculate-price', ['App\\Modules\\SessionTypes\\BookingController', 'calculatePrice']);
        $router->post('/{id}/classes/{classId}/validate-credit-code', ['App\\Modules\\SessionTypes\\BookingController', 'validateCreditCode']);
        $router->post('/{id}/classes/{classId}/validate-gift-code', ['App\\Modules\\SessionTypes\\BookingController', 'validateGiftCode']);
        $router->post('/{id}/classes/{classId}/attendees/{attendeeId}/cancel', ['App\\Modules\\SessionTypes\\BookingController', 'cancel']);

        // ─── View routes ────────────────────────────────────────────────────────
        $router->group(['permission' => 'session_types.view'], function (Router $router) {
            $router->get('/', ['App\\Modules\\SessionTypes\\SessionTypeController', 'index']);
            $router->get('/resources', ['App\\Modules\\SessionTypes\\SessionTypeController', 'resources']);
            $router->get('/booking-groups', ['App\\Modules\\SessionTypes\\BookingController', 'listBookingGroups']);
            $router->get('/{id}', ['App\\Modules\\SessionTypes\\SessionTypeController', 'show']);
            $router->get('/{id}/classes', ['App\\Modules\\SessionTypes\\ClassController', 'index']);
            $router->get('/{id}/form-fields', ['App\\Modules\\SessionTypes\\FormFieldController', 'index']);
            $router->get('/{id}/classes/{classId}/notes', ['App\\Modules\\SessionTypes\\ClassNotesController', 'index']);
            $router->get('/{id}/classes/{classId}/courts', ['App\\Modules\\SessionTypes\\ClassCourtsController', 'index']);
            $router->get('/{id}/classes/{classId}/attendees', ['App\\Modules\\SessionTypes\\ClassAttendeeController', 'index']);
            $router->get('/{id}/classes/{classId}/hot-deal', ['App\\Modules\\SessionTypes\\HotDealController', 'show']);
            $router->get('/{id}/classes/{classId}/early-bird', ['App\\Modules\\SessionTypes\\EarlyBirdController', 'show']);
            $router->get('/{id}/classes/{classId}/feedback', ['App\\Modules\\SessionTypes\\FeedbackController', 'show']);
            $router->get('/{id}/booking-groups/{groupId}', ['App\\Modules\\SessionTypes\\BookingController', 'bookingGroupDetails']);
        });

        // ─── Create routes ───────────────────────────────────────────────────────
        $router->group(['permission' => 'session_types.create'], function (Router $router) {
            $router->post('/', ['App\\Modules\\SessionTypes\\SessionTypeController', 'store']);
            $router->post('/{id}/copy', ['App\\Modules\\SessionTypes\\SessionTypeController', 'copy']);
            $router->post('/{id}/classes', ['App\\Modules\\SessionTypes\\ClassController', 'store']);
            $router->post('/{id}/form-fields', ['App\\Modules\\SessionTypes\\FormFieldController', 'store']);
            $router->post('/{id}/classes/{classId}/notes', ['App\\Modules\\SessionTypes\\ClassNotesController', 'store']);
            $router->post('/{id}/classes/{classId}/attendees', ['App\\Modules\\SessionTypes\\ClassAttendeeController', 'store']);
        });

        // ─── Update routes ───────────────────────────────────────────────────────
        $router->group(['permission' => 'session_types.update'], function (Router $router) {
            $router->put('/{id}', ['App\\Modules\\SessionTypes\\SessionTypeController', 'update']);
            $router->put('/{id}/spots', ['App\\Modules\\SessionTypes\\SessionTypeController', 'updateSpots']);
            $router->put('/{id}/classes/{classId}', ['App\\Modules\\SessionTypes\\ClassController', 'update']);
            $router->post('/{id}/classes/bulk-delete', ['App\\Modules\\SessionTypes\\ClassController', 'bulkDestroy']);
            $router->put('/{id}/form-fields/sync', ['App\\Modules\\SessionTypes\\FormFieldController', 'sync']);
            $router->put('/{id}/form-fields/{fieldId}', ['App\\Modules\\SessionTypes\\FormFieldController', 'update']);
            $router->put('/{id}/classes/{classId}/courts', ['App\\Modules\\SessionTypes\\ClassCourtsController', 'sync']);
            $router->put('/{id}/classes/{classId}/attendees/{attendeeId}', ['App\\Modules\\SessionTypes\\ClassAttendeeController', 'update']);
            // Admin booking management (refund, issue-credit, cancel booking group)
            $router->post('/{id}/classes/{classId}/attendees/{attendeeId}/refund', ['App\\Modules\\SessionTypes\\BookingController', 'refund']);
            $router->post('/{id}/classes/{classId}/attendees/{attendeeId}/issue-credit', ['App\\Modules\\SessionTypes\\BookingController', 'issueCredit']);
            $router->post('/{id}/booking-groups/{groupId}/cancel', ['App\\Modules\\SessionTypes\\BookingController', 'cancelBookingGroup']);
            // Hot deal / early bird / feedback management
            $router->put('/{id}/classes/{classId}/hot-deal', ['App\\Modules\\SessionTypes\\HotDealController', 'upsert']);
            $router->put('/{id}/classes/{classId}/early-bird', ['App\\Modules\\SessionTypes\\EarlyBirdController', 'upsert']);
            $router->put('/{id}/classes/{classId}/feedback', ['App\\Modules\\SessionTypes\\FeedbackController', 'upsert']);
            $router->post('/{id}/classes/{classId}/feedback/send', ['App\\Modules\\SessionTypes\\FeedbackController', 'send']);
        });

        // ─── Delete routes ───────────────────────────────────────────────────────
        $router->group(['permission' => 'session_types.delete'], function (Router $router) {
            $router->delete('/{id}', ['App\\Modules\\SessionTypes\\SessionTypeController', 'destroy']);
            $router->delete('/{id}/classes/{classId}', ['App\\Modules\\SessionTypes\\ClassController', 'destroy']);
            $router->delete('/{id}/form-fields/{fieldId}', ['App\\Modules\\SessionTypes\\FormFieldController', 'destroy']);
            $router->delete('/{id}/classes/{classId}/notes/{noteId}', ['App\\Modules\\SessionTypes\\ClassNotesController', 'destroy']);
            $router->delete('/{id}/classes/{classId}/attendees/{attendeeId}', ['App\\Modules\\SessionTypes\\ClassAttendeeController', 'destroy']);
            $router->delete('/{id}/classes/{classId}/hot-deal', ['App\\Modules\\SessionTypes\\HotDealController', 'destroy']);
            $router->delete('/{id}/classes/{classId}/early-bird', ['App\\Modules\\SessionTypes\\EarlyBirdController', 'destroy']);
        });
    });
};
