<?php
$title = 'System Users';
$breadcrumbs = [['label' => 'System Users']];

$tableId = 'platform-users-table';
$apiUrl = ($baseUrl ?? '') . '/api/platform/users';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'first_name', 'label' => 'Name', 'sortable' => true, 'render' => "function(row) { return (row.first_name || '') + ' ' + (row.last_name || ''); }"],
    ['key' => 'email', 'label' => 'Email', 'sortable' => true],
    ['key' => 'organization_name', 'label' => 'Organization', 'render' => "function(row) { return row.organization_name || '<span class=\"text-surface-400\">None</span>'; }"],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        const colors = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', inactive: 'bg-surface-100 text-surface-600', suspended: 'bg-red-100 text-red-700' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/platform/system-users/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
];
$createUrl = ($baseUrl ?? '') . '/platform/system-users/create';
$createLabel = 'Create User';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/platform.php';
?>
