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
        const fileUrl = location.origin + APP_BASE + '/storage/' + row.path;
        const mime = row.mime_type || '';
        let html = '<div class=\"flex items-center justify-end gap-1\">';
        if (row.path && mime.startsWith('image/')) {
            html += '<button onclick=\"previewFile(\\'' + fileUrl + '\\', \\'' + (row.original_name||'').replace(/'/g, '') + '\\', \\'image\\')\" class=\"text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors\">Preview</button>';
        }
        if (row.path && mime.startsWith('video/')) {
            html += '<button onclick=\"previewFile(\\'' + fileUrl + '\\', \\'' + (row.original_name||'').replace(/'/g, '') + '\\', \\'video\\')\" class=\"text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors\">Preview</button>';
        }
        if (row.path) {
            html += '<button onclick=\"copyFileUrl(\\'' + fileUrl + '\\')\" class=\"text-amber-500 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-500/10 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors\">Copy URL</button>';
            html += '<a href=\"' + fileUrl + '\" download=\"' + (row.original_name || '') + '\" class=\"text-primary-500 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-500/10 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors\">Download</a>';
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
function copyFileUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'URL copied to clipboard', type: 'success' } }));
    }).catch(() => {
        prompt('Copy this URL:', url);
    });
}

function previewFile(url, name, type) {
    const overlay = document.getElementById('file-preview-overlay');
    const title = document.getElementById('file-preview-title');
    const body = document.getElementById('file-preview-body');
    title.textContent = name || 'Preview';
    if (type === 'image') {
        body.innerHTML = '<img src="' + url + '" alt="" class="max-w-full max-h-[75vh] rounded-xl mx-auto shadow-lg">';
    } else if (type === 'video') {
        body.innerHTML = '<video src="' + url + '" controls autoplay class="max-w-full max-h-[75vh] rounded-xl mx-auto shadow-lg"></video>';
    }
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
}

function closePreview() {
    const overlay = document.getElementById('file-preview-overlay');
    const body = document.getElementById('file-preview-body');
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');
    body.innerHTML = '';
}

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

<!-- File Preview Modal -->
<div id="file-preview-overlay" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/70 backdrop-blur-sm" onclick="if(event.target===this)closePreview()">
    <div class="relative w-full max-w-4xl mx-4">
        <button onclick="closePreview()" class="absolute -top-10 right-0 text-white/80 hover:text-white text-sm font-semibold flex items-center gap-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Close
        </button>
        <p id="file-preview-title" class="text-white text-sm font-medium mb-3 truncate"></p>
        <div id="file-preview-body"></div>
    </div>
</div>
<?php
$content = ob_get_clean();

include __DIR__ . '/../../layouts/admin.php';
?>
