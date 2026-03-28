<?php
/**
 * Migration: Clean up navigation items
 * - Remove "Book a Court" system nav item
 * - Change Schedule from dropdown to link type
 */
require __DIR__ . '/../vendor/autoload.php';
$cfg = require __DIR__ . '/../config/database.php';
$pdo = new PDO(
    'mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['name'] . ';charset=' . $cfg['charset'],
    $cfg['user'],
    $cfg['pass'],
    $cfg['options'] ?? []
);

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
