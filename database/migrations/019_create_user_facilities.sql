-- 019: Create user_facilities pivot table
-- Allows a user to be assigned to one or multiple facilities within an organization.

CREATE TABLE IF NOT EXISTS `user_facilities` (
    `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`         BIGINT UNSIGNED NOT NULL,
    `facility_id`     BIGINT UNSIGNED NOT NULL,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_facility` (`user_id`, `facility_id`),
    INDEX `idx_uf_user`         (`user_id`),
    INDEX `idx_uf_facility`     (`facility_id`),
    INDEX `idx_uf_org`          (`organization_id`),
    FOREIGN KEY (`user_id`)         REFERENCES `users`(`id`)         ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`)     REFERENCES `facilities`(`id`)    ON DELETE CASCADE,
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
