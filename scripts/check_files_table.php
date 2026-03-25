<?php
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();
$cfg = require __DIR__ . '/../config/database.php';

$pdo = new PDO(
    'mysql:host=' . $cfg['host'] . ';port=' . $cfg['port'] . ';dbname=' . $cfg['name'] . ';charset=utf8mb4',
    $cfg['user'],
    $cfg['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

echo "=== Files table (recent uploads) ===\n";
$rows = $pdo->query("SELECT id, organization_id, context, path, created_at FROM files ORDER BY created_at DESC LIMIT 20")->fetchAll();
foreach ($rows as $r) {
    echo "  ID={$r['id']} org={$r['organization_id']} ctx={$r['context']} path={$r['path']} at={$r['created_at']}\n";
}

echo "\n=== File records for facility context ===\n";
$rows = $pdo->query("SELECT id, organization_id, path, created_at FROM files WHERE context='facility' ORDER BY created_at DESC")->fetchAll();
foreach ($rows as $r) {
    echo "  ID={$r['id']} org={$r['organization_id']} path={$r['path']} at={$r['created_at']}\n";
    // Check if the physical file exists
    $physPath = __DIR__ . '/../storage/' . $r['path'];
    echo "    Physical file: " . (file_exists($physPath) ? 'EXISTS' : 'MISSING') . "\n";
}

echo "\n=== Orphaned files (in storage but NOT in DB) ===\n";
$storageBase = __DIR__ . '/../storage/uploads';
foreach (glob($storageBase . '/*/facility/*') as $f) {
    if (!is_file($f)) continue;
    $rel = str_replace(__DIR__ . '/../storage/', '', $f);
    $rel = str_replace('\\', '/', $rel);
    $row = $pdo->prepare("SELECT id FROM files WHERE path = ?")->execute([$rel]);
    $found = $pdo->query("SELECT id FROM files WHERE path = " . $pdo->quote($rel))->fetch();
    if (!$found) {
        echo "  ORPHAN: $rel\n";
    }
}

echo "\nDone.\n";
