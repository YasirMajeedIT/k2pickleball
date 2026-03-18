<?php
$pdo = new PDO('mysql:host=localhost;dbname=k2pickleball', 'root', '');
$r = $pdo->query("SHOW COLUMNS FROM facilities LIKE 'tax_rate'");
print_r($r->fetch(PDO::FETCH_ASSOC));
$r2 = $pdo->query("SHOW COLUMNS FROM categories LIKE 'is_taxable'");
print_r($r2->fetch(PDO::FETCH_ASSOC));
$r3 = $pdo->query("SHOW COLUMNS FROM st_class_attendees WHERE Field IN ('tax_amount','tax_rate')");
while ($row = $r3->fetch(PDO::FETCH_ASSOC)) print_r($row);
