<?php
$title = 'Membership Plans';
$breadcrumbs = [['label' => 'Membership Plans']];

$tableId = 'membership-plans-table';
$apiUrl = ($baseUrl ?? '') . '/api/membership-plans';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'name', 'label' => 'Plan Name', 'sortable' => true, 'render' => "function(row) {
        var color = row.color || '#6366f1';
        return '<div class=\"flex items-center gap-2.5\">' +
            '<span class=\"w-2.5 h-2.5 rounded-full flex-shrink-0\" style=\"background:' + color + '\"></span>' +
            '<span class=\"font-semibold text-surface-800 dark:text-surface-100\">' + (row.name || '—') + '</span></div>';
    }"],
    ['key' => 'facility_name', 'label' => 'Facility', 'render' => "function(row) { return row.facility_name || '—'; }"],
    ['key' => 'duration_type', 'label' => 'Duration', 'render' => "function(row) {
        var labels = { monthly: '1 Month', '3months': '3 Months', '6months': '6 Months', '12months': '12 Months', custom: row.duration_value + ' Mo' };
        return '<span class=\"text-xs font-medium\">' + (labels[row.duration_type] || row.duration_type) + '</span>';
    }"],
    ['key' => 'price', 'label' => 'Price', 'sortable' => true, 'render' => "function(row) { return '<span class=\"font-semibold\">\$' + parseFloat(row.price || 0).toFixed(2) + '</span>'; }"],
    ['key' => 'active_members', 'label' => 'Members', 'render' => "function(row) {
        var count = parseInt(row.active_members || 0);
        var max = row.max_members ? '/' + row.max_members : '';
        var cls = count > 0 ? 'text-primary-600 dark:text-primary-400 font-semibold' : 'text-surface-400';
        return '<span class=\"' + cls + '\">' + count + max + '</span>';
    }"],
    ['key' => 'is_active', 'label' => 'Status', 'render' => "function(row) {
        return row.is_active == 1
            ? '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400\">Active</span>'
            : '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-400\">Inactive</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/membership-plans/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/membership-plans/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$deleteAction = ($baseUrl ?? '') . '/api/membership-plans/{id}';
$deletePermission = 'memberships.delete';
$createUrl = ($baseUrl ?? '') . '/admin/membership-plans/create';
$createLabel = 'Add Membership Plan';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
