<?php
$title = 'File Manager';
$breadcrumbs = [['label' => 'Files']];

$tableId = 'files-table';
$apiUrl = ($baseUrl ?? '') . '/api/files';
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
    ['key' => 'original_name', 'label' => 'Filename', 'sortable' => true],
    ['key' => 'mime_type', 'label' => 'Type', 'render' => "function(row) {
        const t = row.mime_type || '';
        if (t.startsWith('image/')) return '🖼️ Image';
        if (t.startsWith('video/')) return '🎬 Video';
        if (t.includes('pdf')) return '📄 PDF';
        if (t.includes('spreadsheet') || t.includes('excel')) return '📊 Spreadsheet';
        if (t.includes('document') || t.includes('word')) return '📝 Document';
        return '📎 ' + t;
    }"],
    ['key' => 'size', 'label' => 'Size', 'render' => "function(row) {
        if (!row.size) return '-';
        const s = parseInt(row.size);
        if (s < 1024) return s + ' B';
        if (s < 1048576) return (s/1024).toFixed(1) + ' KB';
        return (s/1048576).toFixed(1) + ' MB';
    }"],
    ['key' => 'context', 'label' => 'Context'],
    ['key' => 'created_at', 'label' => 'Uploaded', 'sortable' => true, 'render' => "function(row) { return row.created_at ? new Date(row.created_at).toLocaleDateString() : '-'; }"],
];
$actions = [
    ['label' => 'Download', 'url' => ($baseUrl ?? '') . '/api/files/{id}/download', 'class' => 'text-primary-500 hover:text-primary-700'],
    ['label' => 'Delete', 'url' => ($baseUrl ?? '') . '/admin/files/{id}/delete', 'class' => 'text-red-500 hover:text-red-700'],
];
$createUrl = ($baseUrl ?? '') . '/admin/files/upload';
$createLabel = 'Upload File';

ob_start();
include __DIR__ . '/../../components/data-table.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
