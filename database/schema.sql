-- ============================================
-- K2 Pickleball SaaS Platform
-- Complete Database Schema (Unified)
-- Includes ALL migrations (001-030 + extras)
-- MySQL 8.0+ / MariaDB 10.6+
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ============================================
-- 1. Organizations
-- ============================================
CREATE TABLE IF NOT EXISTS `organizations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `website` VARCHAR(255) DEFAULT NULL,
    `logo_url` VARCHAR(500) DEFAULT NULL,
    `address_line1` VARCHAR(255) DEFAULT NULL,
    `address_line2` VARCHAR(255) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `state` VARCHAR(50) DEFAULT NULL,
    `zip` VARCHAR(20) DEFAULT NULL,
    `country` VARCHAR(2) DEFAULT 'US',
    `timezone` VARCHAR(50) DEFAULT 'America/New_York',
    `currency` VARCHAR(3) DEFAULT 'USD',
    `square_customer_id` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('active','inactive','suspended','trial','cancelled') NOT NULL DEFAULT 'trial',
    `trial_ends_at` DATETIME DEFAULT NULL,
    `settings` JSON DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_org_slug` (`slug`),
    INDEX `idx_org_status` (`status`),
    INDEX `idx_org_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. Organization Domains
-- ============================================
CREATE TABLE IF NOT EXISTS `organization_domains` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `domain` VARCHAR(255) NOT NULL UNIQUE,
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
    `verified_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_orgdom_org` (`organization_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. Facilities (merged: base + 001 + 015 + 021)
-- ============================================
CREATE TABLE IF NOT EXISTS `facilities` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `tagline` VARCHAR(255) DEFAULT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `address_line1` VARCHAR(255) DEFAULT NULL,
    `address_line2` VARCHAR(255) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `state` VARCHAR(50) DEFAULT NULL,
    `zip` VARCHAR(20) DEFAULT NULL,
    `country` VARCHAR(2) DEFAULT 'US',
    `phone` VARCHAR(30) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `timezone` VARCHAR(50) DEFAULT 'America/New_York',
    `latitude` DECIMAL(10,7) DEFAULT NULL,
    `longitude` DECIMAL(10,7) DEFAULT NULL,
    `status` ENUM('active','inactive','maintenance') NOT NULL DEFAULT 'active',
    `tax_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `image_url` VARCHAR(500) DEFAULT NULL,
    `settings` JSON DEFAULT NULL,
    `smtp_host` VARCHAR(255) DEFAULT NULL,
    `smtp_port` INT UNSIGNED DEFAULT NULL,
    `smtp_username` VARCHAR(255) DEFAULT NULL,
    `smtp_password` VARCHAR(255) DEFAULT NULL,
    `smtp_encryption` VARCHAR(10) DEFAULT NULL,
    `smtp_from_email` VARCHAR(255) DEFAULT NULL,
    `smtp_from_name` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_facility_slug_org` (`organization_id`, `slug`),
    INDEX `idx_fac_org` (`organization_id`),
    INDEX `idx_fac_status` (`status`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. Courts
-- ============================================
CREATE TABLE IF NOT EXISTS `courts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `sport_type` ENUM('pickleball','tennis','badminton','racquetball','squash','general') NOT NULL DEFAULT 'pickleball',
    `surface_type` VARCHAR(50) DEFAULT NULL,
    `is_indoor` TINYINT(1) NOT NULL DEFAULT 0,
    `is_lighted` TINYINT(1) NOT NULL DEFAULT 1,
    `court_number` INT UNSIGNED DEFAULT NULL,
    `hourly_rate` DECIMAL(10,2) DEFAULT 0.00,
    `max_players` INT UNSIGNED DEFAULT 4,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('active','inactive','maintenance','reserved') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_court_org` (`organization_id`),
    INDEX `idx_court_facility` (`facility_id`),
    INDEX `idx_court_sport` (`sport_type`),
    INDEX `idx_court_status` (`status`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. Users (merged: base + google_oauth + 017 drops + 018 rename + profile cols)
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `professional_title` VARCHAR(100) DEFAULT NULL,
    `membership_id` VARCHAR(50) DEFAULT NULL,
    `certification_level` VARCHAR(100) DEFAULT NULL,
    `years_experience` SMALLINT UNSIGNED DEFAULT NULL,
    `emergency_contact_name` VARCHAR(150) DEFAULT NULL,
    `emergency_contact_phone` VARCHAR(30) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,
    `avatar_url` VARCHAR(500) DEFAULT NULL,
    `google_id` VARCHAR(255) DEFAULT NULL,
    `email_verified_at` DATETIME DEFAULT NULL,
    `last_login_at` DATETIME DEFAULT NULL,
    `last_login_ip` VARCHAR(45) DEFAULT NULL,
    `failed_login_attempts` INT UNSIGNED DEFAULT 0,
    `locked_until` DATETIME DEFAULT NULL,
    `status` ENUM('active','inactive','suspended','pending') NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_org` (`organization_id`),
    INDEX `idx_user_email` (`email`),
    INDEX `idx_user_status` (`status`),
    INDEX `idx_user_name` (`last_name`, `first_name`),
    INDEX `idx_users_google_id` (`google_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. Roles
-- ============================================
CREATE TABLE IF NOT EXISTS `roles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` VARCHAR(500) DEFAULT NULL,
    `is_system` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_role_slug_org` (`organization_id`, `slug`),
    INDEX `idx_role_org` (`organization_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. Permissions
-- ============================================
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `module` VARCHAR(50) NOT NULL,
    `description` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_perm_module` (`module`),
    INDEX `idx_perm_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. Role Permissions (pivot)
-- ============================================
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `permission_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_role_perm` (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. User Roles (pivot)
-- ============================================
CREATE TABLE IF NOT EXISTS `user_roles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_role_org` (`user_id`, `role_id`, `organization_id`),
    INDEX `idx_ur_user` (`user_id`),
    INDEX `idx_ur_role` (`role_id`),
    INDEX `idx_ur_org` (`organization_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. User Facilities (pivot, from 019)
-- ============================================
CREATE TABLE IF NOT EXISTS `user_facilities` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_facility` (`user_id`, `facility_id`),
    INDEX `idx_uf_user` (`user_id`),
    INDEX `idx_uf_facility` (`facility_id`),
    INDEX `idx_uf_org` (`organization_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. Players (merged: base + 002)
-- ============================================
CREATE TABLE IF NOT EXISTS `players` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `password_hash` VARCHAR(255) DEFAULT NULL,
    `date_of_birth` DATE DEFAULT NULL,
    `gender` ENUM('male','female','other','prefer_not_to_say') DEFAULT NULL,
    `skill_level` ENUM('beginner','intermediate','advanced','pro') DEFAULT 'beginner',
    `rating` DECIMAL(5,2) DEFAULT NULL,
    `dupr_rating` DECIMAL(5,3) DEFAULT NULL,
    `dupr_id` VARCHAR(50) DEFAULT NULL,
    `emergency_contact_name` VARCHAR(200) DEFAULT NULL,
    `emergency_contact_phone` VARCHAR(30) DEFAULT NULL,
    `medical_notes` TEXT DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `avatar_url` VARCHAR(500) DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `state` VARCHAR(50) DEFAULT NULL,
    `zip_code` VARCHAR(20) DEFAULT NULL,
    `is_waiver` TINYINT(1) NOT NULL DEFAULT 0,
    `is_teen` TINYINT(1) NOT NULL DEFAULT 0,
    `is_email_marketing` TINYINT(1) NOT NULL DEFAULT 1,
    `is_sms_marketing` TINYINT(1) NOT NULL DEFAULT 1,
    `date_joined` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_player_org` (`organization_id`),
    INDEX `idx_player_user` (`user_id`),
    INDEX `idx_player_name` (`last_name`, `first_name`),
    INDEX `idx_player_skill` (`skill_level`),
    INDEX `idx_player_status` (`status`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. Staff
-- ============================================
CREATE TABLE IF NOT EXISTS `staff` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED DEFAULT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(100) DEFAULT NULL,
    `department` VARCHAR(100) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,
    `certifications` JSON DEFAULT NULL,
    `hire_date` DATE DEFAULT NULL,
    `hourly_rate` DECIMAL(10,2) DEFAULT NULL,
    `status` ENUM('active','inactive','on_leave') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_staff_org` (`organization_id`),
    INDEX `idx_staff_facility` (`facility_id`),
    INDEX `idx_staff_user` (`user_id`),
    INDEX `idx_staff_status` (`status`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. Categories (merged: 003 + 025)
-- ============================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `color` VARCHAR(7) NOT NULL DEFAULT '#6366f1',
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_taxable` TINYINT(1) NOT NULL DEFAULT 0,
    `is_system` TINYINT(1) NOT NULL DEFAULT 0,
    `system_slug` VARCHAR(50) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `description` TEXT DEFAULT NULL,
    `image_url` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_cat_name_org` (`organization_id`, `name`),
    UNIQUE KEY `uk_cat_system_slug_org` (`organization_id`, `system_slug`),
    INDEX `idx_cat_org` (`organization_id`),
    INDEX `idx_cat_sort` (`sort_order`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 14. Resources (merged: 003 + 005 + 009)
-- ============================================
CREATE TABLE IF NOT EXISTS `resources` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `field_type` ENUM('checkbox','selectbox','radio','input') NOT NULL DEFAULT 'checkbox',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_res_name_org` (`organization_id`, `name`),
    INDEX `idx_res_org` (`organization_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 15. Resource Values (from 003)
-- ============================================
CREATE TABLE IF NOT EXISTS `resource_values` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `resource_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_rv_name_res` (`resource_id`, `name`),
    INDEX `idx_rv_resource` (`resource_id`),
    INDEX `idx_rv_sort` (`sort_order`),
    FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 16. Sessions (from 007)
-- ============================================
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `category_id` BIGINT UNSIGNED DEFAULT NULL,
    `session_name` VARCHAR(255) NOT NULL,
    `session_tagline` VARCHAR(500) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `picture` VARCHAR(500) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sessions_org` (`organization_id`),
    INDEX `idx_sessions_category` (`category_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 17. Session Facilities (pivot, from 017)
-- ============================================
CREATE TABLE IF NOT EXISTS `session_facilities` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_session_facility` (`session_id`, `facility_id`),
    FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 18. Session Types (merged: 006 + 007 + 009-014)
--     Columns removed by migrations are omitted.
-- ============================================
CREATE TABLE IF NOT EXISTS `session_types` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED DEFAULT NULL,
    `category_id` BIGINT UNSIGNED DEFAULT NULL,
    `session_id` BIGINT UNSIGNED DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `internal_title` VARCHAR(255) DEFAULT NULL,
    `session_type` ENUM('class','series','series_rolling') NOT NULL DEFAULT 'class',
    `capacity` INT UNSIGNED DEFAULT NULL,
    `duration` INT UNSIGNED DEFAULT NULL COMMENT 'Duration in minutes',
    `standard_price` DECIMAL(10,2) DEFAULT NULL,
    `pricing_mode` ENUM('single','time_based','user_based') NOT NULL DEFAULT 'single',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `private` TINYINT(1) NOT NULL DEFAULT 0,
    `scheduling_url` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_st_org` (`organization_id`),
    INDEX `idx_st_facility` (`facility_id`),
    INDEX `idx_st_category` (`category_id`),
    INDEX `idx_session_types_session` (`session_id`),
    INDEX `idx_st_active` (`is_active`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_st_session` FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 19. Session Type Resource Values (pivot, from 006)
-- ============================================
CREATE TABLE IF NOT EXISTS `session_type_resource_values` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `resource_value_id` BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY `uk_strv` (`session_type_id`, `resource_value_id`),
    INDEX `idx_strv_st` (`session_type_id`),
    INDEX `idx_strv_rv` (`resource_value_id`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`resource_value_id`) REFERENCES `resource_values`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 20. Session Type Resource Inputs (from 009)
-- ============================================
CREATE TABLE IF NOT EXISTS `session_type_resource_inputs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `resource_id` BIGINT UNSIGNED NOT NULL,
    `value` TEXT DEFAULT NULL,
    UNIQUE KEY `uk_stri` (`session_type_id`, `resource_id`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 21. Session Type Settings (from 013)
-- ============================================
CREATE TABLE IF NOT EXISTS `session_type_settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_st_setting` (`session_type_id`, `setting_key`),
    CONSTRAINT `fk_sts_session_type` FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 22. Session Form Fields (from 011)
-- ============================================
CREATE TABLE IF NOT EXISTS `session_form_fields` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `field_label` VARCHAR(255) NOT NULL,
    `field_name` VARCHAR(255) NOT NULL,
    `field_type` ENUM('text','number','email','phone','date','textarea','select','checkbox','radio','toggle') NOT NULL DEFAULT 'text',
    `field_options` JSON DEFAULT NULL,
    `placeholder` VARCHAR(255) DEFAULT NULL,
    `is_required` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_session_field_name` (`session_type_id`, `field_name`),
    CONSTRAINT `fk_sff_session_type` FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 23. Pricing Rules (from 007)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_pricing_rules` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `pricing_type` ENUM('time_based','user_based') NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `start_offset_days` INT UNSIGNED DEFAULT NULL,
    `max_users` INT UNSIGNED DEFAULT NULL,
    `priority` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pricing_session_type` (`session_type_id`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 24. Rolling Prices (from 007)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_rolling_prices` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `number_of_weeks` INT UNSIGNED NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    INDEX `idx_rolling_session_type` (`session_type_id`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 25. ST Classes (merged: 008 + 009 drop)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_classes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED DEFAULT NULL,
    `scheduled_at` DATETIME NOT NULL,
    `slots` INT UNSIGNED NOT NULL DEFAULT 0,
    `slots_available` INT UNSIGNED NOT NULL DEFAULT 0,
    `coach_id` BIGINT UNSIGNED DEFAULT NULL,
    `booking_status` TINYINT(1) NOT NULL DEFAULT 1,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_stc_session_type` (`session_type_id`),
    INDEX `idx_stc_facility` (`facility_id`),
    INDEX `idx_stc_scheduled` (`scheduled_at`),
    INDEX `idx_stc_coach` (`coach_id`),
    INDEX `idx_stc_active` (`is_active`),
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`coach_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 26. ST Class Notes (from 018)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_class_notes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `note` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_class_notes_class` (`class_id`),
    CONSTRAINT `fk_class_notes_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_class_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 27. ST Class Courts (from 018)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_class_courts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `court_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_class_court` (`class_id`, `court_id`),
    CONSTRAINT `fk_class_courts_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_class_courts_court` FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 28. Booking Groups (from 022) — before attendees for FK
-- ============================================
CREATE TABLE IF NOT EXISTS `booking_groups` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED DEFAULT NULL,
    `first_class_id` BIGINT UNSIGNED NOT NULL,
    `rolling_weeks` INT UNSIGNED NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `per_session_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(30) NOT NULL DEFAULT 'manual',
    `payment_id` BIGINT UNSIGNED DEFAULT NULL,
    `square_payment_id` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('active','partially_cancelled','fully_cancelled') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_bg_org` (`organization_id`),
    INDEX `idx_bg_session_type` (`session_type_id`),
    INDEX `idx_bg_player` (`player_id`),
    INDEX `idx_bg_first_class` (`first_class_id`),
    INDEX `idx_bg_status` (`status`),
    CONSTRAINT `fk_bg_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bg_session_type` FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bg_player` FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_bg_first_class` FOREIGN KEY (`first_class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 29. ST Class Attendees (merged: 018 + 020 + 022 + 023)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_class_attendees` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED DEFAULT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL DEFAULT '',
    `email` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `partner_id` BIGINT UNSIGNED DEFAULT NULL,
    `status` ENUM('registered','waitlisted','cancelled','no_show') NOT NULL DEFAULT 'registered',
    `amount_paid` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `quote_amount` DECIMAL(10,2) DEFAULT NULL,
    `payment_method` VARCHAR(30) DEFAULT 'manual',
    `payment_id` BIGINT UNSIGNED DEFAULT NULL,
    `square_payment_id` VARCHAR(100) DEFAULT NULL,
    `payment_status` VARCHAR(30) DEFAULT 'pending',
    `discount_code` VARCHAR(50) DEFAULT NULL,
    `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
    `credit_code_id` BIGINT UNSIGNED DEFAULT NULL,
    `credit_amount` DECIMAL(10,2) DEFAULT 0.00,
    `gift_certificate_id` BIGINT UNSIGNED DEFAULT NULL,
    `gift_amount` DECIMAL(10,2) DEFAULT 0.00,
    `tax_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tax_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `refunded_amount` DECIMAL(10,2) DEFAULT 0.00,
    `cancelled_at` DATETIME DEFAULT NULL,
    `cancelled_reason` TEXT DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `booking_group_id` BIGINT UNSIGNED DEFAULT NULL,
    `checked_in` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_attendees_class` (`class_id`),
    INDEX `idx_attendees_player` (`player_id`),
    INDEX `idx_sca_payment_id` (`payment_id`),
    INDEX `idx_sca_square_payment_id` (`square_payment_id`),
    INDEX `idx_sca_credit_code_id` (`credit_code_id`),
    INDEX `idx_sca_gift_certificate_id` (`gift_certificate_id`),
    INDEX `idx_sca_booking_group` (`booking_group_id`),
    CONSTRAINT `fk_attendees_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_attendees_player` FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_attendees_partner` FOREIGN KEY (`partner_id`) REFERENCES `st_class_attendees`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sca_booking_group` FOREIGN KEY (`booking_group_id`) REFERENCES `booking_groups`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 30. Labels (from 018)
-- ============================================
CREATE TABLE IF NOT EXISTS `labels` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `color` VARCHAR(20) NOT NULL DEFAULT '#6366f1',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_labels_org` (`organization_id`),
    CONSTRAINT `fk_labels_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 31. Attendee Labels (pivot, from 018)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_attendee_labels` (
    `attendee_id` BIGINT UNSIGNED NOT NULL,
    `label_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`attendee_id`, `label_id`),
    CONSTRAINT `fk_al_attendee` FOREIGN KEY (`attendee_id`) REFERENCES `st_class_attendees`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_al_label` FOREIGN KEY (`label_id`) REFERENCES `labels`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 32. Hot Deals (merged: 018 + 021)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_hot_deals` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `discount_price` DECIMAL(10,2) NOT NULL,
    `original_price` DECIMAL(10,2) NOT NULL,
    `min_registrations` INT UNSIGNED DEFAULT NULL,
    `label` VARCHAR(100) DEFAULT 'Hot Deal',
    `expires_at` DATETIME DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_hot_deal_class` (`class_id`),
    CONSTRAINT `fk_hot_deal_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 33. Early Birds (from 018)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_early_birds` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `discount_price` DECIMAL(10,2) NOT NULL,
    `cutoff_hours` INT UNSIGNED NOT NULL DEFAULT 24,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_early_bird_class` (`class_id`),
    CONSTRAINT `fk_early_bird_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 34. Feedback Requests (from 018)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_feedback_requests` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `class_id` BIGINT UNSIGNED NOT NULL,
    `message` TEXT DEFAULT NULL,
    `sent_at` DATETIME DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_feedback_req_class` (`class_id`),
    CONSTRAINT `fk_feedback_req_class` FOREIGN KEY (`class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 35. Feedback Responses (from 018)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_feedback_responses` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `feedback_request_id` BIGINT UNSIGNED NOT NULL,
    `attendee_id` BIGINT UNSIGNED NOT NULL,
    `rating` TINYINT UNSIGNED DEFAULT NULL,
    `comment` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_fb_resp_request` (`feedback_request_id`),
    CONSTRAINT `fk_fb_resp_request` FOREIGN KEY (`feedback_request_id`) REFERENCES `st_feedback_requests`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fb_resp_attendee` FOREIGN KEY (`attendee_id`) REFERENCES `st_class_attendees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 36. Credit Codes (from 004)
-- ============================================
CREATE TABLE IF NOT EXISTS `credit_codes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `type` ENUM('credit','infacility') NOT NULL DEFAULT 'credit',
    `category` ENUM('system','admin') NOT NULL DEFAULT 'admin',
    `reason` VARCHAR(100) DEFAULT NULL,
    `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `issued_to` BIGINT UNSIGNED DEFAULT NULL,
    `issued_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_after_days` INT UNSIGNED DEFAULT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `applied_to` JSON DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_cc_code_facility` (`facility_id`, `code`),
    INDEX `idx_cc_org` (`organization_id`),
    INDEX `idx_cc_facility` (`facility_id`),
    INDEX `idx_cc_issued_to` (`issued_to`),
    INDEX `idx_cc_active` (`active`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`issued_to`) REFERENCES `players`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 37. Credit Code Usages (from 004)
-- ============================================
CREATE TABLE IF NOT EXISTS `credit_code_usages` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `credit_code_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED DEFAULT NULL,
    `amount_used` DECIMAL(10,2) NOT NULL,
    `usage_type` ENUM('SESSION','MANUAL_PURCHASE','ADMIN_ADJUSTMENT','REFUND') NOT NULL DEFAULT 'MANUAL_PURCHASE',
    `notes` TEXT DEFAULT NULL,
    `used_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ccu_code` (`credit_code_id`),
    INDEX `idx_ccu_player` (`player_id`),
    FOREIGN KEY (`credit_code_id`) REFERENCES `credit_codes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 38. Gift Certificates (from 004)
-- ============================================
CREATE TABLE IF NOT EXISTS `gift_certificates` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `certificate_name` VARCHAR(200) DEFAULT NULL,
    `value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `original_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `status` ENUM('active','redeemed','expired') NOT NULL DEFAULT 'active',
    `buyer_first_name` VARCHAR(100) DEFAULT NULL,
    `buyer_last_name` VARCHAR(100) DEFAULT NULL,
    `buyer_email` VARCHAR(255) DEFAULT NULL,
    `buyer_phone` VARCHAR(30) DEFAULT NULL,
    `recipient_first_name` VARCHAR(100) DEFAULT NULL,
    `recipient_last_name` VARCHAR(100) DEFAULT NULL,
    `recipient_email` VARCHAR(255) DEFAULT NULL,
    `recipient_phone` VARCHAR(30) DEFAULT NULL,
    `gift_message` TEXT DEFAULT NULL,
    `start_using_after` DATE DEFAULT NULL,
    `expired_at` DATETIME DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_gc_code_facility` (`facility_id`, `code`),
    INDEX `idx_gc_org` (`organization_id`),
    INDEX `idx_gc_facility` (`facility_id`),
    INDEX `idx_gc_status` (`status`),
    INDEX `idx_gc_buyer_email` (`buyer_email`),
    INDEX `idx_gc_recipient_email` (`recipient_email`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 39. Gift Certificate Usage (from 004)
-- ============================================
CREATE TABLE IF NOT EXISTS `gift_certificate_usage` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `gift_certificate_id` BIGINT UNSIGNED NOT NULL,
    `usage_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `amount_used` DECIMAL(10,2) NOT NULL,
    `remaining_balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `reference_id` VARCHAR(100) DEFAULT NULL,
    `reference_type` VARCHAR(50) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `used_by` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_gcu_cert` (`gift_certificate_id`),
    FOREIGN KEY (`gift_certificate_id`) REFERENCES `gift_certificates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 40. Discount Rules (from 015)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_discount_rules` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `discount_category` VARCHAR(100) DEFAULT NULL,
    `coupon_code` VARCHAR(100) DEFAULT NULL,
    `discount_type` ENUM('fixed','percent') NOT NULL DEFAULT 'fixed',
    `discount_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `valid_from` DATE DEFAULT NULL,
    `valid_to` DATE DEFAULT NULL,
    `usage_limit` INT UNSIGNED DEFAULT NULL,
    `used_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_dr_org` (`organization_id`),
    INDEX `idx_dr_facility` (`facility_id`),
    INDEX `idx_dr_coupon` (`coupon_code`),
    INDEX `idx_dr_active` (`is_active`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 41. Discount Session Types (pivot, from 015)
-- ============================================
CREATE TABLE IF NOT EXISTS `st_discount_session_types` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `discount_rule_id` BIGINT UNSIGNED NOT NULL,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_discount_st` (`discount_rule_id`, `session_type_id`),
    FOREIGN KEY (`discount_rule_id`) REFERENCES `st_discount_rules`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 42. Waivers (from 016)
-- ============================================
CREATE TABLE IF NOT EXISTS `waivers` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` LONGTEXT NOT NULL,
    `version` VARCHAR(50) NOT NULL DEFAULT '1.0',
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `effective_date` DATETIME DEFAULT NULL,
    `expiry_date` DATETIME DEFAULT NULL,
    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_waivers_org` (`organization_id`),
    INDEX `idx_waivers_active` (`organization_id`, `is_active`),
    CONSTRAINT `fk_waivers_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 43. Court Bookings (from 024)
-- ============================================
CREATE TABLE IF NOT EXISTS `court_bookings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `court_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED DEFAULT NULL,
    `booking_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `duration_minutes` INT UNSIGNED NOT NULL DEFAULT 60,
    `num_players` INT UNSIGNED NOT NULL DEFAULT 1,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(30) DEFAULT NULL,
    `payment_id` VARCHAR(100) DEFAULT NULL,
    `payment_status` ENUM('pending','paid','refunded','failed') NOT NULL DEFAULT 'pending',
    `status` ENUM('confirmed','cancelled','completed','no_show') NOT NULL DEFAULT 'confirmed',
    `notes` TEXT DEFAULT NULL,
    `cancelled_at` DATETIME DEFAULT NULL,
    `cancelled_reason` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cb_org` (`organization_id`),
    INDEX `idx_cb_facility` (`facility_id`),
    INDEX `idx_cb_court` (`court_id`),
    INDEX `idx_cb_date` (`booking_date`),
    INDEX `idx_cb_court_date` (`court_id`, `booking_date`),
    INDEX `idx_cb_player` (`player_id`),
    INDEX `idx_cb_status` (`status`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`court_id`) REFERENCES `courts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 44. Subscription Plans
-- ============================================
CREATE TABLE IF NOT EXISTS `plans` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `price_monthly` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `price_yearly` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `max_facilities` INT UNSIGNED DEFAULT NULL,
    `max_courts` INT UNSIGNED DEFAULT NULL,
    `max_users` INT UNSIGNED DEFAULT NULL,
    `max_players` INT UNSIGNED DEFAULT NULL,
    `max_storage_mb` INT UNSIGNED DEFAULT 1024,
    `features` JSON DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `square_plan_id` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_plan_active` (`is_active`),
    INDEX `idx_plan_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 45. Subscriptions
-- ============================================
CREATE TABLE IF NOT EXISTS `subscriptions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `plan_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('active','past_due','cancelled','trialing','paused','expired') NOT NULL DEFAULT 'trialing',
    `billing_cycle` ENUM('monthly','yearly') NOT NULL DEFAULT 'monthly',
    `current_period_start` DATETIME DEFAULT NULL,
    `current_period_end` DATETIME DEFAULT NULL,
    `trial_ends_at` DATETIME DEFAULT NULL,
    `cancelled_at` DATETIME DEFAULT NULL,
    `cancel_reason` TEXT DEFAULT NULL,
    `square_subscription_id` VARCHAR(100) DEFAULT NULL,
    `metadata` JSON DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sub_org` (`organization_id`),
    INDEX `idx_sub_plan` (`plan_id`),
    INDEX `idx_sub_status` (`status`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`plan_id`) REFERENCES `plans`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 46. Invoices
-- ============================================
CREATE TABLE IF NOT EXISTS `invoices` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `subscription_id` BIGINT UNSIGNED DEFAULT NULL,
    `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tax` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(3) DEFAULT 'USD',
    `status` ENUM('draft','sent','paid','overdue','cancelled','refunded') NOT NULL DEFAULT 'draft',
    `due_date` DATE DEFAULT NULL,
    `paid_at` DATETIME DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `line_items` JSON DEFAULT NULL,
    `square_invoice_id` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_inv_org` (`organization_id`),
    INDEX `idx_inv_sub` (`subscription_id`),
    INDEX `idx_inv_status` (`status`),
    INDEX `idx_inv_due` (`due_date`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 47. Payment Methods
-- ============================================
CREATE TABLE IF NOT EXISTS `payment_methods` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` ENUM('card','bank_account','digital_wallet') NOT NULL DEFAULT 'card',
    `brand` VARCHAR(50) DEFAULT NULL,
    `last_four` VARCHAR(4) DEFAULT NULL,
    `exp_month` TINYINT UNSIGNED DEFAULT NULL,
    `exp_year` SMALLINT UNSIGNED DEFAULT NULL,
    `cardholder_name` VARCHAR(200) DEFAULT NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `square_card_id` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('active','expired','revoked') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pm_org` (`organization_id`),
    INDEX `idx_pm_user` (`user_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 48. Payments
-- ============================================
CREATE TABLE IF NOT EXISTS `payments` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `invoice_id` BIGINT UNSIGNED DEFAULT NULL,
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `payment_method_id` BIGINT UNSIGNED DEFAULT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(3) DEFAULT 'USD',
    `status` ENUM('pending','processing','completed','failed','refunded','partially_refunded','cancelled') NOT NULL DEFAULT 'pending',
    `description` VARCHAR(500) DEFAULT NULL,
    `idempotency_key` VARCHAR(100) DEFAULT NULL UNIQUE,
    `square_payment_id` VARCHAR(100) DEFAULT NULL,
    `square_receipt_url` VARCHAR(500) DEFAULT NULL,
    `refunded_amount` DECIMAL(10,2) DEFAULT 0.00,
    `metadata` JSON DEFAULT NULL,
    `processed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pay_org` (`organization_id`),
    INDEX `idx_pay_invoice` (`invoice_id`),
    INDEX `idx_pay_user` (`user_id`),
    INDEX `idx_pay_status` (`status`),
    INDEX `idx_pay_created` (`created_at`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 49. Transactions (ledger)
-- ============================================
CREATE TABLE IF NOT EXISTS `transactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `payment_id` BIGINT UNSIGNED DEFAULT NULL,
    `type` ENUM('charge','refund','payout','adjustment','credit') NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(3) DEFAULT 'USD',
    `description` VARCHAR(500) DEFAULT NULL,
    `reference_type` VARCHAR(50) DEFAULT NULL,
    `reference_id` BIGINT UNSIGNED DEFAULT NULL,
    `square_transaction_id` VARCHAR(100) DEFAULT NULL,
    `metadata` JSON DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_txn_org` (`organization_id`),
    INDEX `idx_txn_payment` (`payment_id`),
    INDEX `idx_txn_type` (`type`),
    INDEX `idx_txn_created` (`created_at`),
    INDEX `idx_txn_ref` (`reference_type`, `reference_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 50. Email Logs (from 021)
-- ============================================
CREATE TABLE IF NOT EXISTS `email_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED DEFAULT NULL,
    `recipient_email` VARCHAR(255) NOT NULL,
    `recipient_name` VARCHAR(200) DEFAULT NULL,
    `player_id` BIGINT UNSIGNED DEFAULT NULL,
    `subject` VARCHAR(500) NOT NULL,
    `body_html` LONGTEXT DEFAULT NULL,
    `email_type` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(100) DEFAULT NULL,
    `entity_id` BIGINT UNSIGNED DEFAULT NULL,
    `status` ENUM('sent','failed','queued') NOT NULL DEFAULT 'sent',
    `error_message` TEXT DEFAULT NULL,
    `sent_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_el_org` (`organization_id`),
    INDEX `idx_el_facility` (`facility_id`),
    INDEX `idx_el_player` (`player_id`),
    INDEX `idx_el_type` (`email_type`),
    INDEX `idx_el_entity` (`entity_type`, `entity_id`),
    INDEX `idx_el_created` (`created_at`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`sent_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 51. Notifications
-- ============================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED DEFAULT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(100) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT DEFAULT NULL,
    `data` JSON DEFAULT NULL,
    `channel` ENUM('email','sms','push','in_app') NOT NULL DEFAULT 'in_app',
    `read_at` DATETIME DEFAULT NULL,
    `sent_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_org` (`organization_id`),
    INDEX `idx_notif_user` (`user_id`),
    INDEX `idx_notif_type` (`type`),
    INDEX `idx_notif_read` (`read_at`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 52. Files
-- ============================================
CREATE TABLE IF NOT EXISTS `files` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED DEFAULT NULL,
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `path` VARCHAR(500) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `size` BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `disk` VARCHAR(50) DEFAULT 'local',
    `context` ENUM('avatar','document','logo','attachment','import','export','facility','general') DEFAULT 'attachment',
    `metadata` JSON DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_file_org` (`organization_id`),
    INDEX `idx_file_user` (`user_id`),
    INDEX `idx_file_context` (`context`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 53. Activity Logs (Audit)
-- ============================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED DEFAULT NULL,
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(100) DEFAULT NULL,
    `entity_id` BIGINT UNSIGNED DEFAULT NULL,
    `old_values` JSON DEFAULT NULL,
    `new_values` JSON DEFAULT NULL,
    `description` VARCHAR(500) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_al_org` (`organization_id`),
    INDEX `idx_al_user` (`user_id`),
    INDEX `idx_al_action` (`action`),
    INDEX `idx_al_entity` (`entity_type`, `entity_id`),
    INDEX `idx_al_created` (`created_at`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 54. API Tokens
-- ============================================
CREATE TABLE IF NOT EXISTS `api_tokens` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL UNIQUE,
    `abilities` JSON DEFAULT NULL,
    `last_used_at` DATETIME DEFAULT NULL,
    `expires_at` DATETIME DEFAULT NULL,
    `revoked_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_at_org` (`organization_id`),
    INDEX `idx_at_user` (`user_id`),
    INDEX `idx_at_hash` (`token_hash`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 55. Settings (key-value store)
-- ============================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED DEFAULT NULL,
    `group_name` VARCHAR(100) NOT NULL,
    `key_name` VARCHAR(100) NOT NULL,
    `value` TEXT DEFAULT NULL,
    `type` ENUM('string','integer','boolean','json','float') NOT NULL DEFAULT 'string',
    `description` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_setting` (`organization_id`, `group_name`, `key_name`),
    INDEX `idx_set_org` (`organization_id`),
    INDEX `idx_set_group` (`group_name`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 56. Refresh Tokens
-- ============================================
CREATE TABLE IF NOT EXISTS `refresh_tokens` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL UNIQUE,
    `expires_at` DATETIME NOT NULL,
    `revoked_at` DATETIME DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_rt_user` (`user_id`),
    INDEX `idx_rt_hash` (`token_hash`),
    INDEX `idx_rt_expires` (`expires_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 57. Password Resets
-- ============================================
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pr_email` (`email`),
    INDEX `idx_pr_token` (`token_hash`),
    INDEX `idx_pr_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 58. Email Verifications (from google_oauth)
-- ============================================
CREATE TABLE IF NOT EXISTS `email_verifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `token_hash` VARCHAR(64) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    INDEX `idx_email_verifications_email` (`email`),
    INDEX `idx_email_verifications_token` (`token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 59. Rate Limits
-- ============================================
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key_name` VARCHAR(255) NOT NULL UNIQUE,
    `hits` INT UNSIGNED NOT NULL DEFAULT 0,
    `reset_at` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_rl_key` (`key_name`),
    INDEX `idx_rl_reset` (`reset_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 60. Consultations (from 026)
-- ============================================
CREATE TABLE IF NOT EXISTS `consultations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `consultation_type` ENUM('partnership','software_integration','general') NOT NULL DEFAULT 'partnership',
    `facility_stage` VARCHAR(50) DEFAULT NULL,
    `planned_location` VARCHAR(200) DEFAULT NULL,
    `number_of_courts` VARCHAR(20) DEFAULT NULL,
    `software_interest` VARCHAR(255) DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `status` ENUM('new','contacted','in_progress','closed') NOT NULL DEFAULT 'new',
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_consultations_status` (`status`),
    INDEX `idx_consultations_type` (`consultation_type`),
    INDEX `idx_consultations_email` (`email`),
    INDEX `idx_consultations_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 61. Contact Submissions (from 027)
-- ============================================
CREATE TABLE IF NOT EXISTS `contact_submissions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` ENUM('partnership','demo','support','press','other') NOT NULL DEFAULT 'other',
    `message` TEXT NOT NULL,
    `status` ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
    `notes` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_contact_status` (`status`),
    INDEX `idx_contact_subject` (`subject`),
    INDEX `idx_contact_email` (`email`),
    INDEX `idx_contact_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 62. Site Settings (from 028)
-- ============================================
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` TEXT DEFAULT NULL,
    `setting_type` ENUM('string','boolean','integer','json') NOT NULL DEFAULT 'string',
    `description` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_site_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 63. Extensions (from 029)
-- ============================================
CREATE TABLE IF NOT EXISTS `extensions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `version` VARCHAR(20) DEFAULT '1.0.0',
    `category` VARCHAR(100) DEFAULT 'general',
    `icon` VARCHAR(100) DEFAULT NULL,
    `price_monthly` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `price_yearly` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `settings_schema` JSON DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ext_slug` (`slug`),
    INDEX `idx_ext_active` (`is_active`),
    INDEX `idx_ext_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 64. Organization Extensions (from 029)
-- ============================================
CREATE TABLE IF NOT EXISTS `organization_extensions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `extension_id` BIGINT UNSIGNED NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `settings` JSON DEFAULT NULL,
    `installed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_org_ext` (`organization_id`, `extension_id`),
    INDEX `idx_oe_org` (`organization_id`),
    INDEX `idx_oe_ext` (`extension_id`),
    FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`extension_id`) REFERENCES `extensions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 65. Facility Extension Settings (from 022/029)
-- ============================================
CREATE TABLE IF NOT EXISTS `facility_extension_settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `organization_extension_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `settings` JSON DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_fes_org_ext_facility` (`organization_extension_id`, `facility_id`),
    CONSTRAINT `fk_fes_org_ext` FOREIGN KEY (`organization_extension_id`) REFERENCES `organization_extensions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fes_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 66. Announcements (from 029)
-- ============================================
CREATE TABLE IF NOT EXISTS `announcements` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info','warning','critical','maintenance') NOT NULL DEFAULT 'info',
    `target` ENUM('all','specific') NOT NULL DEFAULT 'all',
    `target_org_ids` TEXT DEFAULT NULL,
    `starts_at` DATETIME DEFAULT NULL,
    `ends_at` DATETIME DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ann_active` (`is_active`),
    INDEX `idx_ann_type` (`type`),
    INDEX `idx_ann_dates` (`starts_at`, `ends_at`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 67. Schema Migrations (from 030)
-- ============================================
CREATE TABLE IF NOT EXISTS `schema_migrations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(255) NOT NULL UNIQUE,
    `batch` INT NOT NULL DEFAULT 1,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success','failed') NOT NULL DEFAULT 'success',
    `error_message` TEXT DEFAULT NULL,
    INDEX `idx_sm_batch` (`batch`),
    INDEX `idx_sm_executed` (`executed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Seed Data
-- ============================================

-- Site Settings defaults
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
    ('maintenance_mode', '0', 'boolean', 'When enabled, the client site shows a maintenance page'),
    ('maintenance_message', 'We are currently performing scheduled maintenance. Please check back soon.', 'string', 'Message shown on the maintenance page'),
    ('password_protection_enabled', '0', 'boolean', 'When enabled, visitors must enter a password to access the site'),
    ('site_password', '', 'string', 'Password visitors must enter to access the site when protection is enabled'),
    ('maintenance_allowed_ips', '[]', 'json', 'JSON array of IP addresses that bypass maintenance mode')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;

-- Extension catalog
INSERT IGNORE INTO `extensions` (`name`, `slug`, `description`, `version`, `category`, `icon`, `price_monthly`, `price_yearly`, `is_active`, `settings_schema`, `sort_order`) VALUES
('Tournament Manager', 'tournament-manager', 'Organize and manage pickleball tournaments with brackets, seeding, and live scoring.', '1.0.0', 'competitions', NULL, 29.99, 299.99, 1, NULL, 1),
('League Management', 'league-management', 'Run recurring leagues with standings, schedules, and automated match generation.', '1.0.0', 'competitions', NULL, 24.99, 249.99, 1, NULL, 2),
('Online Booking', 'online-booking', 'Allow players to book courts online with real-time availability and payments.', '1.0.0', 'booking', NULL, 19.99, 199.99, 1, NULL, 3),
('Membership Portal', 'membership-portal', 'Self-service membership signup, renewals, and member directory.', '1.0.0', 'membership', NULL, 14.99, 149.99, 1, NULL, 4),
('Advanced Analytics', 'advanced-analytics', 'Player performance tracking, court utilization analytics, and revenue insights.', '1.0.0', 'analytics', NULL, 19.99, 199.99, 1, NULL, 5),
('Email Marketing', 'email-marketing', 'Send newsletters, event promotions, and automated email campaigns to members.', '1.0.0', 'marketing', NULL, 9.99, 99.99, 1, NULL, 6),
('Mobile App', 'mobile-app', 'White-label mobile app for your facility with push notifications.', '1.0.0', 'mobile', NULL, 49.99, 499.99, 1, NULL, 7),
('Payment Gateway Plus', 'payment-gateway-plus', 'Additional payment methods: ACH, Apple Pay, Google Pay, and installment plans.', '1.0.0', 'payments', NULL, 14.99, 149.99, 1, NULL, 8),
('Custom Branding', 'custom-branding', 'Custom domain, logo, colors, and white-label the entire admin interface.', '1.0.0', 'customization', NULL, 9.99, 99.99, 1, NULL, 9),
('API Access', 'api-access', 'REST API access for third-party integrations and custom development.', '1.0.0', 'developer', NULL, 24.99, 249.99, 1, NULL, 10),
('Square Terminal POS', 'square-terminal-pos', 'Process payments using Square Terminal hardware devices.', '1.0.0', 'payments', 'credit-card', 0.00, 0.00, 1, '{"type":"object","properties":{"device_id":{"type":"string","title":"Terminal Device ID"},"device_name":{"type":"string","title":"Terminal Name"}}}', 11);

SET FOREIGN_KEY_CHECKS = 1;
