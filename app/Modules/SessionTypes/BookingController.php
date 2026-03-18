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
use App\Core\Services\EmailNotificationService;
use App\Modules\AuditLogs\AuditLogRepository;
use App\Modules\Payments\SquarePaymentService;

/**
 * Handles booking, cancellation, refunds, and credit issuance for class attendees.
 * Integrates with Square payments, discounts, credit codes, gift certificates,
 * email notifications (facility SMTP with env fallback), and audit trail.
 */
final class BookingController extends Controller
{
    private Connection $db;
    private EmailNotificationService $emailService;
    private AuditLogRepository $auditLog;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
        $this->emailService = new EmailNotificationService($db);
        $this->auditLog = new AuditLogRepository($db);
    }

    /**
     * POST /{id}/classes/{classId}/book
     * Full booking with payment processing.
     */
    public function book(Request $request, int $id, int $classId): Response
    {
        $class = $this->ensureClassExists($id, $classId);
        $orgId = $request->organizationId();

        $data = Validator::validate($request->all(), [
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'nullable|string|max:100',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:30',
            'player_id'           => 'nullable|integer',
            'status'              => 'nullable|string',
            'notes'               => 'nullable|string',
            'payment_method'      => 'nullable|string',
            'source_id'           => 'nullable|string',
            'amount_paid'         => 'nullable|numeric',
            'quote_amount'        => 'nullable|numeric',
            'discount_code'       => 'nullable|string|max:50',
            'discount_amount'     => 'nullable|numeric',
            'credit_code'         => 'nullable|string|max:50',
            'credit_amount'       => 'nullable|numeric',
            'gift_code'           => 'nullable|string|max:50',
            'gift_amount'         => 'nullable|numeric',
            'tax_amount'          => 'nullable|numeric',
            'tax_rate'            => 'nullable|numeric',
            'send_email'          => 'nullable|boolean',
            'rolling_package_weeks' => 'nullable|integer',
            'terminal_payment_id' => 'nullable|string',
        ]);

        $paymentMethod = $data['payment_method'] ?? 'manual';
        $status = in_array($data['status'] ?? '', ['registered', 'waitlisted', 'reserved']) ? $data['status'] : 'registered';
        $quoteAmount = isset($data['quote_amount']) ? round((float) $data['quote_amount'], 2) : 0;
        $discountAmount = round((float) ($data['discount_amount'] ?? 0), 2);
        $creditAmount = round((float) ($data['credit_amount'] ?? 0), 2);
        $giftAmount = round((float) ($data['gift_amount'] ?? 0), 2);
        $taxAmount = round((float) ($data['tax_amount'] ?? 0), 2);
        $taxRate = round((float) ($data['tax_rate'] ?? 0), 2);
        $finalAmount = max(0, $quoteAmount - $discountAmount - $creditAmount - $giftAmount) + $taxAmount;

        $uuid = $this->generateUuid();
        $playerId = !empty($data['player_id']) ? (int) $data['player_id'] : null;
        $facilityId = $class['facility_id'] ?? null;

        // --- Validate and apply credit code ---
        $creditCodeId = null;
        if (!empty($data['credit_code']) && $creditAmount > 0) {
            $creditCode = $this->db->fetch(
                "SELECT * FROM credit_codes WHERE code = ? AND active = 1 AND facility_id = ?",
                [strtoupper(trim($data['credit_code'])), $facilityId]
            );
            if (!$creditCode) {
                return $this->error('Invalid or inactive credit code', 422);
            }
            // Enforce player binding
            if ($creditCode['issued_to'] && $playerId && (int) $creditCode['issued_to'] !== $playerId) {
                return $this->error('This credit code is assigned to a different player', 422);
            }
            if ((float) $creditCode['balance'] < $creditAmount) {
                return $this->error('Credit code balance insufficient (available: $' . number_format((float) $creditCode['balance'], 2) . ')', 422);
            }
            $creditCodeId = (int) $creditCode['id'];
        }

        // --- Validate and apply gift certificate ---
        $giftCertificateId = null;
        if (!empty($data['gift_code']) && $giftAmount > 0) {
            $giftCert = $this->db->fetch(
                "SELECT * FROM gift_certificates WHERE code = ? AND status = 'active' AND organization_id = ?",
                [strtoupper(trim($data['gift_code'])), $orgId]
            );
            if (!$giftCert) {
                return $this->error('Invalid or inactive gift certificate', 422);
            }
            if ((float) $giftCert['value'] < $giftAmount) {
                return $this->error('Gift certificate balance insufficient (available: $' . number_format((float) $giftCert['value'], 2) . ')', 422);
            }
            $giftCertificateId = (int) $giftCert['id'];
        }

        // --- Validate discount code ---
        if (!empty($data['discount_code']) && $discountAmount > 0) {
            $discount = $this->db->fetch(
                "SELECT * FROM st_discount_rules WHERE coupon_code = ? AND is_active = 1 AND organization_id = ?",
                [strtoupper(trim($data['discount_code'])), $orgId]
            );
            if (!$discount) {
                return $this->error('Invalid or inactive discount code', 422);
            }
        }

        // --- Process payment ---
        $squarePaymentId = null;
        $paymentId = null;
        $paymentStatus = 'pending';

        // Terminal payment (Square Terminal POS extension)
        if ($paymentMethod === 'terminal' && $finalAmount > 0) {
            if (!empty($data['terminal_payment_id'])) {
                // Terminal payment already completed (legacy flow)
                $squarePaymentId = $data['terminal_payment_id'];
                $paymentStatus = 'paid';

                $amountCents = (int) round($finalAmount * 100);
                $paymentId = $this->db->insert('payments', [
                    'uuid' => $this->generateUuid(),
                    'organization_id' => $orgId,
                    'user_id' => $request->userId(),
                    'amount' => $amountCents,
                    'currency' => 'USD',
                    'status' => 'completed',
                    'description' => 'Terminal payment - class booking',
                    'square_payment_id' => $squarePaymentId,
                    'idempotency_key' => bin2hex(random_bytes(16)),
                    'metadata' => json_encode(['source' => 'terminal', 'terminal_payment_id' => $squarePaymentId]),
                    'processed_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $this->db->insert('transactions', [
                    'uuid' => $this->generateUuid(),
                    'organization_id' => $orgId,
                    'payment_id' => $paymentId,
                    'type' => 'charge',
                    'amount' => $amountCents,
                    'currency' => 'USD',
                    'description' => 'Terminal payment - class booking',
                    'square_transaction_id' => $squarePaymentId,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                // Book-first flow: booking created with pending payment, terminal checkout happens after
                $paymentStatus = 'pending';
            }
        } elseif ($paymentMethod === 'card' && !empty($data['source_id']) && $finalAmount > 0) {
            try {
                $square = new SquarePaymentService();
                $amountCents = (int) round($finalAmount * 100);
                $result = $square->createPayment($amountCents, 'USD', $data['source_id'], [
                    'reference_id' => 'BK-' . $uuid,
                    'note' => 'Class booking: ' . Sanitizer::string($data['first_name']) . ' ' . Sanitizer::string($data['last_name'] ?? ''),
                ]);

                $squarePaymentId = $result['id'];
                $paymentStatus = 'paid';

                // Store payment record
                $paymentId = $this->db->insert('payments', [
                    'uuid' => $this->generateUuid(),
                    'organization_id' => $orgId,
                    'user_id' => $request->userId(),
                    'amount' => $amountCents,
                    'currency' => 'USD',
                    'status' => 'completed',
                    'description' => 'Class booking',
                    'square_payment_id' => $squarePaymentId,
                    'square_receipt_url' => $result['receipt_url'] ?? null,
                    'idempotency_key' => bin2hex(random_bytes(16)),
                    'metadata' => json_encode($result),
                    'processed_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // Record transaction
                $this->db->insert('transactions', [
                    'uuid' => $this->generateUuid(),
                    'organization_id' => $orgId,
                    'payment_id' => $paymentId,
                    'type' => 'charge',
                    'amount' => $amountCents,
                    'currency' => 'USD',
                    'description' => 'Class booking payment',
                    'square_transaction_id' => $squarePaymentId,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
                return $this->error('Payment failed: ' . $e->getMessage(), 422);
            }
        } elseif ($paymentMethod === 'cash' || $paymentMethod === 'manual') {
            $paymentMethod = 'cash';
            $paymentStatus = ($data['amount_paid'] ?? 0) > 0 ? 'paid' : 'pending';
            $finalAmount = round((float) ($data['amount_paid'] ?? $finalAmount), 2);
        } elseif ($paymentMethod === 'free' || $finalAmount <= 0) {
            $paymentMethod = 'free';
            $paymentStatus = 'free';
            $finalAmount = 0;
        }

        // --- Rolling enrollment path ---
        $rollingWeeks = !empty($data['rolling_package_weeks']) ? (int) $data['rolling_package_weeks'] : 0;
        if ($rollingWeeks > 0) {
            return $this->processRollingEnrollment(
                $request, $id, $classId, $class, $orgId, $data,
                $uuid, $playerId, $facilityId, $status,
                $quoteAmount, $discountAmount, $creditAmount, $giftAmount, $finalAmount,
                $paymentMethod, $paymentStatus, $paymentId, $squarePaymentId,
                $creditCodeId, $creditCode ?? null, $giftCertificateId, $giftCert ?? null,
                $discount ?? null, $rollingWeeks, $taxAmount, $taxRate
            );
        }

        // --- Create attendee record (single booking) ---
        $attId = $this->db->insert('st_class_attendees', [
            'uuid'                => $uuid,
            'class_id'            => $classId,
            'player_id'           => $playerId,
            'first_name'          => Sanitizer::string($data['first_name']),
            'last_name'           => Sanitizer::string($data['last_name'] ?? ''),
            'email'               => $data['email'] ?? null,
            'phone'               => $data['phone'] ?? null,
            'status'              => $status,
            'amount_paid'         => $finalAmount,
            'quote_amount'        => $quoteAmount,
            'payment_method'      => $paymentMethod,
            'payment_id'          => $paymentId,
            'square_payment_id'   => $squarePaymentId,
            'payment_status'      => $paymentStatus,
            'discount_code'       => !empty($data['discount_code']) ? strtoupper(trim($data['discount_code'])) : null,
            'discount_amount'     => $discountAmount,
            'credit_code_id'      => $creditCodeId,
            'credit_amount'       => $creditAmount,
            'gift_certificate_id' => $giftCertificateId,
            'gift_amount'         => $giftAmount,
            'tax_amount'          => $taxAmount,
            'tax_rate'            => $taxRate,
            'notes'               => $data['notes'] ?? null,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        // --- Deduct credit code balance ---
        if ($creditCodeId && $creditAmount > 0) {
            $this->db->insert('credit_code_usages', [
                'uuid'           => $this->generateUuid(),
                'credit_code_id' => $creditCodeId,
                'player_id'      => $playerId,
                'amount_used'    => $creditAmount,
                'usage_type'     => 'SESSION',
                'notes'          => 'Booking for class #' . $classId,
                'used_at'        => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
            $newBalance = max(0, (float) $creditCode['balance'] - $creditAmount);
            $this->db->update('credit_codes', [
                'balance' => $newBalance,
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $creditCodeId]);
        }

        // --- Deduct gift certificate balance ---
        if ($giftCertificateId && $giftAmount > 0) {
            $newGiftBalance = max(0, (float) $giftCert['value'] - $giftAmount);
            $this->db->insert('gift_certificate_usage', [
                'uuid'                => $this->generateUuid(),
                'gift_certificate_id' => $giftCertificateId,
                'usage_date'          => date('Y-m-d H:i:s'),
                'amount_used'         => $giftAmount,
                'remaining_balance'   => $newGiftBalance,
                'reference_id'        => (string) $attId,
                'reference_type'      => 'class_booking',
                'notes'               => 'Booking for class #' . $classId,
                'used_by'             => Sanitizer::string($data['first_name'] . ' ' . ($data['last_name'] ?? '')),
                'created_at'          => date('Y-m-d H:i:s'),
            ]);
            $giftUpdate = ['value' => $newGiftBalance, 'updated_at' => date('Y-m-d H:i:s')];
            if ($newGiftBalance <= 0) {
                $giftUpdate['status'] = 'redeemed';
            }
            $this->db->update('gift_certificates', $giftUpdate, ['id' => $giftCertificateId]);
        }

        // --- Increment discount usage count ---
        if (!empty($data['discount_code']) && $discountAmount > 0 && isset($discount)) {
            $this->db->query(
                "UPDATE st_discount_rules SET used_count = used_count + 1, updated_at = NOW() WHERE id = ?",
                [$discount['id']]
            );
        }

        // --- Decrement available slots ---
        if ($status === 'registered') {
            $this->db->query(
                "UPDATE st_classes SET slots_available = GREATEST(0, slots_available - 1), updated_at = NOW() WHERE id = ?",
                [$classId]
            );
        }

        $created = $this->db->fetch("SELECT * FROM st_class_attendees WHERE id = ?", [$attId]);

        // Audit trail
        $this->auditLog->log(
            $orgId, $request->userId(), 'booking_created', 'class_attendee', $attId,
            null, $created, $request->ip(), $request->userAgent()
        );

        // Send email if requested
        if (!empty($data['send_email']) && !empty($created['email'])) {
            $sessionType = $this->db->fetch("SELECT * FROM session_types WHERE id = ?", [$id]);
            if ($status === 'reserved') {
                $this->emailService->sendReservationConfirmation($created, $class, $sessionType ?: [], $orgId, $request->userId());
            } else {
                $this->emailService->sendBookingConfirmation($created, $class, $sessionType ?: [], $orgId, $request->userId());
            }
        }

        return $this->created($created, 'Attendee booked successfully');
    }

    /**
     * POST /{id}/classes/{classId}/calculate-price
     * Returns price breakdown for a booking.
     */
    public function calculatePrice(Request $request, int $id, int $classId): Response
    {
        $class = $this->ensureClassExists($id, $classId);
        $orgId = $request->organizationId();

        // Get session type pricing
        $sessionType = $this->db->fetch("SELECT * FROM session_types WHERE id = ?", [$id]);
        $basePrice = (float) ($sessionType['standard_price'] ?? 0);

        // Rolling package price override
        $rollingWeeks = $request->input('rolling_package_weeks') ? (int) $request->input('rolling_package_weeks') : 0;
        $rollingInfo = null;
        if ($rollingWeeks > 0 && ($sessionType['session_type'] ?? '') === 'series_rolling') {
            $rollingPrice = $this->db->fetch(
                "SELECT * FROM st_rolling_prices WHERE session_type_id = ? AND number_of_weeks = ?",
                [$id, $rollingWeeks]
            );
            if ($rollingPrice) {
                $basePrice = (float) $rollingPrice['price'];
                $rollingInfo = [
                    'weeks' => $rollingWeeks,
                    'total_price' => $basePrice,
                    'per_session_price' => round($basePrice / $rollingWeeks, 2),
                ];
            }
        }

        // Check hot deal (time-based expiry AND/OR registration-based threshold) — skip for rolling packages
        $hotDeal = null;
        if (!$rollingInfo) {
            $hotDeal = $this->db->fetch("SELECT * FROM st_hot_deals WHERE class_id = ? AND is_active = 1", [$classId]);
        } else {
            $hotDeal = null;
        }
        if ($hotDeal) {
            $hotDealApplies = false;
            // Time-based: if expires_at is set, check it; if null, always applies
            if (!empty($hotDeal['expires_at'])) {
                $hotDealApplies = $hotDeal['expires_at'] > date('Y-m-d H:i:s');
            } else {
                $hotDealApplies = true;
            }
            // Registration-based: if min_registrations is set, only apply if booked count >= threshold
            if ($hotDealApplies && !empty($hotDeal['min_registrations'])) {
                $bookedCount = (int) ($class['slots'] ?? 0) - (int) ($class['slots_available'] ?? 0);
                $hotDealApplies = $bookedCount >= (int) $hotDeal['min_registrations'];
            }
            if ($hotDealApplies) {
                $basePrice = (float) $hotDeal['discount_price'];
            } else {
                $hotDeal = null; // Not applicable, treat as no deal
            }
        }

        // Check early bird
        $earlyBird = $this->db->fetch("SELECT * FROM st_early_birds WHERE class_id = ? AND is_active = 1", [$classId]);
        if ($earlyBird && !$hotDeal) {
            $cutoffHours = (int) ($earlyBird['cutoff_hours'] ?? 24);
            $classTime = $class['scheduled_at'] ?? '';
            if ($classTime && strtotime($classTime) - time() > ($cutoffHours * 3600)) {
                $basePrice = (float) $earlyBird['discount_price'];
            }
        }

        // Validate discount code if provided
        $discountAmount = 0;
        $discountInfo = null;
        $discountCode = $request->input('discount_code');
        if ($discountCode) {
            $discount = $this->db->fetch(
                "SELECT * FROM st_discount_rules WHERE coupon_code = ? AND is_active = 1 AND organization_id = ?",
                [strtoupper(trim($discountCode)), $orgId]
            );
            if ($discount) {
                $now = date('Y-m-d');
                $valid = true;
                if ($discount['valid_from'] && $now < $discount['valid_from']) $valid = false;
                if ($discount['valid_to'] && $now > $discount['valid_to']) $valid = false;
                if ($discount['usage_limit'] && $discount['used_count'] >= $discount['usage_limit']) $valid = false;

                if ($valid) {
                    if ($discount['discount_type'] === 'percent') {
                        $discountAmount = round($basePrice * (float) $discount['discount_value'] / 100, 2);
                    } else {
                        $discountAmount = min($basePrice, round((float) $discount['discount_value'], 2));
                    }
                    $discountInfo = [
                        'name' => $discount['name'],
                        'type' => $discount['discount_type'],
                        'value' => (float) $discount['discount_value'],
                        'amount' => $discountAmount,
                    ];
                }
            }
        }

        // Validate credit code if provided
        $creditBalance = 0;
        $creditInfo = null;
        $creditCode = $request->input('credit_code');
        $playerId = $request->input('player_id') ? (int) $request->input('player_id') : null;
        $facilityId = $class['facility_id'] ?? null;
        if ($creditCode && $facilityId) {
            $cc = $this->db->fetch(
                "SELECT * FROM credit_codes WHERE code = ? AND active = 1 AND facility_id = ?",
                [strtoupper(trim($creditCode)), $facilityId]
            );
            if ($cc) {
                // Enforce player binding
                if ($cc['issued_to'] && $playerId && (int) $cc['issued_to'] !== $playerId) {
                    $creditInfo = ['error' => 'This credit code belongs to a different player'];
                } else {
                    $creditBalance = (float) $cc['balance'];
                    $creditInfo = [
                        'id' => $cc['id'],
                        'code' => $cc['code'],
                        'balance' => $creditBalance,
                        'name' => $cc['name'],
                        'issued_to' => $cc['issued_to'],
                    ];
                }
            }
        }

        // Validate gift certificate if provided
        $giftBalance = 0;
        $giftInfo = null;
        $giftCode = $request->input('gift_code');
        if ($giftCode) {
            $gc = $this->db->fetch(
                "SELECT * FROM gift_certificates WHERE code = ? AND status = 'active' AND organization_id = ?",
                [strtoupper(trim($giftCode)), $orgId]
            );
            if ($gc) {
                $giftBalance = (float) $gc['value'];
                $giftInfo = [
                    'id' => $gc['id'],
                    'code' => $gc['code'],
                    'balance' => $giftBalance,
                    'name' => $gc['certificate_name'] ?? '',
                    'recipient_name' => trim(($gc['recipient_first_name'] ?? '') . ' ' . ($gc['recipient_last_name'] ?? '')),
                    'recipient_email' => $gc['recipient_email'] ?? '',
                ];
            }
        }

        $afterDiscount = max(0, $basePrice - $discountAmount);
        $totalDeductions = $discountAmount;

        return $this->success([
            'base_price'      => $basePrice,
            'discount_amount' => $discountAmount,
            'discount'        => $discountInfo,
            'after_discount'  => $afterDiscount,
            'credit'          => $creditInfo,
            'credit_balance'  => $creditBalance,
            'gift'            => $giftInfo,
            'gift_balance'    => $giftBalance,
            'hot_deal'        => $hotDeal ? true : false,
            'early_bird'      => $earlyBird ? true : false,
            'rolling'         => $rollingInfo,
        ]);
    }

    /**
     * POST /{id}/classes/{classId}/attendees/{attendeeId}/cancel
     * Cancel an attendee with options: full_refund, partial_refund, issue_credit, no_refund
     */
    public function cancel(Request $request, int $id, int $classId, int $attendeeId): Response
    {
        $class = $this->ensureClassExists($id, $classId);
        $att = $this->findAttendee($classId, $attendeeId);

        $data = Validator::validate($request->all(), [
            'cancel_mode'   => 'required|string',
            'refund_amount' => 'nullable|numeric',
            'reason'        => 'nullable|string|max:500',
            'send_email'    => 'nullable|boolean',
        ]);

        $cancelMode = $data['cancel_mode'];
        $reason = $data['reason'] ?? 'Cancelled by admin';
        $refundAmount = 0;
        $orgId = $request->organizationId();

        $wasRegistered = $att['status'] === 'registered';

        // Update attendee status to cancelled
        $updateData = [
            'status'           => 'cancelled',
            'cancelled_at'     => date('Y-m-d H:i:s'),
            'cancelled_reason' => Sanitizer::string($reason),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        if ($cancelMode === 'full_refund') {
            $refundAmount = (float) $att['amount_paid'];
        } elseif ($cancelMode === 'partial_refund') {
            $refundAmount = min((float) ($data['refund_amount'] ?? 0), (float) $att['amount_paid']);
        }

        // Process Square refund if paid by card
        if ($refundAmount > 0 && $att['square_payment_id'] && in_array($cancelMode, ['full_refund', 'partial_refund'])) {
            try {
                $square = new SquarePaymentService();
                $refundCents = (int) round($refundAmount * 100);
                $result = $square->refundPayment($att['square_payment_id'], $refundCents, 'USD', $reason);

                $updateData['refunded_amount'] = $refundAmount;
                $updateData['payment_status'] = $cancelMode === 'full_refund' ? 'refunded' : 'partially_refunded';

                // Update payment record if exists
                if ($att['payment_id']) {
                    $newPayStatus = $cancelMode === 'full_refund' ? 'refunded' : 'partially_refunded';
                    $this->db->update('payments', [
                        'status' => $newPayStatus,
                        'refunded_amount' => $refundAmount * 100,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ], ['id' => $att['payment_id']]);

                    // Record refund transaction
                    $this->db->insert('transactions', [
                        'uuid' => $this->generateUuid(),
                        'organization_id' => $orgId,
                        'payment_id' => $att['payment_id'],
                        'type' => 'refund',
                        'amount' => $refundCents,
                        'currency' => 'USD',
                        'description' => $reason,
                        'square_transaction_id' => $result['id'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            } catch (\Exception $e) {
                return $this->error('Refund failed: ' . $e->getMessage(), 422);
            }
        } elseif ($refundAmount > 0 && in_array($att['payment_method'], ['manual', 'cash'])) {
            // Cash/manual refund — just mark as refunded
            $updateData['refunded_amount'] = $refundAmount;
            $updateData['payment_status'] = $cancelMode === 'full_refund' ? 'refunded' : 'partially_refunded';
        }

        // Handle credit code refund (return credit balance)
        if (in_array($cancelMode, ['full_refund', 'no_refund']) && $att['credit_code_id'] && (float) $att['credit_amount'] > 0) {
            $this->db->insert('credit_code_usages', [
                'uuid'           => $this->generateUuid(),
                'credit_code_id' => (int) $att['credit_code_id'],
                'player_id'      => $att['player_id'],
                'amount_used'    => (float) $att['credit_amount'],
                'usage_type'     => 'REFUND',
                'notes'          => 'Cancellation refund for class #' . $classId,
                'used_at'        => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
            $currentBalance = (float) ($this->db->fetch("SELECT balance FROM credit_codes WHERE id = ?", [(int) $att['credit_code_id']])['balance'] ?? 0);
            $this->db->update('credit_codes', [
                'balance' => $currentBalance + (float) $att['credit_amount'],
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => (int) $att['credit_code_id']]);
        }

        // Handle gift certificate refund (return balance)
        if (in_array($cancelMode, ['full_refund', 'no_refund']) && $att['gift_certificate_id'] && (float) $att['gift_amount'] > 0) {
            $gc = $this->db->fetch("SELECT * FROM gift_certificates WHERE id = ?", [(int) $att['gift_certificate_id']]);
            if ($gc) {
                $newGcBalance = (float) $gc['value'] + (float) $att['gift_amount'];
                $this->db->update('gift_certificates', [
                    'value' => $newGcBalance,
                    'status' => 'active',
                    'updated_at' => date('Y-m-d H:i:s'),
                ], ['id' => (int) $att['gift_certificate_id']]);
            }
        }

        if ($cancelMode === 'no_refund') {
            $updateData['payment_status'] = 'cancelled';
        }

        $this->db->update('st_class_attendees', $updateData, ['id' => $attendeeId]);

        // Re-increment slots if they were registered
        if ($wasRegistered) {
            $this->db->query(
                "UPDATE st_classes SET slots_available = slots_available + 1, updated_at = NOW() WHERE id = ?",
                [$classId]
            );
        }

        $updated = $this->db->fetch("SELECT * FROM st_class_attendees WHERE id = ?", [$attendeeId]);

        // Audit trail
        $this->auditLog->log(
            $orgId, $request->userId(), 'booking_cancelled', 'class_attendee', $attendeeId,
            ['status' => $att['status'], 'payment_status' => $att['payment_status']],
            ['status' => 'cancelled', 'cancel_mode' => $cancelMode, 'refund_amount' => $refundAmount],
            $request->ip(), $request->userAgent()
        );

        // Send email if requested
        if (!empty($data['send_email']) && !empty($updated['email'])) {
            $sessionType = $this->db->fetch("SELECT * FROM session_types WHERE id = ?", [$id]);
            $this->emailService->sendCancellationNotice($updated, $class, $sessionType ?: [], $cancelMode, $orgId, $request->userId());
        }

        return $this->success($updated, 'Attendee cancelled');
    }

    /**
     * POST /{id}/classes/{classId}/attendees/{attendeeId}/refund
     * Process a refund for a specific attendee.
     */
    public function refund(Request $request, int $id, int $classId, int $attendeeId): Response
    {
        $class = $this->ensureClassExists($id, $classId);
        $att = $this->findAttendee($classId, $attendeeId);
        $orgId = $request->organizationId();

        $data = Validator::validate($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        $refundAmount = min(round((float) $data['amount'], 2), (float) $att['amount_paid'] - (float) ($att['refunded_amount'] ?? 0));
        if ($refundAmount <= 0) {
            return $this->error('Nothing to refund', 422);
        }

        $reason = $data['reason'] ?? 'Admin refund';

        // Process Square refund if card payment
        if ($att['square_payment_id']) {
            try {
                $square = new SquarePaymentService();
                $refundCents = (int) round($refundAmount * 100);
                $result = $square->refundPayment($att['square_payment_id'], $refundCents, 'USD', $reason);

                if ($att['payment_id']) {
                    $this->db->insert('transactions', [
                        'uuid' => $this->generateUuid(),
                        'organization_id' => $orgId,
                        'payment_id' => $att['payment_id'],
                        'type' => 'refund',
                        'amount' => $refundCents,
                        'currency' => 'USD',
                        'description' => $reason,
                        'square_transaction_id' => $result['id'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            } catch (\Exception $e) {
                return $this->error('Refund failed: ' . $e->getMessage(), 422);
            }
        }

        $totalRefunded = (float) ($att['refunded_amount'] ?? 0) + $refundAmount;
        $newPayStatus = $totalRefunded >= (float) $att['amount_paid'] ? 'refunded' : 'partially_refunded';

        $this->db->update('st_class_attendees', [
            'refunded_amount' => $totalRefunded,
            'payment_status'  => $newPayStatus,
            'updated_at'      => date('Y-m-d H:i:s'),
        ], ['id' => $attendeeId]);

        $updated = $this->db->fetch("SELECT * FROM st_class_attendees WHERE id = ?", [$attendeeId]);

        // Audit trail
        $this->auditLog->log(
            $orgId, $request->userId(), 'refund_processed', 'class_attendee', $attendeeId,
            ['refunded_amount' => (float) ($att['refunded_amount'] ?? 0)],
            ['refunded_amount' => $totalRefunded, 'new_refund' => $refundAmount],
            $request->ip(), $request->userAgent()
        );

        // Send email if requested
        if (!empty($data['send_email']) && !empty($updated['email'])) {
            $sessionType = $this->db->fetch("SELECT * FROM session_types WHERE id = ?", [$id]);
            $this->emailService->sendRefundNotice($updated, $class, $sessionType ?: [], $refundAmount, $orgId, $request->userId());
        }

        return $this->success($updated, 'Refund of $' . number_format($refundAmount, 2) . ' processed');
    }

    /**
     * POST /{id}/classes/{classId}/attendees/{attendeeId}/issue-credit
     * Issue a credit code to an attendee (for cancellation, goodwill, etc.).
     */
    public function issueCredit(Request $request, int $id, int $classId, int $attendeeId): Response
    {
        $class = $this->ensureClassExists($id, $classId);
        $att = $this->findAttendee($classId, $attendeeId);
        $orgId = $request->organizationId();

        $data = Validator::validate($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:200',            'send_email' => 'nullable|boolean',            'send_email' => 'nullable|boolean',
        ]);

        $amount = round((float) $data['amount'], 2);
        $reason = $data['reason'] ?? 'Cancellation credit';
        $facilityId = $class['facility_id'] ?? null;

        // Generate unique code
        $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $attempts = 0;
        while ($this->db->fetch("SELECT id FROM credit_codes WHERE code = ? AND facility_id = ?", [$code, $facilityId]) && $attempts < 10) {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $attempts++;
        }

        $ccId = $this->db->insert('credit_codes', [
            'uuid'               => $this->generateUuid(),
            'organization_id'    => $orgId,
            'facility_id'        => $facilityId,
            'name'               => 'Credit for ' . Sanitizer::string($att['first_name'] . ' ' . ($att['last_name'] ?? '')),
            'code'               => $code,
            'type'               => 'credit',
            'category'           => 'system',
            'reason'             => Sanitizer::string($reason),
            'amount'             => $amount,
            'balance'            => $amount,
            'issued_to'          => $att['player_id'],
            'active'             => 1,
            'issued_at'          => date('Y-m-d H:i:s'),
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        // Update attendee payment status
        $this->db->update('st_class_attendees', [
            'payment_status' => 'credited',
            'updated_at'     => date('Y-m-d H:i:s'),
        ], ['id' => $attendeeId]);

        $creditCode = $this->db->fetch("SELECT * FROM credit_codes WHERE id = ?", [$ccId]);

        // Audit trail
        $this->auditLog->log(
            $orgId, $request->userId(), 'credit_issued', 'class_attendee', $attendeeId,
            null, ['credit_code' => $code, 'amount' => $amount, 'player_id' => $att['player_id']],
            $request->ip(), $request->userAgent()
        );

        // Send email if requested
        if (!empty($data['send_email']) && !empty($att['email'])) {
            $sessionType = $this->db->fetch("SELECT * FROM session_types WHERE id = ?", [$id]);
            $this->emailService->sendCreditIssuedNotice($att, $class, $sessionType ?: [], $code, $amount, $orgId, $request->userId());
        }

        return $this->created([
            'credit_code' => $creditCode,
            'attendee_id' => $attendeeId,
        ], 'Credit code issued: ' . $code . ' ($' . number_format($amount, 2) . ')');
    }

    /**
     * POST /{id}/classes/{classId}/validate-credit-code
     * Validate a credit code for a player.
     */
    public function validateCreditCode(Request $request, int $id, int $classId): Response
    {
        $class = $this->ensureClassExists($id, $classId);
        $facilityId = $class['facility_id'] ?? null;

        $code = strtoupper(trim($request->input('code', '')));
        $playerId = $request->input('player_id') ? (int) $request->input('player_id') : null;

        if (empty($code)) {
            return $this->error('Credit code is required', 422);
        }

        $cc = $this->db->fetch(
            "SELECT * FROM credit_codes WHERE code = ? AND active = 1 AND facility_id = ?",
            [$code, $facilityId]
        );

        if (!$cc) {
            return $this->error('Invalid or inactive credit code', 404);
        }

        // Enforce player binding
        if ($cc['issued_to'] && $playerId && (int) $cc['issued_to'] !== $playerId) {
            return $this->error('This credit code is assigned to a different player', 403);
        }

        if ((float) $cc['balance'] <= 0) {
            return $this->error('Credit code has no remaining balance', 422);
        }

        return $this->success([
            'id'        => $cc['id'],
            'code'      => $cc['code'],
            'name'      => $cc['name'],
            'balance'   => (float) $cc['balance'],
            'issued_to' => $cc['issued_to'],
        ], 'Credit code is valid');
    }

    /**
     * POST /{id}/classes/{classId}/validate-gift-code
     * Validate a gift certificate.
     */
    public function validateGiftCode(Request $request, int $id, int $classId): Response
    {
        $this->ensureClassExists($id, $classId);
        $orgId = $request->organizationId();

        $code = strtoupper(trim($request->input('code', '')));
        if (empty($code)) {
            return $this->error('Gift certificate code is required', 422);
        }

        $gc = $this->db->fetch(
            "SELECT * FROM gift_certificates WHERE code = ? AND status = 'active' AND organization_id = ?",
            [$code, $orgId]
        );

        if (!$gc) {
            return $this->error('Invalid or inactive gift certificate', 404);
        }

        if ((float) $gc['value'] <= 0) {
            return $this->error('Gift certificate has no remaining balance', 422);
        }

        return $this->success([
            'id'              => $gc['id'],
            'code'            => $gc['code'],
            'name'            => $gc['certificate_name'] ?? '',
            'balance'         => (float) $gc['value'],
            'recipient_name'  => trim(($gc['recipient_first_name'] ?? '') . ' ' . ($gc['recipient_last_name'] ?? '')),
            'recipient_email' => $gc['recipient_email'] ?? '',
        ], 'Gift certificate is valid');
    }

    // =========================================================================
    // Rolling Booking Groups
    // =========================================================================

    /**
     * GET /booking-groups?facility_id=X
     * List all booking groups for a facility (across all session types).
     */
    public function listBookingGroups(Request $request): Response
    {
        $orgId = $request->organizationId();
        $facilityId = (int) ($request->query('facility_id') ?? 0);
        if (!$facilityId) {
            return $this->validationError(['facility_id' => 'Facility ID is required']);
        }

        $groups = $this->db->fetchAll(
            "SELECT bg.*, st.name AS session_type_name,
                    p.first_name AS player_first_name, p.last_name AS player_last_name, p.email AS player_email,
                    fc.scheduled_at AS first_class_date,
                    (SELECT COUNT(*) FROM st_class_attendees WHERE booking_group_id = bg.id AND status != 'cancelled') AS active_sessions,
                    (SELECT COUNT(*) FROM st_class_attendees WHERE booking_group_id = bg.id AND status = 'cancelled') AS cancelled_sessions
             FROM booking_groups bg
             JOIN session_types st ON st.id = bg.session_type_id
             LEFT JOIN players p ON p.id = bg.player_id
             LEFT JOIN st_classes fc ON fc.id = bg.first_class_id
             WHERE bg.organization_id = ? AND st.facility_id = ?
             ORDER BY bg.created_at DESC",
            [$orgId, $facilityId]
        );

        return $this->success($groups);
    }

    // =========================================================================
    // Rolling Booking Group Cancellation
    // =========================================================================

    /**
     * POST /{id}/booking-groups/{groupId}/cancel
     * Cancel a rolling booking group: single session or remaining series.
     */
    public function cancelBookingGroup(Request $request, int $id, int $groupId): Response
    {
        $orgId = $request->organizationId();

        $group = $this->db->fetch(
            "SELECT * FROM booking_groups WHERE id = ? AND session_type_id = ? AND organization_id = ?",
            [$groupId, $id, $orgId]
        );
        if (!$group) {
            throw new NotFoundException('Booking group not found');
        }

        $data = Validator::validate($request->all(), [
            'cancel_scope'  => 'required|string',
            'attendee_id'   => 'nullable|integer',
            'cancel_mode'   => 'required|string',
            'refund_amount' => 'nullable|numeric',
            'reason'        => 'nullable|string|max:500',
            'send_email'    => 'nullable|boolean',
        ]);

        $cancelScope = $data['cancel_scope']; // 'single' or 'remaining'
        $cancelMode = $data['cancel_mode'];    // 'full_refund', 'partial_refund', 'issue_credit', 'no_refund'
        $reason = $data['reason'] ?? 'Cancelled by admin';
        $perSessionPrice = (float) $group['per_session_price'];

        // Get attendees to cancel
        if ($cancelScope === 'single') {
            if (empty($data['attendee_id'])) {
                return $this->error('attendee_id is required for single-session cancel', 422);
            }
            $attendees = $this->db->fetchAll(
                "SELECT a.*, c.scheduled_at FROM st_class_attendees a
                 JOIN st_classes c ON a.class_id = c.id
                 WHERE a.id = ? AND a.booking_group_id = ? AND a.status != 'cancelled'",
                [$data['attendee_id'], $groupId]
            );
        } else {
            // Cancel all remaining (future) non-cancelled sessions
            $attendees = $this->db->fetchAll(
                "SELECT a.*, c.scheduled_at FROM st_class_attendees a
                 JOIN st_classes c ON a.class_id = c.id
                 WHERE a.booking_group_id = ? AND a.status != 'cancelled'
                   AND c.scheduled_at >= NOW()
                 ORDER BY c.scheduled_at ASC",
                [$groupId]
            );
        }

        if (empty($attendees)) {
            return $this->error('No eligible sessions to cancel', 422);
        }

        $totalRefund = 0;
        $cancelledCount = 0;

        foreach ($attendees as $att) {
            $wasRegistered = $att['status'] === 'registered';
            $sessionRefund = 0;

            if ($cancelMode === 'full_refund') {
                $sessionRefund = $perSessionPrice;
            } elseif ($cancelMode === 'partial_refund' && $cancelScope === 'single') {
                $sessionRefund = min((float) ($data['refund_amount'] ?? 0), $perSessionPrice);
            } elseif ($cancelMode === 'partial_refund') {
                $sessionRefund = $perSessionPrice;
            }

            $this->db->update('st_class_attendees', [
                'status' => 'cancelled',
                'cancelled_at' => date('Y-m-d H:i:s'),
                'cancelled_reason' => Sanitizer::string($reason),
                'refunded_amount' => $sessionRefund,
                'payment_status' => $cancelMode === 'no_refund' ? 'cancelled' : ($sessionRefund > 0 ? 'refunded' : 'cancelled'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $att['id']]);

            // Re-increment slot
            if ($wasRegistered) {
                $this->db->query(
                    "UPDATE st_classes SET slots_available = slots_available + 1, updated_at = NOW() WHERE id = ?",
                    [$att['class_id']]
                );
            }

            $totalRefund += $sessionRefund;
            $cancelledCount++;
        }

        // Process Square refund for total amount
        if ($totalRefund > 0 && $group['square_payment_id'] && in_array($cancelMode, ['full_refund', 'partial_refund'])) {
            try {
                $square = new SquarePaymentService();
                $refundCents = (int) round($totalRefund * 100);
                $result = $square->refundPayment($group['square_payment_id'], $refundCents, 'USD', $reason);

                if ($group['payment_id']) {
                    $this->db->insert('transactions', [
                        'uuid' => $this->generateUuid(),
                        'organization_id' => $orgId,
                        'payment_id' => $group['payment_id'],
                        'type' => 'refund',
                        'amount' => $refundCents,
                        'currency' => 'USD',
                        'description' => 'Rolling group cancel: ' . Sanitizer::string($reason),
                        'square_transaction_id' => $result['id'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            } catch (\Exception $e) {
                return $this->error('Refund failed: ' . $e->getMessage(), 422);
            }
        }

        // Issue credit if requested
        if ($cancelMode === 'issue_credit' && $totalRefund <= 0) {
            $creditAmount = $perSessionPrice * $cancelledCount;
            $facilityId = $this->db->fetchColumn(
                "SELECT facility_id FROM session_types WHERE id = ?", [$id]
            );
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $this->db->insert('credit_codes', [
                'uuid' => $this->generateUuid(),
                'organization_id' => $orgId,
                'facility_id' => $facilityId,
                'name' => 'Rolling cancel credit',
                'code' => $code,
                'type' => 'credit',
                'category' => 'system',
                'reason' => Sanitizer::string($reason),
                'amount' => $creditAmount,
                'balance' => $creditAmount,
                'issued_to' => $group['player_id'],
                'active' => 1,
                'issued_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Update booking group status
        $remaining = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM st_class_attendees WHERE booking_group_id = ? AND status != 'cancelled'",
            [$groupId]
        );
        $newGroupStatus = $remaining === 0 ? 'fully_cancelled' : 'partially_cancelled';
        $this->db->update('booking_groups', [
            'status' => $newGroupStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $groupId]);

        // Audit trail
        $this->auditLog->log(
            $orgId, $request->userId(), 'rolling_group_cancelled', 'booking_group', $groupId,
            ['status' => $group['status']],
            ['new_status' => $newGroupStatus, 'scope' => $cancelScope, 'cancelled_count' => $cancelledCount, 'refund_total' => $totalRefund],
            $request->ip(), $request->userAgent()
        );

        // Send email if requested (to the first attendee's email)
        if (!empty($data['send_email'])) {
            $firstAtt = $attendees[0] ?? null;
            if ($firstAtt && !empty($firstAtt['email'])) {
                $sessionType = $this->db->fetch("SELECT * FROM session_types WHERE id = ?", [$id]);
                $firstClass = $this->db->fetch("SELECT * FROM st_classes WHERE id = ?", [$firstAtt['class_id']]);
                $this->emailService->sendCancellationNotice(
                    $firstAtt, $firstClass ?: [], $sessionType ?: [], $cancelMode, $orgId, $request->userId()
                );
            }
        }

        return $this->success([
            'cancelled_count' => $cancelledCount,
            'refund_total' => $totalRefund,
            'group_status' => $newGroupStatus,
        ], $cancelledCount . ' session(s) cancelled');
    }

    /**
     * GET /{id}/booking-groups/{groupId}
     * Get booking group details with all attendee records.
     */
    public function bookingGroupDetails(Request $request, int $id, int $groupId): Response
    {
        $orgId = $request->organizationId();

        $group = $this->db->fetch(
            "SELECT * FROM booking_groups WHERE id = ? AND session_type_id = ? AND organization_id = ?",
            [$groupId, $id, $orgId]
        );
        if (!$group) {
            throw new NotFoundException('Booking group not found');
        }

        $attendees = $this->db->fetchAll(
            "SELECT a.*, c.scheduled_at, c.booking_status, c.coach_id
             FROM st_class_attendees a
             JOIN st_classes c ON a.class_id = c.id
             WHERE a.booking_group_id = ?
             ORDER BY c.scheduled_at ASC",
            [$groupId]
        );

        $group['attendees'] = $attendees;
        $group['active_count'] = count(array_filter($attendees, fn($a) => $a['status'] !== 'cancelled'));
        $group['cancelled_count'] = count(array_filter($attendees, fn($a) => $a['status'] === 'cancelled'));

        return $this->success($group);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function ensureClassExists(int $sessionTypeId, int $classId): array
    {
        $class = $this->db->fetch(
            "SELECT c.*, st.facility_id FROM st_classes c JOIN session_types st ON st.id = c.session_type_id WHERE c.id = ? AND c.session_type_id = ?",
            [$classId, $sessionTypeId]
        );
        if (!$class) {
            throw new NotFoundException('Class not found');
        }
        return $class;
    }

    private function findAttendee(int $classId, int $attendeeId): array
    {
        $att = $this->db->fetch(
            "SELECT * FROM st_class_attendees WHERE id = ? AND class_id = ?",
            [$attendeeId, $classId]
        );
        if (!$att) {
            throw new NotFoundException('Attendee not found');
        }
        return $att;
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // =========================================================================
    // Rolling Enrollment Logic
    // =========================================================================

    /**
     * Process a rolling package enrollment: create booking_group + N attendee records.
     */
    private function processRollingEnrollment(
        Request $request, int $sessionTypeId, int $classId, array $class, int $orgId, array $data,
        string $uuid, ?int $playerId, ?int $facilityId, string $status,
        float $quoteAmount, float $discountAmount, float $creditAmount, float $giftAmount, float $finalAmount,
        string $paymentMethod, string $paymentStatus, ?int $paymentId, ?string $squarePaymentId,
        ?int $creditCodeId, ?array $creditCode, ?int $giftCertificateId, ?array $giftCert,
        ?array $discount, int $rollingWeeks, float $taxAmount = 0, float $taxRate = 0
    ): Response {
        // Validate session type is series_rolling
        $sessionType = $this->db->fetch("SELECT * FROM session_types WHERE id = ?", [$sessionTypeId]);
        if (!$sessionType || ($sessionType['session_type'] ?? '') !== 'series_rolling') {
            return $this->error('Rolling packages only apply to series_rolling session types', 422);
        }

        // Validate the rolling package exists
        $rollingPrice = $this->db->fetch(
            "SELECT * FROM st_rolling_prices WHERE session_type_id = ? AND number_of_weeks = ?",
            [$sessionTypeId, $rollingWeeks]
        );
        if (!$rollingPrice) {
            return $this->error('Invalid rolling package selection', 422);
        }

        // Calculate per-session price (handle rounding)
        $perSessionBase = floor($finalAmount * 100 / $rollingWeeks) / 100;
        $firstSessionPrice = round($finalAmount - ($perSessionBase * ($rollingWeeks - 1)), 2);

        // Gather future classes from this class forward
        $futureClasses = $this->db->fetchAll(
            "SELECT * FROM st_classes WHERE session_type_id = ? AND scheduled_at >= ? AND is_active = 1
             ORDER BY scheduled_at ASC LIMIT ?",
            [$sessionTypeId, $class['scheduled_at'], $rollingWeeks]
        );

        // Auto-create missing classes if needed
        if (count($futureClasses) < $rollingWeeks) {
            $futureClasses = $this->autoCreateMissingClasses(
                $futureClasses, $sessionTypeId, $sessionType, $rollingWeeks
            );
        }

        if (count($futureClasses) < $rollingWeeks) {
            return $this->error('Could not create enough future classes for this rolling package', 422);
        }

        // Create booking group
        $bookingGroupId = $this->db->insert('booking_groups', [
            'uuid' => $this->generateUuid(),
            'organization_id' => $orgId,
            'session_type_id' => $sessionTypeId,
            'player_id' => $playerId,
            'first_class_id' => $classId,
            'rolling_weeks' => $rollingWeeks,
            'total_price' => $finalAmount,
            'per_session_price' => $perSessionBase,
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
            'square_payment_id' => $squarePaymentId,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create an attendee record for each class
        $attendeeIds = [];
        foreach ($futureClasses as $i => $futureClass) {
            $isFirst = ($i === 0);
            $sessionPrice = $isFirst ? $firstSessionPrice : $perSessionBase;

            $attId = $this->db->insert('st_class_attendees', [
                'uuid'                => $this->generateUuid(),
                'class_id'            => (int) $futureClass['id'],
                'player_id'           => $playerId,
                'first_name'          => Sanitizer::string($data['first_name']),
                'last_name'           => Sanitizer::string($data['last_name'] ?? ''),
                'email'               => $data['email'] ?? null,
                'phone'               => $data['phone'] ?? null,
                'status'              => $status,
                'amount_paid'         => $sessionPrice,
                'quote_amount'        => $sessionPrice,
                'payment_method'      => $paymentMethod,
                'payment_id'          => $paymentId,
                'square_payment_id'   => $isFirst ? $squarePaymentId : null,
                'payment_status'      => $paymentStatus,
                'discount_code'       => $isFirst && !empty($data['discount_code']) ? strtoupper(trim($data['discount_code'])) : null,
                'discount_amount'     => $isFirst ? $discountAmount : 0,
                'credit_code_id'      => $isFirst ? $creditCodeId : null,
                'credit_amount'       => $isFirst ? $creditAmount : 0,
                'gift_certificate_id' => $isFirst ? $giftCertificateId : null,
                'gift_amount'         => $isFirst ? $giftAmount : 0,
                'booking_group_id'    => $bookingGroupId,
                'tax_amount'          => $isFirst ? $taxAmount : 0,
                'tax_rate'            => $taxRate,
                'notes'               => $isFirst ? ($data['notes'] ?? null) : null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);
            $attendeeIds[] = $attId;

            // Decrement available slots
            if ($status === 'registered') {
                $this->db->query(
                    "UPDATE st_classes SET slots_available = GREATEST(0, slots_available - 1), updated_at = NOW() WHERE id = ?",
                    [(int) $futureClass['id']]
                );
            }
        }

        // Deduct credit code balance (once, for total)
        if ($creditCodeId && $creditAmount > 0 && $creditCode) {
            $this->db->insert('credit_code_usages', [
                'uuid'           => $this->generateUuid(),
                'credit_code_id' => $creditCodeId,
                'player_id'      => $playerId,
                'amount_used'    => $creditAmount,
                'usage_type'     => 'SESSION',
                'notes'          => 'Rolling package booking (' . $rollingWeeks . ' weeks)',
                'used_at'        => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
            $newBalance = max(0, (float) $creditCode['balance'] - $creditAmount);
            $this->db->update('credit_codes', [
                'balance' => $newBalance,
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $creditCodeId]);
        }

        // Deduct gift certificate balance (once, for total)
        if ($giftCertificateId && $giftAmount > 0 && $giftCert) {
            $newGiftBalance = max(0, (float) $giftCert['value'] - $giftAmount);
            $this->db->insert('gift_certificate_usage', [
                'uuid'                => $this->generateUuid(),
                'gift_certificate_id' => $giftCertificateId,
                'usage_date'          => date('Y-m-d H:i:s'),
                'amount_used'         => $giftAmount,
                'remaining_balance'   => $newGiftBalance,
                'reference_id'        => (string) $bookingGroupId,
                'reference_type'      => 'rolling_booking',
                'notes'               => 'Rolling package (' . $rollingWeeks . ' weeks)',
                'used_by'             => Sanitizer::string($data['first_name'] . ' ' . ($data['last_name'] ?? '')),
                'created_at'          => date('Y-m-d H:i:s'),
            ]);
            $giftUpdate = ['value' => $newGiftBalance, 'updated_at' => date('Y-m-d H:i:s')];
            if ($newGiftBalance <= 0) {
                $giftUpdate['status'] = 'redeemed';
            }
            $this->db->update('gift_certificates', $giftUpdate, ['id' => $giftCertificateId]);
        }

        // Increment discount usage
        if (!empty($data['discount_code']) && $discountAmount > 0 && $discount) {
            $this->db->query(
                "UPDATE st_discount_rules SET used_count = used_count + 1, updated_at = NOW() WHERE id = ?",
                [$discount['id']]
            );
        }

        // Get the first attendee record for response
        $created = $this->db->fetch("SELECT * FROM st_class_attendees WHERE id = ?", [$attendeeIds[0]]);

        // Audit trail
        $this->auditLog->log(
            $orgId, $request->userId(), 'rolling_booking_created', 'booking_group', $bookingGroupId,
            null,
            [
                'rolling_weeks' => $rollingWeeks,
                'total_price' => $finalAmount,
                'per_session_price' => $perSessionBase,
                'attendee_count' => count($attendeeIds),
                'class_ids' => array_column($futureClasses, 'id'),
            ],
            $request->ip(), $request->userAgent()
        );

        // Send email if requested
        if (!empty($data['send_email']) && !empty($created['email'])) {
            $this->emailService->sendBookingConfirmation($created, $class, $sessionType, $orgId, $request->userId());
        }

        return $this->created([
            'attendee' => $created,
            'booking_group' => [
                'id' => $bookingGroupId,
                'rolling_weeks' => $rollingWeeks,
                'total_price' => $finalAmount,
                'per_session_price' => $perSessionBase,
                'attendee_count' => count($attendeeIds),
                'attendee_ids' => $attendeeIds,
            ],
        ], 'Rolling package booked: ' . $rollingWeeks . ' weeks');
    }

    /**
     * Auto-create missing classes to fulfil a rolling package.
     * Extrapolates the schedule interval from existing classes.
     */
    private function autoCreateMissingClasses(array $existingClasses, int $sessionTypeId, array $sessionType, int $needed): array
    {
        if (empty($existingClasses)) {
            return [];
        }

        // Calculate interval between classes (default 7 days if only one exists)
        $intervalDays = 7;
        if (count($existingClasses) >= 2) {
            $last = end($existingClasses);
            $secondLast = $existingClasses[count($existingClasses) - 2];
            $diff = strtotime($last['scheduled_at']) - strtotime($secondLast['scheduled_at']);
            if ($diff > 0) {
                $intervalDays = (int) round($diff / 86400);
            }
        }

        $capacity = (int) ($sessionType['capacity'] ?? 10);
        $facilityId = $sessionType['facility_id'] ?? ($existingClasses[0]['facility_id'] ?? null);
        $lastClass = end($existingClasses);
        $lastTime = strtotime($lastClass['scheduled_at']);

        $missing = $needed - count($existingClasses);
        for ($i = 0; $i < $missing; $i++) {
            $nextTime = $lastTime + (($i + 1) * $intervalDays * 86400);
            $scheduledAt = date('Y-m-d H:i:s', $nextTime);

            // Avoid duplicates
            $existing = $this->db->fetch(
                "SELECT id FROM st_classes WHERE session_type_id = ? AND scheduled_at = ?",
                [$sessionTypeId, $scheduledAt]
            );
            if ($existing) {
                $existingClasses[] = $this->db->fetch("SELECT * FROM st_classes WHERE id = ?", [$existing['id']]);
                continue;
            }

            $classId = $this->db->insert('st_classes', [
                'uuid' => $this->generateUuid(),
                'session_type_id' => $sessionTypeId,
                'facility_id' => $facilityId,
                'scheduled_at' => $scheduledAt,
                'slots' => $capacity,
                'slots_available' => $capacity,
                'booking_status' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $existingClasses[] = $this->db->fetch("SELECT * FROM st_classes WHERE id = ?", [$classId]);
        }

        return $existingClasses;
    }
}
