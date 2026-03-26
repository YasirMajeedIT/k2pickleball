<?php
/**
 * Reusable data table component — Premium SaaS Design.
 *
 * Variables:
 *  $tableId      - Unique ID for the table
 *  $apiUrl       - API endpoint for data
 *  $columns      - Array of ['key' => 'field', 'label' => 'Header', 'sortable' => bool, 'render' => callable|null]
 *  $actions      - Array of ['label' => 'Edit', 'url' => ($baseUrl ?? '') . '/admin/items/{id}/edit', 'class' => 'text-blue-500']
 *  $createUrl    - URL for "Create New" button (null to hide)
 *  $createLabel  - Label for create button
 *  $searchable   - Whether to show search bar
 */

$tableId = $tableId ?? 'data-table';
$columns = $columns ?? [];
$actions = $actions ?? [];
$createUrl = $createUrl ?? null;
$createLabel = $createLabel ?? 'Create New';
$searchable = $searchable ?? true;
$apiUrl = $apiUrl ?? '';
$deleteAction = $deleteAction ?? null;   // API URL template with {id}, e.g. '/api/facilities/{id}'
$deletePermission = $deletePermission ?? null; // permission slug, e.g. 'facilities.delete'
?>

<?php
$hasRenders = array_filter($columns, fn($c) => isset($c['render']));
$rendersVarName = '__dtRenders_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $tableId);
$deleteActionJs = $deleteAction ? "'" . addslashes($deleteAction) . "'" : 'null';
$deletePermJs   = $deletePermission ? "'" . addslashes($deletePermission) . "'" : 'null';
if ($hasRenders): ?>
<script>
var <?= $rendersVarName ?> = {
<?php foreach ($columns as $col): if (!isset($col['render'])) continue; ?>
    '<?= htmlspecialchars($col['key']) ?>': <?= $col['render'] ?>,
<?php endforeach; ?>
};
</script>
<?php endif; ?>
<div x-data="dataTable('<?= htmlspecialchars($tableId) ?>', '<?= htmlspecialchars($apiUrl) ?>', typeof <?= $rendersVarName ?> !== 'undefined' ? <?= $rendersVarName ?> : {}, <?= $deleteActionJs ?>, <?= $deletePermJs ?>)" x-init="init()">
    <!-- Table Header -->
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <?php if ($searchable): ?>
        <div class="relative w-full sm:w-80 group">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" x-model="search" x-on:input.debounce.300ms="fetchData()" placeholder="Search records..."
                   class="w-full rounded-xl border border-surface-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 dark:border-surface-700 dark:bg-surface-800 dark:text-white dark:focus:border-primary-500 shadow-soft placeholder:text-surface-400">
        </div>
        <?php endif; ?>

        <?php if ($createUrl): ?>
        <a href="<?= htmlspecialchars($createUrl) ?>"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <?= htmlspecialchars($createLabel) ?>
        </a>
        <?php endif; ?>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
        <p class="text-sm text-surface-400">Loading data...</p>
    </div>

    <!-- Table -->
    <div x-show="!loading" class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 overflow-hidden shadow-soft">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-50 dark:bg-surface-800/50">
                        <?php foreach ($columns as $col): ?>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 whitespace-nowrap"
                            <?php if (!empty($col['sortable'])): ?>
                            x-on:click="toggleSort('<?= htmlspecialchars($col['key']) ?>')" style="cursor:pointer"
                            <?php endif; ?>>
                            <span class="flex items-center gap-1.5 <?= !empty($col['sortable']) ? 'hover:text-primary-500 transition-colors' : '' ?>">
                                <?= htmlspecialchars($col['label']) ?>
                                <?php if (!empty($col['sortable'])): ?>
                                <span class="flex flex-col -space-y-1.5">
                                    <svg x-show="sortField !== '<?= htmlspecialchars($col['key']) ?>'" class="w-3.5 h-3.5 text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                                    <svg x-show="sortField === '<?= htmlspecialchars($col['key']) ?>' && sortDir === 'asc'" class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    <svg x-show="sortField === '<?= htmlspecialchars($col['key']) ?>' && sortDir === 'desc'" class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                                <?php endif; ?>
                            </span>
                        </th>
                        <?php endforeach; ?>
                        <?php if (!empty($actions)): ?>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                    <template x-for="row in rows" :key="row.id">
                        <tr class="hover:bg-primary-50/30 dark:hover:bg-primary-500/[0.03] transition-colors">
                            <?php foreach ($columns as $col): ?>
                            <td class="px-5 py-4 text-surface-700 dark:text-surface-300 whitespace-nowrap">
                                <?php if (isset($col['render'])): ?>
                                <span x-html="renders['<?= htmlspecialchars($col['key']) ?>'](row)"></span>
                                <?php else: ?>
                                <span x-text="row['<?= htmlspecialchars($col['key']) ?>'] ?? '-'"></span>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                            <?php if (!empty($actions)): ?>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <?php foreach ($actions as $action): ?>
                                    <a :href="'<?= htmlspecialchars($action['url']) ?>'.replace('{id}', row.id)"
                                       class="<?= htmlspecialchars($action['class'] ?? 'text-primary-500 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-500/10') ?> inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-colors">
                                        <?= htmlspecialchars($action['label']) ?>
                                    </a>
                                    <?php endforeach; ?>
                                    <?php if ($deleteAction): ?>
                                    <button x-show="canDelete"
                                            @click="deleteRow(row.id, row.name || row.title || ('#' + row.id))"
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                    </template>
                    <template x-if="rows.length === 0">
                        <tr>
                            <td colspan="<?= count($columns) + (!empty($actions) ? 1 : 0) ?>" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-12 h-12 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p class="text-sm font-medium text-surface-400">No records found</p>
                                    <p class="text-xs text-surface-300 dark:text-surface-500">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between border-t border-surface-100 dark:border-surface-800 px-5 py-3.5 bg-surface-50/50 dark:bg-surface-800/30">
            <div class="text-xs text-surface-400 font-medium">
                Showing <span class="text-surface-600 dark:text-surface-300" x-text="rows.length > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></span>-<span class="text-surface-600 dark:text-surface-300" x-text="Math.min(currentPage * perPage, totalRecords)"></span>
                of <span class="text-surface-600 dark:text-surface-300" x-text="totalRecords"></span> results
            </div>
            <div class="flex items-center gap-1.5">
                <button x-on:click="prevPage()" :disabled="currentPage <= 1"
                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 disabled:opacity-40 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Prev
                </button>
                <span class="inline-flex items-center rounded-lg bg-primary-50 dark:bg-primary-500/10 px-3 py-1.5 text-xs font-semibold text-primary-600 dark:text-primary-400">
                    <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                </span>
                <button x-on:click="nextPage()" :disabled="currentPage >= totalPages"
                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 disabled:opacity-40 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                    Next
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
        <div x-show="confirmModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="confirmModal = false" class="fixed inset-0 bg-surface-950/60 backdrop-blur-sm"></div>
        <div x-show="confirmModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-surface-800 p-6 shadow-xl border border-surface-200 dark:border-surface-700 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/10 mb-4">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-1">Delete Record</h3>
            <p class="text-sm text-surface-500 mb-1">Are you sure you want to delete</p>
            <p class="text-sm font-semibold text-surface-700 dark:text-surface-300 mb-3" x-text="'\"' + confirmLabel + '\"'"></p>
            <p class="text-xs text-red-500 font-medium mb-5">This action cannot be undone.</p>
            <div class="flex items-center justify-center gap-3">
                <button @click="confirmModal = false"
                        class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-5 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                    Cancel
                </button>
                <button @click="confirmDelete()"
                        class="rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-5 py-2.5 text-sm font-semibold text-white hover:from-red-700 hover:to-red-800 shadow-soft transition-all">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>
<script>
function dataTable(tableId, apiUrl, renders, deleteApiUrl, deletePermission) {
    renders = renders || {};
    deleteApiUrl = deleteApiUrl || null;
    deletePermission = deletePermission || null;
    return {
        rows: [],
        renders: renders,
        loading: true,
        search: '',
        sortField: '',
        sortDir: 'asc',
        currentPage: 1,
        perPage: 20,
        totalRecords: 0,
        totalPages: 0,
        canDelete: false,
        confirmModal: false,
        confirmId: null,
        confirmLabel: '',
        async init() {
            // Determine delete permission after me() resolves
            if (deleteApiUrl) {
                try {
                    await getMe();
                    this.canDelete = deletePermission ? k2Can(deletePermission) : k2Can('*');
                } catch(_) { this.canDelete = false; }
            }
            this.fetchData();
        },
        toggleSort(field) {
            if (this.sortField === field) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDir = 'asc';
            }
            this.fetchData();
        },
        prevPage() { if (this.currentPage > 1) { this.currentPage--; this.fetchData(); } },
        nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; this.fetchData(); } },
        async deleteRow(id, label) {
            this.confirmId = id;
            this.confirmLabel = label;
            this.confirmModal = true;
        },
        async confirmDelete() {
            const id = this.confirmId;
            this.confirmModal = false;
            const url = deleteApiUrl.replace('{id}', id);
            try {
                const res = await authFetch(url, { method: 'DELETE' });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Deleted successfully', type: 'success' } }));
                    this.fetchData();
                } else {
                    const json = await res.json().catch(() => ({}));
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Delete failed', type: 'error' } }));
                }
            } catch(e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
        },
        async fetchData() {
            this.loading = true;
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.perPage,
            });
            if (this.search) params.set('search', this.search);
            if (this.sortField) { params.set('sort', this.sortField); params.set('direction', this.sortDir); }

            try {
                const token = localStorage.getItem('access_token');
                const res = await fetch(apiUrl + '?' + params.toString(), {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (res.status === 401) {
                    const refreshToken = localStorage.getItem('refresh_token');
                    if (refreshToken) {
                        const rr = await fetch(APP_BASE + '/api/auth/refresh', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ refresh_token: refreshToken })
                        });
                        if (rr.ok) {
                            const rd = await rr.json();
                            if (rd.data?.access_token) {
                                localStorage.setItem('access_token', rd.data.access_token);
                                if (rd.data.refresh_token) localStorage.setItem('refresh_token', rd.data.refresh_token);
                                await this.fetchData();
                                return;
                            }
                        }
                    }
                    window.location.href = APP_BASE + '/admin/login';
                    return;
                }
                const json = await res.json();
                this.rows = json.data || [];
                this.totalRecords = json.meta?.total || 0;
                this.totalPages = json.meta?.last_page || 1;
                this.currentPage = json.meta?.page || 1;
            } catch (e) {
                console.error('Data fetch error:', e);
                this.rows = [];
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
