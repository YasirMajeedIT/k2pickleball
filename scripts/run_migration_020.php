<?php
/**
 * Run migration 020: Add payment/booking columns to st_class_attendees
 */
require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}",
        $config['user'], $config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "Running migration 020: Add attendee payment columns...\n";

    // Add columns one at a time to handle "already exists" gracefully
    $columns = [
        "ADD COLUMN payment_method VARCHAR(30) DEFAULT 'manual' AFTER quote_amount",
        "ADD COLUMN payment_id BIGINT UNSIGNED NULL AFTER payment_method",
        "ADD COLUMN square_payment_id VARCHAR(100) NULL AFTER payment_id",
        "ADD COLUMN payment_status VARCHAR(30) DEFAULT 'pending' AFTER square_payment_id",
        "ADD COLUMN discount_code VARCHAR(50) NULL AFTER payment_status",
        "ADD COLUMN discount_amount DECIMAL(10,2) DEFAULT 0.00 AFTER discount_code",
        "ADD COLUMN credit_code_id BIGINT UNSIGNED NULL AFTER discount_amount",
        "ADD COLUMN credit_amount DECIMAL(10,2) DEFAULT 0.00 AFTER credit_code_id",
        "ADD COLUMN gift_certificate_id BIGINT UNSIGNED NULL AFTER credit_amount",
        "ADD COLUMN gift_amount DECIMAL(10,2) DEFAULT 0.00 AFTER gift_certificate_id",
        "ADD COLUMN refunded_amount DECIMAL(10,2) DEFAULT 0.00 AFTER gift_amount",
        "ADD COLUMN cancelled_at DATETIME NULL AFTER refunded_amount",
        "ADD COLUMN cancelled_reason TEXT NULL AFTER cancelled_at",
    ];

    foreach ($columns as $col) {
        try {
            $pdo->exec("ALTER TABLE st_class_attendees $col");
            echo "  OK: $col\n";
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate column name')) {
                echo "  SKIP (exists): $col\n";
            } else {
                throw $e;
            }
        }
    }

    // Add indexes
    $indexes = [
        'idx_sca_payment_id' => 'payment_id',
        'idx_sca_square_payment_id' => 'square_payment_id',
        'idx_sca_credit_code_id' => 'credit_code_id',
        'idx_sca_gift_certificate_id' => 'gift_certificate_id',
    ];
    foreach ($indexes as $name => $col) {
        try {
            $pdo->exec("ALTER TABLE st_class_attendees ADD INDEX $name ($col)");
            echo "  OK: INDEX $name ($col)\n";
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate key name')) {
                echo "  SKIP (exists): INDEX $name\n";
            } else {
                throw $e;
            }
        }
    }

    echo "\nMigration 020 completed successfully!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
