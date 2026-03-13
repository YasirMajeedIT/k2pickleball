<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->get('/api/audit-logs', ['App\\Modules\\AuditLogs\\AuditLogController', 'index']);
    $router->get('/api/audit-logs/{entityType}/{entityId}', ['App\\Modules\\AuditLogs\\AuditLogController', 'entity']);
};
