<?php

declare(strict_types=1);

/**
 * Run Migration 034 — Booking Invoices Module
 * Creates: booking_invoices, booking_invoice_items, booking_invoice_payments
 */

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v);
    }
}

$host   = $_ENV['DB_HOST']     ?? 'localhost';
$dbName = $_ENV['DB_DATABASE'] ?? 'k2pickleball';
$user   = $_ENV['DB_USERNAME'] ?? 'root';
$pass   = $_ENV['DB_PASSWORD'] ?? '';

$conn = new mysqli($host, $user, $pass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}
echo "Connected to: $dbName\n";

$sql = file_get_contents(__DIR__ . '/../database/migrations/034_create_booking_invoices.sql');

// Strip -- comment lines before splitting on semicolons
$lines      = explode("\n", $sql);
$cleanLines = array_filter($lines, fn($l) => !str_starts_with(trim($l), '--'));
$cleanSql   = implode("\n", $cleanLines);

$statements = array_filter(array_map('trim', explode(';', $cleanSql)), fn($s) => $s !== '');

foreach ($statements as $stmt) {
    if ($conn->query($stmt) === true) {
        // Detect table name from CREATE TABLE statement
        if (preg_match('/CREATE TABLE IF NOT EXISTS `([^`]+)`/i', $stmt, $m)) {
            echo "Created table: {$m[1]}\n";
        }
    } else {
        echo "Error: " . $conn->error . "\n";
        echo "Statement: " . substr($stmt, 0, 120) . "...\n";
    }
}

$conn->close();
echo "Done.\n";
