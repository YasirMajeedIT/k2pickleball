<?php
require __DIR__ . '/../vendor/autoload.php';
$cfg = require __DIR__ . '/../config/database.php';
$pdo = new PDO('mysql:host='.$cfg['host'].';dbname='.$cfg['name'], $cfg['user'], $cfg['pass']);
$rows = $pdo->query('SELECT id, label, url, type, system_key, is_system, is_visible, sort_order, visibility_rule FROM navigation_items WHERE organization_id = 1 ORDER BY sort_order ASC')->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) {
    echo sprintf("id=%d  label=%-15s  url=%-20s  type=%-10s  sys_key=%-15s  sys=%d  vis=%d  sort=%d  rule=%s\n",
        $r['id'], $r['label'], $r['url'], $r['type'], $r['system_key'] ?? '-', $r['is_system'], $r['is_visible'], $r['sort_order'], $r['visibility_rule'] ?? '-');
}

echo "\n--- Categories ---\n";
$cats = $pdo->query('SELECT id, name, slug, is_active FROM categories WHERE organization_id = 1 ORDER BY sort_order ASC')->fetchAll(PDO::FETCH_ASSOC);
foreach($cats as $c2) {
    echo sprintf("id=%d  name=%-20s  slug=%-20s  active=%d\n", $c2['id'], $c2['name'], $c2['slug'], $c2['is_active']);
}

echo "\n--- Custom Forms ---\n";
$forms = $pdo->query("SELECT id, title, slug, status, show_in_nav FROM custom_forms WHERE organization_id = 1")->fetchAll(PDO::FETCH_ASSOC);
foreach($forms as $f) {
    echo sprintf("id=%d  title=%-20s  slug=%-20s  status=%-10s  nav=%d\n", $f['id'], $f['title'], $f['slug'], $f['status'], $f['show_in_nav']);
}
