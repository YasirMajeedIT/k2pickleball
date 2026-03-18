<?php
/**
 * Email: Reservation Confirmation
 * Variables: $attendee, $class, $sessionType, $appName
 */
$recipientEmail = $attendee['email'] ?? '';
$appUrl = $_ENV['APP_URL'] ?? '#';
$firstName = $attendee['first_name'] ?? 'there';
$lastName = $attendee['last_name'] ?? '';
$sessionTitle = $sessionType['title'] ?? 'Session';
$scheduledAt = isset($class['scheduled_at']) ? date('l, F j, Y \a\t g:i A', strtotime($class['scheduled_at'])) : 'TBD';
$duration = ($sessionType['duration'] ?? 60) . ' minutes';
$quoteAmount = number_format((float)($attendee['quote_amount'] ?? 0), 2);

$subject = $subject ?? 'Reservation Confirmation - ' . htmlspecialchars($sessionTitle);

$bodyContent = '
<h1>Spot Reserved! 📋</h1>
<p>Hi ' . htmlspecialchars($firstName) . ', your spot has been reserved. Please pay in person to complete your registration.</p>

<div class="info-box">
  <p><strong>📋 Session:</strong> ' . htmlspecialchars($sessionTitle) . '<br>
  <strong>📅 Date & Time:</strong> ' . htmlspecialchars($scheduledAt) . '<br>
  <strong>⏱ Duration:</strong> ' . htmlspecialchars($duration) . '<br>
  <strong>👤 Name:</strong> ' . htmlspecialchars($firstName . ' ' . $lastName) . '</p>
</div>

<div style="background:#fefce8; border:1px solid #fde047; border-radius:10px; padding:16px 20px; margin:16px 0;">
  <p style="color:#854d0e; margin:0; font-size:14px;">
    <strong>⏳ Payment Pending</strong><br>
    Amount Due: <strong>$' . $quoteAmount . '</strong><br>
    Please pay at the facility to confirm your registration.
  </p>
</div>

<p>If you need to make any changes to your reservation, please contact us.</p>

<hr class="divider">
<p><small>This is an automated confirmation from ' . htmlspecialchars($appName ?? 'K2 Pickleball') . '.</small></p>
';

include __DIR__ . '/layout.php';
