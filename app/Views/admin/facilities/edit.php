<?php
$title = 'Edit Facility';
$breadcrumbs = [['label' => 'Facilities', 'url' => ($baseUrl ?? '') . '/admin/facilities'], ['label' => 'Edit']];

$formId = 'facilityEditForm';
$apiUrl = ($baseUrl ?? '') . '/api/facilities';
$method = 'PUT';
$backUrl = ($baseUrl ?? '') . '/admin/facilities';
$fields = [
    ['name' => 'name', 'label' => 'Facility Name', 'required' => true],
    ['name' => 'slug', 'label' => 'Slug', 'required' => true, 'help' => 'URL-friendly identifier'],
    ['name' => 'address_line1', 'label' => 'Address Line 1', 'required' => true],
    ['name' => 'address_line2', 'label' => 'Address Line 2'],
    ['name' => 'city', 'label' => 'City', 'required' => true, 'cols' => 'half'],
    ['name' => 'state', 'label' => 'State', 'required' => true, 'cols' => 'half'],
    ['name' => 'zip', 'label' => 'ZIP Code', 'required' => true, 'cols' => 'half'],
    ['name' => 'country', 'label' => 'Country', 'cols' => 'half'],
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
    ['name' => 'operating_hours', 'label' => 'Operating Hours', 'type' => 'json', 'help' => 'JSON object'],
    ['name' => 'amenities', 'label' => 'Amenities', 'type' => 'json', 'help' => 'JSON array'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function facilityEditForm() {
    const token = localStorage.getItem('access_token');
    const id = <?= (int)($id ?? 0) ?>;

    return {
        form: { name: '', slug: '', address_line1: '', address_line2: '', city: '', state: '', zip: '', country: '', phone: '', email: '', timezone: '', status: '', operating_hours: '', amenities: '', description: '' },
        errors: {},
        submitting: false,
        async init() {
            try {
                const res = await fetch(APP_BASE + '/api/facilities/' + id, {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (json.data) {
                    const d = json.data;
                    this.form = {
                        name: d.name || '', slug: d.slug || '',
                        address_line1: d.address_line1 || '', address_line2: d.address_line2 || '',
                        city: d.city || '', state: d.state || '', zip: d.zip || '',
                        country: d.country || '', phone: d.phone || '', email: d.email || '',
                        timezone: d.timezone || '', status: d.status || '',
                        operating_hours: (d.settings && d.settings.operating_hours) ? JSON.stringify(d.settings.operating_hours, null, 2) : '',
                        amenities: (d.settings && d.settings.amenities) ? JSON.stringify(d.settings.amenities) : '',
                        description: d.description || ''
                    };
                }
            } catch (e) { console.error(e); }
        },
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
                const res = await fetch(APP_BASE + '/api/facilities/' + id, {
                    method: 'PUT',
                    headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Facility updated', type: 'success' } }));
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
