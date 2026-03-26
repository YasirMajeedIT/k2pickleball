<?php
// Run migration 033: Membership renewal pricing fields
// Usage: php scripts/run_migration_033.php

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

$sql = file_get_contents(__DIR__ . '/../database/migrations/033_membership_renewal_pricing.sql');

// Remove comment lines before splitting
$lines = explode("\n", $sql);
$cleanLines = array_filter($lines, fn($l) => !str_starts_with(trim($l), '--'));
$cleanSql = implode("\n", $cleanLines);

$statements = array_filter(array_map('trim', explode(';', $cleanSql)), fn($s) => $s !== '');

foreach ($statements as $stmt) {
    try {
        $pdo->exec($stmt);
        echo "OK: " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 80) . "...\n";
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column')) {
            echo "SKIP (column already exists): " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 60) . "...\n";
        } else {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nDone.\n";
