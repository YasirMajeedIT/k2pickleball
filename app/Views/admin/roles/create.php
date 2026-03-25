<?php
$title = 'Create Role';
$breadcrumbs = [['label' => 'Roles', 'url' => ($baseUrl ?? '') . '/admin/roles'], ['label' => 'Create']];

$formId = 'roleForm';
$apiUrl = ($baseUrl ?? '') . '/api/roles';
$method = 'POST';
$backUrl = ($baseUrl ?? '') . '/admin/roles';
$fields = [
    ['name' => 'name', 'label' => 'Role Name', 'required' => true, 'placeholder' => 'Tournament Director'],
    ['name' => 'slug', 'label' => 'Slug', 'required' => true, 'placeholder' => 'tournament-director', 'help' => 'URL-friendly identifier (auto-generated)'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<!-- Permissions Selection -->
<div class="mx-auto max-w-3xl mt-6 rounded-2xl bg-white dark:bg-surface-800/60 shadow-soft border border-surface-200/60 dark:border-surface-700/50 overflow-hidden"
     x-data="permissionsSelector()">
    <div class="border-b border-surface-200 dark:border-surface-700/50 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/80">
        <h3 class="text-base font-semibold text-surface-900 dark:text-white">Permissions</h3>
    </div>
    <div class="px-6 py-4 space-y-4">
        <template x-for="(perms, group) in groupedPermissions" :key="group">
            <div>
                <h4 class="text-sm font-semibold text-surface-700 dark:text-surface-300 capitalize mb-2" x-text="group"></h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <template x-for="perm in perms" :key="perm.id">
                        <label class="inline-flex items-center gap-2.5 text-sm rounded-xl border border-surface-200 dark:border-surface-700 px-3 py-2 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors cursor-pointer">
                            <input type="checkbox" :value="perm.id" x-model="selectedPermissions"
                                class="rounded border-surface-300 text-primary-500 focus:ring-primary-500 dark:border-surface-600 dark:bg-surface-900">
                            <span x-text="perm.name" class="text-surface-600 dark:text-surface-400"></span>
                        </label>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function roleForm() {
    return {
        form: { name: '', slug: '', description: '' },
        errors: {},
        submitting: false,
        async submitForm() {
            this.submitting = true; this.errors = {};
            try {
                const permsEl = document.querySelector('[x-data="permissionsSelector()"]');
                const permIds = permsEl ? Alpine.$data(permsEl).selectedPermissions : [];
                const body = { ...this.form, permissions: permIds };
                const res = await authFetch('<?= $apiUrl ?>', {
                    method: 'POST',
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Role created', type: 'success' } }));
                    setTimeout(() => window.location.href = '<?= $backUrl ?>', 500);
                } else { this.errors = json.errors || {}; window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Validation failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            this.submitting = false;
        }
    };
}

function permissionsSelector() {
    return {
        allPermissions: [],
        selectedPermissions: [],
        groupedPermissions: {},
        async init() {
            try {
                const res = await authFetch(APP_BASE + '/api/permissions');
                const json = await res.json();
                if (json.data) {
                    this.allPermissions = json.data;
                    this.groupedPermissions = {};
                    json.data.forEach(p => {
                        const group = p.slug?.split('.')[0] || 'general';
                        if (!this.groupedPermissions[group]) this.groupedPermissions[group] = [];
                        this.groupedPermissions[group].push(p);
                    });
                }
            } catch (e) { console.error(e); }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
