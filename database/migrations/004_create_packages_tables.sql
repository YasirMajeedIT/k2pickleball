-- ============================================
-- Create Packages & Discounts Tables
-- Credit Codes and Gift Certificates
-- Both facility-scoped
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Credit Codes
CREATE TABLE IF NOT EXISTS `credit_codes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `type` ENUM('credit','infacility') NOT NULL DEFAULT 'credit',
    `category` ENUM('system','admin') NOT NULL DEFAULT 'admin',
    `reason` VARCHAR(100) DEFAULT NULL,
    `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `issued_to` BIGINT UNSIGNED DEFAULT NULL,
    `issued_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_after_days` INT UNSIGNED DEFAULT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `applied_to` JSON DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_cc_code_facility` (`facility_id`, `code`),
    INDEX `idx_cc_org` (`organization_id`),
    INDEX `idx_cc_facility` (`facility_id`),
    INDEX `idx_cc_issued_to` (`issued_to`),
    INDEX `idx_cc_active` (`active`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`issued_to`) REFERENCES `players`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Credit Code Usages
CREATE TABLE IF NOT EXISTS `credit_code_usages` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `credit_code_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED DEFAULT NULL,
    `amount_used` DECIMAL(10,2) NOT NULL,
    `usage_type` ENUM('SESSION','MANUAL_PURCHASE','ADMIN_ADJUSTMENT','REFUND') NOT NULL DEFAULT 'MANUAL_PURCHASE',
    `notes` TEXT DEFAULT NULL,
    `used_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ccu_code` (`credit_code_id`),
    INDEX `idx_ccu_player` (`player_id`),
    FOREIGN KEY (`credit_code_id`) REFERENCES `credit_codes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gift Certificates
CREATE TABLE IF NOT EXISTS `gift_certificates` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `certificate_name` VARCHAR(200) DEFAULT NULL,
    `value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `original_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `status` ENUM('active','redeemed','expired') NOT NULL DEFAULT 'active',
    `buyer_first_name` VARCHAR(100) DEFAULT NULL,
    `buyer_last_name` VARCHAR(100) DEFAULT NULL,
    `buyer_email` VARCHAR(255) DEFAULT NULL,
    `buyer_phone` VARCHAR(30) DEFAULT NULL,
    `recipient_first_name` VARCHAR(100) DEFAULT NULL,
    `recipient_last_name` VARCHAR(100) DEFAULT NULL,
    `recipient_email` VARCHAR(255) DEFAULT NULL,
    `recipient_phone` VARCHAR(30) DEFAULT NULL,
    `gift_message` TEXT DEFAULT NULL,
    `start_using_after` DATE DEFAULT NULL,
    `expired_at` DATETIME DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_gc_code_facility` (`facility_id`, `code`),
    INDEX `idx_gc_org` (`organization_id`),
    INDEX `idx_gc_facility` (`facility_id`),
    INDEX `idx_gc_status` (`status`),
    INDEX `idx_gc_buyer_email` (`buyer_email`),
    INDEX `idx_gc_recipient_email` (`recipient_email`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gift Certificate Usage
CREATE TABLE IF NOT EXISTS `gift_certificate_usage` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `gift_certificate_id` BIGINT UNSIGNED NOT NULL,
    `usage_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `amount_used` DECIMAL(10,2) NOT NULL,
    `remaining_balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `reference_id` VARCHAR(100) DEFAULT NULL,
    `reference_type` VARCHAR(50) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `used_by` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_gcu_cert` (`gift_certificate_id`),
    FOREIGN KEY (`gift_certificate_id`) REFERENCES `gift_certificates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
