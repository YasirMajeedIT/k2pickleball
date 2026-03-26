<?php
$title = 'Subscriptions';
$breadcrumbs = [['label' => 'Subscriptions']];

$tableId = 'subscriptions-table';
$apiUrl = ($baseUrl ?? '') . '/api/subscriptions';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'plan_name', 'label' => 'Plan', 'render' => "function(row) {
        return '<span class=\"font-semibold text-surface-800 dark:text-surface-100\">' + (row.plan_name || 'Plan #' + row.plan_id) + '</span>';
    }"],
    ['key' => 'billing_cycle', 'label' => 'Cycle', 'render' => "function(row) {
        var colors = { monthly: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', yearly: 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize ' + (colors[row.billing_cycle] || 'bg-surface-100 text-surface-600') + '\">' + (row.billing_cycle || '-') + '</span>';
    }"],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        var colors = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400', past_due: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400', trialing: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', paused: 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400', expired: 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'current_period_start', 'label' => 'Started', 'render' => "function(row) { return row.current_period_start ? new Date(row.current_period_start).toLocaleDateString() : '-'; }"],
    ['key' => 'current_period_end', 'label' => 'Renews', 'render' => "function(row) { return row.current_period_end ? new Date(row.current_period_end).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/subscriptions/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
];
$createUrl = null;
$createLabel = null;

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
