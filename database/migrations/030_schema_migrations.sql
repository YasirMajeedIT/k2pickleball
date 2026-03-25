-- Migration 030: Create schema_migrations tracking table
-- Enables the platform Migrations dashboard to track which migrations have run.

CREATE TABLE IF NOT EXISTS `schema_migrations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Migration filename',
    `batch` INT NOT NULL DEFAULT 1,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success','failed') NOT NULL DEFAULT 'success',
    `error_message` TEXT DEFAULT NULL,
    INDEX `idx_sm_batch` (`batch`),
    INDEX `idx_sm_executed` (`executed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
