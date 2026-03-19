<?php
require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}",
        $config['user'], $config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Running migration 012: Rename additional_tagline to internal_title...\n";
    $sql = file_get_contents(__DIR__ . '/../database/migrations/012_rename_tagline_to_internal_title.sql');

    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        if ($stmt) {
            $pdo->exec($stmt);
            echo "  Executed: " . substr($stmt, 0, 80) . "...\n";
        }
    }

    echo "Migration 012 completed successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
