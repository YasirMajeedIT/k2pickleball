-- Migration 016: Create waivers table
-- Only one waiver may be active per organization at a time.

CREATE TABLE IF NOT EXISTS `waivers` (
    `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid`            CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `title`           VARCHAR(255) NOT NULL,
    `content`         LONGTEXT NOT NULL,
    `version`         VARCHAR(50) NOT NULL DEFAULT '1.0',
    `is_active`       TINYINT(1) NOT NULL DEFAULT 0,
    `effective_date`  DATETIME DEFAULT NULL,
    `expiry_date`     DATETIME DEFAULT NULL,
    `created_by`      BIGINT UNSIGNED DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_waivers_org`    (`organization_id`),
    INDEX `idx_waivers_active` (`organization_id`, `is_active`),

    CONSTRAINT `fk_waivers_org`
        FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

