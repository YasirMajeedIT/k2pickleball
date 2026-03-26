<?php
// Run migration 032: Create membership plan tables
// Usage: php scripts/run_migration_032.php

$env = [];
foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\r\n\"'");
}

$dsn = 'mysql:host=' . $env['DB_HOST'] . ';dbname=' . $env['DB_NAME'] . ';charset=utf8mb4';
$pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
echo "Connected to: {$env['DB_NAME']}\n\n";

$sql = file_get_contents(__DIR__ . '/../database/migrations/032_create_membership_plans.sql');
$statements = array_filter(array_map('trim', explode(';', $sql)), fn($s) => $s !== '' && !str_starts_with($s, '--'));

foreach ($statements as $stmt) {
    try {
        $pdo->exec($stmt);
        // Extract table name
        if (preg_match('/CREATE TABLE.*?`(\w+)`/i', $stmt, $m)) {
            echo "Created table: {$m[1]}\n";
        }
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'already exists')) {
            if (preg_match('/CREATE TABLE.*?`(\w+)`/i', $stmt, $m)) {
                echo "Table already exists: {$m[1]} (skipped)\n";
            }
        } else {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nDone.\n";
