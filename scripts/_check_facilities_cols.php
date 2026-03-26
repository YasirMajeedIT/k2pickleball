<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=k2pickleball;charset=utf8mb4','root','');
$rows = $pdo->query("SHOW COLUMNS FROM facilities")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) { echo $r['Field'] . ' — ' . $r['Type'] . "\n"; }
