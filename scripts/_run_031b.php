<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=k2pickleball;charset=utf8mb4','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sqls = [
    "ALTER TABLE `facilities` ADD COLUMN `instagram_url` VARCHAR(500) NULL AFTER `image_url`",
    "ALTER TABLE `facilities` ADD COLUMN `facebook_url` VARCHAR(500) NULL AFTER `instagram_url`",
    "ALTER TABLE `facilities` ADD COLUMN `youtube_url` VARCHAR(500) NULL AFTER `facebook_url`",
    "ALTER TABLE `facilities` ADD COLUMN `twilio_sid` VARCHAR(255) NULL AFTER `youtube_url`",
    "ALTER TABLE `facilities` ADD COLUMN `twilio_auth_token` VARCHAR(255) NULL AFTER `twilio_sid`",
    "ALTER TABLE `facilities` ADD COLUMN `twilio_from_number` VARCHAR(20) NULL AFTER `twilio_auth_token`",
    "ALTER TABLE `facilities` ADD COLUMN `twilio_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `twilio_from_number`",
];

foreach ($sqls as $s) {
    try {
        $pdo->exec($s);
        echo "OK: " . substr($s, 0, 70) . "\n";
    } catch (Exception $e) {
        echo "SKIP/WARN: " . $e->getMessage() . "\n";
    }
}
echo "Done\n";
