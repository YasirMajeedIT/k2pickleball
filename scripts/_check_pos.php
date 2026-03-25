<?php
/**
 * Temporary diagnostic script - check POS extension state in DB.
 * Safe to delete after investigation.
 */
$cfg = require dirname(__DIR__) . '/config/database.php';
$pdo = new PDO(
    'mysql:host=' . $cfg['host'] . ';port=' . $cfg['port'] . ';dbname=' . $cfg['name'] . ';charset=utf8mb4',
    $cfg['user'],
    $cfg['pass']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Check extensions table
$tables = $pdo->query("SHOW TABLES LIKE 'extensions'")->fetchAll();
echo "extensions table exists: " . (count($tables) > 0 ? 'YES' : 'NO') . PHP_EOL;

if (count($tables) > 0) {
    $rows = $pdo->query("SELECT id, name, slug, is_active FROM extensions")->fetchAll(PDO::FETCH_ASSOC);
    echo "Extensions rows:\n";
    foreach ($rows as $r) {
        echo "  id={$r['id']} name={$r['name']} slug={$r['slug']} is_active={$r['is_active']}\n";
    }
    if (empty($rows)) {
        echo "  (none)\n";
    }
} else {
    echo "  extensions table NOT found!\n";
}

// 2. Check organization_extensions table
$t2 = $pdo->query("SHOW TABLES LIKE 'organization_extensions'")->fetchAll();
echo "\norganization_extensions table exists: " . (count($t2) > 0 ? 'YES' : 'NO') . PHP_EOL;
if (count($t2) > 0) {
    $r2 = $pdo->query("SELECT * FROM organization_extensions")->fetchAll(PDO::FETCH_ASSOC);
    echo "Org extension rows:\n";
    foreach ($r2 as $r) {
        echo "  " . json_encode($r) . "\n";
    }
    if (empty($r2)) {
        echo "  (none)\n";
    }
}

// 3. Check facility_extension_settings
$t3 = $pdo->query("SHOW TABLES LIKE 'facility_extension_settings'")->fetchAll();
echo "\nfacility_extension_settings table exists: " . (count($t3) > 0 ? 'YES' : 'NO') . PHP_EOL;
if (count($t3) > 0) {
    $r3 = $pdo->query("SELECT * FROM facility_extension_settings")->fetchAll(PDO::FETCH_ASSOC);
    echo "Facility settings rows:\n";
    foreach ($r3 as $r) {
        echo "  " . json_encode($r) . "\n";
    }
    if (empty($r3)) {
        echo "  (none)\n";
    }
}

// 4. Check .installed lock file
echo "\n.installed lock file: " . (file_exists(dirname(__DIR__) . '/storage/.installed') ? 'EXISTS' : 'NOT FOUND') . PHP_EOL;
