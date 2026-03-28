<?php
/**
 * Run migration 037 (facility_hero_video): Add hero_video_url column.
 *
 * Usage: php scripts/run_migration_037b.php
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "=== Migration 037b: Add hero_video_url to facilities ===\n\n";

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

$host   = $env['DB_HOST'] ?? '127.0.0.1';
$port   = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_NAME'] ?? 'k2pickleball';
$user   = $env['DB_USER'] ?? 'root';
$pass   = $env['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "Connected to DB: {$dbName}\n\n";
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage() . "\n");
}

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
