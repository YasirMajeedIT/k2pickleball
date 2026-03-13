<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Database\Connection;
use App\Core\Exceptions\TenantException;
use App\Core\Http\MiddlewareInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\Config;

/**
 * Tenant resolution middleware.
 * Resolves the current organization from subdomain, sets tenant context on request.
 *
 * Special subdomains:
 *   platform.* → Super Admin panel (no tenant scoping)
 *   admin.*    → Generic admin login (no tenant scoping until login)
 *   api.*      → API server (tenant from JWT or header)
 *   {slug}.*   → Organization tenant from subdomain slug
 */
final class TenantResolver implements MiddlewareInterface
{
    private Connection $db;

    /** Subdomains that bypass tenant resolution */
    private const SYSTEM_SUBDOMAINS = ['platform', 'admin', 'api', 'www', ''];

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function handle(Request $request, callable $next): Response
    {
        $subdomain = $request->subdomain();

        // Store subdomain context
        $request->setAttribute('subdomain', $subdomain);

        // Determine panel type
        $panelType = match ($subdomain) {
            'platform' => 'platform',
            'admin' => 'admin',
            'api', '' => 'api',
            default => 'tenant',
        };
        $request->setAttribute('panel_type', $panelType);

        // System subdomains don't need tenant resolution
        if (in_array($subdomain, self::SYSTEM_SUBDOMAINS, true)) {
            return $next($request);
        }

        // Resolve tenant from subdomain slug
        $organization = $this->resolveBySlug($subdomain);

        if ($organization === null) {
            // Try resolving by custom domain
            $organization = $this->resolveByDomain($request->host());
        }

        if ($organization === null) {
            throw new TenantException("Organization not found for subdomain: {$subdomain}");
        }

        // Check organization status
        if ($organization['status'] === 'suspended') {
            throw new TenantException('This organization has been suspended. Contact support.');
        }

        if ($organization['status'] === 'cancelled') {
            throw new TenantException('This organization account has been cancelled.');
        }

        // Set tenant context on request
        $request->setAttribute('tenant', $organization);
        $request->setAttribute('tenant_id', (int) $organization['id']);
        $request->setAttribute('organization_id', (int) $organization['id']);
        $request->setAttribute('organization', $organization);

        return $next($request);
    }

    private function resolveBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `organizations` WHERE `slug` = ? AND `status` IN ('active', 'trial') LIMIT 1",
            [$slug]
        );
    }

    private function resolveByDomain(string $domain): ?array
    {
        $domainRecord = $this->db->fetch(
            "SELECT `organization_id` FROM `organization_domains` WHERE `domain` = ? AND `verified_at` IS NOT NULL LIMIT 1",
            [$domain]
        );

        if ($domainRecord === null) {
            return null;
        }

        return $this->db->fetch(
            "SELECT * FROM `organizations` WHERE `id` = ? AND `status` IN ('active', 'trial') LIMIT 1",
            [$domainRecord['organization_id']]
        );
    }
}
