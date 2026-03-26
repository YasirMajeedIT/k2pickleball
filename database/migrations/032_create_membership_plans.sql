-- Migration 032: Membership Plans for Facilities
-- Allows facility admins to create membership plans that players can subscribe to
-- Plans can include/discount specific categories and session types

-- =============================================
-- 1. Membership Plans (the plan definitions)
-- =============================================
CREATE TABLE IF NOT EXISTS `membership_plans` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `duration_type` ENUM('monthly','3months','6months','12months','custom') NOT NULL DEFAULT 'monthly',
    `duration_value` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Duration in months',
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `setup_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `renewal_type` ENUM('auto','manual','none') NOT NULL DEFAULT 'auto',
    `is_taxable` TINYINT(1) NOT NULL DEFAULT 0,
    `color` VARCHAR(7) DEFAULT '#6366f1',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `max_members` INT UNSIGNED DEFAULT NULL COMMENT 'NULL = unlimited',
    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_mp_org` (`organization_id`),
    INDEX `idx_mp_facility` (`facility_id`),
    INDEX `idx_mp_active` (`is_active`),
    INDEX `idx_mp_sort` (`sort_order`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. Membership Plan → Category benefits
-- =============================================
CREATE TABLE IF NOT EXISTS `membership_plan_categories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `membership_plan_id` BIGINT UNSIGNED NOT NULL,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `benefit_type` ENUM('included','discounted') NOT NULL DEFAULT 'included',
    `price` DECIMAL(10,2) DEFAULT NULL COMMENT 'Override price for included categories',
    `discount_percentage` DECIMAL(5,2) DEFAULT NULL COMMENT 'Discount % for discounted categories',
    `usage_limit` INT UNSIGNED DEFAULT NULL COMMENT 'NULL = unlimited',
    `usage_period` ENUM('day','week','monthly','unlimited') NOT NULL DEFAULT 'unlimited',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_mpc` (`membership_plan_id`, `category_id`),
    FOREIGN KEY (`membership_plan_id`) REFERENCES `membership_plans`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. Membership Plan → Session Type benefits
-- =============================================
CREATE TABLE IF NOT EXISTS `membership_plan_session_types` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `membership_plan_id` BIGINT UNSIGNED NOT NULL,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `benefit_type` ENUM('included','discounted') NOT NULL DEFAULT 'included',
    `price` DECIMAL(10,2) DEFAULT NULL COMMENT 'Override price for included session types',
    `discount_percentage` DECIMAL(5,2) DEFAULT NULL COMMENT 'Discount % for discounted session types',
    `usage_limit` INT UNSIGNED DEFAULT NULL COMMENT 'NULL = unlimited',
    `usage_period` ENUM('day','week','monthly','unlimited') NOT NULL DEFAULT 'unlimited',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_mpst` (`membership_plan_id`, `session_type_id`),
    FOREIGN KEY (`membership_plan_id`) REFERENCES `membership_plans`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. Player Memberships (active subscriptions)
-- =============================================
CREATE TABLE IF NOT EXISTS `player_memberships` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `membership_plan_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('active','expired','cancelled','suspended','pending') NOT NULL DEFAULT 'pending',
    `start_date` DATE NOT NULL,
    `end_date` DATE DEFAULT NULL,
    `renewed_at` DATETIME DEFAULT NULL,
    `cancelled_at` DATETIME DEFAULT NULL,
    `cancel_reason` TEXT DEFAULT NULL,
    `amount_paid` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_reference` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pm_org` (`organization_id`),
    INDEX `idx_pm_plan` (`membership_plan_id`),
    INDEX `idx_pm_player` (`player_id`),
    INDEX `idx_pm_facility` (`facility_id`),
    INDEX `idx_pm_status` (`status`),
    INDEX `idx_pm_dates` (`start_date`, `end_date`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`membership_plan_id`) REFERENCES `membership_plans`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
