<?php

declare(strict_types=1);

namespace App\Modules\Tenant;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;

/**
 * Public API endpoints for tenant-facing frontend.
 * All endpoints are scoped to the organization resolved by TenantResolver.
 * No authentication required — these serve the public website.
 */
class PublicApiController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * GET /api/public/org
     * Organization branding, name, logo, settings for the public site.
     */
    public function organization(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $org = $this->db->fetch(
            "SELECT `id`, `name`, `slug`, `email`, `phone`, `timezone`, `currency`, `settings`
             FROM `organizations` WHERE `id` = ? AND `status` IN ('active', 'trial')",
            [$orgId]
        );

        if (!$org) {
            return $this->error('Organization not found', 404);
        }

        $org['settings'] = json_decode($org['settings'] ?? '{}', true) ?: [];

        // Load branding settings
        $branding = $this->db->fetchAll(
            "SELECT `key_name`, `value` FROM `settings`
             WHERE `organization_id` = ? AND `group_name` = 'branding'",
            [$orgId]
        );
        $org['branding'] = [];
        foreach ($branding as $row) {
            $org['branding'][$row['key_name']] = $row['value'];
        }

        return $this->success($org);
    }

    /**
     * GET /api/public/facilities
     * List all active facilities for the organization.
     */
    public function facilities(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $facilities = $this->db->fetchAll(
            "SELECT `id`, `name`, `slug`, `tagline`, `description`, `address_line1`, `address_line2`,
                    `city`, `state`, `zip_code`, `country`, `phone`, `email`,
                    `latitude`, `longitude`, `timezone`, `image_url`, `status`
             FROM `facilities`
             WHERE `organization_id` = ? AND `status` = 'active'
             ORDER BY `name` ASC",
            [$orgId]
        );

        return $this->success($facilities);
    }

    /**
     * GET /api/public/facilities/{slug}
     * Single facility detail by slug.
     */
    public function facility(Request $request, string $slug): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $facility = $this->db->fetch(
            "SELECT `id`, `name`, `slug`, `tagline`, `description`, `address_line1`, `address_line2`,
                    `city`, `state`, `zip_code`, `country`, `phone`, `email`,
                    `latitude`, `longitude`, `timezone`, `image_url`, `status`
             FROM `facilities`
             WHERE `organization_id` = ? AND `slug` = ? AND `status` = 'active'",
            [$orgId, $slug]
        );

        if (!$facility) {
            return $this->error('Facility not found', 404);
        }

        // Load courts
        $facility['courts'] = $this->db->fetchAll(
            "SELECT `id`, `name`, `sport_type`, `is_indoor`, `is_lighted`, `court_number`, `max_players`
             FROM `courts` WHERE `facility_id` = ? AND `status` = 'active' ORDER BY `court_number` ASC",
            [$facility['id']]
        );

        return $this->success($facility);
    }

    /**
     * GET /api/public/categories
     * Session categories for the organization (only active ones).
     */
    public function categories(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $categories = $this->db->fetchAll(
            "SELECT `id`, `name`, `color`, `sort_order`, `is_system`, `system_slug`, `is_active`, `description`, `image_url`
             FROM `categories`
             WHERE `organization_id` = ? AND `is_active` = 1
             ORDER BY `sort_order` ASC, `name` ASC",
            [$orgId]
        );

        return $this->success($categories);
    }

    /**
     * GET /api/public/court-category
     * Returns the Book a Court system category for this org (name, description, image, active status).
     * The book-court page uses this to display the admin-customized title/description.
     */
    public function courtCategory(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $category = $this->db->fetch(
            "SELECT `id`, `name`, `color`, `description`, `image_url`, `is_active`
             FROM `categories`
             WHERE `organization_id` = ? AND `system_slug` = 'book-a-court'",
            [$orgId]
        );

        if (!$category) {
            return $this->success([
                'name' => 'Book a Court',
                'description' => 'Reserve a court for your group.',
                'color' => '#d4af37',
                'image_url' => null,
                'is_active' => true,
            ]);
        }

        $category['is_active'] = (bool) $category['is_active'];
        return $this->success($category);
    }

    /**
     * GET /api/public/sessions
     * Session types listing (what programs/classes are offered).
     * Optional: ?facility_id=X&category_id=X
     */
    public function sessions(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $facilityId = (int) $request->input('facility_id', 0);
        $categoryId = (int) $request->input('category_id', 0);

        // Session details = the named programs (like "Round Robin", "Drill & Train")
        $sql = "SELECT sd.`id`, sd.`session_name`, sd.`session_tagline`, sd.`description`, sd.`picture`,
                       sd.`category_id`, c.`name` as `category_name`, c.`color` as `category_color`
                FROM `sessions` sd
                LEFT JOIN `categories` c ON c.`id` = sd.`category_id`
                WHERE sd.`organization_id` = ?";
        $params = [$orgId];

        if ($facilityId > 0) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM `session_facilities` sf WHERE sf.`session_id` = sd.`id` AND sf.`facility_id` = ?
            )";
            $params[] = $facilityId;
        }

        if ($categoryId > 0) {
            $sql .= " AND sd.`category_id` = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY sd.`session_name` ASC";
        $sessions = $this->db->fetchAll($sql, $params);

        return $this->success($sessions);
    }

    /**
     * GET /api/public/sessions/{id}
     * Session detail with upcoming classes.
     */
    public function sessionDetail(Request $request, int $id): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $session = $this->db->fetch(
            "SELECT sd.*, c.`name` as `category_name`, c.`color` as `category_color`
             FROM `sessions` sd
             LEFT JOIN `categories` c ON c.`id` = sd.`category_id`
             WHERE sd.`id` = ? AND sd.`organization_id` = ?",
            [$id, $orgId]
        );

        if (!$session) {
            return $this->error('Session not found', 404);
        }

        return $this->success($session);
    }

    /**
     * GET /api/public/schedule
     * Upcoming classes/events for a facility. Powers the public calendar.
     * Required: ?facility_id=X
     * Optional: ?start=YYYY-MM-DD&end=YYYY-MM-DD&category_id=X
     */
    public function schedule(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $facilityId = (int) $request->input('facility_id', 0);
        if ($facilityId <= 0) {
            return $this->error('facility_id is required', 422);
        }

        $start = $request->input('start', date('Y-m-d'));
        $end = $request->input('end', date('Y-m-d', strtotime('+14 days')));
        $categoryId = (int) $request->input('category_id', 0);

        $sql = "SELECT cls.`id`, cls.`session_type_id`,
                       cls.`scheduled_at` AS `start_time`,
                       DATE_ADD(cls.`scheduled_at`, INTERVAL st.`duration` MINUTE) AS `end_time`,
                       cls.`slots` AS `max_participants`,
                       st.`title` AS `session_type_name`, st.`standard_price` AS `price`,
                       sd.`description`,
                       cat.`name` as `category_name`, cat.`color` as `category_color`,
                       sd.`session_name`, sd.`picture`,
                       (SELECT COUNT(*) FROM `st_class_attendees` a
                        WHERE a.`class_id` = cls.`id` AND a.`status` IN ('registered','reserved')) as `booked_count`
                FROM `st_classes` cls
                JOIN `session_types` st ON st.`id` = cls.`session_type_id`
                LEFT JOIN `categories` cat ON cat.`id` = st.`category_id`
                LEFT JOIN `sessions` sd ON sd.`id` = st.`session_id`
                WHERE st.`organization_id` = ?
                  AND st.`facility_id` = ?
                  AND cls.`scheduled_at` >= ?
                  AND cls.`scheduled_at` <= ?
                  AND cls.`is_active` = 1
                  AND st.`private` = 0";
        $params = [$orgId, $facilityId, $start . ' 00:00:00', $end . ' 23:59:59'];

        if ($categoryId > 0) {
            $sql .= " AND st.`category_id` = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY cls.`scheduled_at` ASC";
        $classes = $this->db->fetchAll($sql, $params);

        // Add availability
        foreach ($classes as &$cls) {
            $cls['spots_left'] = max(0, (int) $cls['max_participants'] - (int) $cls['booked_count']);
            $cls['is_full'] = $cls['spots_left'] <= 0;
        }

        return $this->success($classes);
    }

    /**
     * GET /api/public/classes/{id}
     * Single class detail with pricing, courts, and availability.
     */
    public function classDetail(Request $request, int $id): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $class = $this->db->fetch(
            "SELECT cls.*,
                    cls.`scheduled_at` AS `start_time`,
                    DATE_ADD(cls.`scheduled_at`, INTERVAL st.`duration` MINUTE) AS `end_time`,
                    cls.`slots` AS `max_participants`,
                    st.`title` AS `session_type_name`, st.`standard_price` AS `price`,
                    sd.`description`,
                    st.`facility_id`, st.`category_id`, st.`duration`,
                    cat.`name` as `category_name`, cat.`color` as `category_color`,
                    sd.`session_name`, sd.`picture`,
                    f.`name` as `facility_name`, f.`slug` as `facility_slug`,
                    (SELECT COUNT(*) FROM `st_class_attendees` a
                     WHERE a.`class_id` = cls.`id` AND a.`status` IN ('registered','reserved')) as `booked_count`
             FROM `st_classes` cls
             JOIN `session_types` st ON st.`id` = cls.`session_type_id`
             LEFT JOIN `categories` cat ON cat.`id` = st.`category_id`
             LEFT JOIN `sessions` sd ON sd.`id` = st.`session_id`
             LEFT JOIN `facilities` f ON f.`id` = st.`facility_id`
             WHERE cls.`id` = ? AND st.`organization_id` = ? AND st.`private` = 0",
            [$id, $orgId]
        );

        if (!$class) {
            return $this->error('Class not found', 404);
        }

        $class['spots_left'] = max(0, (int) $class['max_participants'] - (int) $class['booked_count']);
        $class['is_full'] = $class['spots_left'] <= 0;

        // Courts
        $class['courts'] = $this->db->fetchAll(
            "SELECT c.`name`, c.`sport_type`, c.`is_indoor`
             FROM `st_class_courts` cc
             JOIN `courts` c ON c.`id` = cc.`court_id`
             WHERE cc.`class_id` = ?",
            [$id]
        );

        // Rolling prices
        $class['rolling_prices'] = $this->db->fetchAll(
            "SELECT `weeks`, `total_price`, `per_session_price`, `savings_label`
             FROM `st_rolling_prices`
             WHERE `session_type_id` = ? ORDER BY `weeks` ASC",
            [$class['session_type_id']]
        );

        // Hot deal
        $class['hot_deal'] = $this->db->fetch(
            "SELECT `deal_price`, `original_price`, `label`, `expires_at`
             FROM `st_hot_deals`
             WHERE `class_id` = ? AND `is_active` = 1 AND (`expires_at` IS NULL OR `expires_at` > NOW())",
            [$id]
        );

        // Early bird
        $class['early_bird'] = $this->db->fetch(
            "SELECT `discounted_price`, `original_price`, `cutoff_date`, `label`
             FROM `st_early_birds`
             WHERE `class_id` = ? AND `is_active` = 1 AND `cutoff_date` > NOW()",
            [$id]
        );

        return $this->success($class);
    }

    /* ── helpers ── */

    private function requireOrg(Request $request): int|Response
    {
        $orgId = $request->organizationId();
        if (!$orgId) {
            return $this->error('Organization context required', 400);
        }
        return $orgId;
    }

    /**
     * GET /api/public/courts/availability
     * Returns courts and available time slots for a facility on a given date.
     * Required: ?facility_id=X&date=YYYY-MM-DD
     * Optional: ?sport_type=pickleball
     */
    public function courtAvailability(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $facilityId = (int) $request->input('facility_id', 0);
        if ($facilityId <= 0) {
            return $this->error('facility_id is required', 422);
        }

        $date = $request->input('date', date('Y-m-d'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->error('Invalid date format. Use YYYY-MM-DD', 422);
        }

        $sportType = $request->input('sport_type', '');

        // Load facility with settings (operating hours)
        $facility = $this->db->fetch(
            "SELECT `id`, `name`, `settings` FROM `facilities`
             WHERE `id` = ? AND `organization_id` = ? AND `status` = 'active'",
            [$facilityId, $orgId]
        );
        if (!$facility) {
            return $this->error('Facility not found', 404);
        }

        $settings = json_decode($facility['settings'] ?? '{}', true) ?: [];
        $operatingHours = $settings['operating_hours'] ?? [];

        // Determine day-of-week operating hours
        $dayName = strtolower(date('D', strtotime($date))); // mon, tue, wed...
        $dayHours = $operatingHours[$dayName] ?? '07:00-21:00'; // fallback
        if ($dayHours === 'closed') {
            return $this->success(['facility' => $facility['name'], 'date' => $date, 'courts' => [], 'closed' => true]);
        }

        [$openTime, $closeTime] = explode('-', $dayHours);

        // Load courts
        $courtSql = "SELECT `id`, `name`, `sport_type`, `is_indoor`, `is_lighted`, `court_number`,
                            `hourly_rate`, `max_players`, `surface_type`
                     FROM `courts`
                     WHERE `facility_id` = ? AND `status` = 'active'";
        $courtParams = [$facilityId];
        if ($sportType !== '') {
            $courtSql .= " AND `sport_type` = ?";
            $courtParams[] = $sportType;
        }
        $courtSql .= " ORDER BY `court_number` ASC";
        $courts = $this->db->fetchAll($courtSql, $courtParams);

        if (empty($courts)) {
            return $this->success(['facility' => $facility['name'], 'date' => $date, 'courts' => []]);
        }

        $courtIds = array_column($courts, 'id');
        $placeholders = implode(',', array_fill(0, count($courtIds), '?'));

        // Load existing court bookings for the date
        $bookings = $this->db->fetchAll(
            "SELECT `court_id`, `start_time`, `end_time`
             FROM `court_bookings`
             WHERE `court_id` IN ({$placeholders})
               AND `booking_date` = ?
               AND `status` IN ('confirmed','completed')
             ORDER BY `start_time` ASC",
            [...$courtIds, $date]
        );

        // Load class assignments for the date (classes that occupy courts)
        $classAssignments = $this->db->fetchAll(
            "SELECT cc.`court_id`,
                    TIME(cls.`scheduled_at`) as `start_time`,
                    TIME(DATE_ADD(cls.`scheduled_at`, INTERVAL COALESCE(st.`duration`, 60) MINUTE)) as `end_time`
             FROM `st_class_courts` cc
             JOIN `st_classes` cls ON cls.`id` = cc.`class_id`
             LEFT JOIN `session_types` st ON st.`id` = cls.`session_type_id`
             WHERE cc.`court_id` IN ({$placeholders})
               AND DATE(cls.`scheduled_at`) = ?
               AND cls.`is_active` = 1",
            [...$courtIds, $date]
        );

        // Build occupied-slots map per court
        $occupied = [];
        foreach ($bookings as $b) {
            $occupied[$b['court_id']][] = ['start' => $b['start_time'], 'end' => $b['end_time']];
        }
        foreach ($classAssignments as $ca) {
            $occupied[$ca['court_id']][] = ['start' => $ca['start_time'], 'end' => $ca['end_time']];
        }

        // Generate 30-minute time slots for each court
        $slotMinutes = 30;
        foreach ($courts as &$court) {
            $slots = [];
            $current = strtotime($date . ' ' . $openTime);
            $close = strtotime($date . ' ' . $closeTime);

            while ($current < $close) {
                $slotStart = date('H:i:s', $current);
                $slotEnd = date('H:i:s', $current + $slotMinutes * 60);

                $available = true;
                if (isset($occupied[$court['id']])) {
                    foreach ($occupied[$court['id']] as $block) {
                        // Overlap check: slot overlaps if slotStart < blockEnd AND slotEnd > blockStart
                        if ($slotStart < $block['end'] && $slotEnd > $block['start']) {
                            $available = false;
                            break;
                        }
                    }
                }

                // Don't show past time slots for today
                if ($date === date('Y-m-d') && $current < time()) {
                    $available = false;
                }

                $slots[] = [
                    'start' => substr($slotStart, 0, 5),
                    'end'   => substr($slotEnd, 0, 5),
                    'available' => $available,
                ];

                $current += $slotMinutes * 60;
            }

            $court['slots'] = $slots;
        }
        unset($court);

        return $this->success([
            'facility' => $facility['name'],
            'date' => $date,
            'operating_hours' => $dayHours,
            'courts' => $courts,
        ]);
    }

    /**
     * POST /api/public/courts/book
     * Book a court for a specific time slot.
     * Body: { facility_id, court_id, date, start_time, end_time, first_name, last_name, email, phone?, num_players? }
     */
    public function bookCourt(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $facilityId = (int) $request->input('facility_id', 0);
        $courtId    = (int) $request->input('court_id', 0);
        $date       = trim((string) $request->input('date', ''));
        $startTime  = trim((string) $request->input('start_time', ''));
        $endTime    = trim((string) $request->input('end_time', ''));
        $firstName  = trim((string) $request->input('first_name', ''));
        $lastName   = trim((string) $request->input('last_name', ''));
        $email      = trim((string) $request->input('email', ''));
        $phone      = trim((string) $request->input('phone', ''));
        $numPlayers = max(1, (int) $request->input('num_players', 1));

        // Validation
        $errors = [];
        if ($facilityId <= 0) $errors[] = 'facility_id is required';
        if ($courtId <= 0) $errors[] = 'court_id is required';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $errors[] = 'Valid date (YYYY-MM-DD) is required';
        if (!preg_match('/^\d{2}:\d{2}$/', $startTime)) $errors[] = 'Valid start_time (HH:MM) is required';
        if (!preg_match('/^\d{2}:\d{2}$/', $endTime)) $errors[] = 'Valid end_time (HH:MM) is required';
        if ($firstName === '') $errors[] = 'first_name is required';
        if ($lastName === '') $errors[] = 'last_name is required';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';

        if (!empty($errors)) {
            return $this->error(implode(', ', $errors), 422);
        }

        // Must be today or future
        if ($date < date('Y-m-d')) {
            return $this->error('Cannot book in the past', 422);
        }

        // Validate court belongs to facility and org
        $court = $this->db->fetch(
            "SELECT c.`id`, c.`hourly_rate`, c.`max_players`, c.`name`
             FROM `courts` c
             JOIN `facilities` f ON f.`id` = c.`facility_id`
             WHERE c.`id` = ? AND c.`facility_id` = ? AND f.`organization_id` = ?
               AND c.`status` = 'active' AND f.`status` = 'active'",
            [$courtId, $facilityId, $orgId]
        );
        if (!$court) {
            return $this->error('Court not found', 404);
        }

        if ($numPlayers > (int) $court['max_players']) {
            return $this->error("Maximum {$court['max_players']} players allowed for this court", 422);
        }

        // Calculate duration and price
        $startTs = strtotime($date . ' ' . $startTime . ':00');
        $endTs   = strtotime($date . ' ' . $endTime . ':00');
        if ($endTs <= $startTs) {
            return $this->error('end_time must be after start_time', 422);
        }
        $durationMinutes = ($endTs - $startTs) / 60;
        if ($durationMinutes < 30 || $durationMinutes > 240) {
            return $this->error('Duration must be between 30 minutes and 4 hours', 422);
        }

        $hourlyRate = (float) $court['hourly_rate'];
        $totalPrice = round($hourlyRate * ($durationMinutes / 60), 2);

        $startTimeFull = $startTime . ':00';
        $endTimeFull = $endTime . ':00';

        // Check for overlapping bookings
        $overlap = $this->db->fetch(
            "SELECT `id` FROM `court_bookings`
             WHERE `court_id` = ? AND `booking_date` = ?
               AND `start_time` < ? AND `end_time` > ?
               AND `status` IN ('confirmed','completed')",
            [$courtId, $date, $endTimeFull, $startTimeFull]
        );
        if ($overlap) {
            return $this->error('This time slot is no longer available', 409);
        }

        // Check for overlapping class assignments
        $classOverlap = $this->db->fetch(
            "SELECT cc.`id`
             FROM `st_class_courts` cc
             JOIN `st_classes` cls ON cls.`id` = cc.`class_id`
             LEFT JOIN `session_types` st ON st.`id` = cls.`session_type_id`
             WHERE cc.`court_id` = ? AND DATE(cls.`scheduled_at`) = ?
               AND TIME(cls.`scheduled_at`) < ?
               AND TIME(DATE_ADD(cls.`scheduled_at`, INTERVAL COALESCE(st.`duration`, 60) MINUTE)) > ?
               AND cls.`is_active` = 1",
            [$courtId, $date, $endTimeFull, $startTimeFull]
        );
        if ($classOverlap) {
            return $this->error('This court is reserved for a class during that time', 409);
        }

        // Insert booking
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $this->db->execute(
            "INSERT INTO `court_bookings`
             (`uuid`, `organization_id`, `facility_id`, `court_id`, `player_id`,
              `booking_date`, `start_time`, `end_time`, `duration_minutes`, `num_players`,
              `first_name`, `last_name`, `email`, `phone`, `total_price`, `status`)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')",
            [
                $uuid, $orgId, $facilityId, $courtId, null,
                $date, $startTimeFull, $endTimeFull, $durationMinutes, $numPlayers,
                $firstName, $lastName, $email, $phone, $totalPrice,
            ]
        );

        return $this->success([
            'booking_id'  => $uuid,
            'court_name'  => $court['name'],
            'date'        => $date,
            'start_time'  => $startTime,
            'end_time'    => $endTime,
            'duration'    => $durationMinutes,
            'num_players' => $numPlayers,
            'total_price' => $totalPrice,
            'status'      => 'confirmed',
        ], 201);
    }

    /* ────────────────────────────────────────────
     * NEW PUBLIC ENDPOINTS (035+)
     * ──────────────────────────────────────────── */

    /**
     * GET /api/public/navigation
     * Returns the visible navigation tree for this organization.
     * Evaluates visibility rules (has_memberships, auth_only, etc).
     * Also returns categories that have type=category nav items (for Schedule dropdown children).
     */
    public function navigation(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $items = $this->db->fetchAll(
            "SELECT `id`, `parent_id`, `label`, `url`, `type`, `target`, `icon`,
                    `category_id`, `is_system`, `system_key`, `is_visible`, `sort_order`,
                    `visibility_rule`
             FROM `navigation_items`
             WHERE `organization_id` = ? AND `is_visible` = 1
             ORDER BY `sort_order` ASC, `label` ASC",
            [$orgId]
        );

        // Check membership visibility
        $hasMemberships = (int) ($this->db->fetch(
            "SELECT COUNT(*) as cnt FROM `membership_plans` WHERE `organization_id` = ? AND `is_active` = 1",
            [$orgId]
        )['cnt'] ?? 0) > 0;

        // Filter by visibility rules
        $filtered = [];
        foreach ($items as $item) {
            $rule = $item['visibility_rule'];
            if ($rule === 'has_memberships' && !$hasMemberships) {
                continue;
            }
            $filtered[] = $item;
        }

        // Build tree (top-level + children)
        $tree = [];
        $childMap = [];
        foreach ($filtered as $item) {
            if ($item['parent_id']) {
                $childMap[$item['parent_id']][] = $item;
            } else {
                $tree[] = $item;
            }
        }
        foreach ($tree as &$node) {
            $node['children'] = $childMap[$node['id']] ?? [];
            // Schedule shows as a simple link to /schedule ("All Schedule")
            if ($node['system_key'] === 'schedule') {
                $node['type'] = 'link';
                $node['children'] = [];
            }
        }
        unset($node);

        // Collect extra nav items (pages, forms) into a "More" dropdown
        $moreChildren = [];

        // Append custom forms flagged for nav
        $navForms = $this->db->fetchAll(
            "SELECT `id`, `title`, `slug` FROM `custom_forms`
             WHERE `organization_id` = ? AND `status` = 'active' AND `show_in_nav` = 1
             ORDER BY `title` ASC",
            [$orgId]
        );
        foreach ($navForms as $f) {
            $moreChildren[] = [
                'id' => 'form-' . $f['id'],
                'label' => $f['title'],
                'url' => '/forms/' . $f['slug'],
                'type' => 'form',
            ];
        }

        // Only add the "More" dropdown if there are children
        if (!empty($moreChildren)) {
            $tree[] = [
                'id' => 'more-dropdown',
                'label' => 'More',
                'url' => '#',
                'type' => 'dropdown',
                'children' => $moreChildren,
            ];
        }

        return $this->success([
            'items' => $tree,
            'has_memberships' => $hasMemberships,
        ]);
    }

    /**
     * GET /api/public/membership-plans
     * Returns active membership plans for the org (optionally filtered by facility_id).
     */
    public function membershipPlans(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $facilityId = (int) $request->input('facility_id', 0);

        $sql = "SELECT mp.`id`, mp.`uuid`, mp.`name`, mp.`description`,
                       mp.`duration_type`, mp.`duration_value`, mp.`price`, mp.`setup_fee`,
                       mp.`renewal_type`, mp.`color`, mp.`is_taxable`, mp.`max_members`,
                       f.`name` AS `facility_name`, f.`slug` AS `facility_slug`
                FROM `membership_plans` mp
                JOIN `facilities` f ON f.`id` = mp.`facility_id`
                WHERE mp.`organization_id` = ? AND mp.`is_active` = 1 AND f.`status` = 'active'";
        $params = [$orgId];

        if ($facilityId > 0) {
            $sql .= " AND mp.`facility_id` = ?";
            $params[] = $facilityId;
        }

        $sql .= " ORDER BY mp.`sort_order` ASC, mp.`price` ASC";
        $plans = $this->db->fetchAll($sql, $params);

        // Load benefits for each plan
        foreach ($plans as &$plan) {
            $plan['category_benefits'] = $this->db->fetchAll(
                "SELECT mpc.*, c.`name` AS category_name, c.`color` AS category_color
                 FROM `membership_plan_categories` mpc
                 JOIN `categories` c ON c.`id` = mpc.`category_id`
                 WHERE mpc.`membership_plan_id` = ?",
                [$plan['id']]
            );
            $plan['session_type_benefits'] = $this->db->fetchAll(
                "SELECT mpst.*, st.`title` AS session_type_title
                 FROM `membership_plan_session_types` mpst
                 JOIN `session_types` st ON st.`id` = mpst.`session_type_id`
                 WHERE mpst.`membership_plan_id` = ?",
                [$plan['id']]
            );
            // Active member count
            $cnt = $this->db->fetch(
                "SELECT COUNT(*) as cnt FROM `player_memberships`
                 WHERE `membership_plan_id` = ? AND `status` = 'active'",
                [$plan['id']]
            );
            $plan['active_members'] = (int) ($cnt['cnt'] ?? 0);
            $plan['spots_left'] = $plan['max_members']
                ? max(0, (int) $plan['max_members'] - $plan['active_members'])
                : null;
        }
        unset($plan);

        return $this->success($plans);
    }

    /**
     * GET /api/public/theme
     * Returns the organization's theme settings (colors, fonts, layout prefs).
     */
    public function theme(Request $request): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $rows = $this->db->fetchAll(
            "SELECT `key_name`, `value`, `type` FROM `settings`
             WHERE `organization_id` = ? AND `group_name` = 'theme'",
            [$orgId]
        );

        $theme = [];
        foreach ($rows as $row) {
            $val = $row['value'];
            if ($row['type'] === 'boolean') $val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
            elseif ($row['type'] === 'integer') $val = (int) $val;
            elseif ($row['type'] === 'json') $val = json_decode($val, true);
            $theme[$row['key_name']] = $val;
        }

        // Provide defaults
        $defaults = [
            'primary_color'     => '#d4af37',
            'accent_color'      => '#4a7ec4',
            'background_color'  => '#060d1a',
            'text_color'        => '#f8fafc',
            'font_display'      => 'Plus Jakarta Sans',
            'font_body'         => 'Inter',
            'nav_style'         => 'glass',
            'footer_style'      => 'standard',
            'hero_overlay'      => true,
            'card_style'        => 'glass',
        ];

        $merged = array_merge($defaults, $theme);
        return $this->success($merged);
    }

    /**
     * GET /api/public/category/{slug}
     * Returns a single category with its view settings (for dynamic category pages).
     */
    public function categoryBySlug(Request $request, string $slug): Response
    {
        $orgId = $this->requireOrg($request);
        if ($orgId instanceof Response) return $orgId;

        $category = $this->db->fetch(
            "SELECT c.`id`, c.`name`, c.`slug`, c.`color`, c.`description`, c.`image_url`, c.`is_active`
             FROM `categories` c
             WHERE c.`organization_id` = ? AND (c.`slug` = ? OR c.`id` = ?) AND c.`is_active` = 1",
            [$orgId, $slug, is_numeric($slug) ? (int) $slug : 0]
        );

        if (!$category) {
            return $this->error('Category not found', 404);
        }

        // Load view settings
        $viewSettings = $this->db->fetch(
            "SELECT * FROM `category_view_settings` WHERE `category_id` = ?",
            [$category['id']]
        );

        $category['view_settings'] = $viewSettings ?: [
            'default_view'          => 'week',
            'enabled_views'         => ['week', 'month', 'today', 'list'],
            'show_filters'          => true,
            'show_category_filter'  => false,
            'page_title'            => null,
            'page_description'      => null,
            'page_hero_image'       => null,
        ];

        if (is_string($category['view_settings']['enabled_views'])) {
            $category['view_settings']['enabled_views'] = json_decode($category['view_settings']['enabled_views'], true) ?: ['week', 'month', 'today', 'list'];
        }

        return $this->success($category);
    }
}

