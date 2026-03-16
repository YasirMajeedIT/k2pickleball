<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';port=' . ($_ENV['DB_PORT'] ?? '3306') . ';dbname=' . $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("ALTER TABLE files MODIFY COLUMN context ENUM('avatar','document','logo','attachment','import','export','facility','general') DEFAULT 'attachment'");
echo "Files context enum updated.\n";
