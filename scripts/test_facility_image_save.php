<?php
/**
 * Diagnostic: directly update a facility's image_url to confirm the DB layer works.
 */
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

// Check which facility files exist in storage
$storageBase = __DIR__ . '/../storage/uploads';
$facilityFiles = [];
foreach (glob($storageBase . '/*/facility/*') as $f) {
    if (is_file($f)) {
        // Determine org id from path
        preg_match('#/uploads/(\d+)/facility/(.+)$#', $f, $m);
        if ($m) {
            $facilityFiles[] = ['org' => $m[1], 'filename' => $m[2], 'path' => 'uploads/' . $m[1] . '/facility/' . $m[2]];
        }
    }
}

echo "=== Files in storage/uploads/*/facility/ ===\n";
foreach ($facilityFiles as $f) {
    echo "  org={$f['org']} file={$f['filename']}\n";
}

// Directly test updating image_url on facility 5
$testUrl = '/storage/uploads/1/facility/test-diagnostic.jpg';
$stmt = $pdo->prepare('UPDATE facilities SET image_url = ?, updated_at = NOW() WHERE id = 5');
$ok = $stmt->execute([$testUrl]);
echo "\n=== Direct UPDATE test ===\n";
echo "  Rows affected: " . $stmt->rowCount() . "\n";

// Read it back
$row = $pdo->query("SELECT id, name, image_url FROM facilities WHERE id = 5")->fetch();
echo "  After update: image_url=" . ($row['image_url'] ?? '(null)') . "\n";

// Restore to null
$pdo->exec("UPDATE facilities SET image_url = NULL WHERE id = 5");
echo "  Restored to NULL\n";

echo "\n=== All facility image_url values ===\n";
foreach ($pdo->query("SELECT id, name, image_url FROM facilities ORDER BY id")->fetchAll() as $r) {
    echo "  ID={$r['id']} {$r['name']}: " . ($r['image_url'] ?? '(null)') . "\n";
}

echo "\nDone.\n";
