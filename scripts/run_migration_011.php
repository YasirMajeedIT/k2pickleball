<?php
require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/database.php';
$pdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}",
    $config['user'], $config['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Check if table already exists
$check = $pdo->query("SHOW TABLES LIKE 'session_form_fields'");
if ($check->rowCount() > 0) {
    echo "Table session_form_fields already exists.\n";
    exit;
}

// Check the id column type of session_types
$cols = $pdo->query("SHOW COLUMNS FROM session_types WHERE Field = 'id'");
$col = $cols->fetch(PDO::FETCH_ASSOC);
echo "session_types.id type: " . $col['Type'] . "\n";

$sql = "CREATE TABLE `session_form_fields` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_type_id` INT UNSIGNED NOT NULL,
    `field_label` VARCHAR(255) NOT NULL,
    `field_name` VARCHAR(255) NOT NULL,
    `field_type` ENUM('text','number','email','phone','date','textarea','select','checkbox','radio','toggle') NOT NULL DEFAULT 'text',
    `field_options` JSON DEFAULT NULL,
    `placeholder` VARCHAR(255) DEFAULT NULL,
    `is_required` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_session_field_name` (`session_type_id`, `field_name`),
    INDEX `idx_sff_session_type` (`session_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

try {
    $pdo->exec($sql);
    echo "OK: Created session_form_fields table.\n";
} catch (Exception $e) {
    echo "ERR: " . $e->getMessage() . "\n";
}
echo "Done.\n";
