<?php
$title = 'Users';
$breadcrumbs = [['label' => 'Users']];

$tableId = 'users-table';
$apiUrl = ($baseUrl ?? '') . '/api/users';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'first_name', 'label' => 'Name', 'sortable' => true, 'render' => "function(row) { return (row.first_name || '') + ' ' + (row.last_name || ''); }"],
    ['key' => 'email', 'label' => 'Email', 'sortable' => true],
    ['key' => 'phone', 'label' => 'Phone'],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        const colors = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', inactive: 'bg-surface-100 text-surface-600', suspended: 'bg-red-100 text-red-700', pending: 'bg-yellow-100 text-yellow-700' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'facilities', 'label' => 'Facilities', 'render' => "function(row) {
        const list = row.facilities || [];
        if (!list.length) return '<span class=\"text-surface-400 text-xs\">—</span>';
        const visible = list.slice(0, 2);
        const extra = list.length - 2;
        let html = visible.map(f => '<span class=\"inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 mr-1\">' + f.name + '</span>').join('');
        if (extra > 0) html += '<span class=\"inline-block rounded-full bg-surface-100 dark:bg-surface-700 px-2 py-0.5 text-xs font-medium text-surface-500 dark:text-surface-400\">+' + extra + ' more</span>';
        return html;
    }"],
    ['key' => 'created_at', 'label' => 'Joined', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/users/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/users/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$createUrl = ($baseUrl ?? '') . '/admin/users/create';
$createLabel = 'Add User';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
