<?php

declare(strict_types=1);

use App\Core\Http\Router;

return function (Router $router): void {
    $router->group(['permission' => 'audit_logs.view'], function (Router $router) {
        $router->get('/api/audit-logs', ['App\\Modules\\AuditLogs\\AuditLogController', 'index']);
        $router->get('/api/audit-logs/{entityType}/{entityId}', ['App\\Modules\\AuditLogs\\AuditLogController', 'entity']);
    });
};
