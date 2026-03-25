<?php
/**
 * GitHub Webhook Auto-Deploy Script
 * 
 * Place this file in your public/ directory on the server.
 * Configure GitHub webhook to POST to: https://k2pickleball.com/deploy.php
 * 
 * SETUP:
 * 1. Go to your GitHub repo → Settings → Webhooks → Add webhook
 * 2. Payload URL: https://k2pickleball.com/deploy.php
 * 3. Content type: application/json
 * 4. Secret: (enter the same secret you set in DEPLOY_SECRET below)
 * 5. Events: Just the push event
 * 
 * IMPORTANT: Change DEPLOY_SECRET to your own secret string!
 */

// ─── Configuration ───────────────────────────────────────
define('DEPLOY_SECRET', 'CHANGE_THIS_TO_A_RANDOM_SECRET_STRING');
define('DEPLOY_BRANCH', 'main');
define('PROJECT_ROOT', dirname(__DIR__)); // One level up from public/
define('LOG_FILE', PROJECT_ROOT . '/storage/logs/deploy.log');

// ─── Verify Request ──────────────────────────────────────
// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Verify GitHub signature
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (empty($signature)) {
    http_response_code(403);
    exit('No signature');
}

$expected = 'sha256=' . hash_hmac('sha256', $payload, DEPLOY_SECRET);
if (!hash_equals($expected, $signature)) {
    http_response_code(403);
    deployLog('REJECTED: Invalid signature');
    exit('Invalid signature');
}

// Parse payload
$data = json_decode($payload, true);
$ref = $data['ref'] ?? '';

// Only deploy on pushes to the configured branch
if ($ref !== 'refs/heads/' . DEPLOY_BRANCH) {
    http_response_code(200);
    deployLog("Ignored push to {$ref} (not " . DEPLOY_BRANCH . ")");
    exit('Not target branch');
}

// ─── Deploy ──────────────────────────────────────────────
deployLog('=== Deploy started ===');
deployLog('Commit: ' . ($data['head_commit']['message'] ?? 'unknown'));
deployLog('By: ' . ($data['pusher']['name'] ?? 'unknown'));

$commands = [
    'cd ' . escapeshellarg(PROJECT_ROOT),
    'git fetch origin ' . DEPLOY_BRANCH . ' 2>&1',
    'git reset --hard origin/' . DEPLOY_BRANCH . ' 2>&1',
    'composer install --no-dev --optimize-autoloader 2>&1',
];

$output = [];
foreach ($commands as $cmd) {
    $result = shell_exec($cmd);
    $output[] = "$ {$cmd}\n{$result}";
    deployLog("$ {$cmd}");
    deployLog($result ?: '(no output)');
}

deployLog('=== Deploy finished ===');

// Respond to GitHub
http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    'status'  => 'deployed',
    'branch'  => DEPLOY_BRANCH,
    'time'    => date('Y-m-d H:i:s'),
]);

function deployLog(string $msg): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . trim($msg) . "\n";
    @file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);
}
