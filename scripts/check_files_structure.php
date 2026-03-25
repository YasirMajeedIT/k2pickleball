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

echo "=== Files table structure ===\n";
foreach ($pdo->query("DESCRIBE files")->fetchAll() as $col) {
    echo "  {$col['Field']} | {$col['Type']} | NULL:{$col['Null']} | Key:{$col['Key']} | Default:" . ($col['Default'] ?? 'NULL') . "\n";
}

echo "\n=== Files table foreign keys ===\n";
$fks = $pdo->query("
    SELECT kcu.COLUMN_NAME, kcu.REFERENCED_TABLE_NAME, kcu.REFERENCED_COLUMN_NAME, rc.DELETE_RULE
    FROM information_schema.KEY_COLUMN_USAGE kcu
    JOIN information_schema.REFERENTIAL_CONSTRAINTS rc ON rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME AND rc.CONSTRAINT_SCHEMA = kcu.TABLE_SCHEMA
    WHERE kcu.TABLE_SCHEMA = DATABASE() AND kcu.TABLE_NAME = 'files'
")->fetchAll();
if (empty($fks)) {
    echo "  (none)\n";
} else {
    foreach ($fks as $fk) {
        echo "  {$fk['COLUMN_NAME']} → {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']} (on delete: {$fk['DELETE_RULE']})\n";
    }
}

echo "\n=== Test INSERT into files ===\n";
try {
    $pdo->exec("
        INSERT INTO files (uuid, organization_id, user_id, name, original_name, mime_type, size, path, disk, context, created_at, updated_at)
        VALUES ('test-uuid-123', 1, 1, 'test.jpg', 'test.jpg', 'image/jpeg', 1000, 'uploads/1/facility/test.jpg', 'local', 'facility', NOW(), NOW())
    ");
    $lastId = $pdo->lastInsertId();
    echo "  INSERT OK, id=$lastId\n";
    
    // Check if it's readable
    $row = $pdo->query("SELECT id, path FROM files WHERE id = $lastId")->fetch();
    echo "  Read back: id={$row['id']} path={$row['path']}\n";
    
    // Clean up
    $pdo->exec("DELETE FROM files WHERE id = $lastId");
    echo "  Cleaned up\n";
} catch (PDOException $e) {
    echo "  INSERT FAILED: " . $e->getMessage() . "\n";
}

echo "\n=== Does files table have AUTO_INCREMENT? ===\n";
$status = $pdo->query("SHOW TABLE STATUS LIKE 'files'")->fetch();
echo "  Auto_increment: " . ($status['Auto_increment'] ?? 'n/a') . "\n";
echo "  Engine: " . ($status['Engine'] ?? 'n/a') . "\n";

echo "\nDone.\n";
