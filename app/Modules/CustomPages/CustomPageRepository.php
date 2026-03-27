<?php

declare(strict_types=1);

namespace App\Modules\CustomPages;

use App\Core\Database\Repository;

final class CustomPageRepository extends Repository
{
    protected string $table = 'custom_pages';

    public function findAllForOrg(int $orgId): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, u.`name` AS created_by_name
             FROM `{$this->table}` p
             LEFT JOIN `users` u ON u.`id` = p.`created_by`
             WHERE p.`organization_id` = ?
             ORDER BY p.`sort_order` ASC, p.`title` ASC",
            [$orgId]
        );
    }

    public function findById(int $orgId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `id` = ? AND `organization_id` = ?",
            [$id, $orgId]
        );
    }

    public function findBySlug(int $orgId, string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `slug` = ? AND `organization_id` = ?",
            [$slug, $orgId]
        );
    }

    public function findPublished(int $orgId, string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `slug` = ? AND `organization_id` = ? AND `status` = 'published'",
            [$slug, $orgId]
        );
    }

    public function createPage(int $orgId, array $data): int
    {
        return $this->db->insert($this->table, [
            'organization_id' => $orgId,
            'title'           => $data['title'],
            'slug'            => $data['slug'],
            'content'         => $data['content'] ?? null,
            'meta_description'=> $data['meta_description'] ?? null,
            'status'          => $data['status'] ?? 'draft',
            'show_in_nav'     => (int) ($data['show_in_nav'] ?? 0),
            'show_in_footer'  => (int) ($data['show_in_footer'] ?? 0),
            'sort_order'      => (int) ($data['sort_order'] ?? 0),
            'created_by'      => $data['created_by'] ?? null,
        ]);
    }

    public function updatePage(int $orgId, int $id, array $data): void
    {
        $fields = [];
        $params = [];

        $allowed = ['title', 'slug', 'content', 'meta_description', 'status',
                     'show_in_nav', 'show_in_footer', 'sort_order'];

        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $fields[] = "`{$key}` = ?";
                $val = $data[$key];
                if (in_array($key, ['show_in_nav', 'show_in_footer', 'sort_order'])) $val = (int) $val;
                $params[] = $val;
            }
        }

        if (empty($fields)) return;

        $params[] = $id;
        $params[] = $orgId;
        $this->db->query(
            "UPDATE `{$this->table}` SET " . implode(', ', $fields) . " WHERE `id` = ? AND `organization_id` = ?",
            $params
        );
    }

    public function deletePage(int $orgId, int $id): void
    {
        $this->db->query(
            "DELETE FROM `{$this->table}` WHERE `id` = ? AND `organization_id` = ?",
            [$id, $orgId]
        );
    }

    /** Get pages flagged for nav display */
    public function getNavPages(int $orgId): array
    {
        return $this->db->fetchAll(
            "SELECT `id`, `title`, `slug` FROM `{$this->table}`
             WHERE `organization_id` = ? AND `status` = 'published' AND `show_in_nav` = 1
             ORDER BY `sort_order` ASC, `title` ASC",
            [$orgId]
        );
    }

    /** Get pages flagged for footer display */
    public function getFooterPages(int $orgId): array
    {
        return $this->db->fetchAll(
            "SELECT `id`, `title`, `slug` FROM `{$this->table}`
             WHERE `organization_id` = ? AND `status` = 'published' AND `show_in_footer` = 1
             ORDER BY `sort_order` ASC, `title` ASC",
            [$orgId]
        );
    }
}
