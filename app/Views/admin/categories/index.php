<?php
$title = 'Categories';
$breadcrumbs = [['label' => 'Categories']];

$tableId = 'categories-table';
$apiUrl = ($baseUrl ?? '') . '/api/categories';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'color', 'label' => 'Color', 'render' => "function(row) {
        if (!row.color) return '-';
        return '<span class=\"inline-flex items-center gap-2\"><span class=\"h-4 w-4 rounded-full border border-surface-200 dark:border-surface-600\" style=\"background:' + row.color + '\"></span><span class=\"text-xs font-mono text-surface-500\">' + row.color + '</span></span>';
    }"],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'sort_order', 'label' => 'Order', 'sortable' => true],
    ['key' => 'is_taxable', 'label' => 'Taxable', 'render' => "function(row) {
        return row.is_taxable ? '<span class=\"inline-block rounded-full bg-green-100 dark:bg-green-500/10 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400\">Yes</span>' : '<span class=\"inline-block rounded-full bg-surface-100 dark:bg-surface-700 px-2 py-0.5 text-xs font-medium text-surface-500\">No</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/categories/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$createUrl = ($baseUrl ?? '') . '/admin/categories/create';
$createLabel = 'Add Category';
$deleteAction = ($baseUrl ?? '') . '/api/categories/{id}';
$deletePermission = 'categories.delete';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
