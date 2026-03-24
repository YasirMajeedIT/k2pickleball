<?php
/**
 * Run migration 018: Master Schedule tables
 */
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$migrationsDir = __DIR__ . '/../database/migrations';
$sql = file_get_contents("$migrationsDir/018_master_schedule_tables.sql");
$sql = preg_replace('/--[^\n]*\n/', "\n", $sql);

$statements = array_filter(array_map('trim', explode(';', $sql)));
foreach ($statements as $stmt) {
    if (!$stmt) continue;
    try {
        $pdo->exec($stmt);
        echo "[OK] " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 90) . PHP_EOL;
    } catch (PDOException $e) {
        echo "[ERR] " . $e->getMessage() . PHP_EOL;
        echo "      SQL: " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 80) . PHP_EOL;
    }
}

echo PHP_EOL . "Migration 018 complete." . PHP_EOL;
