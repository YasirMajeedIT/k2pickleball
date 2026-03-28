<?php
$title = 'Edit Facility';
$breadcrumbs = [['label' => 'Facilities', 'url' => ($baseUrl ?? '') . '/admin/facilities'], ['label' => 'Edit']];

$formId = 'facilityEditForm';
$apiUrl = ($baseUrl ?? '') . '/api/facilities';
$method = 'PUT';
$backUrl = ($baseUrl ?? '') . '/admin/facilities';
$fields = [
    ['name' => 'name', 'label' => 'Facility Name', 'required' => true],
    ['name' => 'tagline', 'label' => 'Tagline', 'placeholder' => 'Premier pickleball destination', 'help' => 'A short description shown to players'],
    ['name' => 'slug', 'label' => 'Slug', 'required' => true, 'help' => 'URL-friendly identifier'],
    ['name' => 'facility_type', 'label' => 'Facility Type', 'type' => 'select', 'required' => true, 'options' => [
        'sports_facility' => 'Sports Facility', 'recreational_center' => 'Recreational Center',
        'fitness_center' => 'Fitness Center', 'community_center' => 'Community Center',
        'country_club' => 'Country Club', 'resort' => 'Resort', 'school' => 'School',
        'park' => 'Park', 'other' => 'Other',
    ]],
    ['name' => 'sport_type', 'label' => 'Sport Type', 'type' => 'custom'],
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
    ['name' => 'social_links', 'label' => 'Social Media Links', 'type' => 'custom'],
    ['name' => 'twilio_settings', 'label' => 'Twilio SMS / Messaging', 'type' => 'custom'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function facilityEditForm() {
    const token = localStorage.getItem('access_token');
    const id = <?= (int)($id ?? 0) ?>;

    const defaultAmenities = ['restrooms','parking','pro_shop','locker_rooms','spectator_seating','water_fountains','lighting','wifi','vending_machines','first_aid'];
    const dayLabels = {mon:'Monday',tue:'Tuesday',wed:'Wednesday',thu:'Thursday',fri:'Friday',sat:'Saturday',sun:'Sunday'};

    return {
        form: {
            name: '', tagline: '', slug: '', address_line1: '', address_line2: '',
            city: '', state: '', zip: '', country: '', phone: '', email: '',
            timezone: '', status: '', tax_rate: '0.00',
            sport_type: 'pickleball', custom_sport_type: '',
            facility_type: 'sports_facility',
            facility_image: '', _file_facility_image: null, _preview_facility_image: '', _existing_image_url: '',
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
            instagram_url: '', facebook_url: '', youtube_url: '', hero_video_url: '',
            twilio_enabled: false, twilio_sid: '', twilio_auth_token: '', twilio_from_number: '',
            description: ''
        },
        errors: {},
        submitting: false,
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
        async init() {
            try {
                const res = await authFetch(APP_BASE + '/api/facilities/' + id);
                const json = await res.json();
                if (json.data) {
                    const d = json.data;
                    const settings = (typeof d.settings === 'string') ? JSON.parse(d.settings || '{}') : (d.settings || {});
                    const amenities = Array.isArray(settings.amenities) ? settings.amenities : [];
                    const customAmenities = amenities.filter(a => !defaultAmenities.includes(a));
                    const smtp = settings.smtp || {};

                    // Parse operating hours
                    const hours = { ...this.form.hours };
                    if (settings.operating_hours && typeof settings.operating_hours === 'object') {
                        for (const [day, val] of Object.entries(settings.operating_hours)) {
                            if (hours[day]) {
                                if (val === 'closed') {
                                    hours[day].closed = true;
                                } else if (typeof val === 'string' && val.includes('-')) {
                                    const [open, close] = val.split('-');
                                    hours[day].open = open.trim();
                                    hours[day].close = close.trim();
                                    hours[day].closed = false;
                                }
                            }
                        }
                    }

                    this.form = {
                        ...this.form,
                        name: d.name || '', tagline: d.tagline || '', slug: d.slug || '',
                        sport_type: d.sport_type || 'pickleball',
                        custom_sport_type: d.custom_sport_type || '',
                        facility_type: d.facility_type || 'sports_facility',
                        address_line1: d.address_line1 || '', address_line2: d.address_line2 || '',
                        city: d.city || '', state: d.state || '', zip: d.zip || '',
                        country: d.country || '', phone: d.phone || '', email: d.email || '',
                        timezone: d.timezone || '', status: d.status || '',
                        tax_rate: d.tax_rate || '0.00',
                        facility_image: '', _file_facility_image: null,
                        _preview_facility_image: d.image_url || '',
                        _existing_image_url: d.image_url || '',
                        amenities: amenities,
                        _custom_amenities: customAmenities,
                        hours: hours,
                        use_own_smtp: !!(smtp.enabled), smtp_email: smtp.email || '', smtp_password: smtp.password || '',
                        instagram_url: d.instagram_url || '',
                        facebook_url: d.facebook_url || '',
                        youtube_url: d.youtube_url || '',
                        hero_video_url: d.hero_video_url || '',
                        twilio_enabled: !!(d.twilio_enabled),
                        twilio_sid: d.twilio_sid || '',
                        twilio_auth_token: d.twilio_auth_token || '',
                        twilio_from_number: d.twilio_from_number || '',
                        description: d.description || ''
                    };
                }
            } catch (e) { console.error(e); }
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
                    sport_type: this.form.sport_type,
                    custom_sport_type: this.form.sport_type === 'other' ? this.form.custom_sport_type : null,
                    facility_type: this.form.facility_type,
                    address_line1: this.form.address_line1, address_line2: this.form.address_line2,
                    city: this.form.city, state: this.form.state, zip_code: this.form.zip,
                    country: this.form.country, phone: this.form.phone, email: this.form.email,
                    timezone: this.form.timezone, status: this.form.status,
                    tax_rate: this.form.tax_rate, description: this.form.description,
                    instagram_url: this.form.instagram_url || null,
                    facebook_url: this.form.facebook_url || null,
                    youtube_url: this.form.youtube_url || null,
                    hero_video_url: this.form.hero_video_url || null,
                    twilio_enabled: this.form.twilio_enabled ? 1 : 0,
                    twilio_sid: this.form.twilio_enabled ? this.form.twilio_sid : null,
                    twilio_auth_token: this.form.twilio_enabled ? this.form.twilio_auth_token : null,
                    twilio_from_number: this.form.twilio_enabled ? this.form.twilio_from_number : null,
                };
                const settings = {};
                settings.operating_hours = opHours;
                if (this.form.amenities && this.form.amenities.length > 0) {
                    settings.amenities = [...this.form.amenities];
                }
                if (this.form.use_own_smtp) {
                    settings.smtp = { email: this.form.smtp_email, password: this.form.smtp_password, enabled: true };
                }
                body.settings = JSON.stringify(settings);

                // Upload image first if new file provided
                let imageUrl = null;
                if (this.form._file_facility_image) {
                    const fd = new FormData();
                    fd.append('file', this.form._file_facility_image);
                    fd.append('context', 'facility');
                    const uploadRes = await authFetch(APP_BASE + '/api/files', { method: 'POST', body: fd });
                    const uploadJson = await uploadRes.json();
                    if (uploadRes.ok && uploadJson.data && uploadJson.data.path) {
                        imageUrl = APP_BASE + '/storage/' + uploadJson.data.path;
                    } else {
                        const uploadMsg = uploadJson?.message || 'Image upload failed. Check storage permissions.';
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: uploadMsg, type: 'error' } }));
                        this.submitting = false;
                        return;
                    }
                }
                if (imageUrl) {
                    body.image_url = imageUrl;
                } else if (this.form._preview_facility_image && this.form._existing_image_url) {
                    // Preserve existing image if not cleared
                    body.image_url = this.form._existing_image_url;
                }

                const res = await authFetch(APP_BASE + '/api/facilities/' + id, {
                    method: 'PUT',
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
