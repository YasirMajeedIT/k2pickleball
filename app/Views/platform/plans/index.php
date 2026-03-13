<?php
$title = 'Subscription Plans';
$breadcrumbs = [['label' => 'Plans']];

$tableId = 'platform-plans-table';
$apiUrl = ($baseUrl ?? '') . '/api/subscriptions/plans';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'name', 'label' => 'Plan Name', 'sortable' => true],
    ['key' => 'slug', 'label' => 'Slug'],
    ['key' => 'monthly_price', 'label' => 'Monthly', 'render' => "function(row) { return row.monthly_price ? '$' + parseFloat(row.monthly_price).toFixed(2) : 'Free'; }"],
    ['key' => 'annual_price', 'label' => 'Annual', 'render' => "function(row) { return row.annual_price ? '$' + parseFloat(row.annual_price).toFixed(2) : 'Free'; }"],
    ['key' => 'max_facilities', 'label' => 'Max Facilities', 'render' => "function(row) { return row.max_facilities || '∞'; }"],
    ['key' => 'max_users', 'label' => 'Max Users', 'render' => "function(row) { return row.max_users || '∞'; }"],
    ['key' => 'is_active', 'label' => 'Active', 'render' => "function(row) {
        return row.is_active
            ? '<span class=\"inline-block rounded-full bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400 px-2 py-0.5 text-xs font-medium\">Active</span>'
            : '<span class=\"inline-block rounded-full bg-surface-100 text-surface-600 px-2 py-0.5 text-xs font-medium\">Inactive</span>';
    }"],
];
$actions = [
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/platform/plans/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$createUrl = ($baseUrl ?? '') . '/platform/plans/create';
$createLabel = 'Add Plan';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/platform.php';
?>
