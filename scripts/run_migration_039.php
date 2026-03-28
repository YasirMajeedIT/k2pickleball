<?php
/**
 * Run migration 039: Move sport_type to facilities, remove hourly_rate from courts
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "=== Migration 039: Move sport_type to facilities ===\n\n";

$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    $envFile = dirname(__DIR__) . '/.env';
}
if (!file_exists($envFile)) {
    die("ERROR: .env file not found\n");
}

$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    [$key, $val] = explode('=', $line, 2);
    $env[trim($key)] = trim($val, " \t\n\r\0\x0B\"'");
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$name = $env['DB_NAME'] ?? 'k2pickleball';
$user = $env['DB_USER'] ?? 'root';
$pass = $env['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "Connected to {$name}\n\n";

    // Check if sport_type already exists on facilities
    $cols = $pdo->query("SHOW COLUMNS FROM `facilities` LIKE 'sport_type'")->fetchAll();
    if (count($cols) > 0) {
        echo "facilities.sport_type already exists — skipping ADD.\n";
    } else {
        $pdo->exec("ALTER TABLE `facilities`
            ADD COLUMN `sport_type` VARCHAR(100) NOT NULL DEFAULT 'pickleball' AFTER `description`,
            ADD COLUMN `custom_sport_type` VARCHAR(100) NULL AFTER `sport_type`,
            ADD COLUMN `facility_type` VARCHAR(100) NOT NULL DEFAULT 'sports_facility' AFTER `custom_sport_type`");
        echo "Added sport_type, custom_sport_type, facility_type to facilities.\n";
    }

    // Check if sport_type still exists on courts
    $cols = $pdo->query("SHOW COLUMNS FROM `courts` LIKE 'sport_type'")->fetchAll();
    if (count($cols) > 0) {
        $pdo->exec("ALTER TABLE `courts` DROP COLUMN `sport_type`");
        echo "Dropped sport_type from courts.\n";
    } else {
        echo "courts.sport_type already removed — skipping.\n";
    }

    // Check if hourly_rate still exists on courts
    $cols = $pdo->query("SHOW COLUMNS FROM `courts` LIKE 'hourly_rate'")->fetchAll();
    if (count($cols) > 0) {
        $pdo->exec("ALTER TABLE `courts` DROP COLUMN `hourly_rate`");
        echo "Dropped hourly_rate from courts.\n";
    } else {
        echo "courts.hourly_rate already removed — skipping.\n";
    }

    // Record migration
    $pdo->exec("INSERT INTO `schema_migrations` (`migration`, `batch`, `executed_at`, `status`)
                VALUES ('039_move_sport_type_to_facilities', 39, NOW(), 'success')
                ON DUPLICATE KEY UPDATE `status` = 'success'");

    echo "\nMigration 039 complete!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
