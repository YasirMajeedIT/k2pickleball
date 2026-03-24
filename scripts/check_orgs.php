<?php
define('K2_ROOT', dirname(__DIR__));
require K2_ROOT . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(K2_ROOT);
$dotenv->load();

$db = new PDO(
    'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'k2pickleball') . ';charset=utf8mb4',
    $_ENV['DB_USER'] ?? 'root',
    $_ENV['DB_PASS'] ?? ''
);

echo "=== ORGANIZATIONS ===\n";
$orgs = $db->query('SELECT id, name, slug, status FROM organizations ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
if (empty($orgs)) {
    echo "NO ORGANIZATIONS FOUND\n";
} else {
    foreach ($orgs as $o) {
        echo "ID:{$o['id']} | slug:{$o['slug']} | status:{$o['status']} | name:{$o['name']}\n";
    }
}

echo "\n=== ORGANIZATION_DOMAINS ===\n";
$domains = $db->query('SELECT * FROM organization_domains')->fetchAll(PDO::FETCH_ASSOC);
if (empty($domains)) { echo "NONE\n"; } else { foreach ($domains as $d) echo json_encode($d) . "\n"; }
