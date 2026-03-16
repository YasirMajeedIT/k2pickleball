<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$db   = $_ENV['DB_DATABASE'] ?? 'k2pickleball';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$port = $_ENV['DB_PORT'] ?? '3306';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

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
