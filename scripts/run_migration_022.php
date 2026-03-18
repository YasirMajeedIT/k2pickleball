<?php
/**
 * Run migration 022: Rolling enrollment support + Extension facility settings
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$name = $_ENV['DB_DATABASE'] ?? 'k2pickleball';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';

$pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

echo "Running migration 022...\n";

// 1. Create booking_groups table
echo "  Creating booking_groups table...\n";
$pdo->exec("
CREATE TABLE IF NOT EXISTS `booking_groups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` CHAR(36) NOT NULL,
    `organization_id` BIGINT UNSIGNED NOT NULL,
    `session_type_id` BIGINT UNSIGNED NOT NULL,
    `player_id` BIGINT UNSIGNED NULL,
    `first_class_id` BIGINT UNSIGNED NOT NULL COMMENT 'The class where enrollment starts',
    `rolling_weeks` INT UNSIGNED NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `per_session_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(30) NOT NULL DEFAULT 'manual',
    `payment_id` BIGINT UNSIGNED NULL,
    `square_payment_id` VARCHAR(100) NULL,
    `status` ENUM('active','partially_cancelled','fully_cancelled') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_bg_uuid` (`uuid`),
    INDEX `idx_bg_org` (`organization_id`),
    INDEX `idx_bg_session_type` (`session_type_id`),
    INDEX `idx_bg_player` (`player_id`),
    INDEX `idx_bg_first_class` (`first_class_id`),
    INDEX `idx_bg_status` (`status`),
    CONSTRAINT `fk_bg_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bg_session_type` FOREIGN KEY (`session_type_id`) REFERENCES `session_types`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bg_player` FOREIGN KEY (`player_id`) REFERENCES `players`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_bg_first_class` FOREIGN KEY (`first_class_id`) REFERENCES `st_classes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "  ✓ booking_groups created\n";

// 2. Add booking_group_id to st_class_attendees
echo "  Adding booking_group_id to st_class_attendees...\n";
$cols = $pdo->query("SHOW COLUMNS FROM st_class_attendees LIKE 'booking_group_id'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE `st_class_attendees`
        ADD COLUMN `booking_group_id` BIGINT UNSIGNED NULL AFTER `notes`,
        ADD INDEX `idx_sca_booking_group` (`booking_group_id`),
        ADD CONSTRAINT `fk_sca_booking_group` FOREIGN KEY (`booking_group_id`) REFERENCES `booking_groups`(`id`) ON DELETE SET NULL
    ");
    echo "  ✓ booking_group_id added\n";
} else {
    echo "  · booking_group_id already exists, skipping\n";
}

// 3. Create facility_extension_settings table
echo "  Creating facility_extension_settings table...\n";
$pdo->exec("
CREATE TABLE IF NOT EXISTS `facility_extension_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organization_extension_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `settings` JSON NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_fes_org_ext_facility` (`organization_extension_id`, `facility_id`),
    CONSTRAINT `fk_fes_org_ext` FOREIGN KEY (`organization_extension_id`) REFERENCES `organization_extensions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fes_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "  ✓ facility_extension_settings created\n";

// 4. Seed Square Terminal POS extension
echo "  Seeding Square Terminal POS extension...\n";
$existing = $pdo->prepare("SELECT id FROM extensions WHERE slug = 'square-terminal-pos'");
$existing->execute();
if (!$existing->fetch()) {
    $stmt = $pdo->prepare("INSERT INTO extensions (name, slug, description, version, category, icon, price_monthly, price_yearly, is_active, settings_schema, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([
        'Square Terminal POS',
        'square-terminal-pos',
        'Process payments using Square Terminal hardware devices. Supports device pairing, terminal checkouts, and per-facility terminal configuration.',
        '1.0.0',
        'payments',
        'credit-card',
        0.00,
        0.00,
        1,
        '{"type":"object","properties":{"device_id":{"type":"string","title":"Terminal Device ID","description":"The paired Square Terminal device ID"},"device_name":{"type":"string","title":"Terminal Name","description":"Friendly name for this terminal"}}}',
        10,
    ]);
    echo "  ✓ Square Terminal POS extension seeded\n";
} else {
    echo "  · Square Terminal POS extension already exists, skipping\n";
}

echo "\nMigration 022 completed successfully.\n";
