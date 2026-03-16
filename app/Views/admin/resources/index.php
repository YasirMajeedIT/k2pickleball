<?php
$title = 'Resources';
$breadcrumbs = [['label' => 'Resources']];

$tableId = 'resources-table';
$apiUrl = ($baseUrl ?? '') . '/api/resources';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'field_type', 'label' => 'Field Type', 'render' => "function(row) { var m = {checkbox:'Checkbox',selectbox:'Select Box',radio:'Radio'}; return m[row.field_type] || row.field_type || '-'; }"],
    ['key' => 'description', 'label' => 'Description', 'render' => "function(row) { return (row.description || '-').substring(0, 60) + (row.description && row.description.length > 60 ? '...' : ''); }"],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/resources/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/resources/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$createUrl = ($baseUrl ?? '') . '/admin/resources/create';
$createLabel = 'Add Resource';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
