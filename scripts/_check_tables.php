<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=k2pickleball', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// Execute the migration file
$sql = file_get_contents(__DIR__ . '/../database/migrations/032_create_membership_plans.sql');

// Split on semicolons but keep only CREATE TABLE statements
preg_match_all('/CREATE TABLE[^;]+;/s', $sql, $matches);
foreach ($matches[0] as $stmt) {
    try {
        $pdo->exec($stmt);
        preg_match('/`(\w+)`/', $stmt, $m);
        echo "Created: {$m[1]}\n";
    } catch (PDOException $e) {
        preg_match('/`(\w+)`/', $stmt, $m);
        if (str_contains($e->getMessage(), 'already exists')) {
            echo "Exists: {$m[1]}\n";
        } else {
            echo "ERROR on {$m[1]}: {$e->getMessage()}\n";
        }
    }
}

// Verify
$r = $pdo->query("SHOW TABLES LIKE 'membership%'");
echo "\nTables found:\n";
while ($row = $r->fetch()) echo "  " . $row[0] . "\n";
$r2 = $pdo->query("SHOW TABLES LIKE 'player_memberships'");
while ($row = $r2->fetch()) echo "  " . $row[0] . "\n";
