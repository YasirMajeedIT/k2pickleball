<?php
$title = 'Create Facility';
$breadcrumbs = [['label' => 'Facilities', 'url' => ($baseUrl ?? '') . '/admin/facilities'], ['label' => 'Create']];

$formId = 'facilityForm';
$apiUrl = ($baseUrl ?? '') . '/api/facilities';
$method = 'POST';
$backUrl = ($baseUrl ?? '') . '/admin/facilities';
$fields = [
    ['name' => 'name', 'label' => 'Facility Name', 'required' => true, 'placeholder' => 'Main Sports Complex'],
    ['name' => 'slug', 'label' => 'Slug', 'required' => true, 'placeholder' => 'main-sports-complex', 'help' => 'URL-friendly identifier'],
    ['name' => 'address_line1', 'label' => 'Address Line 1', 'required' => true, 'placeholder' => '123 Main Street'],
    ['name' => 'address_line2', 'label' => 'Address Line 2', 'placeholder' => 'Suite 100'],
    ['name' => 'city', 'label' => 'City', 'required' => true, 'cols' => 'half'],
    ['name' => 'state', 'label' => 'State', 'required' => true, 'cols' => 'half'],
    ['name' => 'zip', 'label' => 'ZIP Code', 'required' => true, 'cols' => 'half'],
    ['name' => 'country', 'label' => 'Country', 'cols' => 'half', 'placeholder' => 'US'],
    ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'cols' => 'half'],
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'cols' => 'half'],
    ['name' => 'timezone', 'label' => 'Timezone', 'type' => 'select', 'options' => [
        'America/New_York' => 'Eastern', 'America/Chicago' => 'Central',
        'America/Denver' => 'Mountain', 'America/Los_Angeles' => 'Pacific',
        'America/Phoenix' => 'Arizona', 'Pacific/Honolulu' => 'Hawaii',
    ]],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'options' => [
        'active' => 'Active', 'inactive' => 'Inactive', 'maintenance' => 'Maintenance',
    ]],
    ['name' => 'operating_hours', 'label' => 'Operating Hours', 'type' => 'json', 'placeholder' => '{"mon":"6:00-22:00","tue":"6:00-22:00"}', 'help' => 'JSON object with day abbreviations as keys'],
    ['name' => 'amenities', 'label' => 'Amenities', 'type' => 'json', 'placeholder' => '["parking","restrooms","pro_shop"]', 'help' => 'JSON array of amenity slugs'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function facilityForm() {
    const token = localStorage.getItem('access_token');
    return {
        form: { name: '', slug: '', address_line1: '', address_line2: '', city: '', state: '', zip: '', country: 'US', phone: '', email: '', timezone: 'America/New_York', status: 'active', operating_hours: '', amenities: '', description: '' },
        errors: {},
        submitting: false,
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            try {
                const body = { ...this.form };
                const settings = {};
                if (body.operating_hours) { try { settings.operating_hours = JSON.parse(body.operating_hours); } catch(e) { settings.operating_hours = body.operating_hours; } }
                if (body.amenities) { try { settings.amenities = JSON.parse(body.amenities); } catch(e) { settings.amenities = body.amenities; } }
                if (Object.keys(settings).length) body.settings = JSON.stringify(settings);
                delete body.operating_hours;
                delete body.amenities;
                const res = await fetch('<?= $apiUrl ?>', {
                    method: '<?= $method ?>',
                    headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Facility created successfully', type: 'success' } }));
                    setTimeout(() => window.location.href = '<?= $backUrl ?>', 500);
                } else {
                    this.errors = json.errors || {};
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Validation failed', type: 'error' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network error', type: 'error' } }));
            }
            this.submitting = false;
        }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
