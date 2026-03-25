-- Migration 025: Add system category support
-- Adds is_system, system_slug, is_active, description columns to categories
-- Creates mandatory "Book a Court" category as a system base category

-- Add new columns (each statement is separate so an already-existing column
-- only fails that one statement and doesn't block the others)
ALTER TABLE `categories` ADD COLUMN `is_system` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_taxable`;
ALTER TABLE `categories` ADD COLUMN `system_slug` VARCHAR(50) DEFAULT NULL AFTER `is_system`;
ALTER TABLE `categories` ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `system_slug`;
ALTER TABLE `categories` ADD COLUMN `description` TEXT DEFAULT NULL AFTER `is_active`;
ALTER TABLE `categories` ADD COLUMN `image_url` VARCHAR(500) DEFAULT NULL AFTER `description`;

-- Unique constraint: only one system_slug per org
ALTER TABLE `categories`
    ADD UNIQUE KEY `uk_cat_system_slug_org` (`organization_id`, `system_slug`);
