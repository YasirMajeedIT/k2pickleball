-- Migration 013: Remove unused columns from session_types and create session_type_settings table
-- Date: 2026-03-15

-- Remove unused columns from session_types
ALTER TABLE `session_types`
    DROP COLUMN `league_type`,
    DROP COLUMN `is_partner_required`,
    DROP COLUMN `is_mixed_double`,
    DROP COLUMN `start_date`,
    DROP COLUMN `start_time`;

-- Create session_type_settings table for extensible key-value settings
CREATE TABLE IF NOT EXISTS `session_type_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_st_setting` (`session_type_id`, `setting_key`),
    CONSTRAINT `fk_sts_session_type` FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Record migration
INSERT INTO `migrations` (`migration`, `batch`) VALUES ('013_remove_columns_add_settings', 13);
