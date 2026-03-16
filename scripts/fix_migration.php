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

// 1. Add google_id column if missing
$cols = [];
foreach ($pdo->query('DESCRIBE users') as $c) $cols[] = $c['Field'];

if (!in_array('google_id', $cols)) {
    $pdo->exec("ALTER TABLE `users` ADD COLUMN `google_id` VARCHAR(255) NULL DEFAULT NULL AFTER `email_verified_at`");
    echo "Added google_id column to users" . PHP_EOL;
} else {
    echo "google_id already exists" . PHP_EOL;
}

// Add index on google_id if not exists
try {
    $pdo->exec("ALTER TABLE `users` ADD INDEX `idx_users_google_id` (`google_id`)");
    echo "Added index on google_id" . PHP_EOL;
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "google_id index already exists" . PHP_EOL;
    } else {
        echo "Index error: " . $e->getMessage() . PHP_EOL;
    }
}

// 2. Create email_verifications table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `email_verifications` (
        `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `email`      VARCHAR(255) NOT NULL,
        `token_hash` VARCHAR(64)  NOT NULL,
        `expires_at` DATETIME     NOT NULL,
        `used_at`    DATETIME     NULL DEFAULT NULL,
        `created_at` DATETIME     NOT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_email_verifications_email` (`email`),
        KEY `idx_email_verifications_token` (`token_hash`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "email_verifications table created (or already exists)" . PHP_EOL;

// 3. Mark existing active users as verified
$count = $pdo->exec("UPDATE `users` SET `email_verified_at` = `created_at` WHERE `email_verified_at` IS NULL AND `status` = 'active'");
echo "Marked $count existing active users as email-verified" . PHP_EOL;

// Verify
echo PHP_EOL . "=== VERIFICATION ===" . PHP_EOL;
foreach ($pdo->query('DESCRIBE users') as $c) echo $c['Field'] . ' - ' . $c['Type'] . PHP_EOL;
echo PHP_EOL;
$tables = $pdo->query("SHOW TABLES LIKE 'email_verifications'")->rowCount();
echo "email_verifications exists: " . ($tables > 0 ? 'YES' : 'NO') . PHP_EOL;
