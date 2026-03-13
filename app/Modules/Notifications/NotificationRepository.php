<?php

declare(strict_types=1);

namespace App\Modules\Notifications;

use App\Core\Database\Repository;

final class NotificationRepository extends Repository
{
    protected string $table = 'notifications';

    public function create(array $data): int
    {
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        // notifications table has no updated_at column
        unset($data['updated_at']);
        return $this->db->insert($this->table, $data);
    }

    public function findByUser(int $userId, ?bool $readOnly = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('user_id', $userId);

        if ($readOnly === true) {
            $q->whereNotNull('read_at');
        } elseif ($readOnly === false) {
            $q->whereNull('read_at');
        }

        $q->orderBy('created_at', 'DESC');
        return $q->paginate($page, $perPage);
    }

    public function unreadCount(int $userId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead(int $id): void
    {
        $this->db->update('notifications', [
            'read_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
    }

    public function markAllAsRead(int $userId): void
    {
        $this->db->query(
            "UPDATE `notifications` SET `read_at` = NOW() WHERE `user_id` = ? AND `read_at` IS NULL",
            [$userId]
        );
    }

    /**
     * Create a notification for a user.
     */
    public function notify(int $userId, int $orgId, string $type, string $title, string $message, ?array $data = null): int
    {
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        return $this->create([
            'uuid' => $uuid,
            'organization_id' => $orgId,
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ? json_encode($data) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
