-- Migration 037: Add hero_video_url to facilities
-- Allows facilities to store an external video URL for the public-facing hero section.

ALTER TABLE `facilities`
    ADD COLUMN `hero_video_url` VARCHAR(500) DEFAULT NULL AFTER `image_url`;
