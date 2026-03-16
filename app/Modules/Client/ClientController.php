<?php

declare(strict_types=1);

namespace App\Modules\Client;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;

class ClientController extends Controller
{
    private array $viewMap = [
        // Marketing pages
        'home'                  => 'client/home.php',
        'product'               => 'client/product.php',
        'about'                 => 'client/about.php',
        'contact'               => 'client/contact.php',
        'demo'                  => 'client/demo.php',
        'pricing'               => 'client/pricing.php',
        'privacy-policy'        => 'client/privacy-policy.php',
        'terms'                 => 'client/terms.php',

        // Auth pages
        'login'                 => 'client/auth/login.php',
        'register'              => 'client/auth/register.php',
        'forgot-password'       => 'client/auth/forgot-password.php',
        'reset-password'        => 'client/auth/reset-password.php',
        'verify-email'          => 'client/auth/verify-email.php',
    ];

    public function handleRequest(Request $request): Response
    {
        $path = trim($request->path(), '/');

        // Root path → home
        if ($path === '' || $path === null) {
            $path = 'home';
        }

        // Exact match
        if (isset($this->viewMap[$path])) {
            return $this->renderView($this->viewMap[$path]);
        }

        return Response::html($this->renderNotFound(), 404);
    }

    /** Views that are standalone (no layout wrapper) */
    private const STANDALONE_VIEWS = [
        'client/auth/login.php',
        'client/auth/register.php',
        'client/auth/forgot-password.php',
        'client/auth/reset-password.php',
        'client/auth/verify-email.php',
    ];

    private function renderView(string $viewFile, array $params = []): Response
    {
        $viewPath = dirname(__DIR__, 2) . '/Views/' . $viewFile;
        if (!file_exists($viewPath)) {
            return Response::html($this->renderNotFound(), 404);
        }

        $scriptName  = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath    = rtrim(dirname(dirname($scriptName)), '/\\');
        $params['baseUrl'] = ($basePath === '' || $basePath === '.') ? '' : $basePath;
        $params['contentView'] = $viewFile;

        // Determine which layout to use
        if (in_array($viewFile, self::STANDALONE_VIEWS, true)) {
            // Auth pages render without a layout wrapper
            extract($params);
            ob_start();
            include $viewPath;
            $html = ob_get_clean();
            return Response::html($html);
        }

        $layoutPath = dirname(__DIR__, 2) . '/Views/layouts/client.php';

        extract($params);
        ob_start();
        include $layoutPath;
        $html = ob_get_clean();

        return Response::html($html);
    }

    private function renderNotFound(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | K2 Pickleball</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-gray-950 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-8xl font-extrabold text-emerald-500/20">404</h1>
        <p class="mt-4 text-xl font-semibold text-white">Page not found</p>
        <p class="mt-2 text-gray-400">The page you're looking for doesn't exist.</p>
        <a href="/" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Home
        </a>
    </div>
</body>
</html>
HTML;
    }
}
