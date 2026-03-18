-- Migration 021: Email logs table, hot deal registration threshold, facility SMTP columns

-- ─── EMAIL LOGS TABLE ───
CREATE TABLE IF NOT EXISTS `email_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NULL,
    `recipient_email` VARCHAR(255) NOT NULL,
    `recipient_name` VARCHAR(200) NULL,
    `player_id` BIGINT UNSIGNED NULL,
    `subject` VARCHAR(500) NOT NULL,
    `body_html` LONGTEXT NULL,
    `email_type` VARCHAR(100) NOT NULL COMMENT 'booking_confirmation, cancellation, refund, credit_issued, check_in, general',
    `entity_type` VARCHAR(100) NULL COMMENT 'class_attendee, payment, etc.',
    `entity_id` BIGINT UNSIGNED NULL,
    `status` ENUM('sent','failed','queued') NOT NULL DEFAULT 'sent',
    `error_message` TEXT NULL,
    `sent_by` BIGINT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_el_org` (`organization_id`),
    INDEX `idx_el_facility` (`facility_id`),
    INDEX `idx_el_player` (`player_id`),
    INDEX `idx_el_type` (`email_type`),
    INDEX `idx_el_entity` (`entity_type`, `entity_id`),
    INDEX `idx_el_created` (`created_at`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`sent_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── HOT DEAL: Add registration-based threshold ───
ALTER TABLE `st_hot_deals`
    ADD COLUMN `min_registrations` INT UNSIGNED NULL AFTER `original_price`,
    ADD COLUMN `label` VARCHAR(100) NULL DEFAULT 'Hot Deal' AFTER `min_registrations`;

-- ─── FACILITY SMTP COLUMNS ───
ALTER TABLE `facilities`
    ADD COLUMN `smtp_host` VARCHAR(255) NULL AFTER `settings`,
    ADD COLUMN `smtp_port` INT UNSIGNED NULL AFTER `smtp_host`,
    ADD COLUMN `smtp_username` VARCHAR(255) NULL AFTER `smtp_port`,
    ADD COLUMN `smtp_password` VARCHAR(255) NULL AFTER `smtp_username`,
    ADD COLUMN `smtp_encryption` VARCHAR(10) NULL AFTER `smtp_password`,
    ADD COLUMN `smtp_from_email` VARCHAR(255) NULL AFTER `smtp_encryption`,
    ADD COLUMN `smtp_from_name` VARCHAR(255) NULL AFTER `smtp_from_email`;
