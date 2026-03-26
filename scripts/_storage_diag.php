<?php
// TEMPORARY diagnostic - delete after use
$storagePath = dirname(__DIR__) . '/storage';
$uploadsPath = $storagePath . '/uploads';

echo "PHP running as user: " . get_current_user() . "\n";
echo "Process user (posix): ";
if (function_exists('posix_getpwuid')) {
    $u = posix_getpwuid(posix_geteuid());
    echo $u['name'] . "\n";
} else {
    echo shell_exec('whoami') . "\n";
}

echo "\nstorage path: $storagePath\n";
echo "storage exists: " . (is_dir($storagePath) ? 'YES' : 'NO') . "\n";
echo "storage writable: " . (is_writable($storagePath) ? 'YES' : 'NO') . "\n";
echo "storage owner: " . fileowner($storagePath) . " / perms: " . substr(sprintf('%o', fileperms($storagePath)), -4) . "\n";

echo "\nuploads path: $uploadsPath\n";
echo "uploads exists: " . (is_dir($uploadsPath) ? 'YES' : 'NO') . "\n";
echo "uploads writable: " . (is_writable($uploadsPath) ? 'YES' : 'NO') . "\n";

// Try to create a test subdir
$testDir = $uploadsPath . '/test_write_' . time();
$made = @mkdir($testDir, 0775, true);
echo "mkdir test: " . ($made ? 'SUCCESS' : 'FAILED - ' . error_get_last()['message']) . "\n";
if ($made) rmdir($testDir);

// Check .env STORAGE_PATH override
$env = file_get_contents(dirname(__DIR__) . '/.env');
preg_match('/STORAGE_PATH=(.+)/', $env, $m);
echo "\nSTORAGE_PATH in .env: " . (isset($m[1]) ? trim($m[1]) : '(not set, using default)') . "\n";
