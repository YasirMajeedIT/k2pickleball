<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$c = require __DIR__ . '/../config/payments.php';
echo 'square.access_token exists: ' . (isset($c['square']['access_token']) ? 'YES' : 'NO') . PHP_EOL;
echo 'square.access_token value: ' . substr($c['square']['access_token'] ?? '', 0, 20) . '...' . PHP_EOL;
echo 'square.location_id: ' . ($c['square']['location_id'] ?? 'EMPTY') . PHP_EOL;
echo 'square.environment: ' . ($c['square']['environment'] ?? 'EMPTY') . PHP_EOL;
