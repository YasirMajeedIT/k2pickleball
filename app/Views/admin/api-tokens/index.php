<?php
$title = 'API Tokens';
$breadcrumbs = [['label' => 'API Tokens']];

$tableId = 'api-tokens-table';
$apiUrl = ($baseUrl ?? '') . '/api/api-tokens';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'abilities', 'label' => 'Abilities', 'render' => "function(row) {
        if (!row.abilities) return '-';
        const abs = typeof row.abilities === 'string' ? JSON.parse(row.abilities) : row.abilities;
        if (abs.includes('*')) return '<span class=\"inline-block rounded-full bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 px-2 py-0.5 text-xs font-medium\">Full Access</span>';
        return abs.slice(0, 3).map(a => '<span class=\"inline-block rounded-lg bg-surface-100 dark:bg-surface-700 border border-surface-200 dark:border-surface-600 px-1.5 py-0.5 text-xs mr-1\">' + a + '</span>').join('') + (abs.length > 3 ? ' +' + (abs.length - 3) : '');
    }"],
    ['key' => 'last_used_at', 'label' => 'Last Used', 'render' => "function(row) { return row.last_used_at ? new Date(row.last_used_at).toLocaleDateString() : 'Never'; }"],
    ['key' => 'expires_at', 'label' => 'Expires', 'render' => "function(row) {
        if (!row.expires_at) return '<span class=\"text-surface-400\">Never</span>';
        const d = new Date(row.expires_at);
        const isExpired = d < new Date();
        return isExpired
            ? '<span class=\"text-red-500\">' + d.toLocaleDateString() + ' (Expired)</span>'
            : d.toLocaleDateString();
    }"],
    ['key' => 'created_at', 'label' => 'Created', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'Revoke', 'url' => ($baseUrl ?? '') . '/admin/api-tokens/{id}/revoke', 'class' => 'text-red-500 hover:text-red-700'],
];
$createUrl = ($baseUrl ?? '') . '/admin/api-tokens/create';
$createLabel = 'Generate Token';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
