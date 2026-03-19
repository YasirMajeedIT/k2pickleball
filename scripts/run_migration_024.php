<?php
/**
 * Run migration 024: Create court_bookings table
 */
require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/database.php';

$pdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}",
    $config['user'],
    $config['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = file_get_contents(__DIR__ . '/../database/migrations/024_create_court_bookings.sql');

echo "Running migration 024: Create court_bookings table...\n";

try {
    $pdo->exec($sql);
    echo "✓ court_bookings table created successfully.\n";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'already exists')) {
        echo "⚠ Table already exists, skipping.\n";
    } else {
        echo "✗ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "Done.\n";
