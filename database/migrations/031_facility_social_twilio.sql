-- Migration 031: Add social media URLs and Twilio credentials to facilities
-- Date: 2026-03-26

ALTER TABLE `facilities`
    ADD COLUMN `instagram_url`     VARCHAR(500) NULL AFTER `image_url`,
    ADD COLUMN `facebook_url`      VARCHAR(500) NULL AFTER `instagram_url`,
    ADD COLUMN `youtube_url`       VARCHAR(500) NULL AFTER `facebook_url`,
    ADD COLUMN `twilio_sid`        VARCHAR(255) NULL AFTER `youtube_url`,
    ADD COLUMN `twilio_auth_token` VARCHAR(255) NULL AFTER `twilio_sid`,
    ADD COLUMN `twilio_from_number` VARCHAR(20) NULL AFTER `twilio_auth_token`,
    ADD COLUMN `twilio_enabled`    TINYINT(1) NOT NULL DEFAULT 0 AFTER `twilio_from_number`;
