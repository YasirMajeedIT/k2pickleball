-- ============================================
-- Create Scheduling Tables
-- Categories and Resources with values
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Categories (organization-scoped)
CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `color` VARCHAR(7) NOT NULL DEFAULT '#6366f1',
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_taxable` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_cat_name_org` (`organization_id`, `name`),
    INDEX `idx_cat_org` (`organization_id`),
    INDEX `idx_cat_sort` (`sort_order`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resources (organization-scoped, e.g., "Skill Levels", "Equipment", etc.)
CREATE TABLE IF NOT EXISTS `resources` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_res_name_org` (`organization_id`, `name`),
    INDEX `idx_res_org` (`organization_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resource Values (e.g., "Beginner", "Intermediate", "Advanced" under "Skill Levels")
CREATE TABLE IF NOT EXISTS `resource_values` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `resource_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_rv_name_res` (`resource_id`, `name`),
    INDEX `idx_rv_resource` (`resource_id`),
    INDEX `idx_rv_sort` (`sort_order`),
    FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
