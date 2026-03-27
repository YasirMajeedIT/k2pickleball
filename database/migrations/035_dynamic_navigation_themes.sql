-- Migration 035: Dynamic Navigation, Organization Themes, Category View Settings
-- Enables: dynamic nav menus, per-org theming, category page view configuration,
--          public membership page support

-- =============================================
-- 1. Navigation Items (dynamic menu system)
-- =============================================
CREATE TABLE IF NOT EXISTS `navigation_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `parent_id` BIGINT UNSIGNED DEFAULT NULL,
    `label` VARCHAR(100) NOT NULL,
    `url` VARCHAR(500) DEFAULT NULL,
    `type` ENUM('link','page','category','dropdown','separator') NOT NULL DEFAULT 'link',
    `target` ENUM('_self','_blank') NOT NULL DEFAULT '_self',
    `icon` VARCHAR(100) DEFAULT NULL,
    `category_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Links to category for type=category',
    `is_system` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'System nav items cannot be deleted',
    `system_key` VARCHAR(50) DEFAULT NULL COMMENT 'home, schedule, book-court, about, contact',
    `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `visibility_rule` VARCHAR(50) DEFAULT NULL COMMENT 'always, auth_only, guest_only, has_memberships',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_nav_org` (`organization_id`),
    INDEX `idx_nav_parent` (`parent_id`),
    INDEX `idx_nav_sort` (`sort_order`),
    INDEX `idx_nav_visible` (`is_visible`),
    UNIQUE KEY `uk_nav_system` (`organization_id`, `system_key`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`parent_id`) REFERENCES `navigation_items`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. Category View Settings
-- =============================================
CREATE TABLE IF NOT EXISTS `category_view_settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `default_view` ENUM('week','month','today','list') NOT NULL DEFAULT 'week',
    `enabled_views` JSON NOT NULL DEFAULT '["week","month","today","list"]',
    `show_filters` TINYINT(1) NOT NULL DEFAULT 1,
    `show_category_filter` TINYINT(1) NOT NULL DEFAULT 0,
    `page_title` VARCHAR(200) DEFAULT NULL,
    `page_description` TEXT DEFAULT NULL,
    `page_hero_image` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_cvs_cat` (`category_id`),
    INDEX `idx_cvs_org` (`organization_id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. Seed default theme settings for existing orgs
--    (uses existing `settings` table with group_name='theme')
-- =============================================
-- Theme settings use the existing settings table pattern:
--   group_name = 'theme'
--   key_name = primary_color, accent_color, font_family, nav_style, footer_style, etc.
-- This avoids a new table and reuses the proven settings infrastructure.

-- =============================================
-- 4. Add slug column to categories for URL-friendly routing
-- =============================================
ALTER TABLE `categories` ADD COLUMN `slug` VARCHAR(150) DEFAULT NULL AFTER `name`;
ALTER TABLE `categories` ADD UNIQUE KEY `uk_cat_slug_org` (`organization_id`, `slug`);

-- Backfill slugs for existing categories
UPDATE `categories` SET `slug` = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(`name`, ' ', '-'), '&', 'and'), '/', '-'), '.', '')) WHERE `slug` IS NULL;

-- =============================================
-- 5. Record migration
-- =============================================
INSERT INTO `schema_migrations` (`migration`, `batch`, `executed_at`, `status`)
VALUES ('035_dynamic_navigation_themes', 35, NOW(), 'success')
ON DUPLICATE KEY UPDATE `executed_at` = NOW();
