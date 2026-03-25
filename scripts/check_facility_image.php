<?php
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();
$cfg = require __DIR__ . '/../config/database.php';

$pdo = new PDO(
    'mysql:host=' . $cfg['host'] . ';port=' . $cfg['port'] . ';dbname=' . $cfg['name'] . ';charset=utf8mb4',
    $cfg['user'],
    $cfg['pass']
);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

echo "=== Facilities (image_url) ===\n";
$rows = $pdo->query('SELECT id, name, image_url FROM facilities ORDER BY id')->fetchAll();
foreach ($rows as $r) {
    echo "  ID={$r['id']} name={$r['name']} image_url=" . ($r['image_url'] ?: '(null/empty)') . "\n";
}

echo "\n=== files.upload permission assignment ===\n";
$stmt = $pdo->query("
    SELECT p.slug, r.slug AS role, COUNT(rp.permission_id) AS cnt
    FROM permissions p
    LEFT JOIN role_permissions rp ON rp.permission_id = p.id
    LEFT JOIN roles r ON r.id = rp.role_id
    WHERE p.slug LIKE 'files.%'
    GROUP BY p.slug, r.slug
    ORDER BY p.slug, r.slug
");
foreach ($stmt->fetchAll() as $r) {
    echo "  perm={$r['slug']} role={$r['role']} count={$r['cnt']}\n";
}

echo "\n=== Uploads directory ===\n";
$uploadDir = __DIR__ . '/../storage/uploads';
if (is_dir($uploadDir)) {
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadDir));
    foreach ($iter as $f) {
        if ($f->isFile()) echo '  ' . $f->getPathname() . "\n";
    }
} else {
    echo "  (directory does not exist)\n";
}

echo "\nDone.\n";
