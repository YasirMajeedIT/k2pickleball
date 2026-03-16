-- ============================================
-- Create Session Types table and pivot table
-- For scheduling session types with resource values
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Session Types
CREATE TABLE IF NOT EXISTS `session_types` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED DEFAULT NULL,
    `category_id` BIGINT UNSIGNED DEFAULT NULL,

    -- Basic Info
    `title` VARCHAR(255) NOT NULL,
    `additional_tagline` TEXT DEFAULT NULL,
    `session_type` ENUM('class','series','series_rolling') NOT NULL DEFAULT 'class',

    -- Capacity & Duration
    `capacity` INT UNSIGNED DEFAULT NULL,
    `number_of_courts_used` INT UNSIGNED DEFAULT NULL,
    `duration` INT UNSIGNED DEFAULT NULL COMMENT 'Duration in minutes',

    -- Pricing
    `standard_price` DECIMAL(10,2) DEFAULT NULL,

    -- Schedule
    `start_date` DATE DEFAULT NULL,
    `start_time` VARCHAR(20) DEFAULT NULL,
    `number_of_weeks` INT UNSIGNED DEFAULT NULL,
    `weeks_skipped` TINYINT(1) NOT NULL DEFAULT 0,

    -- Access & Visibility
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `private` TINYINT(1) NOT NULL DEFAULT 0,
    `special_event_spotlight` TINYINT(1) NOT NULL DEFAULT 0,

    -- League Settings
    `league_type` ENUM('individual','partner') DEFAULT NULL,
    `is_partner_required` TINYINT(1) NOT NULL DEFAULT 0,
    `is_mixed_double` TINYINT(1) NOT NULL DEFAULT 0,

    -- Misc
    `is_taxable` TINYINT(1) NOT NULL DEFAULT 0,
    `scheduling_url` VARCHAR(500) DEFAULT NULL,
    `is_slots_show` TINYINT(1) NOT NULL DEFAULT 1,

    -- Timestamps
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_st_org` (`organization_id`),
    INDEX `idx_st_facility` (`facility_id`),
    INDEX `idx_st_category` (`category_id`),
    INDEX `idx_st_active` (`is_active`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pivot: Session Type <-> Resource Values
CREATE TABLE IF NOT EXISTS `session_type_resource_values` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `resource_value_id` BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY `uk_strv` (`session_type_id`, `resource_value_id`),
    INDEX `idx_strv_st` (`session_type_id`),
    INDEX `idx_strv_rv` (`resource_value_id`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`resource_value_id`) REFERENCES `resource_values`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
