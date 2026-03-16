<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== PLANS ===" . PHP_EOL;
$r = $pdo->query('SELECT * FROM plans LIMIT 5');
foreach ($r as $row) {
    echo "  #" . $row['id'] . " " . $row['name'] . " monthly:$" . $row['price_monthly'] . " yearly:$" . ($row['price_yearly'] ?? 'N/A') . PHP_EOL;
}
echo PHP_EOL . "=== PLANS COLUMNS ===" . PHP_EOL;
foreach ($pdo->query('DESCRIBE plans') as $col) {
    echo "  {$col['Field']} - {$col['Type']}" . PHP_EOL;
}

echo PHP_EOL . "=== email_verifications TABLE ===" . PHP_EOL;
foreach ($pdo->query('DESCRIBE email_verifications') as $col) {
    echo "  {$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']}" . PHP_EOL;
}

echo PHP_EOL . "=== USERS (recent) ===" . PHP_EOL;
$r = $pdo->query('SELECT id, email, email_verified_at, google_id, status FROM users ORDER BY id DESC LIMIT 5');
foreach ($r as $row) {
    echo "  #{$row['id']} {$row['email']} verified:{$row['email_verified_at']} google:{$row['google_id']} status:{$row['status']}" . PHP_EOL;
}

echo PHP_EOL . "=== password_resets TABLE ===" . PHP_EOL;
foreach ($pdo->query('DESCRIBE password_resets') as $col) {
    echo "  {$col['Field']} - {$col['Type']}" . PHP_EOL;
}
