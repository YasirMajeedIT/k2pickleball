-- Migration 010: Remove special_event_spotlight, is_taxable, is_slots_show from session_types
-- These fields are not required for session type management

ALTER TABLE `session_types` DROP COLUMN `special_event_spotlight`;
ALTER TABLE `session_types` DROP COLUMN `is_taxable`;
ALTER TABLE `session_types` DROP COLUMN `is_slots_show`;
