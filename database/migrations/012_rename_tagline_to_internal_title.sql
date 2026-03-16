-- Migration 012: Rename additional_tagline to internal_title in session_types
ALTER TABLE `session_types` CHANGE COLUMN `additional_tagline` `internal_title` VARCHAR(255) DEFAULT NULL;