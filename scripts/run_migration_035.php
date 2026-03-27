<?php
/**
 * Migration 035: Dynamic Navigation, Organization Themes, Category View Settings
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$name = $_ENV['DB_NAME'] ?? $_ENV['DB_DATABASE'] ?? 'k2pickleball';
$user = $_ENV['DB_USER'] ?? $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? $_ENV['DB_PASSWORD'] ?? '';

echo "Migration 035: Dynamic Navigation, Organization Themes, Category View Settings\n";
echo str_repeat('=', 70) . "\n";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "[OK] Connected to database: $name\n";
} catch (PDOException $e) {
    die("[FAIL] Connection failed: " . $e->getMessage() . "\n");
}

$steps = [
    // 1. Navigation items table
    "CREATE TABLE IF NOT EXISTS `navigation_items` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `organization_id` BIGINT UNSIGNED NOT NULL,
        `parent_id` BIGINT UNSIGNED DEFAULT NULL,
        `label` VARCHAR(100) NOT NULL,
        `url` VARCHAR(500) DEFAULT NULL,
        `type` ENUM('link','page','category','dropdown','separator') NOT NULL DEFAULT 'link',
        `target` ENUM('_self','_blank') NOT NULL DEFAULT '_self',
        `icon` VARCHAR(100) DEFAULT NULL,
        `category_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Links to category for type=category',
        `is_system` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'System nav items cannot be deleted',
        `system_key` VARCHAR(50) DEFAULT NULL COMMENT 'home, schedule, book-court, about, contact',
        `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
        `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
        `visibility_rule` VARCHAR(50) DEFAULT NULL COMMENT 'always, auth_only, guest_only, has_memberships',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_nav_org` (`organization_id`),
        INDEX `idx_nav_parent` (`parent_id`),
        INDEX `idx_nav_sort` (`sort_order`),
        INDEX `idx_nav_visible` (`is_visible`),
        UNIQUE KEY `uk_nav_system` (`organization_id`, `system_key`),
        FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`parent_id`) REFERENCES `navigation_items`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    // 2. Category view settings table
    "CREATE TABLE IF NOT EXISTS `category_view_settings` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `category_id` BIGINT UNSIGNED NOT NULL,
        `organization_id` BIGINT UNSIGNED NOT NULL,
        `default_view` ENUM('week','month','today','list') NOT NULL DEFAULT 'week',
        `enabled_views` JSON NOT NULL DEFAULT (JSON_ARRAY('week','month','today','list')),
        `show_filters` TINYINT(1) NOT NULL DEFAULT 1,
        `show_category_filter` TINYINT(1) NOT NULL DEFAULT 0,
        `page_title` VARCHAR(200) DEFAULT NULL,
        `page_description` TEXT DEFAULT NULL,
        `page_hero_image` VARCHAR(500) DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `uk_cvs_cat` (`category_id`),
        INDEX `idx_cvs_org` (`organization_id`),
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
];

foreach ($steps as $i => $sql) {
    $label = $i + 1;
    try {
        $pdo->exec($sql);
        echo "[OK] Step {$label} completed\n";
    } catch (PDOException $e) {
        echo "[WARN] Step {$label}: " . $e->getMessage() . "\n";
    }
}

// 3. Add slug column to categories (safe: check if exists first)
try {
    $cols = $pdo->query("SHOW COLUMNS FROM `categories` LIKE 'slug'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE `categories` ADD COLUMN `slug` VARCHAR(150) DEFAULT NULL AFTER `name`");
        echo "[OK] Added slug column to categories\n";

        // Add unique key
        try {
            $pdo->exec("ALTER TABLE `categories` ADD UNIQUE KEY `uk_cat_slug_org` (`organization_id`, `slug`)");
            echo "[OK] Added unique index on categories.slug\n";
        } catch (PDOException $e) {
            echo "[WARN] Slug index: " . $e->getMessage() . "\n";
        }

        // Backfill slugs
        $pdo->exec("UPDATE `categories` SET `slug` = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(`name`, ' ', '-'), '&', 'and'), '/', '-'), '.', '')) WHERE `slug` IS NULL");
        echo "[OK] Backfilled category slugs\n";
    } else {
        echo "[SKIP] categories.slug column already exists\n";
    }
} catch (PDOException $e) {
    echo "[WARN] Category slug: " . $e->getMessage() . "\n";
}

// 4. Seed default navigation items for existing organizations
try {
    $orgs = $pdo->query("SELECT `id` FROM `organizations`")->fetchAll(PDO::FETCH_COLUMN);
    $seeded = 0;
    foreach ($orgs as $orgId) {
        // Check if nav items already exist for this org
        $existing = $pdo->prepare("SELECT COUNT(*) FROM `navigation_items` WHERE `organization_id` = ?");
        $existing->execute([$orgId]);
        if ((int) $existing->fetchColumn() > 0) {
            continue;
        }

        // Get "Book a Court" category ID for this org
        $catStmt = $pdo->prepare("SELECT `id` FROM `categories` WHERE `organization_id` = ? AND `system_slug` = 'book-a-court' LIMIT 1");
        $catStmt->execute([$orgId]);
        $courtCatId = $catStmt->fetchColumn() ?: null;

        $navItems = [
            ['label' => 'Home', 'url' => '/', 'type' => 'link', 'is_system' => 1, 'system_key' => 'home', 'sort_order' => 10],
            ['label' => 'Schedule', 'url' => '/schedule', 'type' => 'dropdown', 'is_system' => 1, 'system_key' => 'schedule', 'sort_order' => 20],
            ['label' => 'Book a Court', 'url' => '/book-court', 'type' => 'link', 'is_system' => 1, 'system_key' => 'book-court', 'sort_order' => 30, 'category_id' => $courtCatId],
            ['label' => 'Facilities', 'url' => '/facilities', 'type' => 'link', 'is_system' => 1, 'system_key' => 'facilities', 'sort_order' => 40],
            ['label' => 'Memberships', 'url' => '/memberships', 'type' => 'link', 'is_system' => 0, 'system_key' => 'memberships', 'sort_order' => 50, 'visibility_rule' => 'has_memberships'],
            ['label' => 'About', 'url' => '/about', 'type' => 'link', 'is_system' => 1, 'system_key' => 'about', 'sort_order' => 60],
            ['label' => 'Contact', 'url' => '/contact', 'type' => 'link', 'is_system' => 1, 'system_key' => 'contact', 'sort_order' => 70],
        ];

        $stmt = $pdo->prepare("INSERT INTO `navigation_items` (`organization_id`, `label`, `url`, `type`, `is_system`, `system_key`, `sort_order`, `category_id`, `visibility_rule`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        foreach ($navItems as $item) {
            $stmt->execute([
                $orgId,
                $item['label'],
                $item['url'],
                $item['type'],
                $item['is_system'],
                $item['system_key'],
                $item['sort_order'],
                $item['category_id'] ?? null,
                $item['visibility_rule'] ?? null,
            ]);
        }
        $seeded++;
    }
    echo "[OK] Seeded default navigation for {$seeded} organization(s)\n";
} catch (PDOException $e) {
    echo "[WARN] Nav seeding: " . $e->getMessage() . "\n";
}

// 5. Record migration
try {
    $pdo->exec("INSERT INTO `schema_migrations` (`migration`, `batch`, `executed_at`, `status`)
                VALUES ('035_dynamic_navigation_themes', 35, NOW(), 'success')
                ON DUPLICATE KEY UPDATE `executed_at` = NOW(), `status` = 'success'");
    echo "[OK] Migration 035 recorded\n";
} catch (PDOException $e) {
    echo "[WARN] Migration record: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "Migration 035 complete!\n";
