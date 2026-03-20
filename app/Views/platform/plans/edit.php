<?php
$title = 'Edit Plan';
$breadcrumbs = [
    ['label' => 'Plans', 'url' => ($baseUrl ?? '') . '/platform/plans'],
    ['label' => 'Edit'],
];
$backUrl = ($baseUrl ?? '') . '/platform/plans';
$formId = 'planEditForm';
$apiUrl = ($baseUrl ?? '') . '/api/plans';
$method = 'PUT';
$fields = [
    ['name' => 'name', 'label' => 'Plan Name', 'required' => true, 'placeholder' => 'Professional', 'cols' => 'half'],
    ['name' => 'slug', 'label' => 'Slug', 'required' => true, 'placeholder' => 'professional', 'help' => 'URL-friendly identifier', 'cols' => 'half'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'placeholder' => 'Full-featured plan for growing businesses'],
    ['name' => 'price_monthly', 'label' => 'Monthly Price ($)', 'type' => 'number', 'required' => true, 'placeholder' => '49.99', 'step' => '0.01', 'min' => '0', 'cols' => 'half'],
    ['name' => 'price_yearly', 'label' => 'Yearly Price ($)', 'type' => 'number', 'required' => true, 'placeholder' => '499.99', 'step' => '0.01', 'min' => '0', 'cols' => 'half'],
    ['name' => 'max_users', 'label' => 'Max Users', 'type' => 'number', 'placeholder' => '50', 'help' => 'Leave empty for unlimited', 'min' => '0', 'cols' => 'half'],
    ['name' => 'max_facilities', 'label' => 'Max Facilities', 'type' => 'number', 'placeholder' => '10', 'help' => 'Leave empty for unlimited', 'min' => '0', 'cols' => 'half'],
    ['name' => 'max_courts', 'label' => 'Max Courts', 'type' => 'number', 'placeholder' => '20', 'help' => 'Leave empty for unlimited', 'min' => '0', 'cols' => 'half'],
    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'placeholder' => '0', 'min' => '0', 'cols' => 'half'],
    ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox', 'help' => 'Plan is available for new subscriptions'],
    ['name' => 'features', 'label' => 'Features (JSON)', 'type' => 'json', 'placeholder' => '["Feature 1", "Feature 2"]'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function planEditForm() {
    const id = <?= (int) ($id ?? 0) ?>;

    return {
        form: {
            name: '', slug: '', description: '',
            price_monthly: '', price_yearly: '',
            max_users: '', max_facilities: '', max_courts: '',
            sort_order: '0', is_active: true, features: '[]'
        },
        errors: {},
        submitting: false,
        async init() {
            try {
                const res = await fetch(APP_BASE + '/api/plans/' + id, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('access_token'), 'Accept': 'application/json' }
                });
                if (res.status === 401) { window.location.href = APP_BASE + '/platform/login'; return; }
                const json = await res.json();
                const d = json.data;
                this.form = {
                    name: d.name || '',
                    slug: d.slug || '',
                    description: d.description || '',
                    price_monthly: d.price_monthly || '',
                    price_yearly: d.price_yearly || '',
                    max_users: d.max_users || '',
                    max_facilities: d.max_facilities || '',
                    max_courts: d.max_courts || '',
                    sort_order: d.sort_order || '0',
                    is_active: !!d.is_active,
                    features: d.features || '[]'
                };
            } catch (e) { console.error(e); }
        },
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            try {
                const payload = { ...this.form };
                payload.is_active = payload.is_active ? 1 : 0;
                payload.max_users = payload.max_users || null;
                payload.max_facilities = payload.max_facilities || null;
                payload.max_courts = payload.max_courts || null;

                const res = await fetch(APP_BASE + '/api/plans/' + id, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok) {
                    if (json.errors) this.errors = json.errors;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed to update', type: 'error' } }));
                    return;
                }
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Plan updated', type: 'success' } }));
                setTimeout(() => window.location.href = APP_BASE + '/platform/plans', 500);
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/platform.php';
?>
