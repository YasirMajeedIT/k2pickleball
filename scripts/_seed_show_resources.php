<?php
/**
 * Seed show_resources and card_resource_ids settings for existing organizations.
 * Safe to run multiple times (INSERT IGNORE).
 */
$envFile = dirname(__DIR__) . '/.env';
$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    [$key, $val] = explode('=', $line, 2);
    $env[trim($key)] = trim($val, " \t\n\r\0\x0B\"'");
}

$pdo = new PDO(
    'mysql:host=' . ($env['DB_HOST'] ?? '127.0.0.1') . ';port=' . ($env['DB_PORT'] ?? '3306') . ';dbname=' . ($env['DB_NAME'] ?? 'k2pickleball') . ';charset=utf8mb4',
    $env['DB_USER'] ?? 'root',
    $env['DB_PASS'] ?? '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$seeds = [
    ['show_resources',   '0',  'boolean', 'Show assigned resources on calendar cards'],
    ['card_resource_ids', '[]', 'json',    'Resource IDs to display on calendar cards'],
];

foreach ($seeds as [$key, $val, $type, $desc]) {
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
         SELECT o.`id`, 'schedule_page', ?, ?, ?, ?, NOW(), NOW()
         FROM `organizations` o WHERE o.`status` IN ('active','trial')"
    );
    $stmt->execute([$key, $val, $type, $desc]);
    echo "  $key: " . $stmt->rowCount() . " org(s)\n";
}

$total = $pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn();
echo "Total resources in DB: $total\n";
