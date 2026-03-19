-- Migration 025: Add system category support
-- Adds is_system, system_slug, is_active, description columns to categories
-- Creates mandatory "Book a Court" category as a system base category

-- Add new columns
ALTER TABLE `categories`
    ADD COLUMN `is_system` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_taxable`,
    ADD COLUMN `system_slug` VARCHAR(50) DEFAULT NULL AFTER `is_system`,
    ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `system_slug`,
    ADD COLUMN `description` TEXT DEFAULT NULL AFTER `is_active`,
    ADD COLUMN `image_url` VARCHAR(500) DEFAULT NULL AFTER `description`,
    ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Unique constraint: only one system_slug per org
ALTER TABLE `categories`
    ADD UNIQUE KEY `uk_cat_system_slug_org` (`organization_id`, `system_slug`);
