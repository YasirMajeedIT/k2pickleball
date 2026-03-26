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
<!-- Social Media & Messaging Section -->
<div class="mt-6 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden" x-data="facilityForm()">
    <!-- Social Media -->
    <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30">
        <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            Social Media Links
        </h3>
        <p class="text-xs text-surface-400 mt-0.5">Connect your facility's social media profiles to display them on the public page.</p>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Instagram URL</label>
            <input type="url" x-model="form.instagram_url" placeholder="https://instagram.com/yourfacility"
                   class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
            <p class="text-xs text-surface-400 mt-1">Leave blank if not applicable</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Facebook URL</label>
            <input type="url" x-model="form.facebook_url" placeholder="https://facebook.com/yourfacility"
                   class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
            <p class="text-xs text-surface-400 mt-1">Leave blank if not applicable</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">YouTube URL</label>
            <input type="url" x-model="form.youtube_url" placeholder="https://youtube.com/@yourfacility"
                   class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
            <p class="text-xs text-surface-400 mt-1">Leave blank if not applicable</p>
        </div>
    </div>

    <!-- Twilio / SMS Section -->
    <div class="px-6 py-4 border-t border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30">
        <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            Twilio SMS / Messaging
        </h3>
        <p class="text-xs text-surface-400 mt-0.5">Configure Twilio to send SMS notifications and reminders to players. Obtain credentials from <a href="https://console.twilio.com" target="_blank" class="text-primary-500 hover:underline">console.twilio.com</a>.</p>
    </div>
    <div class="p-6">
        <label class="flex items-center gap-3 cursor-pointer mb-5">
            <input type="checkbox" x-model="form.twilio_enabled" class="w-4 h-4 rounded accent-primary-600">
            <span class="text-sm font-semibold text-surface-700 dark:text-surface-300">Enable Twilio SMS for this facility</span>
        </label>
        <div x-show="form.twilio_enabled" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Account SID <span class="text-red-500">*</span></label>
                <input type="text" x-model="form.twilio_sid" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                       class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                <p class="text-xs text-surface-400 mt-1">Found in your Twilio Console dashboard under "Account Info"</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Auth Token <span class="text-red-500">*</span></label>
                <input type="password" x-model="form.twilio_auth_token" placeholder="••••••••••••••••••••••••••••••••"
                       class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                <p class="text-xs text-surface-400 mt-1">Your Twilio auth token — keep this secret</p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">From Phone Number <span class="text-red-500">*</span></label>
                <input type="tel" x-model="form.twilio_from_number" placeholder="+15551234567"
                       class="w-full sm:w-72 rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                <p class="text-xs text-surface-400 mt-1">A Twilio-provisioned phone number in E.164 format (e.g. +15551234567)</p>
            </div>
        </div>
    </div>
</div>
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
            instagram_url: '', facebook_url: '', youtube_url: '',
            twilio_enabled: false, twilio_sid: '', twilio_auth_token: '', twilio_from_number: '',
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
                    tax_rate: this.form.tax_rate, description: this.form.description,
                    instagram_url: this.form.instagram_url || null,
                    facebook_url: this.form.facebook_url || null,
                    youtube_url: this.form.youtube_url || null,
                    twilio_enabled: this.form.twilio_enabled ? 1 : 0,
                    twilio_sid: this.form.twilio_enabled ? this.form.twilio_sid : null,
                    twilio_auth_token: this.form.twilio_enabled ? this.form.twilio_auth_token : null,
                    twilio_from_number: this.form.twilio_enabled ? this.form.twilio_from_number : null,
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

                // Upload image FIRST if provided, then include image_url in the create request
                if (this.form._file_facility_image) {
                    const fd = new FormData();
                    fd.append('file', this.form._file_facility_image);
                    fd.append('context', 'facility');
                    const uploadRes = await authFetch(APP_BASE + '/api/files', { method: 'POST', body: fd });
                    const uploadJson = await uploadRes.json();
                    if (uploadRes.ok && uploadJson.data && uploadJson.data.path) {
                        body.image_url = APP_BASE + '/storage/' + uploadJson.data.path;
                    } else {
                        console.error('Image upload failed:', uploadJson);
                    }
                }

                const res = await authFetch('<?= $apiUrl ?>', {
                    method: '<?= $method ?>',
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
