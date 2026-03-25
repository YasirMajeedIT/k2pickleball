<?php
/**
 * Fix: Restore Square Terminal POS extension and create missing table.
 * Run once, then safe to delete.
 */
$cfg = require dirname(__DIR__) . '/config/database.php';
$pdo = new PDO(
    'mysql:host=' . $cfg['host'] . ';port=' . $cfg['port'] . ';dbname=' . $cfg['name'] . ';charset=utf8mb4',
    $cfg['user'],
    $cfg['pass']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Create facility_extension_settings table if missing
echo "Ensuring facility_extension_settings table exists...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS `facility_extension_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organization_extension_id` BIGINT UNSIGNED NOT NULL,
    `facility_id` BIGINT UNSIGNED NOT NULL,
    `settings` JSON NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_fes_org_ext_facility` (`organization_extension_id`, `facility_id`),
    CONSTRAINT `fk_fes_org_ext` FOREIGN KEY (`organization_extension_id`) REFERENCES `organization_extensions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fes_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
echo "  ✓ facility_extension_settings table ensured.\n";

// 2. Insert Square Terminal POS extension if not present
echo "Checking for Square Terminal POS extension...\n";
$stmt = $pdo->prepare("SELECT id FROM extensions WHERE slug = ?");
$stmt->execute(['square-terminal-pos']);
$existingId = $stmt->fetchColumn();

if (!$existingId) {
    $ins = $pdo->prepare("INSERT INTO `extensions` 
        (`name`, `slug`, `description`, `version`, `category`, `icon`, `price_monthly`, `price_yearly`, `is_active`, `settings_schema`, `sort_order`, `created_at`, `updated_at`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $ins->execute([
        'Square Terminal POS',
        'square-terminal-pos',
        'Process payments using Square Terminal hardware devices. Supports device pairing, terminal checkouts, and per-facility terminal configuration.',
        '1.0.0',
        'payments',
        'credit-card',
        0.00,
        0.00,
        1,
        json_encode([
            'type' => 'object',
            'properties' => [
                'device_id' => [
                    'type' => 'string',
                    'title' => 'Terminal Device ID',
                    'description' => 'The paired Square Terminal device ID',
                ],
                'device_name' => [
                    'type' => 'string',
                    'title' => 'Terminal Name',
                    'description' => 'Friendly name for this terminal',
                ],
            ],
        ]),
        10,
    ]);
    echo "  ✓ Square Terminal POS extension inserted (id=" . $pdo->lastInsertId() . ").\n";
} else {
    // Make sure it's active
    $pdo->prepare("UPDATE extensions SET is_active = 1 WHERE id = ?")->execute([$existingId]);
    echo "  · Square Terminal POS already exists (id={$existingId}), ensured is_active=1.\n";
}

// 3. Verify final state
echo "\n--- Verification ---\n";
$rows = $pdo->query("SELECT id, name, slug, is_active FROM extensions ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    $status = $r['is_active'] ? 'ACTIVE' : 'INACTIVE';
    echo "  [{$status}] id={$r['id']} {$r['name']} ({$r['slug']})\n";
}

$t = $pdo->query("SHOW TABLES LIKE 'facility_extension_settings'")->fetchAll();
echo "\nfacility_extension_settings table: " . (count($t) > 0 ? "EXISTS ✓" : "MISSING ✗") . "\n";

echo "\nDone.\n";
