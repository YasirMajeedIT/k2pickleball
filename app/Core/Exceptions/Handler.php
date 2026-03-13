<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use App\Core\Http\Response;
use App\Core\Services\Config;

/**
 * Global exception handler.
 * Converts all exceptions to JSON API responses and logs them.
 */
final class Handler
{
    /**
     * Register as the global exception/error handler.
     */
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Handle uncaught exceptions.
     */
    public static function handleException(\Throwable $e): void
    {
        $statusCode = 500;
        $errors = [];

        if ($e instanceof AppException) {
            $statusCode = $e->getStatusCode();
            $errors = $e->getErrors();
        }

        self::log($e);

        $body = [
            'status' => 'error',
            'message' => $e instanceof AppException ? $e->getMessage() : 'Internal Server Error',
        ];

        if (!empty($errors)) {
            $body['errors'] = $errors;
        }

        $debug = Config::get('app.debug', false);
        if ($debug && !($e instanceof AppException)) {
            $body['message'] = $e->getMessage();
            $body['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString()),
            ];
        }

        $response = Response::json($body, $statusCode);
        $response->send();
    }

    /**
     * Convert PHP errors to exceptions.
     */
    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Handle fatal errors on shutdown.
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            self::handleException(
                new \ErrorException(
                    $error['message'],
                    0,
                    $error['type'],
                    $error['file'],
                    $error['line']
                )
            );
        }
    }

    /**
     * Log an exception to the log file.
     */
    private static function log(\Throwable $e): void
    {
        $logPath = K2_ROOT . '/' . ($_ENV['LOG_PATH'] ?? 'storage/logs');
        $logFile = $logPath . '/app_' . date('Y-m-d') . '.log';

        if (!is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }

        $entry = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }
}
