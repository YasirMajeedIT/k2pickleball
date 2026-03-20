<?php
/**
 * Run migration 026: Create consultations table.
 */
$cfg = require dirname(__DIR__) . '/config/database.php';
$pdo = new PDO(
    "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset={$cfg['charset']}",
    $cfg['user'],
    $cfg['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = file_get_contents(__DIR__ . '/../database/migrations/026_create_consultations.sql');
$pdo->exec($sql);
echo "Migration 026 applied successfully.\n";
