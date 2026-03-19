<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=k2pickleball', 'root', '');
$rows = $pdo->query('SELECT id, name, slug, status FROM organizations ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['id'] . ' | ' . $r['slug'] . ' | ' . $r['name'] . ' | ' . $r['status'] . PHP_EOL;
}
