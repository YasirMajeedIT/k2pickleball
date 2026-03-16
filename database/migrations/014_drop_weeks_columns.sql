-- Migration 014: Drop number_of_weeks and weeks_skipped from session_types
-- These columns are not used; rolling prices use st_rolling_prices table instead.

ALTER TABLE `session_types`
    DROP COLUMN `number_of_weeks`,
    DROP COLUMN `weeks_skipped`;
