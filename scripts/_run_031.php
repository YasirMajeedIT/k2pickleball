<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=k2pickleball;charset=utf8mb4','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = file_get_contents('database/migrations/031_facility_social_twilio.sql');
foreach (array_filter(array_map('trim', explode(';', $sql))) as $s) {
    if ($s && !preg_match('/^--/', $s)) {
        try { $pdo->exec($s); echo "OK: " . substr($s, 0, 80) . "\n"; }
        catch(Exception $e) { echo "WARN: " . $e->getMessage() . "\n"; }
    }
}
echo "Done\n";
