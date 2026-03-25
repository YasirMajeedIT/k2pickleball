-- Migration 029: Create extensions and announcements tables
-- These tables were in an unnumbered migration file and may not have been applied on VPS.

-- ============================================
-- Extensions System
-- ============================================
CREATE TABLE IF NOT EXISTS `extensions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `version` VARCHAR(20) DEFAULT '1.0.0',
    `category` VARCHAR(100) DEFAULT 'general',
    `icon` VARCHAR(100) DEFAULT NULL,
    `price_monthly` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `price_yearly` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `settings_schema` JSON DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ext_slug` (`slug`),
    INDEX `idx_ext_active` (`is_active`),
    INDEX `idx_ext_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `organization_extensions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `extension_id` BIGINT UNSIGNED NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `settings` JSON DEFAULT NULL,
    `installed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_org_ext` (`organization_id`, `extension_id`),
    INDEX `idx_oe_org` (`organization_id`),
    INDEX `idx_oe_ext` (`extension_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`extension_id`) REFERENCES `extensions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Announcements
-- ============================================
CREATE TABLE IF NOT EXISTS `announcements` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info','warning','critical','maintenance') NOT NULL DEFAULT 'info',
    `target` ENUM('all','specific') NOT NULL DEFAULT 'all',
    `target_org_ids` TEXT DEFAULT NULL,
    `starts_at` DATETIME DEFAULT NULL,
    `ends_at` DATETIME DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ann_active` (`is_active`),
    INDEX `idx_ann_type` (`type`),
    INDEX `idx_ann_dates` (`starts_at`, `ends_at`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Per-facility extension settings
-- ============================================
CREATE TABLE IF NOT EXISTS `facility_extension_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organization_extension_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `settings` JSON NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_fes_org_ext_facility` (`organization_extension_id`, `facility_id`),
    CONSTRAINT `fk_fes_org_ext` FOREIGN KEY (`organization_extension_id`) REFERENCES `organization_extensions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fes_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Seed Extensions
-- ============================================
INSERT IGNORE INTO `extensions` (`name`, `slug`, `description`, `version`, `category`, `price_monthly`, `price_yearly`, `is_active`, `sort_order`) VALUES
('Tournament Manager', 'tournament-manager', 'Organize and manage pickleball tournaments with brackets, seeding, and live scoring.', '1.0.0', 'competitions', 29.99, 299.99, 1, 1),
('League Management', 'league-management', 'Run recurring leagues with standings, schedules, and automated match generation.', '1.0.0', 'competitions', 24.99, 249.99, 1, 2),
('Online Booking', 'online-booking', 'Allow players to book courts online with real-time availability and payments.', '1.0.0', 'booking', 19.99, 199.99, 1, 3),
('Membership Portal', 'membership-portal', 'Self-service membership signup, renewals, and member directory.', '1.0.0', 'membership', 14.99, 149.99, 1, 4),
('Advanced Analytics', 'advanced-analytics', 'Player performance tracking, court utilization analytics, and revenue insights.', '1.0.0', 'analytics', 19.99, 199.99, 1, 5),
('Email Marketing', 'email-marketing', 'Send newsletters, event promotions, and automated email campaigns to members.', '1.0.0', 'marketing', 9.99, 99.99, 1, 6),
('Mobile App', 'mobile-app', 'White-label mobile app for your facility with push notifications.', '1.0.0', 'mobile', 49.99, 499.99, 1, 7),
('Payment Gateway Plus', 'payment-gateway-plus', 'Additional payment methods: ACH, Apple Pay, Google Pay, and installment plans.', '1.0.0', 'payments', 14.99, 149.99, 1, 8),
('Custom Branding', 'custom-branding', 'Custom domain, logo, colors, and white-label the entire admin interface.', '1.0.0', 'customization', 9.99, 99.99, 1, 9),
('API Access', 'api-access', 'REST API access for third-party integrations and custom development.', '1.0.0', 'developer', 24.99, 249.99, 1, 10);

-- Square Terminal POS extension (from migration 022)
INSERT IGNORE INTO `extensions` (`name`, `slug`, `description`, `version`, `category`, `icon`, `price_monthly`, `price_yearly`, `is_active`, `settings_schema`, `sort_order`)
VALUES ('Square Terminal POS', 'square-terminal-pos',
        'Process payments using Square Terminal hardware devices. Supports device pairing, terminal checkouts, and per-facility terminal configuration.',
        '1.0.0', 'payments', 'credit-card', 0.00, 0.00, 1,
        '{"type":"object","properties":{"device_id":{"type":"string","title":"Terminal Device ID","description":"The paired Square Terminal device ID"},"device_name":{"type":"string","title":"Terminal Name","description":"Friendly name for this terminal"}}}',
        10);
