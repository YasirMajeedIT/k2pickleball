<?php
$title = 'Audit Logs';
$breadcrumbs = [['label' => 'Audit Logs']];

$tableId = 'platform-audit-logs-table';
$apiUrl = ($baseUrl ?? '') . '/api/platform/audit-logs';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'action', 'label' => 'Action', 'render' => "function(row) {
        const colors = { created: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', updated: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', deleted: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.action] || 'bg-surface-100 text-surface-600') + '\">' + (row.action || '-') + '</span>';
    }"],
    ['key' => 'entity_type', 'label' => 'Entity', 'render' => "function(row) { return (row.entity_type || '-') + (row.entity_id ? ' #' + row.entity_id : ''); }"],
    ['key' => 'user_email', 'label' => 'User', 'render' => "function(row) { return row.first_name ? row.first_name + ' ' + row.last_name : (row.user_email || '-'); }"],
    ['key' => 'organization_name', 'label' => 'Organization', 'render' => "function(row) { return row.organization_name || '<span class=\"text-surface-400\">System</span>'; }"],
    ['key' => 'created_at', 'label' => 'Date', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleString() : '-'; }"],
];
$actions = [];
$createUrl = null;
$createLabel = null;

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/platform.php';
?>
