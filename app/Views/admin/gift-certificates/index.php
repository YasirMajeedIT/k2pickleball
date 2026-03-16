<?php
$title = 'Gift Certificates';
$breadcrumbs = [['label' => 'Gift Certificates']];

$tableId = 'gift-certs-table';
$apiUrl = ($baseUrl ?? '') . '/api/gift-certificates';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'code', 'label' => 'Code', 'render' => "function(row) { return '<span class=\"font-mono text-xs bg-surface-100 dark:bg-surface-800 px-2 py-0.5 rounded\">' + row.code + '</span>'; }"],
    ['key' => 'certificate_name', 'label' => 'Name', 'sortable' => true, 'render' => "function(row) { return row.certificate_name || '—'; }"],
    ['key' => 'original_value', 'label' => 'Original', 'render' => "function(row) { return '$' + parseFloat(row.original_value || 0).toFixed(2); }"],
    ['key' => 'value', 'label' => 'Remaining', 'render' => "function(row) {
        var v = parseFloat(row.value || 0);
        var cls = v > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-surface-400';
        return '<span class=\"' + cls + '\">$' + v.toFixed(2) + '</span>';
    }"],
    ['key' => 'recipient_first_name', 'label' => 'Recipient', 'render' => "function(row) { return (row.recipient_first_name || '') + ' ' + (row.recipient_last_name || '') || '—'; }"],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        var colors = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', redeemed: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', expired: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize ' + (colors[row.status] || '') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/gift-certificates/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/gift-certificates/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$createUrl = ($baseUrl ?? '') . '/admin/gift-certificates/create';
$createLabel = 'Add Gift Certificate';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
