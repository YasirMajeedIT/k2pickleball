<?php
$title = 'Upload File';
$breadcrumbs = [['label' => 'Files', 'url' => ($baseUrl ?? '') . '/admin/files'], ['label' => 'Upload']];

ob_start();
?>
<div x-data="fileUploader()" class="mx-auto max-w-3xl">
    <form @submit.prevent="uploadFile()" class="rounded-2xl bg-white dark:bg-surface-800/60 shadow-soft border border-surface-200/60 dark:border-surface-700/50 overflow-hidden">
        <div class="border-b border-surface-200 dark:border-surface-700/50 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/80">
            <h3 class="text-base font-semibold text-surface-900 dark:text-white">Upload File</h3>
            <p class="text-xs text-surface-500 mt-0.5">Upload a file to the media library</p>
        </div>

        <div class="space-y-5 px-6 py-5">
            <!-- Drop Zone -->
            <div @dragover.prevent="dragging = true" @dragleave="dragging = false" @drop.prevent="handleDrop($event)"
                :class="dragging ? 'border-primary-500 bg-primary-50/50 dark:bg-primary-900/10' : 'border-surface-300 dark:border-surface-600'"
                class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed p-10 transition-all">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500/10 to-primary-600/10 mb-4">
                    <svg class="h-7 w-7 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <p class="text-sm text-surface-600 dark:text-surface-400 mb-3">Drag & drop your file here, or</p>
                <label class="cursor-pointer rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:from-primary-700 hover:to-primary-800 shadow-soft transition-all">
                    Browse Files
                    <input type="file" @change="handleFileSelect($event)" class="hidden">
                </label>
                <p class="text-xs text-surface-400 mt-3">Max file size: 10MB</p>
            </div>

            <!-- Selected File Preview -->
            <template x-if="selectedFile">
                <div class="flex items-center gap-3 rounded-xl border border-surface-200 dark:border-surface-700 p-4 shadow-soft">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-100 dark:bg-surface-700">
                        <svg class="h-5 w-5 text-surface-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-surface-800 dark:text-white truncate" x-text="selectedFile.name"></p>
                        <p class="text-xs text-surface-500" x-text="formatSize(selectedFile.size)"></p>
                    </div>
                    <button type="button" @click="selectedFile = null" class="text-red-500 hover:text-red-600 rounded-lg p-1 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>

            <!-- Collection -->
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-surface-700 dark:text-surface-300">Collection</label>
                <select x-model="collection" class="w-full rounded-xl border-surface-300 shadow-soft focus:border-primary-500 focus:ring-primary-500 dark:border-surface-600 dark:bg-surface-900 dark:text-white px-4 py-3 text-sm">
                    <option value="general">General</option>
                    <option value="documents">Documents</option>
                    <option value="images">Images</option>
                    <option value="logos">Logos</option>
                    <option value="invoices">Invoices</option>
                </select>
            </div>

            <!-- Upload Progress -->
            <template x-if="uploading">
                <div>
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-surface-500 font-medium">Uploading...</span>
                        <span class="text-surface-700 dark:text-surface-300 font-semibold" x-text="progress + '%'"></span>
                    </div>
                    <div class="h-2.5 rounded-full bg-surface-200 dark:bg-surface-700 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary-500 to-primary-600 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-surface-200 dark:border-surface-700/50 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/80">
            <a href="<?= ($baseUrl ?? '') . '/admin/files' ?>" class="rounded-xl border border-surface-300 dark:border-surface-600 px-4 py-2.5 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Cancel</a>
            <button type="submit" :disabled="!selectedFile || uploading" class="rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:from-primary-700 hover:to-primary-800 disabled:opacity-50 shadow-soft transition-all">Upload</button>
        </div>
    </form>
</div>

<script>
function fileUploader() {
    const token = localStorage.getItem('access_token');
    return {
        selectedFile: null,
        collection: 'general',
        uploading: false,
        progress: 0,
        dragging: false,

        handleFileSelect(e) { this.selectedFile = e.target.files[0] || null; },
        handleDrop(e) { this.dragging = false; this.selectedFile = e.dataTransfer.files[0] || null; },

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        },

        async uploadFile() {
            if (!this.selectedFile) return;
            this.uploading = true;
            this.progress = 0;

            const formData = new FormData();
            formData.append('file', this.selectedFile);
            formData.append('context', this.collection);

            try {
                const xhr = new XMLHttpRequest();
                xhr.upload.onprogress = (e) => { if (e.lengthComputable) this.progress = Math.round((e.loaded / e.total) * 100); };
                xhr.onload = () => {
                    this.uploading = false;
                    if (xhr.status >= 200 && xhr.status < 300) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'File uploaded', type: 'success' } }));
                        setTimeout(() => window.location.href = APP_BASE + '/admin/files', 500);
                    } else {
                        const json = JSON.parse(xhr.responseText);
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Upload failed', type: 'error' } }));
                    }
                };
                xhr.onerror = () => {
                    this.uploading = false;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
                };
                xhr.open('POST', APP_BASE + '/api/files');
                xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.send(formData);
            } catch (e) {
                this.uploading = false;
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Upload error', type: 'error' } }));
            }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
