<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';port=' . ($_ENV['DB_PORT'] ?? '3306') . ';dbname=' . $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Gift Certificates ===\n";
$stmt = $pdo->query('SELECT id, certificate_name, code, status, organization_id FROM gift_certificates LIMIT 10');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo "  (none)\n";
} else {
    foreach ($rows as $r) {
        echo "  id={$r['id']} name={$r['certificate_name']} code={$r['code']} status={$r['status']} org_id={$r['organization_id']}\n";
    }
}

echo "\n=== gift_certificate_usage table ===\n";
$stmt = $pdo->query('DESCRIBE gift_certificate_usage');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "  {$col['Field']} | {$col['Type']} | NULL:{$col['Null']}\n";
}
