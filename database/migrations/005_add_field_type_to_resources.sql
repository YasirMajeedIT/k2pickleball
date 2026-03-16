-- ============================================
-- Add field_type column to resources table
-- Determines how resource values are rendered
-- in session type forms (checkbox, selectbox, radio)
-- ============================================

ALTER TABLE `resources`
    ADD COLUMN `field_type` ENUM('checkbox','selectbox','radio') NOT NULL DEFAULT 'checkbox' AFTER `description`;
