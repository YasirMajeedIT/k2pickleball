<?php
$cfg = require dirname(__DIR__) . '/config/database.php';
$pdo = new PDO(
    'mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['name'],
    $cfg['user'],
    $cfg['pass']
);

echo "=== PLANS TABLE ===\n";
$r = $pdo->query('DESCRIBE plans');
foreach ($r as $row) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\n=== PLAN DATA (first 3) ===\n";
$r = $pdo->query('SELECT id, slug, name, description, price_monthly, price_yearly, features, is_active FROM plans LIMIT 3');
foreach ($r as $row) {
    print_r($row);
}
