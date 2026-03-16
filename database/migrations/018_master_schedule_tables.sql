-- Migration 018: Master Schedule tables
-- Adds tables for class notes, court assignments, attendees, labels, hot deals, early birds, feedback

-- Class notes (per-class internal notes/comments)
CREATE TABLE IF NOT EXISTS `st_class_notes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `note` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_class_notes_class` (`class_id`),
    CONSTRAINT `fk_class_notes_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_class_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Class-court assignments (pivot: which courts are assigned to a class)
CREATE TABLE IF NOT EXISTS `st_class_courts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `court_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_class_court` (`class_id`, `court_id`),
    CONSTRAINT `fk_class_courts_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_class_courts_court` FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Class attendees (registrations/bookings for a class)
CREATE TABLE IF NOT EXISTS `st_class_attendees` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` CHAR(36) NOT NULL,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL DEFAULT '',
    `email` VARCHAR(255) NULL,
    `phone` VARCHAR(30) NULL,
    `partner_id` BIGINT UNSIGNED NULL COMMENT 'paired partner attendee id',
    `status` ENUM('registered','waitlisted','cancelled','no_show') NOT NULL DEFAULT 'registered',
    `amount_paid` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `quote_amount` DECIMAL(10,2) NULL COMMENT 'custom quoted price',
    `notes` TEXT NULL,
    `checked_in` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_attendee_uuid` (`uuid`),
    KEY `idx_attendees_class` (`class_id`),
    KEY `idx_attendees_player` (`player_id`),
    CONSTRAINT `fk_attendees_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_attendees_player` FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_attendees_partner` FOREIGN KEY (`partner_id`) REFERENCES `st_class_attendees`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Labels (reusable tags for organizing attendees)
CREATE TABLE IF NOT EXISTS `labels` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `color` VARCHAR(20) NOT NULL DEFAULT '#6366f1',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_labels_org` (`organization_id`),
    CONSTRAINT `fk_labels_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendee-label pivot
CREATE TABLE IF NOT EXISTS `st_attendee_labels` (
    `attendee_id` BIGINT UNSIGNED NOT NULL,
    `label_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`attendee_id`, `label_id`),
    CONSTRAINT `fk_al_attendee` FOREIGN KEY (`attendee_id`) REFERENCES `st_class_attendees`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_al_label` FOREIGN KEY (`label_id`) REFERENCES `labels`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hot deals (temporary discount on a class)
CREATE TABLE IF NOT EXISTS `st_hot_deals` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `discount_price` DECIMAL(10,2) NOT NULL,
    `original_price` DECIMAL(10,2) NOT NULL,
    `expires_at` DATETIME NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_hot_deal_class` (`class_id`),
    CONSTRAINT `fk_hot_deal_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Early bird pricing (advance discount)
CREATE TABLE IF NOT EXISTS `st_early_birds` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `discount_price` DECIMAL(10,2) NOT NULL,
    `cutoff_hours` INT UNSIGNED NOT NULL DEFAULT 24 COMMENT 'hours before class starts',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_early_bird_class` (`class_id`),
    CONSTRAINT `fk_early_bird_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Feedback requests (sent to attendees after a class)
CREATE TABLE IF NOT EXISTS `st_feedback_requests` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `message` TEXT NULL,
    `sent_at` DATETIME NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_feedback_req_class` (`class_id`),
    CONSTRAINT `fk_feedback_req_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Feedback responses (from attendees)
CREATE TABLE IF NOT EXISTS `st_feedback_responses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `feedback_request_id` BIGINT UNSIGNED NOT NULL,
    `attendee_id` BIGINT UNSIGNED NOT NULL,
    `rating` TINYINT UNSIGNED NULL COMMENT '1-5 stars',
    `comment` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_fb_resp_request` (`feedback_request_id`),
    CONSTRAINT `fk_fb_resp_request` FOREIGN KEY (`feedback_request_id`) REFERENCES `st_feedback_requests`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fb_resp_attendee` FOREIGN KEY (`attendee_id`) REFERENCES `st_class_attendees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
