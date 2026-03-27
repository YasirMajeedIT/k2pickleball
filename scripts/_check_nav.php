<?php
require __DIR__ . '/../vendor/autoload.php';
$cfg = require __DIR__ . '/../config/database.php';
$pdo = new PDO('mysql:host='.$cfg['host'].';dbname='.$cfg['name'].';port='.$cfg['port'], $cfg['user'], $cfg['pass']);
$rows = $pdo->query('SELECT id, organization_id, label, url, system_key, is_system, is_visible, sort_order FROM navigation_items ORDER BY organization_id, sort_order')->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($rows) . " navigation items:\n";
echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";

// Also check organizations
$orgs = $pdo->query('SELECT id, name, slug FROM organizations LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
echo "\nOrganizations:\n";
echo json_encode($orgs, JSON_PRETTY_PRINT) . "\n";
