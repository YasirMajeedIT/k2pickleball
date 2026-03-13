<?php
$title = 'Create Organization';
$breadcrumbs = [
    ['label' => 'Organizations', 'url' => ($baseUrl ?? '') . '/platform/organizations'],
    ['label' => 'Create'],
];
$backUrl = ($baseUrl ?? '') . '/platform/organizations';
$formId = 'orgCreateForm';
$apiUrl = ($baseUrl ?? '') . '/api/organizations';
$method = 'POST';
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
function orgCreateForm() {
    return {
        form: {
            name: '', slug: '', email: '', phone: '', website: '',
            address_line1: '', address_line2: '', city: '', state: '',
            zip_code: '', country: '', timezone: 'America/New_York', settings: '{}'
        },
        errors: {},
        submitting: false,
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            try {
                const res = await fetch(APP_BASE + '/api/organizations', {
                    method: 'POST',
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
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Failed to create', type: 'error' } }));
                    return;
                }
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Organization created', type: 'success' } }));
                setTimeout(() => window.location.href = APP_BASE + '/platform/organizations/' + json.data.id, 500);
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
