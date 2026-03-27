<?php
/**
 * Run Migration 036 — Custom Forms, Pages & Nav Fixes
 * Run from CLI: php scripts/run_migration_036.php
 * Or visit in browser (delete after running).
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "<pre style='font-family:monospace;background:#111;color:#0f0;padding:20px;'>";
echo "=== Migration 036: Custom Forms, Pages & Nav Fixes ===\n\n";

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

$sqlFile = dirname(__DIR__) . '/database/migrations/036_custom_forms_pages_nav_fixes.sql';
if (!file_exists($sqlFile)) {
    die("ERROR: Migration file not found at: {$sqlFile}\n");
}

$sql = file_get_contents($sqlFile);

$rawStatements = explode(';', $sql);
$statements = [];
foreach ($rawStatements as $chunk) {
    $lines = explode("\n", $chunk);
    $cleaned = [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '--')) continue;
        $cleaned[] = $line;
    }
    $stmt = trim(implode("\n", $cleaned));
    if ($stmt !== '') $statements[] = $stmt;
}

$success = 0;
$skipped = 0;
$errors  = 0;

foreach ($statements as $stmt) {
    $short = substr(preg_replace('/\s+/', ' ', $stmt), 0, 100);
    try {
        $pdo->exec($stmt);
        echo "[OK]   {$short}\n";
        $success++;
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if (str_contains($msg, 'already exists') || str_contains($msg, 'Duplicate')) {
            echo "[SKIP] {$short}\n       => {$msg}\n";
            $skipped++;
        } else {
            echo "[ERR]  {$short}\n       => {$msg}\n";
            $errors++;
        }
    }
}

echo "\n=== Done ===\n";
echo "Success: {$success} | Skipped: {$skipped} | Errors: {$errors}\n";
echo "</pre>";
