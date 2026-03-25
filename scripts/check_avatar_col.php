<?php
require __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(dirname(__DIR__))->load();
$pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
$stmt = $pdo->query("SHOW COLUMNS FROM players LIKE 'avatar_url'");
$r = $stmt->fetch(PDO::FETCH_ASSOC);
echo $r ? 'avatar_url EXISTS: ' . $r['Type'] . PHP_EOL : 'avatar_url MISSING' . PHP_EOL;
