<?php
$pdo = new PDO('mysql:host=localhost;dbname=k2pickleball', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$queries = [
    'ALTER TABLE `session_types` DROP COLUMN `special_event_spotlight`',
    'ALTER TABLE `session_types` DROP COLUMN `is_taxable`',
    'ALTER TABLE `session_types` DROP COLUMN `is_slots_show`',
];

foreach ($queries as $q) {
    try {
        $pdo->exec($q);
        echo "OK: $q\n";
    } catch (Exception $e) {
        echo "SKIP: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
