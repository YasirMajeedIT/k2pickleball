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
    ['key' => 'actions_custom', 'label' => 'Actions', 'render' => "function(row) {
        let html = '<div class=\"flex items-center justify-end gap-1\">';
        if (row.path) {
            html += '<a href=\"' + APP_BASE + '/storage/' + row.path + '\" target=\"_blank\" class=\"text-primary-500 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-500/10 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors\">Download</a>';
        }
        html += '<button onclick=\"deleteFile(' + row.id + ', \\'" . addslashes($tableId) . "\\')\" class=\"text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-500/10 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors\">Delete</button>';
        html += '</div>';
        return html;
    }"],
];
$actions = [];
$createUrl = ($baseUrl ?? '') . '/admin/files/upload';
$createLabel = 'Upload File';

ob_start();
include __DIR__ . '/../../components/data-table.php';
?>
<script>
async function deleteFile(id, tableId) {
    if (!confirm('Are you sure you want to delete this file?')) return;
    try {
        const res = await authFetch(APP_BASE + '/api/files/' + id, { method: 'DELETE' });
        if (res.ok) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'File deleted', type: 'success' } }));
            const tableEl = document.getElementById(tableId);
            if (tableEl && Alpine.$data(tableEl)) {
                Alpine.$data(tableEl).fetchData();
            }
        } else {
            const json = await res.json();
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Delete failed', type: 'error' } }));
        }
    } catch (e) {
        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
    }
}
</script>
<?php
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
