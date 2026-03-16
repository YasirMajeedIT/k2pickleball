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

echo "=== EXISTING TABLES ===" . PHP_EOL;
$tables = [];
foreach ($pdo->query('SHOW TABLES') as $row) {
    $tables[] = $row[0];
    echo $row[0] . PHP_EOL;
}

echo PHP_EOL . "=== CHECKING users COLUMNS ===" . PHP_EOL;
if (in_array('users', $tables)) {
    foreach ($pdo->query('DESCRIBE users') as $col) {
        echo $col['Field'] . ' - ' . $col['Type'] . PHP_EOL;
    }
}

echo PHP_EOL . "=== CHECKING email_verifications ===" . PHP_EOL;
if (in_array('email_verifications', $tables)) {
    echo "EXISTS" . PHP_EOL;
    foreach ($pdo->query('DESCRIBE email_verifications') as $col) {
        echo $col['Field'] . ' - ' . $col['Type'] . PHP_EOL;
    }
} else {
    echo "MISSING!" . PHP_EOL;
}

echo PHP_EOL . "=== CHECKING password_resets ===" . PHP_EOL;
if (in_array('password_resets', $tables)) {
    echo "EXISTS" . PHP_EOL;
} else {
    echo "MISSING!" . PHP_EOL;
}
