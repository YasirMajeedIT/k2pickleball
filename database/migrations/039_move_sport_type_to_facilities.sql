-- Migration 039: Move sport_type from courts to facilities, remove hourly_rate from courts
-- Add sport_type and facility_type to facilities table

ALTER TABLE `facilities`
    ADD COLUMN `sport_type` VARCHAR(100) NOT NULL DEFAULT 'pickleball' AFTER `description`,
    ADD COLUMN `custom_sport_type` VARCHAR(100) NULL AFTER `sport_type`,
    ADD COLUMN `facility_type` VARCHAR(100) NOT NULL DEFAULT 'sports_facility' AFTER `custom_sport_type`;

ALTER TABLE `courts`
    DROP COLUMN `sport_type`,
    DROP COLUMN `hourly_rate`;
