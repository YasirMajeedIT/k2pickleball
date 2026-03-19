<?php
/**
 * Run migration 021: Email logs, hot deal registration threshold, facility SMTP columns
 */
require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/database.php';

$pdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}",
    $config['user'], $config['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "Running migration 021...\n";

// 1. Create email_logs table
$pdo->exec("
CREATE TABLE IF NOT EXISTS `email_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NULL,
    `recipient_email` VARCHAR(255) NOT NULL,
    `recipient_name` VARCHAR(200) NULL,
    `player_id` BIGINT UNSIGNED NULL,
    `subject` VARCHAR(500) NOT NULL,
    `body_html` LONGTEXT NULL,
    `email_type` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(100) NULL,
    `entity_id` BIGINT UNSIGNED NULL,
    `status` ENUM('sent','failed','queued') NOT NULL DEFAULT 'sent',
    `error_message` TEXT NULL,
    `sent_by` BIGINT UNSIGNED NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "  ✓ email_logs table created\n";

// 2. Hot deal columns
$cols = ['min_registrations', 'label'];
foreach ($cols as $col) {
    try {
        $check = $pdo->query("SHOW COLUMNS FROM `st_hot_deals` LIKE '{$col}'")->fetch();
        if (!$check) {
            if ($col === 'min_registrations') {
                $pdo->exec("ALTER TABLE `st_hot_deals` ADD COLUMN `min_registrations` INT UNSIGNED NULL AFTER `original_price`");
            } else {
                $pdo->exec("ALTER TABLE `st_hot_deals` ADD COLUMN `label` VARCHAR(100) NULL DEFAULT 'Hot Deal' AFTER `min_registrations`");
            }
            echo "  ✓ st_hot_deals.{$col} added\n";
        } else {
            echo "  - st_hot_deals.{$col} already exists\n";
        }
    } catch (Exception $e) {
        echo "  ⚠ st_hot_deals.{$col}: " . $e->getMessage() . "\n";
    }
}

// 3. Facility SMTP columns
$smtpCols = [
    'smtp_host'       => "VARCHAR(255) NULL AFTER `settings`",
    'smtp_port'       => "INT UNSIGNED NULL AFTER `smtp_host`",
    'smtp_username'   => "VARCHAR(255) NULL AFTER `smtp_port`",
    'smtp_password'   => "VARCHAR(255) NULL AFTER `smtp_username`",
    'smtp_encryption' => "VARCHAR(10) NULL AFTER `smtp_password`",
    'smtp_from_email' => "VARCHAR(255) NULL AFTER `smtp_encryption`",
    'smtp_from_name'  => "VARCHAR(255) NULL AFTER `smtp_from_email`",
];

foreach ($smtpCols as $col => $def) {
    try {
        $check = $pdo->query("SHOW COLUMNS FROM `facilities` LIKE '{$col}'")->fetch();
        if (!$check) {
            $pdo->exec("ALTER TABLE `facilities` ADD COLUMN `{$col}` {$def}");
            echo "  ✓ facilities.{$col} added\n";
        } else {
            echo "  - facilities.{$col} already exists\n";
        }
    } catch (Exception $e) {
        echo "  ⚠ facilities.{$col}: " . $e->getMessage() . "\n";
    }
}

echo "\nMigration 021 complete.\n";
