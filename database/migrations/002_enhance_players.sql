-- ============================================
-- Enhance Players Table
-- Adds address, marketing, waiver fields
-- ============================================

ALTER TABLE `players`
    ADD COLUMN `password_hash` VARCHAR(255) DEFAULT NULL AFTER `phone`,
    ADD COLUMN `dupr_id` VARCHAR(50) DEFAULT NULL AFTER `dupr_rating`,
    ADD COLUMN `address` VARCHAR(255) DEFAULT NULL AFTER `avatar_url`,
    ADD COLUMN `city` VARCHAR(100) DEFAULT NULL AFTER `address`,
    ADD COLUMN `state` VARCHAR(50) DEFAULT NULL AFTER `city`,
    ADD COLUMN `zip_code` VARCHAR(20) DEFAULT NULL AFTER `state`,
    ADD COLUMN `is_waiver` TINYINT(1) NOT NULL DEFAULT 0 AFTER `zip_code`,
    ADD COLUMN `is_teen` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_waiver`,
    ADD COLUMN `is_email_marketing` TINYINT(1) NOT NULL DEFAULT 1 AFTER `is_teen`,
    ADD COLUMN `is_sms_marketing` TINYINT(1) NOT NULL DEFAULT 1 AFTER `is_email_marketing`,
    ADD COLUMN `date_joined` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `is_sms_marketing`;
