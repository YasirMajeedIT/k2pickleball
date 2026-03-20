<?php

declare(strict_types=1);

namespace App\Modules\Client;

use App\Core\Database\Connection;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Modules\SiteSettings\SiteSettingsRepository;

class ClientController extends Controller
{
    private SiteSettingsRepository $siteSettings;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->siteSettings = new SiteSettingsRepository($db);
    }

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

        // Customer portal
        'portal'                => 'client/portal/dashboard.php',
        'portal/dashboard'      => 'client/portal/dashboard.php',
        'portal/subscription'   => 'client/portal/subscription.php',
        'portal/invoices'       => 'client/portal/invoices.php',
        'portal/settings'       => 'client/portal/settings.php',
    ];

    public function handleRequest(Request $request): Response
    {
        $path = trim($request->path(), '/');

        // Root path → home
        if ($path === '' || $path === null) {
            $path = 'home';
        }

        // Auth/portal routes bypass the maintenance gate
        $bypassPaths = ['login', 'register', 'forgot-password', 'reset-password', 'verify-email'];
        $isPortal = str_starts_with($path, 'portal');
        $isBypass = in_array($path, $bypassPaths, true) || $isPortal;

        if (!$isBypass) {
            $gate = $this->checkSiteGate($request);
            if ($gate !== null) {
                return $gate;
            }
        }

        // Exact match
        if (isset($this->viewMap[$path])) {
            return $this->renderView($this->viewMap[$path]);
        }

        return Response::html($this->renderNotFound(), 404);
    }

    /**
     * Check maintenance mode and password protection.
     * Returns a Response if the visitor should be blocked, null otherwise.
     */
    private function checkSiteGate(Request $request): ?Response
    {
        $maintenance = $this->siteSettings->get('maintenance_mode', false);
        $passwordProtected = $this->siteSettings->get('password_protection_enabled', false);

        // --- Maintenance mode ---
        if ($maintenance) {
            // Check allowed IPs
            $allowedIps = $this->siteSettings->get('maintenance_allowed_ips', []);
            $visitorIp = $request->ip();
            if (!is_array($allowedIps) || !in_array($visitorIp, $allowedIps, true)) {
                $message = $this->siteSettings->get('maintenance_message', 'We are performing scheduled maintenance. Please check back soon.');
                return Response::html($this->renderMaintenancePage($message), 503);
            }
        }

        // --- Password protection ---
        if ($passwordProtected) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Handle password form submission
            if ($request->method() === 'POST' && $request->input('_site_password') !== null) {
                $sitePassword = $this->siteSettings->get('site_password', '');
                $submitted = (string) $request->input('_site_password');
                if ($sitePassword !== '' && hash_equals($sitePassword, $submitted)) {
                    $_SESSION['site_password_verified'] = true;
                    // Redirect to same page to clear POST
                    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
                    $basePath = rtrim(dirname(dirname($scriptName)), '/\\');
                    $basePath = ($basePath === '' || $basePath === '.') ? '' : $basePath;
                    $redirectUrl = $basePath . '/' . ltrim($request->path(), '/');
                    return Response::redirect($redirectUrl);
                }
                return Response::html($this->renderPasswordPage(true), 200);
            }

            if (empty($_SESSION['site_password_verified'])) {
                return Response::html($this->renderPasswordPage(false), 200);
            }
        }

        return null;
    }

    private function renderMaintenancePage(string $message): string
    {
        $escapedMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance | K2 Pickleball</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Poppins',sans-serif;background:#060d1a;}</style>
</head>
<body class="flex items-center justify-center min-h-screen px-4">
    <div class="text-center max-w-lg">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-[#162844] border border-[rgba(212,175,55,0.15)] mb-8">
            <svg class="w-10 h-10 text-[#d4af37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-5.47-3.16a1 1 0 00-1.45.86v6.34a1 1 0 001.45.86l5.47-3.16a1 1 0 000-1.74z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.42 15.17l-5.47-3.16a1 1 0 00-1.45.86v6.34a1 1 0 001.45.86l5.47-3.16a1 1 0 000-1.74z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 8.83l-5.47-3.16A1 1 0 004.5 6.53v6.34a1 1 0 001.45.86l5.47-3.16a1 1 0 000-1.74z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-white mb-3">We'll Be Right Back</h1>
        <p class="text-[#94a3b8] text-base leading-relaxed mb-8">{$escapedMessage}</p>
        <div class="flex items-center justify-center gap-2 text-xs text-[#475569]">
            <span class="font-bold text-white">K2</span>
            <span class="font-light text-[#d4af37]">Pickleball</span>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function renderPasswordPage(bool $error = false): string
    {
        $errorHtml = $error
            ? '<p class="text-red-400 text-sm mb-4 text-center">Incorrect password. Please try again.</p>'
            : '';
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Required | K2 Pickleball</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Poppins',sans-serif;background:#060d1a;}</style>
</head>
<body class="flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#162844] border border-[rgba(212,175,55,0.15)] mb-5">
                <svg class="w-8 h-8 text-[#d4af37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white mb-1">Password Required</h1>
            <p class="text-sm text-[#94a3b8]">Enter the site password to continue.</p>
        </div>
        {$errorHtml}
        <form method="POST" class="space-y-4">
            <input type="password" name="_site_password" required autofocus
                   class="w-full rounded-xl border border-[#1e3658] bg-[#0b1629] py-3 px-4 text-sm text-white placeholder-[#475569] focus:outline-none focus:border-[#d4af37] focus:ring-1 focus:ring-[#d4af37] transition-colors"
                   placeholder="Enter password">
            <button type="submit"
                    class="w-full py-3 rounded-xl text-sm font-semibold text-[#060d1a] transition-all"
                    style="background:linear-gradient(135deg,#d4af37 0%,#e8c84e 100%);">
                Enter Site
            </button>
        </form>
        <div class="mt-8 flex items-center justify-center gap-2 text-xs text-[#475569]">
            <span class="font-bold text-white">K2</span>
            <span class="font-light text-[#d4af37]">Pickleball</span>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /** Views that render standalone (no layout wrapper) */
    private const STANDALONE_VIEWS = [
        'client/auth/login.php',
        'client/auth/register.php',
        'client/auth/forgot-password.php',
        'client/auth/reset-password.php',
        'client/auth/verify-email.php',
    ];

    /** Views that use the portal layout */
    private const PORTAL_VIEWS = [
        'client/portal/dashboard.php',
        'client/portal/subscription.php',
        'client/portal/invoices.php',
        'client/portal/settings.php',
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

        // Standalone auth pages (no layout)
        if (in_array($viewFile, self::STANDALONE_VIEWS, true)) {
            extract($params);
            ob_start();
            include $viewPath;
            $html = ob_get_clean();
            return Response::html($html);
        }

        // Portal pages use the portal layout
        if (in_array($viewFile, self::PORTAL_VIEWS, true)) {
            $layoutPath = dirname(__DIR__, 2) . '/Views/layouts/portal.php';
            extract($params);
            ob_start();
            include $layoutPath;
            $html = ob_get_clean();
            return Response::html($html);
        }

        // Marketing pages use the client layout
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
