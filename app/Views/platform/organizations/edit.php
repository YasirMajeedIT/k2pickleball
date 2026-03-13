<?php
$title = 'Edit Organization';
$breadcrumbs = [
    ['label' => 'Organizations', 'url' => ($baseUrl ?? '') . '/platform/organizations'],
    ['label' => 'Edit'],
];
$backUrl = ($baseUrl ?? '') . '/platform/organizations/' . ($id ?? '');
$formId = 'orgEditForm';
$apiUrl = ($baseUrl ?? '') . '/api/organizations';
$method = 'PUT';
$fields = [
    ['name' => 'name', 'label' => 'Organization Name', 'required' => true, 'placeholder' => 'Acme Corp', 'cols' => 'half'],
    ['name' => 'slug', 'label' => 'Slug', 'required' => true, 'placeholder' => 'acme-corp', 'help' => 'URL-friendly identifier', 'cols' => 'half'],
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => 'admin@acme.com', 'cols' => 'half'],
    ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'placeholder' => '+1 555-0100', 'cols' => 'half'],
    ['name' => 'website', 'label' => 'Website', 'type' => 'text', 'placeholder' => 'https://acme.com', 'cols' => 'full'],
    ['name' => 'address_line1', 'label' => 'Address Line 1', 'placeholder' => '123 Main St'],
    ['name' => 'address_line2', 'label' => 'Address Line 2', 'placeholder' => 'Suite 100'],
    ['name' => 'city', 'label' => 'City', 'placeholder' => 'Springfield', 'cols' => 'half'],
    ['name' => 'state', 'label' => 'State', 'placeholder' => 'IL', 'cols' => 'half'],
    ['name' => 'zip_code', 'label' => 'ZIP Code', 'placeholder' => '62704', 'cols' => 'half'],
    ['name' => 'country', 'label' => 'Country', 'placeholder' => 'US', 'help' => '2-letter country code', 'cols' => 'half'],
    ['name' => 'timezone', 'label' => 'Timezone', 'type' => 'select', 'options' => [
        'America/New_York' => 'Eastern (America/New_York)',
        'America/Chicago' => 'Central (America/Chicago)',
        'America/Denver' => 'Mountain (America/Denver)',
        'America/Los_Angeles' => 'Pacific (America/Los_Angeles)',
        'America/Phoenix' => 'Arizona (America/Phoenix)',
        'Pacific/Honolulu' => 'Hawaii (Pacific/Honolulu)',
        'America/Anchorage' => 'Alaska (America/Anchorage)',
        'UTC' => 'UTC',
    ]],
    ['name' => 'settings', 'label' => 'Settings (JSON)', 'type' => 'json', 'placeholder' => '{}'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function orgEditForm() {
    const id = <?= (int) ($id ?? 0) ?>;

    return {
        form: {
            name: '', slug: '', email: '', phone: '', website: '',
            address_line1: '', address_line2: '', city: '', state: '',
            zip_code: '', country: '', timezone: '', settings: '{}'
        },
        errors: {},
        submitting: false,
        async init() {
            try {
                const res = await fetch(APP_BASE + '/api/organizations/' + id, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('access_token'), 'Accept': 'application/json' }
                });
                if (res.status === 401) { window.location.href = APP_BASE + '/admin/login'; return; }
                const json = await res.json();
                const d = json.data;
                this.form = {
                    name: d.name || '',
                    slug: d.slug || '',
                    email: d.email || '',
                    phone: d.phone || '',
                    website: d.website || '',
                    address_line1: d.address_line1 || '',
                    address_line2: d.address_line2 || '',
                    city: d.city || '',
                    state: d.state || '',
                    zip_code: d.zip || '',
                    country: d.country || '',
                    timezone: d.timezone || '',
                    settings: d.settings || '{}'
                };
            } catch (e) { console.error(e); }
        },
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            try {
                const res = await fetch(APP_BASE + '/api/organizations/' + id, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });
                const json = await res.json();
                if (!res.ok) {
                    if (json.errors) this.errors = json.errors;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed to update', type: 'error' } }));
                    return;
                }
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Organization updated', type: 'success' } }));
                setTimeout(() => window.location.href = APP_BASE + '/platform/organizations/' + id, 500);
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
