-- K2 Pickleball: Email verification, Google OAuth, and related schema additions
-- Run this migration to enable email verification and Google OAuth login

-- 1. Add email_verified_at and google_id to users table
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `email_verified_at` DATETIME NULL DEFAULT NULL AFTER `email`,
    ADD COLUMN IF NOT EXISTS `google_id` VARCHAR(255) NULL DEFAULT NULL AFTER `email_verified_at`,
    ADD COLUMN IF NOT EXISTS `avatar_url` VARCHAR(500) NULL DEFAULT NULL AFTER `google_id`;

-- Add index on google_id for fast lookup
ALTER TABLE `users` ADD INDEX IF NOT EXISTS `idx_users_google_id` (`google_id`);

-- 2. Create email_verifications table
CREATE TABLE IF NOT EXISTS `email_verifications` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`      VARCHAR(255) NOT NULL,
    `token_hash` VARCHAR(64)  NOT NULL,
    `expires_at` DATETIME     NOT NULL,
    `used_at`    DATETIME     NULL DEFAULT NULL,
    `created_at` DATETIME     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_email_verifications_email` (`email`),
    KEY `idx_email_verifications_token` (`token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Mark all existing active users as email_verified (they were created before verification was required)
UPDATE `users` SET `email_verified_at` = `created_at` WHERE `email_verified_at` IS NULL AND `status` = 'active';
