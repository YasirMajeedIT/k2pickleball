<?php
$cfg = require dirname(__DIR__) . '/config/database.php';
$pdo = new PDO('mysql:host='.$cfg['host'].';port='.$cfg['port'].';dbname='.$cfg['name'].';charset=utf8mb4', $cfg['user'], $cfg['pass']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec(file_get_contents(dirname(__DIR__) . '/database/migrations/030_schema_migrations.sql'));
echo "schema_migrations table created.\n";
