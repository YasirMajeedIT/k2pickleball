-- Migration 028: Create site_settings table
-- Global site settings for maintenance mode, password protection, etc.

CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` TEXT DEFAULT NULL,
    `setting_type` ENUM('string','boolean','integer','json') NOT NULL DEFAULT 'string',
    `description` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_site_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
    ('maintenance_mode', '0', 'boolean', 'When enabled, the client site shows a maintenance page'),
    ('maintenance_message', 'We are currently performing scheduled maintenance. Please check back soon.', 'string', 'Message shown on the maintenance page'),
    ('password_protection_enabled', '0', 'boolean', 'When enabled, visitors must enter a password to access the site'),
    ('site_password', '', 'string', 'Password visitors must enter to access the site when protection is enabled'),
    ('maintenance_allowed_ips', '[]', 'json', 'JSON array of IP addresses that bypass maintenance mode')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
