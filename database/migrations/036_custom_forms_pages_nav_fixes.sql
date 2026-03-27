-- Migration 036: Custom Forms, Custom Pages & Navigation Fixes
-- Adds custom form builder, static pages, and fixes nav structure

-- ═══════════════════════════════════════════════════════════════
-- 1. CUSTOM PAGES — Static content pages managed from admin
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS `custom_pages` (
    `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `title`           VARCHAR(255) NOT NULL,
    `slug`            VARCHAR(255) NOT NULL,
    `content`         LONGTEXT DEFAULT NULL,
    `meta_description` VARCHAR(500) DEFAULT NULL,
    `status`          ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    `show_in_nav`     TINYINT(1) NOT NULL DEFAULT 0,
    `show_in_footer`  TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order`      INT NOT NULL DEFAULT 0,
    `created_by`      BIGINT UNSIGNED DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_page_slug` (`organization_id`, `slug`),
    INDEX `idx_page_org` (`organization_id`),
    INDEX `idx_page_status` (`organization_id`, `status`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════════
-- 2. CUSTOM FORMS — Dynamic form builder
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS `custom_forms` (
    `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `title`           VARCHAR(255) NOT NULL,
    `slug`            VARCHAR(255) NOT NULL,
    `description`     TEXT DEFAULT NULL,
    `status`          ENUM('draft','active','closed','archived') NOT NULL DEFAULT 'draft',
    `success_message` TEXT DEFAULT NULL,
    `redirect_url`    VARCHAR(500) DEFAULT NULL,
    `requires_auth`   TINYINT(1) NOT NULL DEFAULT 0,
    `max_submissions` INT UNSIGNED DEFAULT NULL,
    `closes_at`       DATETIME DEFAULT NULL,
    `show_in_nav`     TINYINT(1) NOT NULL DEFAULT 0,
    `created_by`      BIGINT UNSIGNED DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_form_slug` (`organization_id`, `slug`),
    INDEX `idx_form_org` (`organization_id`),
    INDEX `idx_form_status` (`organization_id`, `status`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `custom_form_fields` (
    `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `form_id`         BIGINT UNSIGNED NOT NULL,
    `label`           VARCHAR(255) NOT NULL,
    `name`            VARCHAR(100) NOT NULL,
    `type`            ENUM('text','textarea','email','phone','number','date','select','radio','checkbox','file','heading','paragraph','hidden') NOT NULL DEFAULT 'text',
    `placeholder`     VARCHAR(255) DEFAULT NULL,
    `help_text`       VARCHAR(500) DEFAULT NULL,
    `is_required`     TINYINT(1) NOT NULL DEFAULT 0,
    `options`         JSON DEFAULT NULL COMMENT 'For select/radio/checkbox: [{label, value}]',
    `validation`      JSON DEFAULT NULL COMMENT '{"min","max","pattern","file_types","max_size_kb"}',
    `sort_order`      INT NOT NULL DEFAULT 0,
    `width`           ENUM('full','half') NOT NULL DEFAULT 'full',
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ff_form` (`form_id`),
    FOREIGN KEY (`form_id`) REFERENCES `custom_forms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `custom_form_submissions` (
    `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `form_id`         BIGINT UNSIGNED NOT NULL,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `player_id`       BIGINT UNSIGNED DEFAULT NULL,
    `ip_address`      VARCHAR(45) DEFAULT NULL,
    `user_agent`      VARCHAR(500) DEFAULT NULL,
    `status`          ENUM('new','reviewed','archived') NOT NULL DEFAULT 'new',
    `submitted_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_fs_form` (`form_id`),
    INDEX `idx_fs_org` (`organization_id`),
    INDEX `idx_fs_status` (`form_id`, `status`),
    FOREIGN KEY (`form_id`) REFERENCES `custom_forms`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `custom_form_submission_data` (
    `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `submission_id`   BIGINT UNSIGNED NOT NULL,
    `field_id`        BIGINT UNSIGNED NOT NULL,
    `field_name`      VARCHAR(100) NOT NULL,
    `value`           TEXT DEFAULT NULL,
    INDEX `idx_fsd_sub` (`submission_id`),
    FOREIGN KEY (`submission_id`) REFERENCES `custom_form_submissions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`field_id`) REFERENCES `custom_form_fields`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════════
-- 3. NAVIGATION FIXES
--    - Move "Book a Court" under Schedule as a category child
--    - Hide "Facilities" from nav by default
-- ═══════════════════════════════════════════════════════════════

-- Make Book A Court a child of Schedule (set parent_id to the Schedule item)
UPDATE `navigation_items` AS bc
    JOIN `navigation_items` AS sched
        ON sched.`organization_id` = bc.`organization_id`
       AND sched.`system_key` = 'schedule'
SET bc.`parent_id` = sched.`id`,
    bc.`sort_order` = 5,
    bc.`type` = 'link'
WHERE bc.`system_key` = 'book-court';

-- Hide Facilities from nav (still accessible via direct URL and footer)
UPDATE `navigation_items` SET `is_visible` = 0 WHERE `system_key` = 'facilities';

-- ═══════════════════════════════════════════════════════════════
-- 4. Record migration
-- ═══════════════════════════════════════════════════════════════
INSERT INTO `migrations` (`migration_name`, `applied_at`)
VALUES ('036_custom_forms_pages_nav_fixes', NOW());
