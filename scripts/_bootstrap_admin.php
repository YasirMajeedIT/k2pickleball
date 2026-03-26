<?php
/**
 * Bootstrap: Create the default organization, assign super-admin role to admin user,
 * and ensure the admin user is properly linked.
 */
$pdo = new PDO('mysql:host=127.0.0.1;dbname=k2pickleball;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Create default organization
echo "1. Creating default organization...\n";
$existing = $pdo->query("SELECT id FROM organizations LIMIT 1")->fetch();
if ($existing) {
    $orgId = $existing['id'];
    echo "   = Organization already exists (ID: $orgId)\n";
} else {
    $pdo->exec("INSERT INTO organizations (name, slug, status, created_at, updated_at) VALUES ('K2 Pickleball', 'k2-pickleball', 'active', NOW(), NOW())");
    $orgId = $pdo->lastInsertId();
    echo "   + Created organization 'K2 Pickleball' (ID: $orgId)\n";
}

// 2. Link admin user to organization
echo "2. Linking admin user to organization...\n";
$admin = $pdo->query("SELECT id, email, organization_id FROM users WHERE email = 'admin@k2pickleball.com' LIMIT 1")->fetch();
if (!$admin) {
    $admin = $pdo->query("SELECT id, email, organization_id FROM users ORDER BY id ASC LIMIT 1")->fetch();
}
if ($admin) {
    if (!$admin['organization_id']) {
        $pdo->prepare("UPDATE users SET organization_id = ? WHERE id = ?")->execute([$orgId, $admin['id']]);
        echo "   + Linked {$admin['email']} to org ID: $orgId\n";
    } else {
        $orgId = $admin['organization_id'];
        echo "   = {$admin['email']} already linked to org ID: $orgId\n";
    }

    // 3. Assign super-admin role
    echo "3. Assigning super-admin role...\n";
    $saRole = $pdo->query("SELECT id FROM roles WHERE slug IN ('super-admin', 'super_admin') AND organization_id IS NULL LIMIT 1")->fetch();
    if ($saRole) {
        $check = $pdo->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role_id = ?");
        $check->execute([$admin['id'], $saRole['id']]);
        if (!$check->fetch()) {
            $pdo->prepare("INSERT INTO user_roles (user_id, role_id, organization_id, created_at) VALUES (?, ?, ?, NOW())")
                ->execute([$admin['id'], $saRole['id'], $orgId]);
            echo "   + Assigned super-admin to {$admin['email']}\n";
        } else {
            echo "   = {$admin['email']} already has super-admin\n";
        }
    }

    // 4. Assign org-admin role
    echo "4. Assigning org-admin role...\n";
    $oaRole = $pdo->query("SELECT id FROM roles WHERE slug IN ('org-admin', 'org_admin', 'org-owner') AND organization_id IS NULL LIMIT 1")->fetch();
    if ($oaRole) {
        $check = $pdo->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role_id = ? AND organization_id = ?");
        $check->execute([$admin['id'], $oaRole['id'], $orgId]);
        if (!$check->fetch()) {
            $pdo->prepare("INSERT INTO user_roles (user_id, role_id, organization_id, created_at) VALUES (?, ?, ?, NOW())")
                ->execute([$admin['id'], $oaRole['id'], $orgId]);
            echo "   + Assigned org-admin to {$admin['email']} for org $orgId\n";
        } else {
            echo "   = {$admin['email']} already has org-admin\n";
        }
    }

    // 5. Assign facility-admin role  
    echo "5. Assigning facility-admin role...\n";
    $faRole = $pdo->query("SELECT id FROM roles WHERE slug = 'facility-admin' AND organization_id IS NULL LIMIT 1")->fetch();
    if ($faRole) {
        $check = $pdo->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role_id = ? AND organization_id = ?");
        $check->execute([$admin['id'], $faRole['id'], $orgId]);
        if (!$check->fetch()) {
            $pdo->prepare("INSERT INTO user_roles (user_id, role_id, organization_id, created_at) VALUES (?, ?, ?, NOW())")
                ->execute([$admin['id'], $faRole['id'], $orgId]);
            echo "   + Assigned facility-admin to {$admin['email']} for org $orgId\n";
        } else {
            echo "   = {$admin['email']} already has facility-admin\n";
        }
    }
} else {
    echo "   ! No admin user found\n";
}

// 6. Verify
echo "\n--- Verification ---\n";
$roles = $pdo->query("SELECT ur.user_id, u.email, r.slug, ur.organization_id FROM user_roles ur JOIN roles r ON ur.role_id = r.id JOIN users u ON ur.user_id = u.id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($roles as $r) {
    echo "  user:{$r['email']} role:{$r['slug']} org:{$r['organization_id']}\n";
}
$u = $pdo->query("SELECT id, email, organization_id FROM users WHERE email = 'admin@k2pickleball.com'")->fetch();
if ($u) echo "\n  Admin user org_id: {$u['organization_id']}\n";

echo "\nDone!\n";
