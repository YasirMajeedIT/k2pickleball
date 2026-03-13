<?php

declare(strict_types=1);

namespace App\Modules\Roles;

use App\Core\Database\Repository;

final class RoleRepository extends Repository
{
    protected string $table = 'roles';
    protected bool $tenantScoped = false;

    public function findBySlug(string $slug, ?int $orgId = null): ?array
    {
        $q = $this->query()->where('slug', $slug);
        if ($orgId) {
            $q->where('organization_id', $orgId);
        } else {
            $q->whereNull('organization_id');
        }
        return $q->first();
    }

    public function findForOrganization(?int $orgId = null): array
    {
        if ($orgId) {
            // Get global + org-specific roles using raw SQL since QueryBuilder doesn't support OR groups
            return $this->db->fetchAll(
                "SELECT * FROM `roles` WHERE `organization_id` IS NULL OR `organization_id` = ? ORDER BY `name` ASC",
                [$orgId]
            );
        }
        return $this->query()->whereNull('organization_id')->orderBy('name', 'ASC')->get();
    }

    public function findWithPermissions(int $id): ?array
    {
        $role = $this->findById($id);
        if (!$role) {
            return null;
        }

        $role['permissions'] = $this->db->fetchAll(
            "SELECT `p`.`id`, `p`.`slug`, `p`.`name`, `p`.`module`
             FROM `role_permissions` `rp`
             JOIN `permissions` `p` ON `p`.`id` = `rp`.`permission_id`
             WHERE `rp`.`role_id` = ?
             ORDER BY `p`.`module`, `p`.`name`",
            [$id]
        );

        return $role;
    }

    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        // Remove existing
        $this->db->query("DELETE FROM `role_permissions` WHERE `role_id` = ?", [$roleId]);

        // Insert new
        foreach ($permissionIds as $permId) {
            $this->db->insert('role_permissions', [
                'role_id' => $roleId,
                'permission_id' => (int) $permId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function getAllPermissions(): array
    {
        return $this->db->fetchAll(
            "SELECT `id`, `slug`, `name`, `module`, `description` FROM `permissions` ORDER BY `module`, `name`"
        );
    }
}
