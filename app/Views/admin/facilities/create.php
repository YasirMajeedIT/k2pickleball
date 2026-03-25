<?php
$title = 'Create Facility';
$breadcrumbs = [['label' => 'Facilities', 'url' => ($baseUrl ?? '') . '/admin/facilities'], ['label' => 'Create']];

$formId = 'facilityForm';
$apiUrl = ($baseUrl ?? '') . '/api/facilities';
$method = 'POST';
$backUrl = ($baseUrl ?? '') . '/admin/facilities';
$fields = [
    ['name' => 'name', 'label' => 'Facility Name', 'required' => true, 'placeholder' => 'Main Sports Complex'],
    ['name' => 'tagline', 'label' => 'Tagline', 'placeholder' => 'Premier pickleball destination', 'help' => 'A short description shown to players'],
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
    ['name' => 'tax_rate', 'label' => 'Tax Rate (%)', 'type' => 'number', 'cols' => 'half', 'placeholder' => '0.00', 'step' => '0.01', 'min' => '0', 'max' => '100', 'help' => 'Applied to taxable items'],
    ['name' => 'facility_image', 'label' => 'Facility Image', 'type' => 'file', 'accept' => 'image/*', 'help' => 'Facility image (JPEG, PNG, SVG)'],
    ['name' => 'amenities', 'label' => 'Amenities', 'type' => 'checkbox-group', 'default_items' => [
        'restrooms' => 'Restrooms', 'parking' => 'Parking', 'pro_shop' => 'Pro Shop',
        'locker_rooms' => 'Locker Rooms', 'spectator_seating' => 'Spectator Seating',
        'water_fountains' => 'Water Fountains', 'lighting' => 'Lighting',
        'wifi' => 'WiFi', 'vending_machines' => 'Vending Machines', 'first_aid' => 'First Aid',
    ]],
    ['name' => 'operating_hours', 'label' => 'Operating Hours', 'type' => 'custom'],
    ['name' => 'use_own_smtp', 'label' => 'Email Settings', 'type' => 'checkbox', 'help' => 'Use facility-specific SMTP settings'],
    ['name' => 'smtp_email', 'label' => 'SMTP Email', 'type' => 'email', 'cols' => 'half', 'placeholder' => 'noreply@facility.com'],
    ['name' => 'smtp_password', 'label' => 'SMTP Password', 'type' => 'password', 'cols' => 'half', 'placeholder' => '••••••••'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function facilityForm() {
    const dayLabels = {mon:'Monday',tue:'Tuesday',wed:'Wednesday',thu:'Thursday',fri:'Friday',sat:'Saturday',sun:'Sunday'};

    return {
        _slugManuallyEdited: false,
        form: {
            name: '', tagline: '', slug: '', address_line1: '', address_line2: '',
            city: '', state: '', zip: '', country: 'US', phone: '', email: '',
            timezone: 'America/New_York', status: 'active', tax_rate: '0.00',
            facility_image: '', _file_facility_image: null, _preview_facility_image: '',
            amenities: [], _custom_amenities: [],
            operating_hours: '',
            hours: {
                mon: { open: '06:00', close: '22:00', closed: false },
                tue: { open: '06:00', close: '22:00', closed: false },
                wed: { open: '06:00', close: '22:00', closed: false },
                thu: { open: '06:00', close: '22:00', closed: false },
                fri: { open: '06:00', close: '22:00', closed: false },
                sat: { open: '08:00', close: '20:00', closed: false },
                sun: { open: '08:00', close: '20:00', closed: false },
            },
            dayLabels: dayLabels,
            use_own_smtp: false, smtp_email: '', smtp_password: '',
            description: ''
        },
        errors: {},
        submitting: false,
        init() {
            this.$watch('form.name', (val) => {
                if (!this._slugManuallyEdited) {
                    this.form.slug = val.toLowerCase().trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/[\s]+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                }
            });
            this.$watch('form.slug', (val, oldVal) => {
                // Mark as manually edited if user types in slug field directly (not from watcher)
                if (val !== oldVal && this.form.name) {
                    const autoSlug = this.form.name.toLowerCase().trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/[\s]+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                    if (val !== autoSlug) this._slugManuallyEdited = true;
                }
            });
        },
        timeOptions() {
            const opts = [];
            for (let h = 0; h < 24; h++) {
                for (let m = 0; m < 60; m += 30) {
                    const val = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                    const hr = h === 0 ? 12 : (h > 12 ? h - 12 : h);
                    const ampm = h < 12 ? 'AM' : 'PM';
                    const label = hr + ':' + String(m).padStart(2,'0') + ' ' + ampm;
                    opts.push({ value: val, label: label });
                }
            }
            return opts;
        },
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            try {
                // Build operating hours from the day-by-day picker
                const opHours = {};
                for (const [day, info] of Object.entries(this.form.hours)) {
                    if (info && typeof info === 'object' && 'closed' in info) {
                        opHours[day] = info.closed ? 'closed' : (info.open + '-' + info.close);
                    }
                }

                const body = {
                    name: this.form.name, tagline: this.form.tagline, slug: this.form.slug,
                    address_line1: this.form.address_line1, address_line2: this.form.address_line2,
                    city: this.form.city, state: this.form.state, zip_code: this.form.zip,
                    country: this.form.country, phone: this.form.phone, email: this.form.email,
                    timezone: this.form.timezone, status: this.form.status,
                    tax_rate: this.form.tax_rate, description: this.form.description
                };
                // Pack settings
                const settings = {};
                settings.operating_hours = opHours;
                if (this.form.amenities && this.form.amenities.length > 0) {
                    settings.amenities = [...this.form.amenities];
                }
                if (this.form.use_own_smtp) {
                    settings.smtp = { email: this.form.smtp_email, password: this.form.smtp_password, enabled: true };
                }
                body.settings = JSON.stringify(settings);

                const res = await authFetch('<?= $apiUrl ?>', {
                    method: '<?= $method ?>',
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (res.ok) {
                    // Upload image if provided
                    let imageUrl = null;
                    if (this.form._file_facility_image && json.data && json.data.id) {
                        const fd = new FormData();
                        fd.append('file', this.form._file_facility_image);
                        fd.append('context', 'facility');
                        const uploadRes = await authFetch(APP_BASE + '/api/files', { method: 'POST', body: fd });
                        const uploadJson = await uploadRes.json();
                        if (uploadRes.ok && uploadJson.data && uploadJson.data.path) {
                            imageUrl = APP_BASE + '/storage/' + uploadJson.data.path;
                            // Update facility with image_url
                            await authFetch(APP_BASE + '/api/facilities/' + json.data.id, {
                                method: 'PUT',
                                body: JSON.stringify({ ...body, image_url: imageUrl })
                            });
                        }
                    }
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Facility created successfully', type: 'success' } }));
                    setTimeout(() => window.location.href = '<?= $backUrl ?>', 500);
                } else {
                    this.errors = json.errors || {};
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: json.message || 'Validation failed', type: 'error' } }));
                }
            } catch (e) {
                console.error(e);
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
