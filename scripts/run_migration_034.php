<?php

declare(strict_types=1);

/**
 * Run Migration 034 — Booking Invoices Module
 * Creates: booking_invoices, booking_invoice_items, booking_invoice_payments
 */

$env = [];
foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\r\n\"'");
}

// Support both naming conventions (DB_NAME/DB_USER/DB_PASS and DB_DATABASE/DB_USERNAME/DB_PASSWORD)
$host   = $env['DB_HOST']     ?? 'localhost';
$dbName = $env['DB_NAME']     ?? ($env['DB_DATABASE'] ?? 'k2pickleball');
$user   = $env['DB_USER']     ?? ($env['DB_USERNAME'] ?? 'root');
$pass   = $env['DB_PASS']     ?? ($env['DB_PASSWORD'] ?? '');

$dsn = 'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4';
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
echo "Connected to: $dbName\n";

$sql = file_get_contents(__DIR__ . '/../database/migrations/034_create_booking_invoices.sql');

// Strip -- comment lines before splitting on semicolons
$lines      = explode("\n", $sql);
$cleanLines = array_filter($lines, fn($l) => !str_starts_with(trim($l), '--'));
$cleanSql   = implode("\n", $cleanLines);

$statements = array_filter(array_map('trim', explode(';', $cleanSql)), fn($s) => $s !== '');

foreach ($statements as $stmt) {
    try {
        $pdo->exec($stmt);
        if (preg_match('/CREATE TABLE IF NOT EXISTS `([^`]+)`/i', $stmt, $m)) {
            echo "Created table: {$m[1]}\n";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "Statement: " . substr($stmt, 0, 120) . "...\n";
    }
}

echo "Done.\n";
