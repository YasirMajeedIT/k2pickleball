-- Migration 015: Add image_url to facilities, create discount_rules tables

ALTER TABLE `facilities` ADD COLUMN `image_url` VARCHAR(500) DEFAULT NULL AFTER `tax_rate`;

-- Discount Rules for session types
CREATE TABLE IF NOT EXISTS `st_discount_rules` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `discount_category` VARCHAR(100) DEFAULT NULL,
    `coupon_code` VARCHAR(100) DEFAULT NULL,
    `discount_type` ENUM('fixed','percent') NOT NULL DEFAULT 'fixed',
    `discount_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `valid_from` DATE DEFAULT NULL,
    `valid_to` DATE DEFAULT NULL,
    `usage_limit` INT UNSIGNED DEFAULT NULL,
    `used_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_dr_org` (`organization_id`),
    INDEX `idx_dr_facility` (`facility_id`),
    INDEX `idx_dr_coupon` (`coupon_code`),
    INDEX `idx_dr_active` (`is_active`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pivot: which session types a discount applies to
CREATE TABLE IF NOT EXISTS `st_discount_session_types` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `discount_rule_id` BIGINT UNSIGNED NOT NULL,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_discount_st` (`discount_rule_id`, `session_type_id`),
    FOREIGN KEY (`discount_rule_id`) REFERENCES `st_discount_rules`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
