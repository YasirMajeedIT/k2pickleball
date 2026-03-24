<?php
require dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$cfg = require dirname(__DIR__) . '/config/database.php';
$dsn = 'mysql:host=' . $cfg['host'] . ';port=' . $cfg['port'] . ';dbname=' . $cfg['name'] . ';charset=utf8mb4';
$pdo = new PDO($dsn, $cfg['user'], $cfg['pass']);
$pdo->exec("SET time_zone = '+00:00'");

// Check organizations table schema
$cols = $pdo->query('DESCRIBE organizations')->fetchAll(PDO::FETCH_ASSOC);
echo "Organizations table:" . PHP_EOL;
foreach ($cols as $c) {
    echo "  {$c['Field']} {$c['Type']} null={$c['Null']} key={$c['Key']} default={$c['Default']}" . PHP_EOL;
}

// Check roles for org-owner
echo PHP_EOL . "Roles containing 'owner' or 'admin':" . PHP_EOL;
$roles = $pdo->query("SELECT id, slug, name, organization_id FROM roles WHERE slug LIKE '%owner%' OR slug LIKE '%admin%' ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($roles as $r) {
    echo "  id={$r['id']} slug={$r['slug']} name={$r['name']} org_id=" . ($r['organization_id'] ?? 'null') . PHP_EOL;
}

