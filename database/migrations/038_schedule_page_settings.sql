-- Migration 038: Schedule Page Settings
-- Adds organization-level schedule page configuration stored in the settings table.
-- Uses the existing settings key-value store with group 'schedule_page'.

-- Seed default schedule page settings for each existing organization
INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'page_title', 'Class Schedule', 'string', 'Schedule page heading (can be renamed)', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'page_subtitle', 'Browse upcoming classes and reserve your spot.', 'string', 'Schedule page subtitle text', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'default_view', 'week', 'string', 'Default view mode: month, week, today, list, calendar', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'enabled_views', '["month","week","today","list","calendar"]', 'json', 'Which view modes are available', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_time', '1', 'boolean', 'Show time on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_title', '1', 'boolean', 'Show session title on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_category', '1', 'boolean', 'Show category badge on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_spots', '1', 'boolean', 'Show spots/availability on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_price', '1', 'boolean', 'Show price on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_coach', '0', 'boolean', 'Show coach/facilitator name on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_description', '0', 'boolean', 'Show session description on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_courts', '0', 'boolean', 'Show assigned courts on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_duration', '0', 'boolean', 'Show duration on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_resources', '0', 'boolean', 'Show assigned resources on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_skill_level', '0', 'boolean', 'Show skill level from resources on calendar cards', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_session_number', '0', 'boolean', 'Show series session number (e.g. 3 of 8)', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_hot_deal_badge', '1', 'boolean', 'Show Hot Deal badge when active', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_early_bird_badge', '1', 'boolean', 'Show Early Bird badge when active', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_category_filter', '1', 'boolean', 'Show category filter buttons', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'show_resource_filters', '0', 'boolean', 'Show resource filters (skill level, etc.)', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'resource_filter_ids', '[]', 'json', 'Resource IDs to show as filters', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'inline_booking', '1', 'boolean', 'Allow click-to-book directly from schedule (inline modal)', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'require_login', '0', 'boolean', 'Require user login before booking', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'payment_methods', '["card","credit_code","gift_certificate"]', 'json', 'Accepted payment methods: card, credit_code, gift_certificate, other', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');

INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
SELECT o.`id`, 'schedule_page', 'color_scheme', 'default', 'string', 'Calendar color scheme: default, category, single', NOW(), NOW()
FROM `organizations` o WHERE o.`status` IN ('active','trial');
