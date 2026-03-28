<?php
/**
 * Run migration 037 (facility_hero_video): Add hero_video_url column.
 *
 * Usage: php scripts/run_migration_037b.php
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dbCfg = require __DIR__ . '/../config/database.php';

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $dbCfg['host'], $dbCfg['port'] ?? 3306, $dbCfg['name']);
$pdo = new PDO($dsn, $dbCfg['user'], $dbCfg['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$sql = file_get_contents(__DIR__ . '/../database/migrations/037_facility_hero_video.sql');

$lines = array_filter(
    explode("\n", $sql),
    fn($l) => !str_starts_with(trim($l), '--') && trim($l) !== ''
);
$clean = implode("\n", $lines);

foreach (array_filter(array_map('trim', explode(';', $clean))) as $stmt) {
    try {
        $pdo->exec($stmt);
        echo "[OK] " . substr($stmt, 0, 80) . "\n";
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column name')) {
            echo "[SKIP] Column already exists.\n";
        } else {
            echo "[ERR] {$e->getMessage()}\n";
        }
    }
}

echo "\nMigration 037 (hero_video) complete.\n";
