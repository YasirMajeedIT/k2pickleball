<?php

declare(strict_types=1);

namespace App\Modules\Users;

use App\Core\Database\Repository;

final class UserRepository extends Repository
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->query()->where('email', $email)->first();
    }

    public function findByUuid(string $uuid): ?array
    {
        return $this->query()->where('uuid', $uuid)->first();
    }

    public function findByOrganization(int $orgId, ?string $search = null, ?string $status = null, int $page = 1, int $perPage = 20): array
    {
        $q = $this->query()->where('organization_id', $orgId);

        if ($search) {
            $q->whereLike('first_name', "%{$search}%");
        }

        if ($status) {
            $q->where('status', $status);
        }

        $q->orderBy('created_at', 'DESC');
        return $q->paginate($page, $perPage);
    }

    public function findWithRoles(int $id): ?array
    {
        $user = $this->findById($id);
        if (!$user) {
            return null;
        }
        unset($user['password_hash']);

        $user['roles'] = $this->db->fetchAll(
            "SELECT `r`.`id`, `r`.`slug`, `r`.`name`, `ur`.`organization_id`
             FROM `user_roles` `ur`
             JOIN `roles` `r` ON `r`.`id` = `ur`.`role_id`
             WHERE `ur`.`user_id` = ?",
            [$id]
        );

        return $user;
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $q = $this->query()->where('email', $email);
        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }
        return $q->exists();
    }

    public function assignRole(int $userId, int $roleId, int $orgId): void
    {
        $exists = $this->db->fetch(
            "SELECT 1 FROM `user_roles` WHERE `user_id` = ? AND `role_id` = ? AND `organization_id` = ?",
            [$userId, $roleId, $orgId]
        );

        if (!$exists) {
            $this->db->insert('user_roles', [
                'user_id' => $userId,
                'role_id' => $roleId,
                'organization_id' => $orgId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function removeRole(int $userId, int $roleId, int $orgId): void
    {
        $this->db->query(
            "DELETE FROM `user_roles` WHERE `user_id` = ? AND `role_id` = ? AND `organization_id` = ?",
            [$userId, $roleId, $orgId]
        );
    }
}
