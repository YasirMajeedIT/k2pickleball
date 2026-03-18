<?php
$pdo = new PDO('mysql:host=localhost;dbname=k2pickleball', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("ALTER TABLE st_class_attendees ADD COLUMN tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER gift_amount, ADD COLUMN tax_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER tax_amount");
echo "Migration 023 OK\n";
