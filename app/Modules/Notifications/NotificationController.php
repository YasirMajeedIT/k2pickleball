<?php

declare(strict_types=1);

namespace App\Modules\Notifications;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class NotificationController extends Controller
{
    private NotificationRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new NotificationRepository($db);
    }

    public function index(Request $request): Response
    {
        $userId = $request->userId();
        [$page, $perPage] = $this->pagination($request);

        $read = $request->input('read');
        $readOnly = null;
        if ($read === '1' || $read === 'true') {
            $readOnly = true;
        } elseif ($read === '0' || $read === 'false') {
            $readOnly = false;
        }

        $result = $this->repo->findByUser($userId, $readOnly, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $notification = $this->repo->findById($id);
        if (!$notification) {
            throw new NotFoundException('Notification not found');
        }
        return $this->success($notification);
    }

    public function unreadCount(Request $request): Response
    {
        $count = $this->repo->unreadCount($request->userId());
        return $this->success(['unread_count' => $count]);
    }

    public function markAsRead(Request $request, int $id): Response
    {
        $notification = $this->repo->findById($id);
        if (!$notification) {
            throw new NotFoundException('Notification not found');
        }

        $this->repo->markAsRead($id);
        return $this->success(null, 'Notification marked as read');
    }

    public function markAllAsRead(Request $request): Response
    {
        $this->repo->markAllAsRead($request->userId());
        return $this->success(null, 'All notifications marked as read');
    }

    public function destroy(Request $request, int $id): Response
    {
        $notification = $this->repo->findById($id);
        if (!$notification) {
            throw new NotFoundException('Notification not found');
        }

        $this->repo->delete($id);
        return $this->success(null, 'Notification deleted');
    }
}
