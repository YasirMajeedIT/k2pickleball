-- Migration 011: Create session_form_fields table for custom registration form fields
-- These are dynamic form fields that admins can add to session types,
-- shown to clients during registration.

CREATE TABLE IF NOT EXISTS `session_form_fields` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `field_label` VARCHAR(255) NOT NULL,
    `field_name` VARCHAR(255) NOT NULL,
    `field_type` ENUM('text','number','email','phone','date','textarea','select','checkbox','radio','toggle') NOT NULL DEFAULT 'text',
    `field_options` JSON DEFAULT NULL COMMENT 'Array of options for select/checkbox/radio fields',
    `placeholder` VARCHAR(255) DEFAULT NULL,
    `is_required` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_session_field_name` (`session_type_id`, `field_name`),
    CONSTRAINT `fk_sff_session_type` FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
