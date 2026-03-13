<?php
$title = 'Payments';
$breadcrumbs = [['label' => 'Payments']];

$tableId = 'payments-table';
$apiUrl = ($baseUrl ?? '') . '/api/payments';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'square_payment_id', 'label' => 'Payment ID'],
    ['key' => 'type', 'label' => 'Type', 'render' => "function(row) {
        const colors = { payment: 'bg-green-100 text-green-700', refund: 'bg-red-100 text-red-700', payout: 'bg-blue-100 text-blue-700' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.type] || 'bg-surface-100 text-surface-600') + '\">' + (row.type || '-') + '</span>';
    }"],
    ['key' => 'amount', 'label' => 'Amount', 'render' => "function(row) { return row.amount ? '$' + parseFloat(row.amount).toFixed(2) : '-'; }"],
    ['key' => 'currency', 'label' => 'Currency'],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        const colors = { completed: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', pending: 'bg-yellow-100 text-yellow-700', failed: 'bg-red-100 text-red-700', refunded: 'bg-purple-100 text-purple-700' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Date', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/payments/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
];
$createUrl = null;
$createLabel = null;

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
