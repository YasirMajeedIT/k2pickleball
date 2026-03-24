-- Migration 027: Create contact_submissions table
-- Stores public contact form submissions

CREATE TABLE IF NOT EXISTS `contact_submissions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` ENUM('partnership','demo','support','press','other') NOT NULL DEFAULT 'other',
    `message` TEXT NOT NULL,
    `status` ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
    `notes` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_contact_uuid` (`uuid`),
    INDEX `idx_contact_status` (`status`),
    INDEX `idx_contact_subject` (`subject`),
    INDEX `idx_contact_email` (`email`),
    INDEX `idx_contact_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
