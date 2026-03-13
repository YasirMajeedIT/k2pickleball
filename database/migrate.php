<?php

declare(strict_types=1);

/**
 * Database Migration Runner
 * 
 * Usage: php database/migrate.php
 * 
 * Reads schema.sql and executes it against the configured database.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbName = $_ENV['DB_NAME'] ?? 'k2pickleball';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

echo "🔧 K2 Pickleball - Database Migration\n";
echo str_repeat('=', 50) . "\n\n";

try {
    // Connect without database first to create it if needed
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Create database if it doesn't exist
    echo "📦 Creating database '{$dbName}' if not exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbName}`");
    echo "✅ Database ready.\n\n";

    // Read and execute schema
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        echo "❌ Error: schema.sql not found at {$schemaFile}\n";
        exit(1);
    }

    echo "📄 Reading schema.sql...\n";
    $sql = file_get_contents($schemaFile);

    // Split by semicolons (naive but works for our schema)
    $statements = array_filter(
        array_map(function($s) {
            return trim(preg_replace('/--.*$/m', '', trim($s)));
        }, explode(';', $sql)),
        fn($s) => !empty($s)
    );

    $count = 0;
    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
            $count++;

            // Extract table name for display
            if (preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?(\w+)`?/i', $statement, $m)) {
                echo "  ✅ Created table: {$m[1]}\n";
            } elseif (preg_match('/CREATE\s+(?:UNIQUE\s+)?INDEX\s+`?(\w+)`?/i', $statement, $m)) {
                echo "  ✅ Created index: {$m[1]}\n";
            }
        } catch (PDOException $e) {
            // Table already exists is OK
            if (str_contains($e->getMessage(), 'already exists')) {
                if (preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?(\w+)`?/i', $statement, $m)) {
                    echo "  ⏭️  Table exists: {$m[1]}\n";
                }
                $count++;
            } else {
                echo "  ❌ Error: " . $e->getMessage() . "\n";
                echo "  Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }

    echo "\n✅ Migration complete! Executed {$count} statements.\n";

} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
