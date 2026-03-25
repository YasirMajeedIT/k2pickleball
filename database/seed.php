<?php

declare(strict_types=1);

/**
 * Database Seeder
 * 
 * Usage: php database/seed.php
 * 
 * Seeds the database with:
 * - Default permissions
 * - Default roles with permissions  
 * - Super admin user
 * - 3 subscription plans
 * - Demo organization with facilities and courts
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbName = $_ENV['DB_NAME'] ?? 'k2pickleball';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

echo "🌱 K2 Pickleball - Database Seeder\n";
echo str_repeat('=', 50) . "\n\n";

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    // ─── Permissions ─────────────────────────────────────────
    echo "📋 Seeding permissions...\n";

    // Load all permissions from config/permissions.php (single source of truth)
    $permConfig = require dirname(__DIR__) . '/config/permissions.php';

    $permissions = [];
    foreach ($permConfig['permissions'] as $slug => $meta) {
        $permissions[] = [
            'name' => ucwords(str_replace(['.', '_'], [' — ', ' '], $slug)),
            'slug' => $slug,
            'module' => $meta['module'],
            'description' => $meta['description'],
        ];
    }

    $stmt = $pdo->prepare("INSERT IGNORE INTO permissions (name, slug, module, description, created_at) VALUES (:name, :slug, :module, :description, NOW())");
    $permCount = 0;
    foreach ($permissions as $perm) {
        $stmt->execute($perm);
        $permCount++;
    }
    echo "  ✅ {$permCount} permissions seeded\n\n";

    // ─── Roles ───────────────────────────────────────────────
    echo "👤 Seeding roles...\n";

    // Build roles from config/permissions.php (single source of truth)
    $roles = [];
    foreach ($permConfig['roles'] as $slug => $meta) {
        $hyphenSlug = str_replace('_', '-', $slug);
        $roles[] = [
            'name' => $meta['name'],
            'slug' => $hyphenSlug,
            'description' => $meta['description'],
            'is_system' => 1,
            'permissions' => $permConfig['role_permissions'][$slug] ?? [],
        ];
    }

    // Get all permission IDs
    $allPerms = $pdo->query("SELECT id, slug FROM permissions")->fetchAll();
    $permMap = array_column($allPerms, 'id', 'slug');

    $roleStmt = $pdo->prepare("INSERT IGNORE INTO roles (name, slug, description, is_system, created_at, updated_at) VALUES (:name, :slug, :description, :is_system, NOW(), NOW())");
    $rpStmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)");

    foreach ($roles as $role) {
        $roleStmt->execute([
            'name' => $role['name'],
            'slug' => $role['slug'],
            'description' => $role['description'],
            'is_system' => $role['is_system'],
        ]);

        $roleId = $pdo->query("SELECT id FROM roles WHERE slug = " . $pdo->quote($role['slug']))->fetchColumn();
        if (!$roleId) continue;

        $permsToAssign = [];
        if ($role['permissions'] === ['*']) {
            // Super admin — all permissions
            $permsToAssign = array_keys($permMap);
        } else {
            foreach ($role['permissions'] as $pattern) {
                if (str_ends_with($pattern, '.*')) {
                    // Wildcard: match all permissions for this module
                    $module = substr($pattern, 0, -2);
                    foreach ($permMap as $pSlug => $pId) {
                        if (str_starts_with($pSlug, $module . '.')) {
                            $permsToAssign[] = $pSlug;
                        }
                    }
                } else {
                    $permsToAssign[] = $pattern;
                }
            }
        }
        foreach ($permsToAssign as $permSlug) {
            if (isset($permMap[$permSlug])) {
                $rpStmt->execute(['role_id' => $roleId, 'permission_id' => $permMap[$permSlug]]);
            }
        }
        echo "  ✅ Role: {$role['name']} (" . count($permsToAssign) . " permissions)\n";
    }
    echo "\n";

    // ─── Super Admin User ────────────────────────────────────
    echo "🔑 Seeding super admin user...\n";
    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'admin@k2pickleball.com';
    $adminPass = $_ENV['ADMIN_PASSWORD'] ?? 'K2Admin!2024';

    $exists = $pdo->query("SELECT id FROM users WHERE email = " . $pdo->quote($adminEmail))->fetchColumn();
    if (!$exists) {
        $hashedPass = password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 12]);
        $adminUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

        $pdo->prepare("INSERT INTO users (uuid, first_name, last_name, email, password_hash, status, email_verified_at, created_at, updated_at) VALUES (:uuid, :first_name, :last_name, :email, :password_hash, 'active', NOW(), NOW(), NOW())")
            ->execute([
                'uuid' => $adminUuid,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => $adminEmail,
                'password_hash' => $hashedPass,
            ]);

        $adminId = $pdo->lastInsertId();
        echo "  ✅ Super admin created: {$adminEmail} / {$adminPass}\n";
    } else {
        $adminId = $exists;
        echo "  ⏭️  Super admin already exists: {$adminEmail}\n";
    }

    // Always ensure admin has super-admin role in user_roles
    $superAdminRoleId = $pdo->query("SELECT id FROM roles WHERE slug = 'super-admin'")->fetchColumn();
    if ($superAdminRoleId && $adminId) {
        $hasRole = $pdo->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role_id = ?");
        $hasRole->execute([$adminId, $superAdminRoleId]);
        if (!$hasRole->fetch()) {
            $adminOrgId = $pdo->query("SELECT organization_id FROM users WHERE id = " . (int)$adminId)->fetchColumn();
            $pdo->prepare("INSERT INTO user_roles (user_id, role_id, organization_id, created_at) VALUES (?, ?, ?, NOW())")
                ->execute([$adminId, $superAdminRoleId, $adminOrgId ?: null]);
            echo "  ✅ Assigned super-admin role to admin user\n";
        } else {
            echo "  ⏭️  Admin already has super-admin role\n";
        }
    }
    echo "\n";

    // ─── Subscription Plans ──────────────────────────────────
    echo "💳 Seeding subscription plans...\n";
    $plans = [
        [
            'name' => 'Free',
            'slug' => 'free',
            'description' => 'Get started with basic features',
            'monthly_price' => 0,
            'annual_price' => 0,
            'max_facilities' => 1,
            'max_courts' => 4,
            'max_users' => 10,
            'features' => json_encode(['basic_scheduling', 'court_management', 'player_profiles']),
            'is_active' => 1,
            'sort_order' => 1,
        ],
        [
            'name' => 'Professional',
            'slug' => 'professional',
            'description' => 'Everything you need to run your club',
            'monthly_price' => 49.99,
            'annual_price' => 499.99,
            'max_facilities' => 5,
            'max_courts' => 20,
            'max_users' => 100,
            'features' => json_encode(['basic_scheduling', 'court_management', 'player_profiles', 'online_payments', 'tournament_management', 'email_notifications', 'reports_analytics', 'api_access']),
            'is_active' => 1,
            'sort_order' => 2,
        ],
        [
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'For large organizations with custom needs',
            'monthly_price' => 149.99,
            'annual_price' => 1499.99,
            'max_facilities' => null,
            'max_courts' => null,
            'max_users' => null,
            'features' => json_encode(['basic_scheduling', 'court_management', 'player_profiles', 'online_payments', 'tournament_management', 'email_notifications', 'reports_analytics', 'api_access', 'custom_branding', 'priority_support', 'sla_guarantee', 'dedicated_account_manager', 'custom_integrations']),
            'is_active' => 1,
            'sort_order' => 3,
        ],
    ];

    $planStmt = $pdo->prepare("INSERT IGNORE INTO plans (name, slug, description, price_monthly, price_yearly, max_facilities, max_courts, max_users, features, is_active, sort_order, created_at, updated_at) VALUES (:name, :slug, :description, :price_monthly, :price_yearly, :max_facilities, :max_courts, :max_users, :features, :is_active, :sort_order, NOW(), NOW())");
    foreach ($plans as $plan) {
        $planStmt->execute([
            'name' => $plan['name'],
            'slug' => $plan['slug'],
            'description' => $plan['description'],
            'price_monthly' => $plan['monthly_price'],
            'price_yearly' => $plan['annual_price'],
            'max_facilities' => $plan['max_facilities'],
            'max_courts' => $plan['max_courts'],
            'max_users' => $plan['max_users'],
            'features' => $plan['features'],
            'is_active' => $plan['is_active'],
            'sort_order' => $plan['sort_order'],
        ]);
        echo "  ✅ Plan: {$plan['name']} (\${$plan['monthly_price']}/mo)\n";
    }
    echo "\n";

    // ─── Demo Organization ───────────────────────────────────
    echo "🏢 Seeding demo organization...\n";
    $demoOrgExists = $pdo->query("SELECT id FROM organizations WHERE slug = 'demo-sports-club'")->fetchColumn();
    if (!$demoOrgExists) {
        $orgUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

        $ownerId = $pdo->query("SELECT id FROM users WHERE email = " . $pdo->quote($adminEmail))->fetchColumn();

        $pdo->prepare("INSERT INTO organizations (uuid, name, slug, status, settings, created_at, updated_at) VALUES (:uuid, :name, :slug, 'active', :settings, NOW(), NOW())")
            ->execute([
                'uuid' => $orgUuid,
                'name' => 'Demo Sports Club',
                'slug' => 'demo-sports-club',
                'settings' => json_encode(['timezone' => 'America/New_York', 'currency' => 'USD']),
            ]);
        $orgId = $pdo->lastInsertId();
        echo "  ✅ Organization: Demo Sports Club\n";

        // Demo Facility
        $facUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

        $pdo->prepare("INSERT INTO facilities (uuid, organization_id, name, slug, address_line1, city, state, zip, country, phone, email, timezone, status, settings, created_at, updated_at) VALUES (:uuid, :org_id, :name, :slug, :addr, :city, :state, :zip, :country, :phone, :email, :tz, 'active', :settings, NOW(), NOW())")
            ->execute([
                'uuid' => $facUuid,
                'org_id' => $orgId,
                'name' => 'Main Pickleball Center',
                'slug' => 'main-center',
                'addr' => '123 Sport Lane',
                'city' => 'Denver',
                'state' => 'CO',
                'zip' => '80202',
                'country' => 'US',
                'phone' => '(303) 555-0100',
                'email' => 'info@demosportsclub.com',
                'tz' => 'America/Denver',
                'settings' => json_encode([
                    'operating_hours' => ['mon' => '06:00-22:00', 'tue' => '06:00-22:00', 'wed' => '06:00-22:00', 'thu' => '06:00-22:00', 'fri' => '06:00-23:00', 'sat' => '07:00-23:00', 'sun' => '07:00-20:00'],
                    'amenities' => ['parking', 'restrooms', 'pro_shop', 'locker_rooms', 'water_station', 'seating_area'],
                ]),
            ]);
        $facilityId = $pdo->lastInsertId();
        echo "  ✅ Facility: Main Pickleball Center\n";

        // Demo Courts
        $courts = [
            ['name' => 'Court 1', 'indoor' => 1, 'surface' => 'hard', 'rate' => 25.00],
            ['name' => 'Court 2', 'indoor' => 1, 'surface' => 'hard', 'rate' => 25.00],
            ['name' => 'Court 3', 'indoor' => 1, 'surface' => 'synthetic', 'rate' => 30.00],
            ['name' => 'Court 4', 'indoor' => 0, 'surface' => 'concrete', 'rate' => 15.00],
            ['name' => 'Court 5', 'indoor' => 0, 'surface' => 'concrete', 'rate' => 15.00],
            ['name' => 'Court 6', 'indoor' => 0, 'surface' => 'hard', 'rate' => 20.00],
        ];

        $courtNum = 0;
        $courtStmt = $pdo->prepare("INSERT INTO courts (uuid, facility_id, organization_id, name, sport_type, surface_type, is_indoor, court_number, hourly_rate, max_players, status, created_at, updated_at) VALUES (:uuid, :fac_id, :org_id, :name, 'pickleball', :surface, :indoor, :court_num, :rate, 4, 'active', NOW(), NOW())");
        foreach ($courts as $court) {
            $courtNum++;
            $courtUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
            $courtStmt->execute([
                'uuid' => $courtUuid,
                'fac_id' => $facilityId,
                'org_id' => $orgId,
                'name' => $court['name'],
                'surface' => $court['surface'],
                'indoor' => $court['indoor'],
                'court_num' => $courtNum,
                'rate' => $court['rate'],
            ]);
            echo "  ✅ Court: {$court['name']} (\${$court['rate']}/hr)\n";
        }

        // Assign super admin to organization and role
        if ($ownerId) {
            $pdo->exec("UPDATE users SET organization_id = {$orgId} WHERE id = {$ownerId}");
            $superAdminRoleId = $pdo->query("SELECT id FROM roles WHERE slug = 'super-admin'")->fetchColumn();
            if ($superAdminRoleId) {
                $pdo->prepare("INSERT IGNORE INTO user_roles (user_id, role_id, organization_id) VALUES (:user_id, :role_id, :org_id)")
                    ->execute(['user_id' => $ownerId, 'role_id' => $superAdminRoleId, 'org_id' => $orgId]);
                echo "  ✅ Super admin assigned to organization with super-admin role\n";
            }
        }
    } else {
        echo "  ⏭️  Demo organization already exists\n";
    }
    echo "\n";

    // ─── Default Settings ────────────────────────────────────
    echo "⚙️  Seeding default settings...\n";
    $settings = [
        ['general', 'app_name', 'K2 Pickleball', 'string'],
        ['general', 'app_description', 'Sports Facility & Club Management Platform', 'string'],
        ['general', 'default_timezone', 'America/New_York', 'string'],
        ['general', 'default_currency', 'USD', 'string'],
        ['general', 'default_locale', 'en_US', 'string'],
        ['branding', 'primary_color', '#3b82f6', 'string'],
        ['branding', 'logo_url', '', 'string'],
        ['notifications', 'email_enabled', '1', 'boolean'],
        ['notifications', 'sms_enabled', '0', 'boolean'],
        ['billing', 'tax_rate', '0', 'float'],
        ['billing', 'trial_days', '14', 'integer'],
        ['billing', 'grace_period_days', '7', 'integer'],
    ];

    $settingStmt = $pdo->prepare("INSERT IGNORE INTO settings (organization_id, `group_name`, `key_name`, value, type, created_at, updated_at) VALUES (NULL, :grp, :key, :value, :type, NOW(), NOW())");
    foreach ($settings as [$group, $key, $value, $type]) {
        $settingStmt->execute(['grp' => $group, 'key' => $key, 'value' => $value, 'type' => $type]);
    }
    echo "  ✅ " . count($settings) . " default settings seeded\n\n";

    echo str_repeat('=', 50) . "\n";
    echo "🎉 Seeding complete!\n";
    echo "\n📌 Login credentials:\n";
    echo "   Email:    {$adminEmail}\n";
    echo "   Password: {$adminPass}\n";
    echo "   Admin:    http://localhost/k2pickleball/admin/login\n";
    echo "   Platform: http://localhost/k2pickleball/platform\n";

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
}
