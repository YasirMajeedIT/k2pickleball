<?php
$title = 'Create Court';
$breadcrumbs = [['label' => 'Courts', 'url' => ($baseUrl ?? '') . '/admin/courts'], ['label' => 'Create']];

$formId = 'courtForm';
$apiUrl = ($baseUrl ?? '') . '/api/courts';
$method = 'POST';
$backUrl = ($baseUrl ?? '') . '/admin/courts';
$fields = [
    ['name' => 'facility_id', 'label' => 'Facility', 'type' => 'select', 'required' => true, 'options' => []],
    ['name' => 'name', 'label' => 'Court Name', 'required' => true, 'placeholder' => 'Court 1'],
    ['name' => 'sport_type', 'label' => 'Sport Type', 'type' => 'select', 'required' => true, 'options' => [
        'pickleball' => 'Pickleball', 'tennis' => 'Tennis', 'badminton' => 'Badminton',
        'basketball' => 'Basketball', 'volleyball' => 'Volleyball', 'multi' => 'Multi-Sport',
    ]],
    ['name' => 'surface_type', 'label' => 'Surface Type', 'type' => 'select', 'options' => [
        'concrete' => 'Concrete', 'asphalt' => 'Asphalt', 'wood' => 'Wood',
        'synthetic' => 'Synthetic', 'clay' => 'Clay', 'grass' => 'Grass',
    ]],
    ['name' => 'is_indoor', 'label' => 'Indoor Court', 'type' => 'select', 'options' => ['0' => 'No', '1' => 'Yes'], 'cols' => 'half'],
    ['name' => 'is_lighted', 'label' => 'Lighted', 'type' => 'select', 'options' => ['0' => 'No', '1' => 'Yes'], 'cols' => 'half'],
    ['name' => 'court_number', 'label' => 'Court Number', 'placeholder' => 'e.g. 1, A, etc.', 'cols' => 'half'],
    ['name' => 'hourly_rate', 'label' => 'Hourly Rate ($)', 'type' => 'number', 'step' => '0.01', 'min' => '0', 'cols' => 'half'],
    ['name' => 'max_players', 'label' => 'Max Players', 'type' => 'number', 'min' => '1', 'cols' => 'half'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'options' => [
        'active' => 'Active', 'inactive' => 'Inactive', 'maintenance' => 'Maintenance', 'reserved' => 'Reserved',
    ]],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
];

ob_start();
include __DIR__ . '/../../components/form.php';
?>
<script>
function courtForm() {
    return {
        form: { facility_id: '', name: '', sport_type: 'pickleball', surface_type: 'concrete', is_indoor: '0', is_lighted: '0', court_number: '', hourly_rate: '', max_players: '4', status: 'active', description: '' },
        errors: {},
        submitting: false,
        async init() {
            // Load facilities for select dropdown
            try {
                const res = await authFetch(APP_BASE + '/api/facilities');
                const json = await res.json();
                if (json.data) {
                    const select = this.$el.querySelector('select[x-model="form.facility_id"]');
                    if (select) {
                        json.data.forEach(f => {
                            const opt = document.createElement('option');
                            opt.value = f.id;
                            opt.textContent = f.name;
                            select.appendChild(opt);
                        });
                    }
                }
            } catch (e) { console.error(e); }
        },
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            try {
                const body = { ...this.form };
                if (body.hourly_rate) body.hourly_rate = parseFloat(body.hourly_rate);
                if (body.max_players) body.max_players = parseInt(body.max_players);
                body.is_indoor = parseInt(body.is_indoor);
                body.is_lighted = parseInt(body.is_lighted);
                const res = await authFetch('<?= $apiUrl ?>', {
                    method: '<?= $method ?>',
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Court created', type: 'success' } }));
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
