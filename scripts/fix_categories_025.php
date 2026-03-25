<?php
// Fix migration 025: add missing columns to categories table
// Also clears stale rate_limit entries

// Parse .env manually (parse_ini_file chokes on URLs/parens in comments)
$env = [];
foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\r\n\"'");
}
if (empty($env)) {
    die("Could not read .env file\n");
}

$dsn = 'mysql:host=' . $env['DB_HOST'] . ';dbname=' . $env['DB_NAME'] . ';charset=utf8mb4';
try {
    $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage() . "\n");
}

echo "Connected to: " . $env['DB_NAME'] . "\n\n";

// Add missing columns from migration 025
$columns = [
    'is_system'   => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_taxable`",
    'system_slug' => "VARCHAR(50) DEFAULT NULL AFTER `is_system`",
    'is_active'   => "TINYINT(1) NOT NULL DEFAULT 1 AFTER `system_slug`",
    'description' => "TEXT DEFAULT NULL AFTER `is_active`",
    'image_url'   => "VARCHAR(500) DEFAULT NULL AFTER `description`",
];

foreach ($columns as $col => $def) {
    try {
        $pdo->exec("ALTER TABLE `categories` ADD COLUMN `$col` $def");
        echo "Added column: $col\n";
    } catch (PDOException $e) {
        echo "Skipped $col: " . $e->getMessage() . "\n";
    }
}

// Add unique key
try {
    $pdo->exec("ALTER TABLE `categories` ADD UNIQUE KEY `uk_cat_system_slug_org` (`organization_id`, `system_slug`)");
    echo "Added unique key: uk_cat_system_slug_org\n";
} catch (PDOException $e) {
    echo "Skipped key: " . $e->getMessage() . "\n";
}

// Clear stale rate limits
$deleted = $pdo->exec("DELETE FROM `rate_limits`");
echo "\nCleared $deleted row(s) from rate_limits table\n";

echo "\nDone! You can now try registering again.\n";
