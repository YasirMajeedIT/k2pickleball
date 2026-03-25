<?php
/**
 * Reset super-admin password.
 * Usage: php scripts/reset_admin_password.php <new_password>
 *
 * Run on server:
 *   /usr/local/lsws/lsphp82/bin/php scripts/reset_admin_password.php YourNewPassword123
 */

if (php_sapi_name() !== 'cli') {
    die('CLI only.');
}

if (!isset($argv[1]) || strlen($argv[1]) < 8) {
    die("Usage: php scripts/reset_admin_password.php <new_password>\nPassword must be at least 8 characters.\n");
}

$newPassword = $argv[1];

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$db   = $_ENV['DB_NAME'] ?? 'k2pickleball';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Find the super admin (platform_role = 'super_admin' or the first user with lowest id)
$stmt = $pdo->query("SELECT id, email, first_name, platform_role FROM users WHERE platform_role = 'super_admin' ORDER BY id ASC LIMIT 5");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($admins)) {
    echo "No super_admin found. Showing first 5 users:\n";
    $stmt = $pdo->query("SELECT id, email, first_name, platform_role FROM users ORDER BY id ASC LIMIT 5");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo "Found accounts:\n";
foreach ($admins as $a) {
    echo "  ID={$a['id']}  email={$a['email']}  name={$a['first_name']}  role={$a['platform_role']}\n";
}

$targetId = $admins[0]['id'];
$targetEmail = $admins[0]['email'];

$hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $pdo->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$hash, $targetId]);

echo "\nPassword reset for: {$targetEmail} (ID: {$targetId})\n";
echo "Hash: {$hash}\n";
echo "Done! You can now login with your new password.\n";
