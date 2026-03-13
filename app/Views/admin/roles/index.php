<?php
$title = 'Roles & Permissions';
$breadcrumbs = [['label' => 'Roles']];

$tableId = 'roles-table';
$apiUrl = ($baseUrl ?? '') . '/api/roles';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'slug', 'label' => 'Slug'],
    ['key' => 'is_system', 'label' => 'System', 'render' => "function(row) {
        return row.is_system ? '<span class=\"inline-block rounded-full bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 px-2 py-0.5 text-xs font-medium\">System</span>' : '<span class=\"text-surface-400 text-xs\">Custom</span>';
    }"],
    ['key' => 'description', 'label' => 'Description'],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/roles/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/roles/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$createUrl = ($baseUrl ?? '') . '/admin/roles/create';
$createLabel = 'Add Role';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
