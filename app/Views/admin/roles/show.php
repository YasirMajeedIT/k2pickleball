<?php
$title = 'Role Details';
$breadcrumbs = [
    ['label' => 'Roles', 'url' => ($baseUrl ?? '') . '/admin/roles'],
    ['label' => 'View'],
];
$backUrl = ($baseUrl ?? '') . '/admin/roles';
$editUrl = ($baseUrl ?? '') . '/admin/roles/' . ($id ?? '') . '/edit';
$apiUrl = ($baseUrl ?? '') . '/api/roles/' . ($id ?? '');

ob_start();
?>
<div x-data="roleShow()" x-init="init()">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-surface-900 dark:text-white" x-text="role.name || 'Role'"></h2>
            <p class="mt-1 text-sm text-surface-500" x-text="role.slug || '-' "></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($backUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors shadow-soft">
                Back
            </a>
            <a href="<?= htmlspecialchars($editUrl) ?>"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-medium transition-all">
                Edit
            </a>
        </div>
    </div>

    <div x-show="loading" class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="relative">
            <div class="h-10 w-10 rounded-full border-[3px] border-surface-200 dark:border-surface-700"></div>
            <div class="absolute top-0 left-0 h-10 w-10 rounded-full border-[3px] border-transparent border-t-primary-500 animate-spin"></div>
        </div>
    </div>

    <div x-show="!loading" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1 rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Role Information</h3>
            </div>
            <div class="divide-y divide-surface-100 dark:divide-surface-800">
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Name</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="role.name || '-' "></dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Slug</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300" x-text="role.slug || '-' "></dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Type</dt>
                    <dd class="mt-1">
                        <span x-show="role.is_system" class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 px-2.5 py-0.5 text-xs font-semibold">System</span>
                        <span x-show="!role.is_system" class="inline-flex items-center rounded-full bg-surface-100 text-surface-600 px-2.5 py-0.5 text-xs font-semibold">Custom</span>
                    </dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-surface-400">Description</dt>
                    <dd class="mt-1 text-sm text-surface-700 dark:text-surface-300 whitespace-pre-line" x-text="role.description || '-' "></dd>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                <h3 class="font-semibold text-surface-800 dark:text-surface-100">Permissions</h3>
            </div>
            <div class="px-6 py-5">
                <template x-if="!permissions.length">
                    <p class="text-sm text-surface-500">No permissions assigned.</p>
                </template>
                <div class="space-y-5" x-show="permissions.length">
                    <template x-for="(items, group) in groupedPermissions" :key="group">
                        <div>
                            <h4 class="mb-2 text-sm font-semibold capitalize text-surface-700 dark:text-surface-300" x-text="group"></h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="perm in items" :key="perm.id">
                                    <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400" x-text="perm.name"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function roleShow() {
    const apiUrl = '<?= htmlspecialchars($apiUrl) ?>';
    return {
        role: {},
        permissions: [],
        groupedPermissions: {},
        loading: true,
        async init() {
            try {
                const res = await authFetch(apiUrl, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                if (res.ok && json.data) {
                    this.role = json.data;
                    this.permissions = json.data.permissions || [];
                    const grouped = {};
                    this.permissions.forEach((p) => {
                        const group = (p.slug || '').split('.')[0] || 'general';
                        if (!grouped[group]) grouped[group] = [];
                        grouped[group].push(p);
                    });
                    this.groupedPermissions = grouped;
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Unable to load role', type: 'error' } }));
                }
            } catch (e) {
                console.error(e);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
