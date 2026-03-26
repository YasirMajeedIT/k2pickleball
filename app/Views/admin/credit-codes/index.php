<?php
$title = 'Credit Codes';
$breadcrumbs = [['label' => 'Credit Codes']];

$tableId = 'credit-codes-table';
$apiUrl = ($baseUrl ?? '') . '/api/credit-codes';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'code', 'label' => 'Code', 'render' => "function(row) { return '<span class=\"font-mono text-xs bg-surface-100 dark:bg-surface-800 px-2 py-0.5 rounded\">' + row.code + '</span>'; }"],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'type', 'label' => 'Type', 'render' => "function(row) {
        var colors = { credit: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', infacility: 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize ' + (colors[row.type] || '') + '\">' + (row.type || '-') + '</span>';
    }"],
    ['key' => 'amount', 'label' => 'Amount', 'render' => "function(row) { return '$' + parseFloat(row.amount || 0).toFixed(2); }"],
    ['key' => 'balance', 'label' => 'Balance', 'render' => "function(row) {
        var bal = parseFloat(row.balance || 0);
        var cls = bal > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-surface-400';
        return '<span class=\"' + cls + '\">$' + bal.toFixed(2) + '</span>';
    }"],
    ['key' => 'active', 'label' => 'Status', 'render' => "function(row) {
        return row.active == 1
            ? '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400\">Active</span>'
            : '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-400\">Inactive</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/credit-codes/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/credit-codes/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$deleteAction = ($baseUrl ?? '') . '/api/credit-codes/{id}';
$deletePermission = 'credit_codes.delete';
$createUrl = ($baseUrl ?? '') . '/admin/credit-codes/create';
$createLabel = 'Add Credit Code';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
