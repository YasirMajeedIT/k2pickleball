<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Database\Connection;

/**
 * Handles all booking/attendee email notifications with:
 * - Facility SMTP config with env fallback
 * - Email logging to email_logs table
 * - Audit trail integration
 */
final class EmailNotificationService
{
    private Connection $db;
    private Mailer $mailer;

    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->mailer = Mailer::getInstance();
    }

    /**
     * Send a booking confirmation email.
     */
    public function sendBookingConfirmation(array $attendee, array $class, array $sessionType, int $orgId, ?int $sentBy = null): bool
    {
        $email = $attendee['email'] ?? null;
        if (!$email) return false;

        $name = trim(($attendee['first_name'] ?? '') . ' ' . ($attendee['last_name'] ?? ''));
        $subject = 'Booking Confirmation - ' . ($sessionType['title'] ?? 'Session');

        $html = $this->mailer->renderTemplate('booking-confirmation', [
            'attendee'    => $attendee,
            'class'       => $class,
            'sessionType' => $sessionType,
            'appName'     => $_ENV['APP_NAME'] ?? 'K2 Pickleball',
        ]);

        return $this->sendAndLog(
            $email, $name, $subject, $html,
            'booking_confirmation', 'class_attendee', (int) ($attendee['id'] ?? 0),
            $orgId, (int) ($class['facility_id'] ?? 0), $attendee['player_id'] ?? null, $sentBy
        );
    }

    /**
     * Send a cancellation email.
     */
    public function sendCancellationNotice(array $attendee, array $class, array $sessionType, string $cancelMode, int $orgId, ?int $sentBy = null): bool
    {
        $email = $attendee['email'] ?? null;
        if (!$email) return false;

        $name = trim(($attendee['first_name'] ?? '') . ' ' . ($attendee['last_name'] ?? ''));
        $subject = 'Booking Cancelled - ' . ($sessionType['title'] ?? 'Session');

        $html = $this->mailer->renderTemplate('booking-cancellation', [
            'attendee'    => $attendee,
            'class'       => $class,
            'sessionType' => $sessionType,
            'cancelMode'  => $cancelMode,
            'appName'     => $_ENV['APP_NAME'] ?? 'K2 Pickleball',
        ]);

        return $this->sendAndLog(
            $email, $name, $subject, $html,
            'cancellation', 'class_attendee', (int) ($attendee['id'] ?? 0),
            $orgId, (int) ($class['facility_id'] ?? 0), $attendee['player_id'] ?? null, $sentBy
        );
    }

    /**
     * Send a refund processed email.
     */
    public function sendRefundNotice(array $attendee, array $class, array $sessionType, float $refundAmount, int $orgId, ?int $sentBy = null): bool
    {
        $email = $attendee['email'] ?? null;
        if (!$email) return false;

        $name = trim(($attendee['first_name'] ?? '') . ' ' . ($attendee['last_name'] ?? ''));
        $subject = 'Refund Processed - $' . number_format($refundAmount, 2);

        $html = $this->mailer->renderTemplate('booking-refund', [
            'attendee'     => $attendee,
            'class'        => $class,
            'sessionType'  => $sessionType,
            'refundAmount' => $refundAmount,
            'appName'      => $_ENV['APP_NAME'] ?? 'K2 Pickleball',
        ]);

        return $this->sendAndLog(
            $email, $name, $subject, $html,
            'refund', 'class_attendee', (int) ($attendee['id'] ?? 0),
            $orgId, (int) ($class['facility_id'] ?? 0), $attendee['player_id'] ?? null, $sentBy
        );
    }

    /**
     * Send a credit issued email.
     */
    public function sendCreditIssuedNotice(array $attendee, array $class, array $sessionType, string $creditCode, float $creditAmount, int $orgId, ?int $sentBy = null): bool
    {
        $email = $attendee['email'] ?? null;
        if (!$email) return false;

        $name = trim(($attendee['first_name'] ?? '') . ' ' . ($attendee['last_name'] ?? ''));
        $subject = 'Credit Issued - $' . number_format($creditAmount, 2);

        $html = $this->mailer->renderTemplate('booking-credit-issued', [
            'attendee'     => $attendee,
            'class'        => $class,
            'sessionType'  => $sessionType,
            'creditCode'   => $creditCode,
            'creditAmount' => $creditAmount,
            'appName'      => $_ENV['APP_NAME'] ?? 'K2 Pickleball',
        ]);

        return $this->sendAndLog(
            $email, $name, $subject, $html,
            'credit_issued', 'class_attendee', (int) ($attendee['id'] ?? 0),
            $orgId, (int) ($class['facility_id'] ?? 0), $attendee['player_id'] ?? null, $sentBy
        );
    }

    /**
     * Send a reservation confirmation email.
     */
    public function sendReservationConfirmation(array $attendee, array $class, array $sessionType, int $orgId, ?int $sentBy = null): bool
    {
        $email = $attendee['email'] ?? null;
        if (!$email) return false;

        $name = trim(($attendee['first_name'] ?? '') . ' ' . ($attendee['last_name'] ?? ''));
        $subject = 'Reservation Confirmation - ' . ($sessionType['title'] ?? 'Session');

        $html = $this->mailer->renderTemplate('booking-reservation', [
            'attendee'    => $attendee,
            'class'       => $class,
            'sessionType' => $sessionType,
            'appName'     => $_ENV['APP_NAME'] ?? 'K2 Pickleball',
        ]);

        return $this->sendAndLog(
            $email, $name, $subject, $html,
            'reservation_confirmation', 'class_attendee', (int) ($attendee['id'] ?? 0),
            $orgId, (int) ($class['facility_id'] ?? 0), $attendee['player_id'] ?? null, $sentBy
        );
    }

    /**
     * Core send + log method.
     */
    private function sendAndLog(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $emailType,
        ?string $entityType,
        ?int $entityId,
        int $orgId,
        ?int $facilityId,
        ?int $playerId,
        ?int $sentBy
    ): bool {
        $facilitySmtp = null;
        if ($facilityId) {
            $facilitySmtp = $this->db->fetch(
                "SELECT smtp_host, smtp_port, smtp_username, smtp_password, smtp_encryption, smtp_from_email, smtp_from_name FROM facilities WHERE id = ?",
                [$facilityId]
            );
        }

        $status = 'sent';
        $error = null;

        try {
            $this->mailer->send($toEmail, $toName, $subject, $htmlBody, '', $facilitySmtp);
        } catch (\Exception $e) {
            $status = 'failed';
            $error = $e->getMessage();
            error_log("[EmailNotification] Failed: {$emailType} to {$toEmail}: {$error}");
        }

        // Log to email_logs table
        try {
            $this->db->insert('email_logs', [
                'uuid'             => $this->generateUuid(),
                'organization_id'  => $orgId,
                'facility_id'      => $facilityId ?: null,
                'recipient_email'  => $toEmail,
                'recipient_name'   => $toName,
                'player_id'        => $playerId,
                'subject'          => $subject,
                'body_html'        => $htmlBody,
                'email_type'       => $emailType,
                'entity_type'      => $entityType,
                'entity_id'        => $entityId,
                'status'           => $status,
                'error_message'    => $error,
                'sent_by'          => $sentBy,
                'created_at'       => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            error_log("[EmailNotification] Failed to log email: " . $e->getMessage());
        }

        return $status === 'sent';
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
