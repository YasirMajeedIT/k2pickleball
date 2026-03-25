<?php

declare(strict_types=1);

namespace App\Modules\Platform;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;

class PlatformController extends Controller
{
    private array $viewMap = [
        'dashboard'                     => 'platform/dashboard.php',
        'organizations'                 => 'platform/organizations/index.php',
        'organizations/create'          => 'platform/organizations/create.php',
        'organizations/{id}'            => 'platform/organizations/show.php',
        'organizations/{id}/edit'       => 'platform/organizations/edit.php',
        'plans'                         => 'platform/plans/index.php',
        'plans/create'                  => 'platform/plans/create.php',
        'plans/{id}/edit'               => 'platform/plans/edit.php',
        'subscriptions'                 => 'platform/subscriptions/index.php',
        'subscriptions/{id}'            => 'platform/subscriptions/show.php',
        'revenue'                       => 'platform/revenue/index.php',
        'system-users'                  => 'platform/system-users/index.php',
        'system-users/create'           => 'platform/system-users/create.php',
        'system-users/{id}'             => 'platform/system-users/show.php',
        'invoices'                      => 'platform/invoices/index.php',
        'extensions'                    => 'platform/extensions/index.php',
        'announcements'                 => 'platform/announcements/index.php',
        'system-settings'               => 'platform/system-settings/index.php',
        'audit-logs'                    => 'platform/audit-logs/index.php',
        'consultations'                 => 'platform/consultations/index.php',
        'contact-submissions'           => 'platform/contact-submissions/index.php',
        'site-settings'                 => 'platform/site-settings/index.php',
        'migrations'                    => 'platform/migrations/index.php',
    ];

    public function handleRequest(Request $request, int|null $id = null): Response
    {
        $path = trim($request->path(), '/');
        $path = preg_replace('#^platform/?#', '', $path);

        if ($path === '' || $path === null) {
            $path = 'dashboard';
        }

        if (isset($this->viewMap[$path])) {
            return $this->renderView($this->viewMap[$path]);
        }

        // Pattern match for {id} routes
        foreach ($this->viewMap as $pattern => $viewFile) {
            if (!str_contains($pattern, '{id}')) continue;
            $regex = '#^' . str_replace('{id}', '([a-zA-Z0-9_-]+)', $pattern) . '$#';
            if (preg_match($regex, $path, $matches)) {
                return $this->renderView($viewFile, ['id' => $matches[1]]);
            }
        }

        return Response::html($this->renderNotFound(), 404);
    }

    private function renderView(string $viewFile, array $params = []): Response
    {
        $viewPath = dirname(__DIR__, 2) . '/Views/' . $viewFile;
        if (!file_exists($viewPath)) {
            return Response::html($this->renderNotFound(), 404);
        }
        $scriptName  = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath    = rtrim(dirname(dirname($scriptName)), '/\\');
        $params['baseUrl'] = ($basePath === '' || $basePath === '.') ? '' : $basePath;
        extract($params);
        ob_start();
        include $viewPath;
        $html = ob_get_clean();
        return Response::html($html);
    }

    private function renderNotFound(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><title>404</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="text-center"><h1 class="text-6xl font-bold text-gray-300">404</h1><p class="mt-4 text-gray-500">Page not found</p><a href="/platform" class="mt-6 inline-block rounded-lg bg-purple-600 px-6 py-2 text-white hover:bg-purple-700">Back to Platform</a></div>
</body></html>
HTML;
    }
}
