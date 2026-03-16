-- Migration 007: Create sessions (session details), pricing rules, rolling prices tables
-- Also add session_id to session_types

-- Sessions table (session details / named groupings)
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `category_id` BIGINT UNSIGNED NULL,
    `session_name` VARCHAR(255) NOT NULL,
    `session_tagline` VARCHAR(500) NULL,
    `description` TEXT NULL,
    `picture` VARCHAR(500) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sessions_org` (`organization_id`),
    INDEX `idx_sessions_category` (`category_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add session_id to session_types
ALTER TABLE `session_types`
    ADD COLUMN `session_id` BIGINT UNSIGNED NULL AFTER `category_id`,
    ADD INDEX `idx_session_types_session` (`session_id`),
    ADD CONSTRAINT `fk_st_session` FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE SET NULL;

-- Pricing rules table (time-based and user-based pricing)
CREATE TABLE IF NOT EXISTS `st_pricing_rules` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `pricing_type` ENUM('time_based', 'user_based') NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `start_offset_days` INT UNSIGNED NULL COMMENT 'Days before event (time_based)',
    `max_users` INT UNSIGNED NULL COMMENT 'User threshold (user_based)',
    `priority` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pricing_session_type` (`session_type_id`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rolling prices table (for series_rolling type)
CREATE TABLE IF NOT EXISTS `st_rolling_prices` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `number_of_weeks` INT UNSIGNED NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    INDEX `idx_rolling_session_type` (`session_type_id`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add pricing_mode to session_types
ALTER TABLE `session_types`
    ADD COLUMN `pricing_mode` ENUM('single', 'time_based', 'user_based') NOT NULL DEFAULT 'single' AFTER `standard_price`;
