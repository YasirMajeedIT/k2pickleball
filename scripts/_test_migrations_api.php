<?php
require dirname(__DIR__) . '/vendor/autoload.php';
$cfg = require dirname(__DIR__) . '/config/database.php';
$db = new App\Core\Database\Connection($cfg['host'], $cfg['port'], $cfg['name'], $cfg['user'], $cfg['pass']);
$ctrl = new App\Modules\Platform\PlatformApiController($db);

// Simulate the migrations() call
$request = new App\Core\Http\Request();
$response = $ctrl->migrations($request);
$body = $response->getBody();
$data = is_string($body) ? json_decode($body, true) : $body;

echo "Status: " . ($data['status'] ?? 'unknown') . "\n";
echo "Total migrations: " . ($data['data']['total'] ?? 0) . "\n";
$pending = 0; $success = 0; $failed = 0;
foreach (($data['data']['data'] ?? []) as $m) {
    if ($m['status'] === 'pending') $pending++;
    elseif ($m['status'] === 'success') $success++;
    else $failed++;
    echo "  [{$m['status']}] {$m['filename']}\n";
}
echo "\nSuccess: {$success}, Pending: {$pending}, Failed: {$failed}\n";
