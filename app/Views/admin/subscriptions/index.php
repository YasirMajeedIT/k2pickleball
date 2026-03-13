<?php
$title = 'Subscriptions';
$breadcrumbs = [['label' => 'Subscriptions']];

$tableId = 'subscriptions-table';
$apiUrl = ($baseUrl ?? '') . '/api/subscriptions';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'plan_id', 'label' => 'Plan ID', 'sortable' => true],
    ['key' => 'billing_cycle', 'label' => 'Cycle', 'render' => "function(row) {
        return '<span class=\"capitalize\">' + (row.billing_cycle || '-') + '</span>';
    }"],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        const colors = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', cancelled: 'bg-red-100 text-red-700', past_due: 'bg-yellow-100 text-yellow-700', trialing: 'bg-blue-100 text-blue-700', paused: 'bg-surface-100 text-surface-600' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'current_period_end', 'label' => 'Renews', 'render' => "function(row) { return row.current_period_end ? new Date(row.current_period_end).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/subscriptions/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Cancel', 'url' => ($baseUrl ?? '') . '/admin/subscriptions/{id}/cancel', 'class' => 'text-red-500 hover:text-red-700'],
];
$createUrl = null;
$createLabel = null;

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
