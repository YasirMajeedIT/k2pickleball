<?php
/**
 * Run Migration 038: Schedule Page Settings
 * Seeds schedule_page settings for all existing organizations.
 * Run from CLI: php scripts/run_migration_038.php
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "=== Migration 038: Schedule Page Settings ===\n\n";

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

$sql = file_get_contents(__DIR__ . '/../database/migrations/038_schedule_page_settings.sql');

// Split on semicolons, skip empty and comment-only lines
$statements = array_filter(array_map('trim', explode(';', $sql)), function ($s) {
    return $s !== '' && !str_starts_with($s, '--');
});

$ok = 0;
foreach ($statements as $stmt) {
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (PDOException $e) {
        echo "WARN: " . $e->getMessage() . "\n";
    }
}

echo "Migration 038 complete: {$ok} statements executed.\n";

// Count settings
$count = $pdo->query("SELECT COUNT(*) FROM settings WHERE group_name = 'schedule_page'")->fetchColumn();
echo "Total schedule_page settings now: {$count}\n";
