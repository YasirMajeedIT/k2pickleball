<?php
$title = 'Audit Logs';
$breadcrumbs = [['label' => 'Audit Logs']];

$tableId = 'audit-logs-table';
$apiUrl = ($baseUrl ?? '') . '/api/audit-logs';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'first_name', 'label' => 'User', 'render' => "function(row) { return (row.first_name || row.last_name) ? ((row.first_name || '') + ' ' + (row.last_name || '')).trim() : '<span class=\"text-surface-400\">System</span>'; }"],
    ['key' => 'action', 'label' => 'Action', 'render' => "function(row) {
        const colors = { created: 'bg-green-100 text-green-700', updated: 'bg-blue-100 text-blue-700', deleted: 'bg-red-100 text-red-700', login: 'bg-purple-100 text-purple-700', logout: 'bg-surface-100 text-surface-600' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.action] || 'bg-surface-100 text-surface-600') + '\">' + (row.action || '-') + '</span>';
    }"],
    ['key' => 'entity_type', 'label' => 'Entity'],
    ['key' => 'entity_id', 'label' => 'Entity ID'],
    ['key' => 'ip_address', 'label' => 'IP Address'],
    ['key' => 'created_at', 'label' => 'Date', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleString() : '-'; }"],
];
$actions = [
    ['label' => 'Details', 'url' => ($baseUrl ?? '') . '/admin/audit-logs/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
];
$createUrl = null;
$createLabel = null;

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
