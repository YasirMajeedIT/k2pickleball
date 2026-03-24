<?php
/**
 * Migration: Seed missing permissions and assign to system roles
 * 
 * Adds all permissions from config/permissions.php that are missing in DB,
 * then assigns them to system roles (org-owner, org-admin, facility-manager, etc.)
 */
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$cfg = require __DIR__ . '/../config/database.php';
$pdo = new PDO(
    "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset=utf8mb4",
    $cfg['user'], $cfg['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

echo "🔑 Seeding missing permissions...\n";

// All permissions that should exist (from config/permissions.php)
$allPermissions = [
    // Organizations
    ['name' => 'View Organizations',    'slug' => 'organizations.view',   'module' => 'organizations'],
    ['name' => 'Create Organizations',  'slug' => 'organizations.create', 'module' => 'organizations'],
    ['name' => 'Update Organizations',  'slug' => 'organizations.update', 'module' => 'organizations'],
    ['name' => 'Delete Organizations',  'slug' => 'organizations.delete', 'module' => 'organizations'],
    // Facilities
    ['name' => 'View Facilities',   'slug' => 'facilities.view',   'module' => 'facilities'],
    ['name' => 'Create Facilities', 'slug' => 'facilities.create', 'module' => 'facilities'],
    ['name' => 'Update Facilities', 'slug' => 'facilities.update', 'module' => 'facilities'],
    ['name' => 'Delete Facilities', 'slug' => 'facilities.delete', 'module' => 'facilities'],
    // Courts
    ['name' => 'View Courts',   'slug' => 'courts.view',   'module' => 'courts'],
    ['name' => 'Create Courts', 'slug' => 'courts.create', 'module' => 'courts'],
    ['name' => 'Update Courts', 'slug' => 'courts.update', 'module' => 'courts'],
    ['name' => 'Delete Courts', 'slug' => 'courts.delete', 'module' => 'courts'],
    // Users
    ['name' => 'View Users',   'slug' => 'users.view',   'module' => 'users'],
    ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users'],
    ['name' => 'Update Users', 'slug' => 'users.update', 'module' => 'users'],
    ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users'],
    // Roles
    ['name' => 'View Roles',         'slug' => 'roles.view',   'module' => 'roles'],
    ['name' => 'Create Roles',       'slug' => 'roles.create', 'module' => 'roles'],
    ['name' => 'Update Roles',       'slug' => 'roles.update', 'module' => 'roles'],
    ['name' => 'Delete Roles',       'slug' => 'roles.delete', 'module' => 'roles'],
    ['name' => 'Assign Roles',       'slug' => 'roles.assign', 'module' => 'roles'],
    // Players
    ['name' => 'View Players',   'slug' => 'players.view',   'module' => 'players'],
    ['name' => 'Create Players', 'slug' => 'players.create', 'module' => 'players'],
    ['name' => 'Update Players', 'slug' => 'players.update', 'module' => 'players'],
    ['name' => 'Delete Players', 'slug' => 'players.delete', 'module' => 'players'],
    // Staff
    ['name' => 'View Staff',   'slug' => 'staff.view',   'module' => 'staff'],
    ['name' => 'Create Staff', 'slug' => 'staff.create', 'module' => 'staff'],
    ['name' => 'Update Staff', 'slug' => 'staff.update', 'module' => 'staff'],
    ['name' => 'Delete Staff', 'slug' => 'staff.delete', 'module' => 'staff'],
    // Subscriptions
    ['name' => 'View Subscriptions',   'slug' => 'subscriptions.view',   'module' => 'subscriptions'],
    ['name' => 'Manage Subscriptions', 'slug' => 'subscriptions.manage', 'module' => 'subscriptions'],
    // Payments
    ['name' => 'View Payments',    'slug' => 'payments.view',    'module' => 'payments'],
    ['name' => 'Process Payments', 'slug' => 'payments.process', 'module' => 'payments'],
    ['name' => 'Refund Payments',  'slug' => 'payments.refund',  'module' => 'payments'],
    // Notifications
    ['name' => 'View Notifications',   'slug' => 'notifications.view',   'module' => 'notifications'],
    ['name' => 'Manage Notifications', 'slug' => 'notifications.manage', 'module' => 'notifications'],
    // Files
    ['name' => 'View Files',   'slug' => 'files.view',   'module' => 'files'],
    ['name' => 'Upload Files', 'slug' => 'files.upload', 'module' => 'files'],
    ['name' => 'Delete Files', 'slug' => 'files.delete', 'module' => 'files'],
    // Settings
    ['name' => 'View Settings',   'slug' => 'settings.view',   'module' => 'settings'],
    ['name' => 'Update Settings', 'slug' => 'settings.update', 'module' => 'settings'],
    // Audit Logs
    ['name' => 'View Audit Logs', 'slug' => 'audit_logs.view', 'module' => 'audit_logs'],
    // API Tokens
    ['name' => 'View API Tokens',   'slug' => 'api_tokens.view',   'module' => 'api_tokens'],
    ['name' => 'Create API Tokens', 'slug' => 'api_tokens.create', 'module' => 'api_tokens'],
    ['name' => 'Revoke API Tokens', 'slug' => 'api_tokens.revoke', 'module' => 'api_tokens'],
    // Platform
    ['name' => 'Manage Platform',   'slug' => 'platform.manage',    'module' => 'platform'],
    ['name' => 'Platform Analytics','slug' => 'platform.analytics', 'module' => 'platform'],
    // Schedule
    ['name' => 'View Schedule', 'slug' => 'schedule.view', 'module' => 'schedule'],
    // Session Types
    ['name' => 'View Session Types',   'slug' => 'session_types.view',   'module' => 'session_types'],
    ['name' => 'Create Session Types', 'slug' => 'session_types.create', 'module' => 'session_types'],
    ['name' => 'Update Session Types', 'slug' => 'session_types.update', 'module' => 'session_types'],
    ['name' => 'Delete Session Types', 'slug' => 'session_types.delete', 'module' => 'session_types'],
    // Session Details
    ['name' => 'View Session Details',   'slug' => 'session_details.view',   'module' => 'session_details'],
    ['name' => 'Create Session Details', 'slug' => 'session_details.create', 'module' => 'session_details'],
    ['name' => 'Update Session Details', 'slug' => 'session_details.update', 'module' => 'session_details'],
    ['name' => 'Delete Session Details', 'slug' => 'session_details.delete', 'module' => 'session_details'],
    // Resources
    ['name' => 'View Resources',   'slug' => 'resources.view',   'module' => 'resources'],
    ['name' => 'Create Resources', 'slug' => 'resources.create', 'module' => 'resources'],
    ['name' => 'Update Resources', 'slug' => 'resources.update', 'module' => 'resources'],
    ['name' => 'Delete Resources', 'slug' => 'resources.delete', 'module' => 'resources'],
    // Categories
    ['name' => 'View Categories',   'slug' => 'categories.view',   'module' => 'categories'],
    ['name' => 'Create Categories', 'slug' => 'categories.create', 'module' => 'categories'],
    ['name' => 'Update Categories', 'slug' => 'categories.update', 'module' => 'categories'],
    ['name' => 'Delete Categories', 'slug' => 'categories.delete', 'module' => 'categories'],
    // Labels
    ['name' => 'View Labels',   'slug' => 'labels.view',   'module' => 'labels'],
    ['name' => 'Create Labels', 'slug' => 'labels.create', 'module' => 'labels'],
    ['name' => 'Update Labels', 'slug' => 'labels.update', 'module' => 'labels'],
    ['name' => 'Delete Labels', 'slug' => 'labels.delete', 'module' => 'labels'],
    // Waivers
    ['name' => 'View Waivers',   'slug' => 'waivers.view',   'module' => 'waivers'],
    ['name' => 'Create Waivers', 'slug' => 'waivers.create', 'module' => 'waivers'],
    ['name' => 'Update Waivers', 'slug' => 'waivers.update', 'module' => 'waivers'],
    ['name' => 'Delete Waivers', 'slug' => 'waivers.delete', 'module' => 'waivers'],
    // Discounts
    ['name' => 'View Discounts',   'slug' => 'discounts.view',   'module' => 'discounts'],
    ['name' => 'Create Discounts', 'slug' => 'discounts.create', 'module' => 'discounts'],
    ['name' => 'Update Discounts', 'slug' => 'discounts.update', 'module' => 'discounts'],
    ['name' => 'Delete Discounts', 'slug' => 'discounts.delete', 'module' => 'discounts'],
    // Credit Codes
    ['name' => 'View Credit Codes',   'slug' => 'credit_codes.view',   'module' => 'credit_codes'],
    ['name' => 'Create Credit Codes', 'slug' => 'credit_codes.create', 'module' => 'credit_codes'],
    ['name' => 'Update Credit Codes', 'slug' => 'credit_codes.update', 'module' => 'credit_codes'],
    ['name' => 'Delete Credit Codes', 'slug' => 'credit_codes.delete', 'module' => 'credit_codes'],
    // Gift Certificates
    ['name' => 'View Gift Certificates',   'slug' => 'gift_certificates.view',   'module' => 'gift_certificates'],
    ['name' => 'Create Gift Certificates', 'slug' => 'gift_certificates.create', 'module' => 'gift_certificates'],
    ['name' => 'Update Gift Certificates', 'slug' => 'gift_certificates.update', 'module' => 'gift_certificates'],
    ['name' => 'Delete Gift Certificates', 'slug' => 'gift_certificates.delete', 'module' => 'gift_certificates'],
    // Extensions
    ['name' => 'View Extensions',      'slug' => 'extensions.view',      'module' => 'extensions'],
    ['name' => 'Install Extensions',   'slug' => 'extensions.install',   'module' => 'extensions'],
    ['name' => 'Configure Extensions', 'slug' => 'extensions.configure', 'module' => 'extensions'],
];

// Insert missing permissions
$stmt = $pdo->prepare("INSERT IGNORE INTO permissions (name, slug, module, description, created_at) VALUES (:name, :slug, :module, :slug, NOW())");
$inserted = 0;
foreach ($allPermissions as $perm) {
    $rows = $stmt->execute($perm);
    if ($pdo->lastInsertId()) $inserted++;
}
echo "  ✅ Inserted {$inserted} new permissions\n\n";

// Reload full permission map from DB
$allPerms = $pdo->query("SELECT id, slug FROM permissions")->fetchAll();
$permMap = array_column($allPerms, 'id', 'slug');

// Role permission assignments for system roles
// Keys = DB role slugs, values = array of permission slugs to grant
$rolePermissions = [
    // org-owner gets all permissions
    'org-owner' => array_column($allPermissions, 'slug'),

    // org-admin gets all except platform management
    'org-admin' => [
        'organizations.view', 'organizations.update',
        'facilities.view', 'facilities.create', 'facilities.update', 'facilities.delete',
        'courts.view', 'courts.create', 'courts.update', 'courts.delete',
        'users.view', 'users.create', 'users.update', 'users.delete',
        'roles.view', 'roles.create', 'roles.update', 'roles.delete', 'roles.assign',
        'players.view', 'players.create', 'players.update', 'players.delete',
        'staff.view', 'staff.create', 'staff.update', 'staff.delete',
        'subscriptions.view', 'subscriptions.manage',
        'payments.view', 'payments.process', 'payments.refund',
        'notifications.view', 'notifications.manage',
        'files.view', 'files.upload', 'files.delete',
        'settings.view', 'settings.update',
        'audit_logs.view',
        'api_tokens.view', 'api_tokens.create', 'api_tokens.revoke',
        'schedule.view',
        'session_types.view', 'session_types.create', 'session_types.update', 'session_types.delete',
        'session_details.view', 'session_details.create', 'session_details.update', 'session_details.delete',
        'resources.view', 'resources.create', 'resources.update', 'resources.delete',
        'categories.view', 'categories.create', 'categories.update', 'categories.delete',
        'labels.view', 'labels.create', 'labels.update', 'labels.delete',
        'waivers.view', 'waivers.create', 'waivers.update', 'waivers.delete',
        'discounts.view', 'discounts.create', 'discounts.update', 'discounts.delete',
        'credit_codes.view', 'credit_codes.create', 'credit_codes.update', 'credit_codes.delete',
        'gift_certificates.view', 'gift_certificates.create', 'gift_certificates.update', 'gift_certificates.delete',
        'extensions.view', 'extensions.install', 'extensions.configure',
    ],

    // facility-manager gets operations access
    'facility-manager' => [
        'facilities.view', 'facilities.update',
        'courts.view', 'courts.create', 'courts.update',
        'users.view', 'users.create', 'users.update',
        'players.view', 'players.create', 'players.update', 'players.delete',
        'staff.view',
        'payments.view', 'payments.process',
        'notifications.view',
        'files.view', 'files.upload',
        'settings.view',
        'schedule.view',
        'session_types.view', 'session_types.create', 'session_types.update', 'session_types.delete',
        'session_details.view', 'session_details.create', 'session_details.update', 'session_details.delete',
        'resources.view', 'resources.create', 'resources.update', 'resources.delete',
        'categories.view', 'categories.create', 'categories.update', 'categories.delete',
        'labels.view', 'labels.create', 'labels.update',
        'waivers.view', 'waivers.create', 'waivers.update',
        'discounts.view', 'discounts.create', 'discounts.update', 'discounts.delete',
        'credit_codes.view', 'credit_codes.create', 'credit_codes.update', 'credit_codes.delete',
        'gift_certificates.view', 'gift_certificates.create', 'gift_certificates.update', 'gift_certificates.delete',
        'extensions.view', 'extensions.configure',
    ],

    // staff gets view + limited manage
    'staff' => [
        'facilities.view',
        'courts.view',
        'players.view',
        'notifications.view',
        'files.view',
        'schedule.view',
        'session_types.view',
        'session_details.view',
        'resources.view',
        'categories.view',
        'waivers.view',
        'discounts.view',
        'credit_codes.view',
        'gift_certificates.view',
    ],

    // player and guest remain minimal
    'player' => [
        'notifications.view',
        'files.view',
    ],
    'guest' => [
        'facilities.view',
        'courts.view',
    ],
];

echo "👤 Assigning permissions to system roles...\n";
$rpStmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id, created_at) VALUES (:role_id, :permission_id, NOW())");

foreach ($rolePermissions as $roleSlug => $perms) {
    $roleRow = $pdo->query("SELECT id, name FROM roles WHERE slug = " . $pdo->quote($roleSlug))->fetch();
    if (!$roleRow) {
        echo "  ⚠️  Role '{$roleSlug}' not found, skipping\n";
        continue;
    }
    $assigned = 0;
    foreach ($perms as $permSlug) {
        if (!isset($permMap[$permSlug])) continue;
        $rpStmt->execute(['role_id' => $roleRow['id'], 'permission_id' => $permMap[$permSlug]]);
        if ($pdo->lastInsertId()) $assigned++;
    }
    echo "  ✅ {$roleRow['name']} ({$roleSlug}): +{$assigned} new permissions\n";
}

echo "\n✅ Done. Permissions migration complete.\n";
