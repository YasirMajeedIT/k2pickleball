<?php
/**
 * Migration 037: Clean up navigation items
 * - Remove "Book a Court" system nav item
 * - Change Schedule from dropdown to link type
 * Run from CLI: php scripts/run_migration_037.php
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "=== Migration 037: Navigation Cleanup ===\n\n";

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

echo "Cleaning up navigation items...\n";

// 1. Remove Book a Court
$stmt = $pdo->prepare("DELETE FROM navigation_items WHERE system_key = 'book-court'");
$stmt->execute();
echo "  Deleted 'Book a Court': {$stmt->rowCount()} row(s)\n";

// 2. Change Schedule from dropdown to link
$stmt = $pdo->prepare("UPDATE navigation_items SET type = 'link' WHERE system_key = 'schedule'");
$stmt->execute();
echo "  Updated Schedule to type=link: {$stmt->rowCount()} row(s)\n";

echo "Done.\n";
