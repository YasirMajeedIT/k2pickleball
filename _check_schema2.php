<?php
require 'vendor/autoload.php';
$d = Dotenv\Dotenv::createImmutable(__DIR__); $d->load();
$pdo = new PDO('mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

echo "=== facilities ===\n";
$cols = $pdo->query('DESCRIBE facilities')->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) echo $c['Field'].' | '.$c['Type'].' | '.$c['Null'].' | '.$c['Default']."\n";

echo "\n=== organizations ===\n";
$cols = $pdo->query('DESCRIBE organizations')->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) echo $c['Field'].' | '.$c['Type'].' | '.$c['Null'].' | '.$c['Default']."\n";

echo "\n=== plans ===\n";
$r = $pdo->query('SELECT id,name,slug,price_monthly,price_yearly,is_active FROM plans LIMIT 5');
if($r) foreach($r->fetchAll(PDO::FETCH_ASSOC) as $row) echo json_encode($row)."\n";
else echo "No plans table\n";

echo "\n=== subscriptions ===\n";
$r = $pdo->query('SELECT s.id,s.organization_id,s.plan_id,s.status,p.name as plan_name FROM subscriptions s LEFT JOIN plans p ON p.id=s.plan_id LIMIT 5');
if($r) foreach($r->fetchAll(PDO::FETCH_ASSOC) as $row) echo json_encode($row)."\n";
else echo "No subscriptions\n";

echo "\n=== discount tables ===\n";
$tables = $pdo->query("SHOW TABLES LIKE '%discount%'")->fetchAll(PDO::FETCH_COLUMN);
echo "Discount tables: ".json_encode($tables)."\n";
$tables2 = $pdo->query("SHOW TABLES LIKE '%coupon%'")->fetchAll(PDO::FETCH_COLUMN);
echo "Coupon tables: ".json_encode($tables2)."\n";
