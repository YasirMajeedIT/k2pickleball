-- ============================================
-- Add 'input' field type to resources
-- Remove number_of_courts_used from session_types and st_classes
-- ============================================

-- Add 'input' to field_type enum
ALTER TABLE `resources`
    MODIFY COLUMN `field_type` ENUM('checkbox','selectbox','radio','input') NOT NULL DEFAULT 'checkbox';

-- Remove number_of_courts_used from session_types
ALTER TABLE `session_types`
    DROP COLUMN `number_of_courts_used`;

-- Remove number_of_courts_used from st_classes
ALTER TABLE `st_classes`
    DROP COLUMN `number_of_courts_used`;

-- Create table for input-type resource values on session types
CREATE TABLE IF NOT EXISTS `session_type_resource_inputs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `resource_id` BIGINT UNSIGNED NOT NULL,
    `value` TEXT DEFAULT NULL,
    UNIQUE KEY `uk_stri` (`session_type_id`, `resource_id`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
