<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Bootstrap the app
$_SERVER['REQUEST_URI'] = '/api/gift-certificates/1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'k2pickleball.local';
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Simulate what the GiftCertificateController::show() does directly
$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';port=' . ($_ENV['DB_PORT'] ?? '3306') . ';dbname=' . $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Simulate findWithUsages(1)
echo "=== findById(1) ===\n";
$stmt = $pdo->prepare("SELECT * FROM gift_certificates WHERE id = ?");
$stmt->execute([1]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);
if ($record) {
    foreach ($record as $k => $v) {
        echo "  $k = " . var_export($v, true) . "\n";
    }
    echo "\n=== usages ===\n";
    $stmt2 = $pdo->prepare("SELECT * FROM gift_certificate_usage WHERE gift_certificate_id = ? ORDER BY usage_date DESC");
    $stmt2->execute([1]);
    $usages = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "  Count: " . count($usages) . "\n";
} else {
    echo "  NOT FOUND\n";
}
