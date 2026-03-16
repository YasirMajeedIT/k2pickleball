<?php
$title = 'Edit Category';
$breadcrumbs = [['label' => 'Categories', 'url' => ($baseUrl ?? '') . '/admin/categories'], ['label' => 'Edit']];

$formId = 'categoryForm';
$apiUrl = ($baseUrl ?? '') . '/api/categories/' . ($id ?? '');
$method = 'PUT';
$backUrl = ($baseUrl ?? '') . '/admin/categories';
$fields = [
    ['name' => 'name', 'label' => 'Category Name', 'required' => true],
    ['name' => 'color', 'label' => 'Color', 'type' => 'color'],
    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'help' => 'Lower numbers appear first'],
    ['name' => 'is_taxable', 'label' => 'Taxable', 'type' => 'checkbox', 'help' => 'Items in this category are subject to tax'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function categoryForm() {
    return {
        form: { name: '', color: '#3b82f6', sort_order: '', is_taxable: false },
        errors: {},
        submitting: false,
        async init() {
            try {
                const res = await authFetch('<?= $apiUrl ?>');
                const json = await res.json();
                if (json.data) {
                    this.form.name = json.data.name || '';
                    this.form.color = json.data.color || '#3b82f6';
                    this.form.sort_order = json.data.sort_order || '';
                    this.form.is_taxable = !!json.data.is_taxable;
                }
            } catch (e) { console.error(e); }
        },
        async submitForm() {
            this.submitting = true; this.errors = {};
            try {
                const res = await authFetch('<?= $apiUrl ?>', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Category updated', type: 'success' } }));
                    setTimeout(() => window.location.href = '<?= $backUrl ?>', 500);
                } else { this.errors = json.errors || {}; window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Validation failed', type: 'error' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } })); }
            this.submitting = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
