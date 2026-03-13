<?php

declare(strict_types=1);

/**
 * K2 Pickleball SaaS Platform - Single Entry Point
 *
 * All HTTP requests are routed through this file.
 */

// Prevent direct access to PHP files
define('K2_ROOT', dirname(__DIR__));
define('K2_START', microtime(true));

// Load Composer autoloader
require_once K2_ROOT . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(K2_ROOT);
$dotenv->load();

// Set error handling based on environment
if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

ini_set('log_errors', '1');
ini_set('error_log', K2_ROOT . '/' . ($_ENV['LOG_PATH'] ?? 'storage/logs') . '/php_errors.log');

// Set timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Boot the application
use App\Core\Application;

try {
    $app = Application::getInstance();
    $app->boot();
    $app->handleRequest();
} catch (\Throwable $e) {
    // Last-resort handler for bootstrap failures (before app is ready)
    App\Core\Exceptions\Handler::handleException($e);
}
