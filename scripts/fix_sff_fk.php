<?php
$pdo = new PDO('mysql:host=localhost;dbname=k2pickleball', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $pdo->exec('ALTER TABLE session_form_fields MODIFY session_type_id BIGINT(20) UNSIGNED NOT NULL');
    echo "OK: Fixed column type to bigint\n";
} catch (Exception $e) {
    echo "ERR: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec('ALTER TABLE session_form_fields ADD CONSTRAINT fk_sff_session_type FOREIGN KEY (session_type_id) REFERENCES session_types(id) ON DELETE CASCADE');
    echo "OK: Added foreign key\n";
} catch (Exception $e) {
    echo "ERR: " . $e->getMessage() . "\n";
}
echo "Done.\n";
