<?php

declare(strict_types=1);

namespace App\Modules\Admin;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;

class AdminController extends Controller
{
    /**
     * Map of admin routes to view files.
     */
    private array $viewMap = [
        // Auth
        'login'                     => 'admin/login.php',

        // Dashboard
        'dashboard'                 => 'admin/dashboard.php',

        // Facilities
        'facilities'                => 'admin/facilities/index.php',
        'facilities/create'         => 'admin/facilities/create.php',
        'facilities/{id}'           => 'admin/facilities/show.php',
        'facilities/{id}/edit'      => 'admin/facilities/edit.php',

        // Courts
        'courts'                    => 'admin/courts/index.php',
        'courts/create'             => 'admin/courts/create.php',
        'courts/{id}'               => 'admin/courts/show.php',
        'courts/{id}/edit'          => 'admin/courts/edit.php',

        // Users
        'users'                     => 'admin/users/index.php',
        'users/create'              => 'admin/users/create.php',
        'users/{id}'                => 'admin/users/show.php',
        'users/{id}/edit'           => 'admin/users/edit.php',

        // Roles
        'roles'                     => 'admin/roles/index.php',
        'roles/create'              => 'admin/roles/create.php',
        'roles/{id}'                => 'admin/roles/show.php',
        'roles/{id}/edit'           => 'admin/roles/edit.php',

        // Subscriptions
        'subscriptions'             => 'admin/subscriptions/index.php',
        'subscriptions/{id}'        => 'admin/subscriptions/show.php',

        // Payments
        'payments'                  => 'admin/payments/index.php',

        // Notifications
        'notifications'             => 'admin/notifications/index.php',
        'notifications/{id}'        => 'admin/notifications/show.php',

        // Files
        'files'                     => 'admin/files/index.php',
        'files/upload'              => 'admin/files/upload.php',

        // API Tokens
        'api-tokens'                => 'admin/api-tokens/index.php',
        'api-tokens/create'         => 'admin/api-tokens/create.php',

        // Audit Logs
        'audit-logs'                => 'admin/audit-logs/index.php',
        'audit-logs/{id}'           => 'admin/audit-logs/show.php',

        // Settings
        'settings'                  => 'admin/settings/index.php',

        // Extensions
        'extensions'                => 'admin/extensions/index.php',

        // Categories
        'categories'                => 'admin/categories/index.php',
        'categories/create'         => 'admin/categories/create.php',
        'categories/{id}/edit'      => 'admin/categories/edit.php',

        // Resources
        'resources'                 => 'admin/resources/index.php',
        'resources/create'          => 'admin/resources/create.php',
        'resources/{id}'            => 'admin/resources/show.php',
        'resources/{id}/edit'       => 'admin/resources/edit.php',

        // Schedule Dashboard
        'schedule-dashboard'                        => 'admin/schedule-dashboard/index.php',
        'schedule-dashboard/session-types'          => 'admin/schedule-dashboard/session-types-list.php',
        'schedule-dashboard/session-types/create'   => 'admin/schedule-dashboard/add-session-type.php',
        'schedule-dashboard/session-types/{id}/edit'=> 'admin/schedule-dashboard/edit-session-type.php',
        'schedule-dashboard/sessions'               => 'admin/schedule-dashboard/session-details-list.php',

        // Players
        'players'                   => 'admin/players/index.php',
        'players/create'            => 'admin/players/create.php',
        'players/{id}'              => 'admin/players/show.php',
        'players/{id}/edit'         => 'admin/players/edit.php',

        // Credit Codes
        'credit-codes'              => 'admin/credit-codes/index.php',
        'credit-codes/create'       => 'admin/credit-codes/create.php',
        'credit-codes/{id}'         => 'admin/credit-codes/show.php',
        'credit-codes/{id}/edit'    => 'admin/credit-codes/edit.php',

        // Gift Certificates
        'gift-certificates'              => 'admin/gift-certificates/index.php',
        'gift-certificates/create'       => 'admin/gift-certificates/create.php',
        'gift-certificates/{id}'         => 'admin/gift-certificates/show.php',
        'gift-certificates/{id}/edit'    => 'admin/gift-certificates/edit.php',

        // Discounts
        'discounts'                      => 'admin/discounts/index.php',

        // Waivers
        'waivers'                        => 'admin/waivers/index.php',

        // Membership Plans
        'membership-plans'               => 'admin/membership-plans/index.php',
        'membership-plans/create'        => 'admin/membership-plans/create.php',
        'membership-plans/{id}'          => 'admin/membership-plans/show.php',
        'membership-plans/{id}/edit'     => 'admin/membership-plans/edit.php',

        // Invoices
        'invoices'                       => 'admin/invoices/index.php',
        'invoices/create'                => 'admin/invoices/create.php',
        'invoices/{id}'                  => 'admin/invoices/show.php',
        'invoices/{id}/edit'             => 'admin/invoices/edit.php',

        // Design
        'design/navigation'         => 'admin/design/navigation.php',
        'design/theme'              => 'admin/design/theme.php',

        // Content (Pages & Forms)
        'pages'                     => 'admin/pages/index.php',
        'pages/create'              => 'admin/pages/create.php',
        'pages/{id}'                => 'admin/pages/show.php',
        'pages/{id}/edit'           => 'admin/pages/edit.php',
        'forms'                     => 'admin/forms/index.php',
        'forms/create'              => 'admin/forms/create.php',
        'forms/{id}'                => 'admin/forms/show.php',
        'forms/{id}/edit'           => 'admin/forms/edit.php',

        // My Account
        'account'                   => 'admin/account/index.php',
        'my-subscription'           => 'admin/my-subscription/index.php',
        'my-invoices'               => 'admin/my-invoices/index.php',
    ];

    public function handleRequest(Request $request, int|null $id = null): Response
    {
        $path = trim($request->path(), '/');

        // Strip 'admin' prefix
        $path = preg_replace('#^admin/?#', '', $path);

        // Default to dashboard
        if ($path === '' || $path === null) {
            $path = 'dashboard';
        }

        // Try exact match first
        if (isset($this->viewMap[$path])) {
            return $this->renderView($this->viewMap[$path]);
        }

        // Try pattern match for {id} routes
        foreach ($this->viewMap as $pattern => $viewFile) {
            if (!str_contains($pattern, '{id}')) {
                continue;
            }
            $regex = '#^' . str_replace('{id}', '([a-zA-Z0-9_-]+)', $pattern) . '$#';
            if (preg_match($regex, $path, $matches)) {
                return $this->renderView($viewFile, ['id' => $matches[1]]);
            }
        }

        // Fallback: 404
        return Response::html($this->renderNotFound(), 404);
    }

    private function renderView(string $viewFile, array $params = []): Response
    {
        $viewPath = dirname(__DIR__, 2) . '/Views/' . $viewFile;

        if (!file_exists($viewPath)) {
            return Response::html($this->renderNotFound(), 404);
        }

        // Always inject the application base URL so views can build correct API paths
        $scriptName  = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath    = rtrim(dirname(dirname($scriptName)), '/\\');
        $baseUrl     = ($basePath === '' || $basePath === '.') ? '' : $basePath;
        $params['baseUrl'] = $baseUrl;

        // Extract params into scope
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-300 dark:text-gray-700">404</h1>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Page not found</p>
        <a href="/admin" class="mt-6 inline-block rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Back to Dashboard</a>
    </div>
</body>
</html>
HTML;
    }
}
