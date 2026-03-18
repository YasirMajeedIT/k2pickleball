<?php

declare(strict_types=1);

namespace App\Modules\Calendar;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;

/**
 * Cross-session-type calendar API.
 * Returns FullCalendar-compatible events for all classes across session types.
 */
final class CalendarController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * GET /api/calendar?facility_id=&start=&end=&category_id=
     * Returns events in FullCalendar format.
     */
    public function index(Request $request): Response
    {
        $facilityId = (int) $request->input('facility_id', 0);
        $start      = $request->input('start', '');
        $end        = $request->input('end', '');
        $categoryId = $request->input('category_id', '');

        if (!$facilityId || !$start || !$end) {
            return $this->success([]);
        }

        $params = [$facilityId, $start, $end];
        $categoryFilter = '';
        if ($categoryId && is_numeric($categoryId)) {
            $categoryFilter = 'AND st.category_id = ?';
            $params[] = (int) $categoryId;
        }

        $sql = "
            SELECT
                c.id AS class_id,
                c.uuid AS class_uuid,
                c.session_type_id,
                c.scheduled_at,
                c.slots,
                c.slots_available,
                c.coach_id,
                c.booking_status,
                c.is_active,
                c.facility_id,
                st.title AS session_title,
                st.session_type,
                st.duration,
                st.standard_price,
                st.capacity,
                st.private AS is_private,
                cat.id AS category_id,
                cat.name AS category_name,
                cat.color AS category_color,
                cat.is_taxable AS is_taxable,
                f.tax_rate AS facility_tax_rate,
                u.first_name AS coach_first_name,
                u.last_name AS coach_last_name,
                hd.id AS hot_deal_id,
                hd.discount_price AS hot_deal_price,
                hd.is_active AS hot_deal_active,
                hd.min_registrations AS hot_deal_min_reg,
                hd.label AS hot_deal_label,
                eb.id AS early_bird_id,
                eb.discount_price AS early_bird_price,
                eb.is_active AS early_bird_active,
                st.internal_title,
                st.pricing_mode,
                (SELECT COUNT(*) FROM st_class_notes cn WHERE cn.class_id = c.id) AS notes_count,
                (SELECT SUBSTRING(cn2.note, 1, 120) FROM st_class_notes cn2 WHERE cn2.class_id = c.id ORDER BY cn2.created_at DESC LIMIT 1) AS first_note_text,
                (SELECT COUNT(*) FROM st_class_courts cc WHERE cc.class_id = c.id) AS courts_count,
                (SELECT COUNT(*) FROM st_class_attendees ca WHERE ca.class_id = c.id AND ca.status = 'registered') AS attendees_count,
                (SELECT GROUP_CONCAT(ct.name ORDER BY ct.court_number SEPARATOR ', ')
                 FROM st_class_courts cc2 JOIN courts ct ON ct.id = cc2.court_id WHERE cc2.class_id = c.id) AS court_names
            FROM st_classes c
            JOIN session_types st ON st.id = c.session_type_id
            LEFT JOIN categories cat ON cat.id = st.category_id
            LEFT JOIN facilities f ON f.id = c.facility_id
            LEFT JOIN users u ON u.id = c.coach_id
            LEFT JOIN st_hot_deals hd ON hd.class_id = c.id
            LEFT JOIN st_early_birds eb ON eb.class_id = c.id
            WHERE c.facility_id = ?
              AND c.scheduled_at >= ?
              AND c.scheduled_at < ?
              AND c.is_active = 1
              {$categoryFilter}
            ORDER BY c.scheduled_at ASC
        ";

        $rows = $this->db->fetchAll($sql, $params);

        // Pre-fetch rolling prices and series session numbers for all session types in result
        $sessionTypeIds = array_unique(array_column($rows, 'session_type_id'));
        $rollingPricesMap = [];
        $seriesSessionMap = []; // class_id => session number (e.g. "3 of 8")
        if (!empty($sessionTypeIds)) {
            $inPlaceholders = implode(',', array_fill(0, count($sessionTypeIds), '?'));
            // Rolling prices
            $rp = $this->db->fetchAll(
                "SELECT * FROM st_rolling_prices WHERE session_type_id IN ({$inPlaceholders}) ORDER BY number_of_weeks ASC",
                array_values($sessionTypeIds)
            );
            foreach ($rp as $r) {
                $rollingPricesMap[(int) $r['session_type_id']][] = [
                    'weeks' => (int) $r['number_of_weeks'],
                    'price' => (float) $r['price'],
                ];
            }
            // Series session numbering: for series type, rank classes by scheduled_at within each session_type_id
            $seriesRows = $this->db->fetchAll(
                "SELECT id, session_type_id,
                        ROW_NUMBER() OVER (PARTITION BY session_type_id ORDER BY scheduled_at ASC) AS session_number,
                        COUNT(*) OVER (PARTITION BY session_type_id) AS total_sessions
                 FROM st_classes
                 WHERE session_type_id IN ({$inPlaceholders}) AND is_active = 1
                 ORDER BY session_type_id, scheduled_at",
                array_values($sessionTypeIds)
            );
            foreach ($seriesRows as $sr) {
                $seriesSessionMap[(int) $sr['id']] = [
                    'number' => (int) $sr['session_number'],
                    'total'  => (int) $sr['total_sessions'],
                ];
            }
        }

        $events = [];
        foreach ($rows as $row) {
            $startDt = $row['scheduled_at'];
            $duration = (int) ($row['duration'] ?? 60);
            $endDt = date('Y-m-d H:i:s', strtotime($startDt) + ($duration * 60));

            $color = $row['category_color'] ?: '#6366f1';
            $booked = (int) $row['slots'] - (int) $row['slots_available'];
            $total  = (int) $row['slots'];

            // Hot deal: check registration-based threshold
            $hotDealActive = $row['hot_deal_id'] && $row['hot_deal_active'];
            if ($hotDealActive && !empty($row['hot_deal_min_reg'])) {
                $hotDealActive = $booked >= (int) $row['hot_deal_min_reg'];
            }

            // Price: for series_rolling use rolling prices range, otherwise standard
            $price = (float) $row['standard_price'];
            $stId = (int) $row['session_type_id'];
            $rollingPrices = $rollingPricesMap[$stId] ?? [];
            $priceRange = null;
            if ($row['session_type'] === 'series_rolling' && !empty($rollingPrices)) {
                $minP = min(array_column($rollingPrices, 'price'));
                $maxP = max(array_column($rollingPrices, 'price'));
                $priceRange = ['min' => $minP, 'max' => $maxP];
            }

            // Series session info
            $sessionInfo = $seriesSessionMap[(int) $row['class_id']] ?? null;

            // Formatted time strings
            $startTime = date('g:i a', strtotime($startDt));
            $endTime   = date('g:i a', strtotime($endDt));

            $events[] = [
                'id'              => (int) $row['class_id'],
                'title'           => $row['session_title'],
                'start'           => $startDt,
                'end'             => $endDt,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'classId'         => (int) $row['class_id'],
                    'classUuid'       => $row['class_uuid'],
                    'sessionTypeId'   => (int) $row['session_type_id'],
                    'sessionType'     => $row['session_type'],
                    'internalTitle'   => $row['internal_title'] ?? '',
                    'pricingMode'     => $row['pricing_mode'] ?? 'single',
                    'duration'        => $duration,
                    'price'           => (float) $row['standard_price'],
                    'capacity'        => $total,
                    'booked'          => $booked,
                    'slotsAvailable'  => (int) $row['slots_available'],
                    'bookingStatus'   => (int) $row['booking_status'],
                    'isPrivate'       => (int) $row['is_private'],
                    'coachId'         => $row['coach_id'] ? (int) $row['coach_id'] : null,
                    'coachName'       => $row['coach_first_name']
                        ? trim($row['coach_first_name'] . ' ' . ($row['coach_last_name'] ?? ''))
                        : null,
                    'categoryId'      => $row['category_id'] ? (int) $row['category_id'] : null,
                    'categoryName'    => $row['category_name'] ?? 'Uncategorized',
                    'categoryColor'   => $color,
                    'startTime'       => $startTime,
                    'endTime'         => $endTime,
                    'courtNames'      => $row['court_names'] ?? '',
                    'hotDeal'         => $hotDealActive ? [
                        'id'    => (int) $row['hot_deal_id'],
                        'price' => (float) $row['hot_deal_price'],
                        'label' => $row['hot_deal_label'] ?? 'Hot Deal',
                        'minReg' => $row['hot_deal_min_reg'] ? (int) $row['hot_deal_min_reg'] : null,
                    ] : null,
                    'earlyBird'       => $row['early_bird_id'] && $row['early_bird_active'] ? [
                        'id'    => (int) $row['early_bird_id'],
                        'price' => (float) $row['early_bird_price'],
                    ] : null,
                    'rollingPrices'   => $rollingPrices ?: null,
                    'priceRange'      => $priceRange,
                    'sessionNumber'   => $sessionInfo ? $sessionInfo['number'] : null,
                    'totalSessions'   => $sessionInfo ? $sessionInfo['total'] : null,
                    'notesCount'      => (int) $row['notes_count'],
                    'firstNoteText'   => $row['first_note_text'] ?? '',
                    'courtsCount'     => (int) $row['courts_count'],
                    'attendeesCount'  => (int) $row['attendees_count'],
                    'isTaxable'       => (bool) ($row['is_taxable'] ?? false),
                    'taxRate'         => (float) ($row['facility_tax_rate'] ?? 0),
                ],
            ];
        }

        return $this->success($events);
    }

    /**
     * GET /api/calendar/categories?facility_id=
     * Returns distinct categories for classes in the given facility.
     */
    public function categories(Request $request): Response
    {
        $facilityId = (int) $request->input('facility_id', 0);
        if (!$facilityId) {
            return $this->success([]);
        }

        $cats = $this->db->fetchAll("
            SELECT DISTINCT cat.id, cat.name, cat.color
            FROM st_classes c
            JOIN session_types st ON st.id = c.session_type_id
            JOIN categories cat ON cat.id = st.category_id
            WHERE c.facility_id = ? AND c.is_active = 1
            ORDER BY cat.name ASC
        ", [$facilityId]);

        return $this->success($cats);
    }
}
