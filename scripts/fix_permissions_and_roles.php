<?php
/**
 * Fix Permissions, Roles & Admin user role assignment
 * 
 * This script:
 * 1. Seeds ALL permissions from config/permissions.php (INSERT IGNORE to avoid duplicates)
 * 2. Ensures system roles exist
 * 3. Assigns super-admin role to the admin user in user_roles
 * 4. Maps all role_permissions for each role based on config
 * 5. Fixes the files.context ENUM to include 'facility' and 'general'
 *
 * Usage: php scripts/fix_permissions_and_roles.php
 * On server: /usr/local/lsws/lsphp82/bin/php scripts/fix_permissions_and_roles.php
 */

if (php_sapi_name() !== 'cli') {
    die('CLI only.');
}

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$db   = $_ENV['DB_NAME'] ?? 'k2pickleball';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

echo "=== K2 Pickleball — Fix Permissions, Roles & Files ENUM ===\n\n";

// Load permissions config
$config = require dirname(__DIR__) . '/config/permissions.php';

// ──────────────────────────────────────────────
// 1. Seed ALL permissions
// ──────────────────────────────────────────────
echo "1. Seeding all permissions...\n";
$permStmt = $pdo->prepare(
    "INSERT IGNORE INTO permissions (name, slug, module, description, created_at) VALUES (:name, :slug, :module, :description, NOW())"
);

$count = 0;
foreach ($config['permissions'] as $slug => $meta) {
    $name = ucwords(str_replace(['.', '_'], [' — ', ' '], $slug));
    $permStmt->execute([
        'name' => $name,
        'slug' => $slug,
        'module' => $meta['module'],
        'description' => $meta['description'],
    ]);
    if ($permStmt->rowCount() > 0) {
        $count++;
        echo "   + Added: $slug\n";
    }
}
echo "   Total new permissions inserted: $count\n\n";

// Build permission slug -> id map
$allPerms = $pdo->query("SELECT id, slug FROM permissions")->fetchAll();
$permMap = array_column($allPerms, 'id', 'slug');

// ──────────────────────────────────────────────
// 2. Ensure system roles exist
// ──────────────────────────────────────────────
echo "2. Ensuring system roles exist...\n";
$roleStmt = $pdo->prepare(
    "INSERT IGNORE INTO roles (name, slug, description, is_system, created_at, updated_at) VALUES (:name, :slug, :description, 1, NOW(), NOW())"
);

foreach ($config['roles'] as $slug => $meta) {
    // Use both underscore and hyphen slug formats for compatibility
    $hyphenSlug = str_replace('_', '-', $slug);
    
    // Check if role exists with either slug format
    $existing = $pdo->prepare("SELECT id, slug FROM roles WHERE slug IN (?, ?) AND (organization_id IS NULL) LIMIT 1");
    $existing->execute([$slug, $hyphenSlug]);
    $existingRole = $existing->fetch();
    
    if (!$existingRole) {
        $roleStmt->execute([
            'name' => $meta['name'],
            'slug' => $hyphenSlug,
            'description' => $meta['description'],
        ]);
        echo "   + Created role: {$meta['name']} ($hyphenSlug)\n";
    } else {
        echo "   = Role exists: {$meta['name']} ({$existingRole['slug']})\n";
    }
}
echo "\n";

// ──────────────────────────────────────────────
// 3. Remove duplicate roles (keep lowest ID per slug)
// ──────────────────────────────────────────────
echo "3. Checking for duplicate roles...\n";
$dupes = $pdo->query(
    "SELECT slug, organization_id, COUNT(*) as cnt, MIN(id) as keep_id 
     FROM roles 
     GROUP BY slug, COALESCE(organization_id, 0) 
     HAVING cnt > 1"
)->fetchAll();

if (!empty($dupes)) {
    foreach ($dupes as $d) {
        $orgCond = $d['organization_id'] ? "AND organization_id = {$d['organization_id']}" : "AND organization_id IS NULL";
        $deleted = $pdo->exec(
            "DELETE FROM roles WHERE slug = " . $pdo->quote($d['slug']) . " $orgCond AND id > {$d['keep_id']}"
        );
        echo "   - Removed $deleted duplicate(s) of role '{$d['slug']}'\n";
    }
} else {
    echo "   No duplicates found.\n";
}
echo "\n";

// ──────────────────────────────────────────────
// 4. Map role_permissions for all system roles
// ──────────────────────────────────────────────
echo "4. Mapping role permissions...\n";
$rpStmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id, created_at) VALUES (:role_id, :permission_id, NOW())");

foreach ($config['role_permissions'] as $roleSlug => $perms) {
    $hyphenSlug = str_replace('_', '-', $roleSlug);
    
    // Find role by either slug format (system roles, no org)
    $role = $pdo->prepare("SELECT id, slug FROM roles WHERE slug IN (?, ?) AND (organization_id IS NULL) LIMIT 1");
    $role->execute([$roleSlug, $hyphenSlug]);
    $roleRow = $role->fetch();
    
    if (!$roleRow) {
        echo "   ! Role not found: $roleSlug — skipping\n";
        continue;
    }
    
    $roleId = (int) $roleRow['id'];
    $assignedCount = 0;
    
    if ($perms === ['*']) {
        // Super admin — assign ALL permissions
        foreach ($permMap as $permSlug => $permId) {
            $rpStmt->execute(['role_id' => $roleId, 'permission_id' => $permId]);
            if ($rpStmt->rowCount() > 0) $assignedCount++;
        }
    } else {
        foreach ($perms as $permPattern) {
            if (str_ends_with($permPattern, '.*')) {
                // Wildcard: assign all permissions for this module
                $module = substr($permPattern, 0, -2);
                foreach ($permMap as $pSlug => $pId) {
                    if (str_starts_with($pSlug, $module . '.')) {
                        $rpStmt->execute(['role_id' => $roleId, 'permission_id' => $pId]);
                        if ($rpStmt->rowCount() > 0) $assignedCount++;
                    }
                }
            } elseif (isset($permMap[$permPattern])) {
                $rpStmt->execute(['role_id' => $roleId, 'permission_id' => $permMap[$permPattern]]);
                if ($rpStmt->rowCount() > 0) $assignedCount++;
            }
        }
    }
    
    echo "   Role '{$roleRow['slug']}': $assignedCount new permission mappings added\n";
}
echo "\n";

// ──────────────────────────────────────────────
// 5. Assign super-admin role to admin user
// ──────────────────────────────────────────────
echo "5. Assigning super-admin role to admin user...\n";

// Find admin user (first user / lowest ID, or by known email)
$adminUser = $pdo->query("SELECT id, email, organization_id FROM users ORDER BY id ASC LIMIT 1")->fetch();
if ($adminUser) {
    // Find super-admin role
    $saRole = $pdo->prepare("SELECT id FROM roles WHERE slug IN ('super-admin', 'super_admin') AND (organization_id IS NULL) LIMIT 1");
    $saRole->execute();
    $saRoleRow = $saRole->fetch();
    
    if ($saRoleRow) {
        $orgId = $adminUser['organization_id'];
        
        // Check if already assigned
        $check = $pdo->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role_id = ?");
        $check->execute([$adminUser['id'], $saRoleRow['id']]);
        
        if (!$check->fetch()) {
            $pdo->prepare("INSERT INTO user_roles (user_id, role_id, organization_id, created_at) VALUES (?, ?, ?, NOW())")
                ->execute([$adminUser['id'], $saRoleRow['id'], $orgId]);
            echo "   + Assigned super-admin role to user: {$adminUser['email']} (ID: {$adminUser['id']})\n";
        } else {
            echo "   = User {$adminUser['email']} already has super-admin role\n";
        }
        
        // Also assign org-admin if they belong to an org
        if ($orgId) {
            $oaRole = $pdo->prepare("SELECT id FROM roles WHERE slug IN ('org-admin', 'org_admin', 'org-owner') AND (organization_id IS NULL) LIMIT 1");
            $oaRole->execute();
            $oaRoleRow = $oaRole->fetch();
            if ($oaRoleRow) {
                $check2 = $pdo->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role_id = ? AND organization_id = ?");
                $check2->execute([$adminUser['id'], $oaRoleRow['id'], $orgId]);
                if (!$check2->fetch()) {
                    $pdo->prepare("INSERT INTO user_roles (user_id, role_id, organization_id, created_at) VALUES (?, ?, ?, NOW())")
                        ->execute([$adminUser['id'], $oaRoleRow['id'], $orgId]);
                    echo "   + Assigned org-admin/owner role to user for org ID: $orgId\n";
                }
            }
        }
    } else {
        echo "   ! super-admin role not found in roles table\n";
    }
} else {
    echo "   ! No users found in database\n";
}
echo "\n";

// ──────────────────────────────────────────────
// 6. Fix files context ENUM
// ──────────────────────────────────────────────
echo "6. Fixing files.context ENUM...\n";
try {
    $pdo->exec("ALTER TABLE files MODIFY COLUMN context ENUM('avatar','document','logo','attachment','import','export','facility','general') DEFAULT 'attachment'");
    echo "   + Files context enum updated (added 'facility', 'general')\n";
} catch (\Throwable $e) {
    echo "   ! Error: " . $e->getMessage() . "\n";
}
echo "\n";

// ──────────────────────────────────────────────
// Summary
// ──────────────────────────────────────────────
$totalPerms = $pdo->query("SELECT COUNT(*) FROM permissions")->fetchColumn();
$totalRoles = $pdo->query("SELECT COUNT(*) FROM roles WHERE organization_id IS NULL")->fetchColumn();
$totalUserRoles = $pdo->query("SELECT COUNT(*) FROM user_roles")->fetchColumn();
$totalRolePerms = $pdo->query("SELECT COUNT(*) FROM role_permissions")->fetchColumn();

echo "=== Summary ===\n";
echo "  Permissions:       $totalPerms\n";
echo "  System Roles:      $totalRoles\n";
echo "  User-Role entries: $totalUserRoles\n";
echo "  Role-Perms entries: $totalRolePerms\n";
echo "\nDone!\n";
