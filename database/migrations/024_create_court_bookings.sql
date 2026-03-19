-- ============================================
-- Migration 024: Create court_bookings table
-- Standalone court reservations (not class-based)
-- ============================================

CREATE TABLE IF NOT EXISTS `court_bookings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `court_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED DEFAULT NULL,
    `booking_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `duration_minutes` INT UNSIGNED NOT NULL DEFAULT 60,
    `num_players` INT UNSIGNED NOT NULL DEFAULT 1,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(30) DEFAULT NULL,
    `payment_id` VARCHAR(100) DEFAULT NULL,
    `payment_status` ENUM('pending','paid','refunded','failed') NOT NULL DEFAULT 'pending',
    `status` ENUM('confirmed','cancelled','completed','no_show') NOT NULL DEFAULT 'confirmed',
    `notes` TEXT DEFAULT NULL,
    `cancelled_at` DATETIME DEFAULT NULL,
    `cancelled_reason` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cb_org` (`organization_id`),
    INDEX `idx_cb_facility` (`facility_id`),
    INDEX `idx_cb_court` (`court_id`),
    INDEX `idx_cb_date` (`booking_date`),
    INDEX `idx_cb_court_date` (`court_id`, `booking_date`),
    INDEX `idx_cb_player` (`player_id`),
    INDEX `idx_cb_status` (`status`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
