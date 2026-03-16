<?php

declare(strict_types=1);

namespace App\Modules\SessionTypes;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class SessionTypeController extends Controller
{
    private SessionTypeRepository $repo;
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
        $this->repo = new SessionTypeRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));
        $facilityId = $request->input('facility_id') ? (int) $request->input('facility_id') : null;
        $categoryId = $request->input('category_id') ? (int) $request->input('category_id') : null;

        $result = $this->repo->findByOrganization($orgId, $search ?: null, $facilityId, $categoryId, $page, $perPage);

        // Append classes count to each session type
        foreach ($result['data'] as &$st) {
            $st['classes_count'] = (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM `st_classes` WHERE `session_type_id` = ?",
                [(int) $st['id']]
            );
        }

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $record = $this->repo->findWithResourceValues($id);
        if (!$record) {
            throw new NotFoundException('Session type not found');
        }

        // Include pricing rules
        $record['pricing_rules'] = $this->db->fetchAll(
            "SELECT * FROM `st_pricing_rules` WHERE `session_type_id` = ? ORDER BY `priority` ASC",
            [$id]
        );

        // Include rolling prices
        $record['rolling_prices'] = $this->db->fetchAll(
            "SELECT * FROM `st_rolling_prices` WHERE `session_type_id` = ? ORDER BY `number_of_weeks` ASC",
            [$id]
        );

        // Include resource input values
        $record['resource_input_values'] = $this->db->fetchAll(
            "SELECT `resource_id`, `value` FROM `session_type_resource_inputs` WHERE `session_type_id` = ?",
            [$id]
        );

        // Include class count
        $record['classes_count'] = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `st_classes` WHERE `session_type_id` = ?",
            [$id]
        );

        // Include custom form fields
        $formFields = $this->db->fetchAll(
            "SELECT * FROM `session_form_fields` WHERE `session_type_id` = ? ORDER BY `sort_order` ASC, `id` ASC",
            [$id]
        );
        foreach ($formFields as &$ff) {
            $ff['field_options'] = $ff['field_options'] ? json_decode($ff['field_options'], true) : [];
            $ff['is_required'] = (bool) $ff['is_required'];
        }
        $record['form_fields'] = $formFields;

        // Include settings
        $settingsRows = $this->db->fetchAll(
            "SELECT `setting_key`, `setting_value` FROM `session_type_settings` WHERE `session_type_id` = ?",
            [$id]
        );
        $settings = [];
        foreach ($settingsRows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        $record['settings'] = $settings;

        return $this->success($record);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'facility_id'            => 'nullable|integer',
            'category_id'            => 'nullable|integer',
            'session_id'             => 'nullable|integer',
            'title'                  => 'required|string|max:255',
            'internal_title'         => 'nullable|string|max:255',
            'session_type'           => 'nullable|string',
            'capacity'               => 'nullable|integer',
            'duration'               => 'nullable|integer',
            'standard_price'         => 'nullable|numeric',
            'pricing_mode'           => 'nullable|string',
            'is_active'              => 'nullable|boolean',
            'private'                => 'nullable|boolean',
            'scheduling_url'         => 'nullable|string|max:500',
            'resource_value_ids'     => 'nullable|array',
            'resource_input_values'  => 'nullable|array',
            'pricing_rules'          => 'nullable|array',
            'rolling_prices'         => 'nullable|array',
            'settings'               => 'nullable|array',
        ]);

        $data['title'] = Sanitizer::string($data['title']);
        $data['session_type'] = in_array($data['session_type'] ?? '', ['class', 'series', 'series_rolling']) ? $data['session_type'] : 'class';
        $data['pricing_mode'] = in_array($data['pricing_mode'] ?? '', ['single', 'time_based', 'user_based']) ? $data['pricing_mode'] : 'single';

        foreach (['is_active', 'private'] as $boolField) {
            if (array_key_exists($boolField, $data)) {
                $data[$boolField] = !empty($data[$boolField]) ? 1 : 0;
            }
        }

        $resourceValueIds = $data['resource_value_ids'] ?? [];
        $resourceInputValues = $data['resource_input_values'] ?? [];
        $pricingRules = $data['pricing_rules'] ?? [];
        $rollingPrices = $data['rolling_prices'] ?? [];
        $settings = $data['settings'] ?? [];
        unset($data['resource_value_ids'], $data['resource_input_values'], $data['pricing_rules'], $data['rolling_prices'], $data['settings']);

        // Only keep columns that exist in the session_types table
        $allowedColumns = [
            'uuid', 'organization_id', 'facility_id', 'category_id', 'session_id',
            'title', 'internal_title', 'session_type', 'capacity', 'duration',
            'standard_price', 'pricing_mode', 'is_active', 'private',
            'scheduling_url', 'created_at', 'updated_at',
        ];
        $data = array_intersect_key($data, array_flip($allowedColumns));

        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $request->organizationId();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);

        if (!empty($resourceValueIds)) {
            $this->repo->syncResourceValues($id, $resourceValueIds);
        }
        $this->syncResourceInputValues($id, $resourceInputValues);

        $this->syncPricingRules($id, $pricingRules);
        $this->syncRollingPrices($id, $rollingPrices);
        $this->syncSettings($id, $settings);

        $record = $this->repo->findWithResourceValues($id);
        $record['pricing_rules'] = $this->db->fetchAll("SELECT * FROM `st_pricing_rules` WHERE `session_type_id` = ? ORDER BY `priority` ASC", [$id]);
        $record['rolling_prices'] = $this->db->fetchAll("SELECT * FROM `st_rolling_prices` WHERE `session_type_id` = ? ORDER BY `number_of_weeks` ASC", [$id]);

        return $this->created($record, 'Session type created');
    }

    public function update(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Session type not found');
        }

        $data = Validator::validate($request->all(), [
            'facility_id'            => 'nullable|integer',
            'category_id'            => 'nullable|integer',
            'session_id'             => 'nullable|integer',
            'title'                  => 'required|string|max:255',
            'internal_title'         => 'nullable|string|max:255',
            'session_type'           => 'nullable|string',
            'capacity'               => 'nullable|integer',
            'duration'               => 'nullable|integer',
            'standard_price'         => 'nullable|numeric',
            'pricing_mode'           => 'nullable|string',
            'is_active'              => 'nullable|boolean',
            'private'                => 'nullable|boolean',
            'scheduling_url'         => 'nullable|string|max:500',
            'resource_value_ids'     => 'nullable|array',
            'resource_input_values'  => 'nullable|array',
            'pricing_rules'          => 'nullable|array',
            'rolling_prices'         => 'nullable|array',
            'settings'               => 'nullable|array',
        ]);

        $data['title'] = Sanitizer::string($data['title']);
        $data['session_type'] = in_array($data['session_type'] ?? '', ['class', 'series', 'series_rolling']) ? $data['session_type'] : 'class';
        $data['pricing_mode'] = in_array($data['pricing_mode'] ?? '', ['single', 'time_based', 'user_based']) ? $data['pricing_mode'] : 'single';

        foreach (['is_active', 'private'] as $boolField) {
            if (array_key_exists($boolField, $data)) {
                $data[$boolField] = !empty($data[$boolField]) ? 1 : 0;
            }
        }

        $resourceValueIds = $data['resource_value_ids'] ?? [];
        $resourceInputValues = $data['resource_input_values'] ?? [];
        $pricingRules = $data['pricing_rules'] ?? [];
        $rollingPrices = $data['rolling_prices'] ?? [];
        $settings = $data['settings'] ?? [];
        unset($data['resource_value_ids'], $data['resource_input_values'], $data['pricing_rules'], $data['rolling_prices'], $data['settings']);

        // Only keep columns that exist in the session_types table
        $allowedColumns = [
            'facility_id', 'category_id', 'session_id',
            'title', 'internal_title', 'session_type', 'capacity', 'duration',
            'standard_price', 'pricing_mode', 'is_active', 'private',
            'scheduling_url', 'updated_at',
        ];
        $data = array_intersect_key($data, array_flip($allowedColumns));

        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->repo->update($id, $data);

        $this->repo->syncResourceValues($id, $resourceValueIds);
        $this->syncResourceInputValues($id, $resourceInputValues);
        $this->syncPricingRules($id, $pricingRules);
        $this->syncRollingPrices($id, $rollingPrices);
        $this->syncSettings($id, $settings);

        $record = $this->repo->findWithResourceValues($id);
        $record['pricing_rules'] = $this->db->fetchAll("SELECT * FROM `st_pricing_rules` WHERE `session_type_id` = ? ORDER BY `priority` ASC", [$id]);
        $record['rolling_prices'] = $this->db->fetchAll("SELECT * FROM `st_rolling_prices` WHERE `session_type_id` = ? ORDER BY `number_of_weeks` ASC", [$id]);

        return $this->success($record, 'Session type updated');
    }

    /**
     * Update spots (capacity) for a session type with optional propagation to classes.
     */
    public function updateSpots(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Session type not found');
        }

        $data = Validator::validate($request->all(), [
            'capacity' => 'required|integer',
            'apply_to_all_classes' => 'nullable|boolean',
        ]);

        $capacity = (int) $data['capacity'];
        $applyToAll = !empty($data['apply_to_all_classes']);

        $this->repo->update($id, ['capacity' => $capacity, 'updated_at' => date('Y-m-d H:i:s')]);

        $message = 'Spots updated on session type';
        if ($applyToAll) {
            // Propagate new capacity to all st_classes for this session_type
            $classes = $this->db->fetchAll(
                "SELECT `id`, `slots`, `slots_available` FROM `st_classes` WHERE `session_type_id` = ?",
                [$id]
            );
            foreach ($classes as $cls) {
                $oldSlots = (int) $cls['slots'];
                $diff = $capacity - $oldSlots;
                $newAvailable = max(0, (int) $cls['slots_available'] + $diff);
                $this->db->update('st_classes', [
                    'slots' => $capacity,
                    'slots_available' => $newAvailable,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], ['id' => (int) $cls['id']]);
            }
            $message = 'Spots updated on session type and ' . count($classes) . ' class(es)';
        }

        return $this->success(['capacity' => $capacity, 'applied_to_classes' => $applyToAll], $message);
    }

    public function destroy(Request $request, int $id): Response
    {
        $record = $this->repo->findById($id);
        if (!$record) {
            throw new NotFoundException('Session type not found');
        }

        $this->repo->delete($id);
        return $this->success(null, 'Session type deleted');
    }

    /**
     * Get all resources with their values for the current org (for session type form).
     */
    public function resources(Request $request): Response
    {
        $orgId = $request->organizationId();
        $resources = $this->db->fetchAll(
            "SELECT r.id, r.name, r.field_type, r.description
             FROM `resources` r
             WHERE r.organization_id = ?
             ORDER BY r.name ASC",
            [$orgId]
        );

        foreach ($resources as &$resource) {
            $resource['values'] = $this->db->fetchAll(
                "SELECT id, name, description, sort_order
                 FROM `resource_values`
                 WHERE `resource_id` = ?
                 ORDER BY `sort_order` ASC, `name` ASC",
                [(int) $resource['id']]
            );
        }

        return $this->success($resources);
    }

    private function syncResourceInputValues(int $sessionTypeId, array $inputValues): void
    {
        $this->db->query("DELETE FROM `session_type_resource_inputs` WHERE `session_type_id` = ?", [$sessionTypeId]);

        foreach ($inputValues as $iv) {
            $resourceId = (int) ($iv['resource_id'] ?? 0);
            $value = trim((string) ($iv['value'] ?? ''));
            if ($resourceId && $value !== '') {
                $this->db->insert('session_type_resource_inputs', [
                    'session_type_id' => $sessionTypeId,
                    'resource_id'     => $resourceId,
                    'value'           => $value,
                ]);
            }
        }
    }

    private function syncPricingRules(int $sessionTypeId, array $rules): void
    {
        $this->db->query("DELETE FROM `st_pricing_rules` WHERE `session_type_id` = ?", [$sessionTypeId]);

        foreach ($rules as $i => $rule) {
            $type = in_array($rule['pricing_type'] ?? '', ['time_based', 'user_based']) ? $rule['pricing_type'] : 'time_based';
            $this->db->insert('st_pricing_rules', [
                'session_type_id'    => $sessionTypeId,
                'pricing_type'       => $type,
                'price'              => (float) ($rule['price'] ?? 0),
                'start_offset_days'  => isset($rule['start_offset_days']) ? (int) $rule['start_offset_days'] : null,
                'max_users'          => isset($rule['max_users']) ? (int) $rule['max_users'] : null,
                'priority'           => $i,
                'created_at'         => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function syncRollingPrices(int $sessionTypeId, array $prices): void
    {
        $this->db->query("DELETE FROM `st_rolling_prices` WHERE `session_type_id` = ?", [$sessionTypeId]);

        foreach ($prices as $price) {
            if (empty($price['number_of_weeks']) || !isset($price['price'])) {
                continue;
            }
            $this->db->insert('st_rolling_prices', [
                'session_type_id' => $sessionTypeId,
                'number_of_weeks' => (int) $price['number_of_weeks'],
                'price'           => (float) $price['price'],
            ]);
        }
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Sync key-value settings for a session type.
     */
    private function syncSettings(int $sessionTypeId, array $settings): void
    {
        if (empty($settings)) return;

        // Whitelist of allowed setting keys
        $allowedKeys = [
            'auto_reserve_partner',
            'auto_generate',
        ];

        foreach ($settings as $key => $value) {
            $key = Sanitizer::string((string) $key);
            if (!in_array($key, $allowedKeys, true)) continue;

            $value = Sanitizer::string((string) ($value ?? ''));

            $existing = $this->db->fetch(
                "SELECT `id` FROM `session_type_settings` WHERE `session_type_id` = ? AND `setting_key` = ?",
                [$sessionTypeId, $key]
            );

            if ($existing) {
                $this->db->update('session_type_settings', [
                    'setting_value' => $value,
                    'updated_at'    => date('Y-m-d H:i:s'),
                ], ['id' => $existing['id']]);
            } else {
                $this->db->insert('session_type_settings', [
                    'session_type_id' => $sessionTypeId,
                    'setting_key'     => $key,
                    'setting_value'   => $value,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
