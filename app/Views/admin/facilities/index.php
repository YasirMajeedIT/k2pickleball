<?php
$title = 'Facilities';
$breadcrumbs = [['label' => 'Facilities']];

$tableId = 'facilities-table';
$apiUrl = ($baseUrl ?? '') . '/api/facilities';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'city', 'label' => 'City', 'sortable' => true],
    ['key' => 'state', 'label' => 'State'],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        const colors = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', inactive: 'bg-surface-100 text-surface-600', maintenance: 'bg-yellow-100 text-yellow-700' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/facilities/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/facilities/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$deleteAction = ($baseUrl ?? '') . '/api/facilities/{id}';
$deletePermission = 'facilities.delete';
$createUrl = ($baseUrl ?? '') . '/admin/facilities/create';
$createLabel = 'Add Facility';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
