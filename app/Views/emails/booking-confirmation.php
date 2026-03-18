<?php
/**
 * Email: Booking Confirmation
 * Variables: $attendee, $class, $sessionType, $appName
 */
$recipientEmail = $attendee['email'] ?? '';
$appUrl = $_ENV['APP_URL'] ?? '#';
$firstName = $attendee['first_name'] ?? 'there';
$lastName = $attendee['last_name'] ?? '';
$sessionTitle = $sessionType['title'] ?? 'Session';
$scheduledAt = isset($class['scheduled_at']) ? date('l, F j, Y \a\t g:i A', strtotime($class['scheduled_at'])) : 'TBD';
$duration = ($sessionType['duration'] ?? 60) . ' minutes';
$amountPaid = number_format((float)($attendee['amount_paid'] ?? 0), 2);
$paymentMethod = ucfirst($attendee['payment_method'] ?? 'manual');
$paymentStatus = ucfirst($attendee['payment_status'] ?? 'pending');
$discountAmount = (float)($attendee['discount_amount'] ?? 0);
$creditAmount = (float)($attendee['credit_amount'] ?? 0);
$giftAmount = (float)($attendee['gift_amount'] ?? 0);

$subject = $subject ?? 'Booking Confirmation - ' . htmlspecialchars($sessionTitle);

$bodyContent = '
<h1>Booking Confirmed! ✅</h1>
<p>Hi ' . htmlspecialchars($firstName) . ', your booking has been confirmed. Here are the details:</p>

<div class="info-box">
  <p><strong>📋 Session:</strong> ' . htmlspecialchars($sessionTitle) . '<br>
  <strong>📅 Date & Time:</strong> ' . htmlspecialchars($scheduledAt) . '<br>
  <strong>⏱ Duration:</strong> ' . htmlspecialchars($duration) . '<br>
  <strong>👤 Name:</strong> ' . htmlspecialchars($firstName . ' ' . $lastName) . '</p>
</div>

<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px 20px; margin:16px 0;">
  <p style="color:#334155; margin:0; font-size:14px;">
    <strong>💳 Payment Summary</strong><br>
    Method: ' . htmlspecialchars($paymentMethod) . '<br>
    Status: <strong>' . htmlspecialchars($paymentStatus) . '</strong><br>';

if ($discountAmount > 0) {
    $bodyContent .= '    Discount: -$' . number_format($discountAmount, 2) . '<br>';
}
if ($creditAmount > 0) {
    $bodyContent .= '    Credit Applied: -$' . number_format($creditAmount, 2) . '<br>';
}
if ($giftAmount > 0) {
    $bodyContent .= '    Gift Certificate: -$' . number_format($giftAmount, 2) . '<br>';
}

$bodyContent .= '    <strong>Amount Charged: $' . $amountPaid . '</strong>
  </p>
</div>

<p>If you need to make any changes to your booking, please contact us.</p>

<hr class="divider">
<p><small>This is an automated confirmation from ' . htmlspecialchars($appName ?? 'K2 Pickleball') . '.</small></p>
';

include __DIR__ . '/layout.php';
