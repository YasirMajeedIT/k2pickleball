<?php
/**
 * Run migration 025: Add system category support + seed "Book a Court" for all orgs.
 */

$config = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}",
        $config['user'],
        $config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Running migration 025: System categories...\n";

    // Step 1: Add columns (skip if already exist)
    $cols = array_column(
        $pdo->query('DESCRIBE categories')->fetchAll(PDO::FETCH_ASSOC),
        'Field'
    );

    if (!in_array('is_system', $cols)) {
        $pdo->exec("ALTER TABLE `categories`
            ADD COLUMN `is_system` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_taxable`,
            ADD COLUMN `system_slug` VARCHAR(50) DEFAULT NULL AFTER `is_system`,
            ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `system_slug`,
            ADD COLUMN `description` TEXT DEFAULT NULL AFTER `is_active`,
            ADD COLUMN `image_url` VARCHAR(500) DEFAULT NULL AFTER `description`");
        echo "  ✓ Added is_system, system_slug, is_active, description, image_url columns\n";
    } else {
        echo "  - Columns already exist, skipping\n";
    }

    if (!in_array('updated_at', $cols)) {
        $pdo->exec("ALTER TABLE `categories`
            ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
        echo "  ✓ Added updated_at column\n";
    }

    // Step 2: Add unique index on system_slug per org (if not exists)
    $indexes = $pdo->query("SHOW INDEX FROM `categories` WHERE Key_name = 'uk_cat_system_slug_org'")->fetchAll();
    if (empty($indexes)) {
        $pdo->exec("ALTER TABLE `categories` ADD UNIQUE KEY `uk_cat_system_slug_org` (`organization_id`, `system_slug`)");
        echo "  ✓ Added unique index on (organization_id, system_slug)\n";
    }

    // Step 3: Seed "Book a Court" system category for every org that doesn't have one
    $orgs = $pdo->query("SELECT `id` FROM `organizations` ORDER BY `id`")->fetchAll(PDO::FETCH_COLUMN);
    $inserted = 0;

    foreach ($orgs as $orgId) {
        // Check if already exists
        $exists = $pdo->prepare("SELECT `id` FROM `categories` WHERE `organization_id` = ? AND `system_slug` = 'book-a-court'");
        $exists->execute([$orgId]);
        if ($exists->fetch()) {
            echo "  - Org #{$orgId}: Book a Court category already exists\n";
            continue;
        }

        // Get next sort order
        $maxSort = $pdo->prepare("SELECT MAX(`sort_order`) FROM `categories` WHERE `organization_id` = ?");
        $maxSort->execute([$orgId]);
        $nextSort = ((int)$maxSort->fetchColumn()) + 1;

        // Generate UUID
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $stmt = $pdo->prepare(
            "INSERT INTO `categories` (`uuid`, `organization_id`, `name`, `color`, `sort_order`, `is_taxable`, `is_system`, `system_slug`, `is_active`, `description`, `created_at`)
             VALUES (?, ?, 'Book a Court', '#d4af37', ?, 0, 1, 'book-a-court', 1, 'Reserve a court for your group. Pick your date, time, and court — instant confirmation.', NOW())"
        );
        $stmt->execute([$uuid, $orgId, $nextSort]);
        $inserted++;
        echo "  ✓ Org #{$orgId}: Created 'Book a Court' system category\n";
    }

    echo "\n✓ Migration 025 complete. Seeded {$inserted} new system categories.\n";

} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
