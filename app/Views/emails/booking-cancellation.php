<?php
/**
 * Email: Booking Cancellation
 * Variables: $attendee, $class, $sessionType, $cancelMode, $appName
 */
$recipientEmail = $attendee['email'] ?? '';
$appUrl = $_ENV['APP_URL'] ?? '#';
$firstName = $attendee['first_name'] ?? 'there';
$lastName = $attendee['last_name'] ?? '';
$sessionTitle = $sessionType['title'] ?? 'Session';
$scheduledAt = isset($class['scheduled_at']) ? date('l, F j, Y \a\t g:i A', strtotime($class['scheduled_at'])) : 'TBD';
$amountPaid = number_format((float)($attendee['amount_paid'] ?? 0), 2);
$reason = $attendee['cancelled_reason'] ?? '';

$modeLabels = [
    'full_refund'    => 'Full refund will be processed',
    'partial_refund' => 'A partial refund will be processed',
    'issue_credit'   => 'A credit code has been issued to your account',
    'no_refund'      => 'No refund will be issued for this cancellation',
];
$modeText = $modeLabels[$cancelMode] ?? 'Your booking has been cancelled';

$subject = $subject ?? 'Booking Cancelled - ' . htmlspecialchars($sessionTitle);

$bodyContent = '
<h1>Booking Cancelled</h1>
<p>Hi ' . htmlspecialchars($firstName) . ', your booking has been cancelled.</p>

<div class="warning-box">
  <p><strong>📋 Session:</strong> ' . htmlspecialchars($sessionTitle) . '<br>
  <strong>📅 Date:</strong> ' . htmlspecialchars($scheduledAt) . '<br>
  <strong>💰 Original Amount:</strong> $' . $amountPaid . '</p>
</div>

<div class="info-box">
  <p><strong>Resolution:</strong> ' . htmlspecialchars($modeText) . '</p>
  ' . ($reason ? '<p><strong>Reason:</strong> ' . htmlspecialchars($reason) . '</p>' : '') . '
</div>

<p>If you have questions about this cancellation, please contact us.</p>

<hr class="divider">
<p><small>This is an automated notice from ' . htmlspecialchars($appName ?? 'K2 Pickleball') . '.</small></p>
';

include __DIR__ . '/layout.php';
