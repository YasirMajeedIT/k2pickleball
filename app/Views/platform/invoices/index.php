<?php
$title = 'Invoices';
$breadcrumbs = [['label' => 'Invoices']];

$tableId = 'platform-invoices-table';
$apiUrl = ($baseUrl ?? '') . '/api/platform/invoices';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'invoice_number', 'label' => 'Invoice #', 'sortable' => true],
    ['key' => 'organization_name', 'label' => 'Organization', 'render' => "function(row) { return row.organization_name || '<span class=\"text-surface-400\">-</span>'; }"],
    ['key' => 'total', 'label' => 'Total', 'render' => "function(row) { return '$' + parseFloat(row.total || 0).toFixed(2); }"],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        var colors = { paid: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', pending: 'bg-yellow-100 text-yellow-700', overdue: 'bg-red-100 text-red-700', draft: 'bg-surface-100 text-surface-600' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'due_date', 'label' => 'Due Date', 'render' => "function(row) { return row.due_date ? new Date(row.due_date).toLocaleDateString() : '-'; }"],
    ['key' => 'paid_at', 'label' => 'Paid', 'render' => "function(row) { return row.paid_at ? new Date(row.paid_at).toLocaleDateString() : '-'; }"],
];
$actions = [];
$createUrl = null;
$createLabel = null;

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/platform.php';
?>
