<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbName = $_ENV['DB_NAME'] ?? 'k2pickleball';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$port = $_ENV['DB_PORT'] ?? '3306';

$pdo = new PDO(
    "mysql:host={$host};port={$port};dbname={$dbName}",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = file_get_contents(__DIR__ . '/../database/migrations/015_add_image_url_and_discounts.sql');

$sql = preg_replace('/--.*$/m', '', $sql);
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $stmt) {
    if (empty($stmt)) continue;
    echo "Executing: " . substr($stmt, 0, 80) . "...\n";
    $pdo->exec($stmt);
    echo "  OK\n";
}

echo "\nMigration 015 complete.\n";
