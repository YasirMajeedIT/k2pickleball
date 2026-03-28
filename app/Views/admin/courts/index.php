<?php
$title = 'Courts';
$breadcrumbs = [['label' => 'Courts']];

$tableId = 'courts-table';
$apiUrl = ($baseUrl ?? '') . '/api/courts';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'facility_name', 'label' => 'Facility'],
    ['key' => 'surface_type', 'label' => 'Surface'],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        const colors = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', inactive: 'bg-surface-100 text-surface-600', maintenance: 'bg-yellow-100 text-yellow-700', reserved: 'bg-blue-100 text-blue-700' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/courts/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/courts/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$deleteAction = ($baseUrl ?? '') . '/api/courts/{id}';
$deletePermission = 'courts.delete';
$createUrl = ($baseUrl ?? '') . '/admin/courts/create';
$createLabel = 'Add Court';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
