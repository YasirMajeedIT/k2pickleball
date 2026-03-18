-- Migration 022: Rolling enrollment support + Extension facility settings
-- 1. booking_groups table for tracking rolling session package enrollments
-- 2. booking_group_id on st_class_attendees to link rolling attendees
-- 3. facility_extension_settings for per-facility extension configuration
-- 4. Seed Square Terminal POS extension record

-- =====================================================================
-- 1. Booking groups for rolling session enrollment
-- =====================================================================
CREATE TABLE IF NOT EXISTS `booking_groups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` CHAR(36) NOT NULL,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED NULL,
    `first_class_id` BIGINT UNSIGNED NOT NULL COMMENT 'The class where enrollment starts',
    `rolling_weeks` INT UNSIGNED NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `per_session_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(30) NOT NULL DEFAULT 'manual',
    `payment_id` BIGINT UNSIGNED NULL,
    `square_payment_id` VARCHAR(100) NULL,
    `status` ENUM('active','partially_cancelled','fully_cancelled') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_bg_uuid` (`uuid`),
    INDEX `idx_bg_org` (`organization_id`),
    INDEX `idx_bg_session_type` (`session_type_id`),
    INDEX `idx_bg_player` (`player_id`),
    INDEX `idx_bg_first_class` (`first_class_id`),
    INDEX `idx_bg_status` (`status`),
    CONSTRAINT `fk_bg_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bg_session_type` FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bg_player` FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_bg_first_class` FOREIGN KEY (`first_class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 2. Link st_class_attendees to booking_groups
-- =====================================================================
ALTER TABLE `st_class_attendees`
    ADD COLUMN `booking_group_id` BIGINT UNSIGNED NULL AFTER `notes`,
    ADD INDEX `idx_sca_booking_group` (`booking_group_id`),
    ADD CONSTRAINT `fk_sca_booking_group` FOREIGN KEY (`booking_group_id`) REFERENCES `booking_groups`(`id`) ON DELETE SET NULL;

-- =====================================================================
-- 3. Per-facility extension settings
-- =====================================================================
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

-- =====================================================================
-- 4. Seed Square Terminal POS extension (if not already present)
-- =====================================================================
INSERT INTO `extensions` (`name`, `slug`, `description`, `version`, `category`, `icon`, `price_monthly`, `price_yearly`, `is_active`, `settings_schema`, `sort_order`, `created_at`, `updated_at`)
SELECT 'Square Terminal POS', 'square-terminal-pos',
       'Process payments using Square Terminal hardware devices. Supports device pairing, terminal checkouts, and per-facility terminal configuration.',
       '1.0.0', 'payments', 'credit-card', 0.00, 0.00, 1,
       '{"type":"object","properties":{"device_id":{"type":"string","title":"Terminal Device ID","description":"The paired Square Terminal device ID"},"device_name":{"type":"string","title":"Terminal Name","description":"Friendly name for this terminal"}}}',
       10, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `extensions` WHERE `slug` = 'square-terminal-pos');
