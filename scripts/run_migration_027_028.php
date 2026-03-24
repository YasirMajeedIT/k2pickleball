<?php
/**
 * Run migrations 027 and 028.
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$db   = $_ENV['DB_NAME'] ?? 'k2pickleball';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $files = [
        __DIR__ . '/../database/migrations/027_create_contact_submissions.sql',
        __DIR__ . '/../database/migrations/028_create_site_settings.sql',
    ];

    foreach ($files as $file) {
        echo "Running " . basename($file) . "...\n";
        $sql = file_get_contents($file);
        $pdo->exec($sql);
        echo "  OK\n";
    }

    echo "\nDone. Verifying tables:\n";
    foreach (['contact_submissions', 'site_settings'] as $t) {
        $stmt = $pdo->query("SELECT COUNT(*) as c FROM $t");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  $t: {$row['c']} rows\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
