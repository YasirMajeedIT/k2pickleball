<?php
$title = 'Players';
$breadcrumbs = [['label' => 'Players']];

$tableId = 'players-table';
$apiUrl = ($baseUrl ?? '') . '/api/players';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'first_name', 'label' => 'Name', 'sortable' => true, 'render' => "function(row) {
        var avatar = row.avatar_url ? '<img src=\"' + APP_BASE + row.avatar_url + '\" class=\"h-8 w-8 rounded-full object-cover border border-surface-200 dark:border-surface-600\">' : '<span class=\"flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-500/10 text-xs font-bold text-primary-600 dark:text-primary-400\">' + (row.first_name||'')[0] + (row.last_name||'')[0] + '</span>';
        return '<span class=\"flex items-center gap-2.5\">' + avatar + '<span>' + (row.first_name || '') + ' ' + (row.last_name || '') + '</span></span>';
    }"],
    ['key' => 'email', 'label' => 'Email', 'sortable' => true],
    ['key' => 'phone', 'label' => 'Phone'],
    ['key' => 'skill_level', 'label' => 'Skill', 'render' => "function(row) {
        var colors = { beginner: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', intermediate: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400', advanced: 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400', pro: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize ' + (colors[row.skill_level] || 'bg-surface-100 text-surface-600') + '\">' + (row.skill_level || '-') + '</span>';
    }"],
    ['key' => 'status', 'label' => 'Status', 'render' => "function(row) {
        var colors = { active: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400', inactive: 'bg-surface-100 text-surface-600', suspended: 'bg-red-100 text-red-700' };
        return '<span class=\"inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize ' + (colors[row.status] || 'bg-surface-100 text-surface-600') + '\">' + (row.status || '-') + '</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Joined', 'sortable' => true, 'render' => "function(row) { return row.date_joined ? new Date(row.date_joined).toLocaleDateString() : (row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'); }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/players/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/players/{id}/edit', 'class' => 'text-amber-500 hover:text-amber-700'],
];
$deleteAction = ($baseUrl ?? '') . '/api/players/{id}';
$deletePermission = 'players.delete';
$createUrl = ($baseUrl ?? '') . '/admin/players/create';
$createLabel = 'Add Player';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
