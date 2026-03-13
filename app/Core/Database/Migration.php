<?php

declare(strict_types=1);

namespace App\Core\Database;

/**
 * Database migration runner.
 * Reads SQL migration files from the migrations directory and tracks execution.
 */
final class Migration
{
    private Connection $db;
    private string $migrationsPath;
    private string $migrationsTable = '_migrations';

    public function __construct(Connection $db, string $migrationsPath)
    {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;
        $this->ensureMigrationsTable();
    }

    /**
     * Run all pending migrations.
     */
    public function migrate(): array
    {
        $executed = $this->getExecutedMigrations();
        $pending = $this->getPendingMigrations($executed);
        $results = [];

        foreach ($pending as $migration) {
            $this->executeMigration($migration);
            $results[] = $migration;
            echo "  ✓ Migrated: {$migration}\n";
        }

        if (empty($results)) {
            echo "  Nothing to migrate.\n";
        }

        return $results;
    }

    /**
     * Rollback the last batch of migrations.
     */
    public function rollback(): array
    {
        $lastBatch = $this->getLastBatch();

        if ($lastBatch === 0) {
            echo "  Nothing to rollback.\n";
            return [];
        }

        $migrations = $this->db->fetchAll(
            "SELECT migration FROM `{$this->migrationsTable}` WHERE batch = ? ORDER BY id DESC",
            [$lastBatch]
        );

        $results = [];
        foreach ($migrations as $row) {
            $downFile = $this->migrationsPath . '/' . str_replace('.sql', '.down.sql', $row['migration']);
            if (file_exists($downFile)) {
                $sql = file_get_contents($downFile);
                $this->db->getPdo()->exec($sql);
            }

            $this->db->delete($this->migrationsTable, ['migration' => $row['migration']]);
            $results[] = $row['migration'];
            echo "  ✓ Rolled back: {$row['migration']}\n";
        }

        return $results;
    }

    /**
     * Create fresh database by running the schema file.
     */
    public function fresh(): void
    {
        $schemaFile = dirname($this->migrationsPath) . '/schema.sql';

        if (!file_exists($schemaFile)) {
            echo "  Schema file not found: {$schemaFile}\n";
            return;
        }

        echo "  Dropping all tables...\n";
        $this->db->getPdo()->exec("SET FOREIGN_KEY_CHECKS = 0");

        $tables = $this->db->fetchAll("SHOW TABLES");
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            $this->db->getPdo()->exec("DROP TABLE IF EXISTS `{$tableName}`");
        }

        $this->db->getPdo()->exec("SET FOREIGN_KEY_CHECKS = 1");

        echo "  Running schema...\n";
        $sql = file_get_contents($schemaFile);
        $this->db->getPdo()->exec($sql);

        echo "  ✓ Database freshly created.\n";
    }

    /**
     * Get migration status.
     */
    public function status(): array
    {
        $executed = $this->getExecutedMigrations();
        $files = $this->getMigrationFiles();
        $status = [];

        foreach ($files as $file) {
            $status[] = [
                'migration' => $file,
                'status' => in_array($file, $executed) ? 'Ran' : 'Pending',
            ];
        }

        return $status;
    }

    // -- Private --

    private function ensureMigrationsTable(): void
    {
        $this->db->getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$this->migrationsTable}` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `migration` VARCHAR(255) NOT NULL,
                `batch` INT UNSIGNED NOT NULL,
                `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function getExecutedMigrations(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT migration FROM `{$this->migrationsTable}` ORDER BY id"
        );

        return array_column($rows, 'migration');
    }

    private function getMigrationFiles(): array
    {
        $files = glob($this->migrationsPath . '/*.sql');
        $migrations = [];

        foreach ($files as $file) {
            $filename = basename($file);
            // Skip .down.sql files
            if (str_ends_with($filename, '.down.sql')) {
                continue;
            }
            $migrations[] = $filename;
        }

        sort($migrations);
        return $migrations;
    }

    private function getPendingMigrations(array $executed): array
    {
        $files = $this->getMigrationFiles();
        return array_diff($files, $executed);
    }

    private function executeMigration(string $migration): void
    {
        $file = $this->migrationsPath . '/' . $migration;

        if (!file_exists($file)) {
            throw new \RuntimeException("Migration file not found: {$file}");
        }

        $sql = file_get_contents($file);
        $this->db->getPdo()->exec($sql);

        $batch = $this->getLastBatch() + 1;
        $this->db->insert($this->migrationsTable, [
            'migration' => $migration,
            'batch' => $batch,
        ]);
    }

    private function getLastBatch(): int
    {
        $result = $this->db->fetch(
            "SELECT MAX(batch) as batch FROM `{$this->migrationsTable}`"
        );

        return (int) ($result['batch'] ?? 0);
    }
}
