<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();
$cfg = require __DIR__ . '/../config/database.php';
$db = new PDO('mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['name'], $cfg['user'], $cfg['pass']);

echo "=== PERMISSIONS IN DB ===" . PHP_EOL;
$perms = $db->query('SELECT slug FROM permissions ORDER BY slug')->fetchAll(PDO::FETCH_COLUMN);
echo implode(PHP_EOL, $perms) . PHP_EOL;

echo PHP_EOL . "=== ROLE PERMISSION COUNTS ===" . PHP_EOL;
$rows = $db->query('SELECT r.slug, COUNT(rp.permission_id) as cnt FROM roles r LEFT JOIN role_permissions rp ON r.id = rp.role_id GROUP BY r.slug ORDER BY r.slug')->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['slug'] . ': ' . $r['cnt'] . PHP_EOL;
}

// Check which config permissions are missing from DB
echo PHP_EOL . "=== MISSING FROM DB ===" . PHP_EOL;
$configPerms = array_keys(require __DIR__ . '/../config/permissions.php'['permissions'] ?? []);
$cfg2 = require __DIR__ . '/../config/permissions.php';
$configPerms = array_keys($cfg2['permissions']);
$dbPerms = array_flip($perms);
foreach ($configPerms as $slug) {
    if (!isset($dbPerms[$slug])) {
        echo "MISSING: $slug" . PHP_EOL;
    }
}
