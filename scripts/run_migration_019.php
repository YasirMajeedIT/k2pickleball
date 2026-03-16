<?php
/**
 * Run migrations 017, 018, 019 + add new user profile columns
 */
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

function makePdo(): PDO {
    global $_ENV;
    return new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}

$pdo = makePdo();

function runSql(PDO $pdo, string $label, string $sql): void {
    $sql = preg_replace('/--[^\n]*\n/', "\n", $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!$stmt) continue;
        try {
            $pdo->exec($stmt);
            echo "[OK] $label: " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 90) . PHP_EOL;
        } catch (PDOException $e) {
            echo "[ERR] $label: " . $e->getMessage() . PHP_EOL;
            echo "      SQL: " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 80) . PHP_EOL;
        }
    }
}

$migrationsDir = __DIR__ . '/../database/migrations';

// --- 017: Remove unused user columns (timezone, locale, dupr_id) ---
runSql($pdo, '017_remove_unused_user_columns', file_get_contents("$migrationsDir/017_remove_unused_user_columns.sql"));

// --- 018: Rename usapa_member_id -> membership_id (conditional, uses PREPARE/EXECUTE) ---
runSql($pdo, '018_rename_user_membership_column', file_get_contents("$migrationsDir/018_rename_user_membership_column.sql"));

// Reconnect — EXECUTE stmt leaves an open result set that blocks subsequent queries
$pdo = makePdo();

// --- 019: Create user_facilities pivot table ---
runSql($pdo, '019_create_user_facilities', file_get_contents("$migrationsDir/019_create_user_facilities.sql"));

// --- Add new user profile columns (idempotent via IF NOT EXISTS) ---
$profileCols = "
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `membership_id`             VARCHAR(50)   DEFAULT NULL AFTER `phone`,
    ADD COLUMN IF NOT EXISTS `professional_title`        VARCHAR(100)  DEFAULT NULL AFTER `membership_id`,
    ADD COLUMN IF NOT EXISTS `certification_level`       VARCHAR(100)  DEFAULT NULL AFTER `professional_title`,
    ADD COLUMN IF NOT EXISTS `years_experience`          SMALLINT UNSIGNED DEFAULT NULL AFTER `certification_level`,
    ADD COLUMN IF NOT EXISTS `emergency_contact_name`    VARCHAR(150)  DEFAULT NULL AFTER `years_experience`,
    ADD COLUMN IF NOT EXISTS `emergency_contact_phone`   VARCHAR(30)   DEFAULT NULL AFTER `emergency_contact_name`,
    ADD COLUMN IF NOT EXISTS `bio`                       TEXT          DEFAULT NULL AFTER `emergency_contact_phone`;
";
runSql($pdo, 'add_user_profile_columns', $profileCols);

echo PHP_EOL . "All migrations complete." . PHP_EOL;
