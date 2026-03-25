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

echo "=== Facilities table columns ===\n";
foreach ($pdo->query("DESCRIBE facilities")->fetchAll() as $col) {
    echo "  {$col['Field']} | {$col['Type']} | NULL:{$col['Null']} | Default:" . ($col['Default'] ?? 'NULL') . "\n";
}

echo "\n=== Triggers on facilities ===\n";
$triggers = $pdo->query("SHOW TRIGGERS LIKE 'facilities'")->fetchAll();
if (empty($triggers)) {
    echo "  (none)\n";
} else {
    foreach ($triggers as $t) {
        echo "  {$t['Trigger']}: {$t['Event']} {$t['Timing']}\n";
        echo "  Statement: {$t['Statement']}\n";
    }
}

echo "\n=== Test: PUT-style update with all fields including image_url ===\n";
// Simulate what FacilityController::update() would do
$testData = [
    'name' => 'Symrna',
    'tagline' => null,
    'slug' => 'symrna',
    'description' => null,
    'address_line1' => '123 Main St',
    'address_line2' => null,
    'city' => 'Smyrna',
    'state' => 'TN',
    'zip' => '37167',
    'country' => 'US',
    'latitude' => null,
    'longitude' => null,
    'phone' => null,
    'email' => null,
    'timezone' => 'America/Chicago',
    'tax_rate' => 0.00,
    'image_url' => '/storage/uploads/1/facility/36a9a49e-8f9f-4cd4-9d8f-2cfb6fe2400d.png',
    'settings' => '{"operating_hours":{"mon":"06:00-22:00"}}',
    'status' => 'active',
    'updated_at' => date('Y-m-d H:i:s'),
];

$setClauses = [];
$params = [];
foreach ($testData as $col => $val) {
    $setClauses[] = "`{$col}` = ?";
    $params[] = $val;
}
$params[] = 5; // facility id
$sql = "UPDATE `facilities` SET " . implode(', ', $setClauses) . " WHERE `id` = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
echo "  Rows affected: " . $stmt->rowCount() . "\n";

$row = $pdo->query("SELECT id, name, image_url FROM facilities WHERE id = 5")->fetch();
echo "  After update: image_url=" . ($row['image_url'] ?? '(null)') . "\n";

// Restore
$pdo->exec("UPDATE facilities SET image_url = NULL WHERE id = 5");
echo "  Restored to NULL\n";

echo "\nDone.\n";
