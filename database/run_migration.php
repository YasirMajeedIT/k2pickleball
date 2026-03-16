<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = file_get_contents(__DIR__ . '/migrations/add_email_verification_google_oauth.sql');
$statements = array_filter(array_map('trim', explode(';', $sql)));
foreach ($statements as $stmt) {
    if ($stmt && !str_starts_with(ltrim($stmt), '--')) {
        try {
            $pdo->exec($stmt);
            echo 'OK: ' . substr(preg_replace('/\s+/', ' ', $stmt), 0, 80) . PHP_EOL;
        } catch (PDOException $e) {
            echo 'ERR: ' . $e->getMessage() . ' | ' . substr($stmt, 0, 60) . PHP_EOL;
        }
    }
}
echo 'Migration complete.' . PHP_EOL;
