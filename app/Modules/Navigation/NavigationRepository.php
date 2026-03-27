<?php

declare(strict_types=1);

namespace App\Modules\Navigation;

use App\Core\Database\Repository;

final class NavigationRepository extends Repository
{
    protected string $table = 'navigation_items';

    /**
     * Get all visible navigation items for an organization (including children).
     * Returns flat list ordered by sort_order — caller builds the tree.
     */
    public function findForOrganization(int $orgId): array
    {
        return $this->db->fetchAll(
            "SELECT `id`, `parent_id`, `label`, `url`, `type`, `target`, `icon`,
                    `category_id`, `is_system`, `system_key`, `is_visible`, `sort_order`,
                    `visibility_rule`
             FROM `{$this->table}`
             WHERE `organization_id` = ? AND `is_visible` = 1
             ORDER BY `sort_order` ASC, `label` ASC",
            [$orgId]
        );
    }

    /**
     * Get ALL navigation items for admin editing (including hidden ones).
     */
    public function findAllForOrganization(int $orgId): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM `{$this->table}`
             WHERE `organization_id` = ?
             ORDER BY `sort_order` ASC, `label` ASC",
            [$orgId]
        );
    }

    /**
     * Rebuild navigation: delete non-system items and re-insert.
     * System items are updated in place.
     */
    public function syncNavigation(int $orgId, array $items): void
    {
        // Delete non-system custom items first
        $this->db->query(
            "DELETE FROM `{$this->table}` WHERE `organization_id` = ? AND `is_system` = 0",
            [$orgId]
        );

        $now = date('Y-m-d H:i:s');

        foreach ($items as $item) {
            if (!empty($item['is_system']) && !empty($item['system_key'])) {
                // Update system item (label, visibility, sort_order)
                $this->db->query(
                    "UPDATE `{$this->table}` SET `label` = ?, `is_visible` = ?, `sort_order` = ?, `updated_at` = ?
                     WHERE `organization_id` = ? AND `system_key` = ?",
                    [
                        $item['label'],
                        (int) ($item['is_visible'] ?? 1),
                        (int) ($item['sort_order'] ?? 0),
                        $now,
                        $orgId,
                        $item['system_key'],
                    ]
                );
            } else {
                // Insert custom item
                $this->db->insert($this->table, [
                    'organization_id' => $orgId,
                    'parent_id'       => $item['parent_id'] ?? null,
                    'label'           => $item['label'],
                    'url'             => $item['url'] ?? null,
                    'type'            => $item['type'] ?? 'link',
                    'target'          => $item['target'] ?? '_self',
                    'icon'            => $item['icon'] ?? null,
                    'category_id'     => $item['category_id'] ?? null,
                    'is_system'       => 0,
                    'system_key'      => null,
                    'is_visible'      => (int) ($item['is_visible'] ?? 1),
                    'sort_order'      => (int) ($item['sort_order'] ?? 0),
                    'visibility_rule' => $item['visibility_rule'] ?? null,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
            }
        }
    }

    /**
     * Seed default navigation items for a new organization.
     */
    public function seedDefaults(int $orgId, ?int $courtCategoryId = null): void
    {
        $now = date('Y-m-d H:i:s');
        $defaults = [
            ['label' => 'Home',       'url' => '/',           'type' => 'link',     'system_key' => 'home',        'sort_order' => 10],
            ['label' => 'Schedule',    'url' => '/schedule',   'type' => 'dropdown', 'system_key' => 'schedule',    'sort_order' => 20],
            ['label' => 'Book a Court','url' => '/book-court', 'type' => 'link',     'system_key' => 'book-court',  'sort_order' => 30, 'category_id' => $courtCategoryId],
            ['label' => 'Facilities',  'url' => '/facilities', 'type' => 'link',     'system_key' => 'facilities',  'sort_order' => 40],
            ['label' => 'Memberships', 'url' => '/memberships','type' => 'link',     'system_key' => 'memberships', 'sort_order' => 50, 'visibility_rule' => 'has_memberships'],
            ['label' => 'About',       'url' => '/about',      'type' => 'link',     'system_key' => 'about',       'sort_order' => 60],
            ['label' => 'Contact',     'url' => '/contact',    'type' => 'link',     'system_key' => 'contact',     'sort_order' => 70],
        ];

        foreach ($defaults as $item) {
            $this->db->insert($this->table, [
                'organization_id' => $orgId,
                'label'           => $item['label'],
                'url'             => $item['url'],
                'type'            => $item['type'],
                'is_system'       => 1,
                'system_key'      => $item['system_key'],
                'sort_order'      => $item['sort_order'],
                'category_id'     => $item['category_id'] ?? null,
                'visibility_rule' => $item['visibility_rule'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }
    }

    /**
     * Check if org has active membership plans (for `has_memberships` visibility rule).
     */
    public function orgHasMemberships(int $orgId): bool
    {
        $row = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `membership_plans` WHERE `organization_id` = ? AND `is_active` = 1",
            [$orgId]
        );
        return ((int) ($row['cnt'] ?? 0)) > 0;
    }
}
