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

        $user['facilities'] = $this->db->fetchAll(
            "SELECT `f`.`id`, `f`.`name`, `f`.`slug`, `f`.`city`, `f`.`state`, `f`.`status`
             FROM `user_facilities` `uf`
             JOIN `facilities` `f` ON `f`.`id` = `uf`.`facility_id`
             WHERE `uf`.`user_id` = ?
             ORDER BY `f`.`name` ASC",
            [$id]
        );

        return $user;
    }

    public function syncFacilities(int $userId, array $facilityIds, int $orgId): void
    {
        $this->db->query(
            "DELETE FROM `user_facilities` WHERE `user_id` = ?",
            [$userId]
        );

        foreach ($facilityIds as $facilityId) {
            $facilityId = (int) $facilityId;
            if ($facilityId > 0) {
                $this->db->insert('user_facilities', [
                    'user_id'         => $userId,
                    'facility_id'     => $facilityId,
                    'organization_id' => $orgId,
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    /**
     * Fetch facilities for multiple users in a single query (for index listings).
     * Returns an array keyed by user_id.
     */
    public function findFacilitiesForUsers(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $rows = $this->db->fetchAll(
            "SELECT `uf`.`user_id`, `f`.`id`, `f`.`name`, `f`.`city`
             FROM `user_facilities` `uf`
             JOIN `facilities` `f` ON `f`.`id` = `uf`.`facility_id`
             WHERE `uf`.`user_id` IN ({$placeholders})
             ORDER BY `f`.`name` ASC",
            $userIds
        );

        $map = [];
        foreach ($rows as $row) {
            $uid = $row['user_id'];
            unset($row['user_id']);
            $map[$uid][] = $row;
        }

        return $map;
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
