<?php
/**
 * Email: Credit Issued
 * Variables: $attendee, $class, $sessionType, $creditCode, $creditAmount, $appName
 */
$recipientEmail = $attendee['email'] ?? '';
$appUrl = $_ENV['APP_URL'] ?? '#';
$firstName = $attendee['first_name'] ?? 'there';
$sessionTitle = $sessionType['title'] ?? 'Session';

$subject = $subject ?? 'Credit Issued - $' . number_format($creditAmount, 2);

$bodyContent = '
<h1>Credit Code Issued 🎁</h1>
<p>Hi ' . htmlspecialchars($firstName) . ', a credit code has been issued to your account.</p>

<div class="info-box">
  <p><strong>🎫 Credit Code:</strong> <span style="font-family:monospace;font-size:18px;font-weight:700;color:#047857;">' . htmlspecialchars($creditCode) . '</span><br>
  <strong>💵 Amount:</strong> $' . number_format($creditAmount, 2) . '<br>
  <strong>📋 From Session:</strong> ' . htmlspecialchars($sessionTitle) . '</p>
</div>

<div class="warning-box">
  <p><strong>Important:</strong> This credit code is linked to your player account and can only be used by you. Present this code at the time of your next booking.</p>
</div>

<hr class="divider">
<p><small>This is an automated notice from ' . htmlspecialchars($appName ?? 'K2 Pickleball') . '.</small></p>
';

include __DIR__ . '/layout.php';
