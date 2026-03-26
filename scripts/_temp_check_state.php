<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=k2pickleball;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "--- Users ---\n";
$rows = $pdo->query("SELECT id, email, organization_id, status FROM users")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "  ID:{$r['id']} email:{$r['email']} org:" . ($r['organization_id'] ?? 'NULL') . " status:{$r['status']}\n";
}

echo "\n--- Organizations ---\n";
$rows = $pdo->query("SELECT id, name FROM organizations")->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) echo "  (none)\n";
foreach ($rows as $r) {
    echo "  ID:{$r['id']} name:{$r['name']}\n";
}

echo "\n--- User Roles ---\n";
$rows = $pdo->query("SELECT ur.user_id, r.slug, ur.organization_id FROM user_roles ur JOIN roles r ON ur.role_id = r.id")->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) echo "  (none)\n";
foreach ($rows as $r) {
    echo "  user_id:{$r['user_id']} role:{$r['slug']} org:" . ($r['organization_id'] ?? 'NULL') . "\n";
}

echo "\n--- Facilities ---\n";
$rows = $pdo->query("SELECT id, name, organization_id FROM facilities")->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) echo "  (none)\n";
foreach ($rows as $r) {
    echo "  ID:{$r['id']} name:{$r['name']} org:{$r['organization_id']}\n";
}
