<?php
$title = 'Organizations';
$breadcrumbs = [['label' => 'Organizations']];

$tableId = 'platform-orgs-table';
$apiUrl = ($baseUrl ?? '') . '/api/organizations';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'slug', 'label' => 'Slug'],
    ['key' => 'owner_email', 'label' => 'Owner'],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        const colors = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', inactive: 'bg-surface-100 text-surface-600', suspended: 'bg-red-100 text-red-700', trial: 'bg-blue-100 text-blue-700' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/platform/organizations/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/platform/organizations/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$createUrl = ($baseUrl ?? '') . '/platform/organizations/create';
$createLabel = 'Add Organization';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/platform.php';
?>
