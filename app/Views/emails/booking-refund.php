<?php
/**
 * Email: Refund Processed
 * Variables: $attendee, $class, $sessionType, $refundAmount, $appName
 */
$recipientEmail = $attendee['email'] ?? '';
$appUrl = $_ENV['APP_URL'] ?? '#';
$firstName = $attendee['first_name'] ?? 'there';
$sessionTitle = $sessionType['title'] ?? 'Session';
$scheduledAt = isset($class['scheduled_at']) ? date('l, F j, Y \a\t g:i A', strtotime($class['scheduled_at'])) : 'TBD';

$subject = $subject ?? 'Refund Processed - $' . number_format($refundAmount, 2);

$bodyContent = '
<h1>Refund Processed 💰</h1>
<p>Hi ' . htmlspecialchars($firstName) . ', a refund has been processed for your booking.</p>

<div class="info-box">
  <p><strong>📋 Session:</strong> ' . htmlspecialchars($sessionTitle) . '<br>
  <strong>📅 Date:</strong> ' . htmlspecialchars($scheduledAt) . '<br>
  <strong>💵 Refund Amount:</strong> <strong>$' . number_format($refundAmount, 2) . '</strong></p>
</div>

<p>The refund should appear in your account within 5-10 business days depending on your payment provider.</p>

<hr class="divider">
<p><small>This is an automated notice from ' . htmlspecialchars($appName ?? 'K2 Pickleball') . '.</small></p>
';

include __DIR__ . '/layout.php';
