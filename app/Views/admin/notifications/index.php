<?php
$title = 'Notifications';
$breadcrumbs = [['label' => 'Notifications']];

$tableId = 'notifications-table';
$apiUrl = ($baseUrl ?? '') . '/api/notifications';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'type', 'label' => 'Type', 'render' => "function(row) {
        const icons = { info: '🔵', success: '🟢', warning: '🟡', error: '🔴' };
        return (icons[row.type] || '⚪') + ' <span class=\"capitalize\">' + (row.type || '-') + '</span>';
    }"],
    ['key' => 'title', 'label' => 'Title', 'sortable' => true],
    ['key' => 'message', 'label' => 'Message', 'render' => "function(row) { return row.message ? (row.message.length > 60 ? row.message.substring(0, 60) + '…' : row.message) : '-'; }"],
    ['key' => 'read_at', 'label' => 'Status', 'render' => "function(row) {
        return row.read_at
            ? '<span class=\"text-surface-400 text-xs\">Read</span>'
            : '<span class=\"inline-block rounded-full bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 px-2 py-0.5 text-xs font-medium\">Unread</span>';
    }"],
    ['key' => 'created_at', 'label' => 'Date', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'View', 'url' => ($baseUrl ?? '') . '/admin/notifications/{id}', 'class' => 'text-primary-500 hover:text-primary-700'],
];
$createUrl = null;
$createLabel = null;
$deleteAction = ($baseUrl ?? '') . '/api/notifications/{id}';
$deletePermission = 'notifications.view';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
