-- ============================================
-- Enhance Facilities Table
-- Adds tagline and tax_rate columns
-- SMTP settings stored in existing settings JSON
-- Logo handled via Files module
-- ============================================

ALTER TABLE `facilities`
    ADD COLUMN `tagline` VARCHAR(255) DEFAULT NULL AFTER `name`,
    ADD COLUMN `tax_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER `status`;
