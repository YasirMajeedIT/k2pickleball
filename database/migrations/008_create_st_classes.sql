-- ============================================
-- Create st_classes table
-- Scheduled class instances under session types
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Scheduled Classes (individual sessions offered under a session type)
CREATE TABLE IF NOT EXISTS `st_classes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED DEFAULT NULL,

    -- Schedule
    `scheduled_at` DATETIME NOT NULL COMMENT 'The date and time this class occurs',

    -- Capacity (copied from session type at creation, editable per-class)
    `slots` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total capacity',
    `slots_available` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Remaining available spots',

    -- Staff
    `coach_id` BIGINT UNSIGNED DEFAULT NULL,
    `number_of_courts_used` INT UNSIGNED DEFAULT NULL,

    -- Status
    `booking_status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=open, 0=closed',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    -- Timestamps
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_stc_session_type` (`session_type_id`),
    INDEX `idx_stc_facility` (`facility_id`),
    INDEX `idx_stc_scheduled` (`scheduled_at`),
    INDEX `idx_stc_coach` (`coach_id`),
    INDEX `idx_stc_active` (`is_active`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`coach_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
