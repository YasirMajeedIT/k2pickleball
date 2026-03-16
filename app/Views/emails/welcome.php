<?php
/**
 * Email: Welcome (sent after successful email verification)
 * Variables: $firstName, $lastName, $orgName, $portalUrl, $appUrl, $recipientEmail
 */
$subject = 'Welcome to K2 Pickleball — your account is ready!';
$bodyContent = '
<h1>Welcome aboard, ' . htmlspecialchars($firstName ?? 'there') . '! 🎉</h1>
<p>Your email is verified and your K2 Pickleball account is all set. Here\'s what you can do next:</p>

<div class="info-box">
  <p>✅ Account: <strong>' . htmlspecialchars(($firstName ?? '') . ' ' . ($lastName ?? '')) . '</strong><br>
  ' . (!empty($orgName) ? '🏢 Organization: <strong>' . htmlspecialchars($orgName) . '</strong><br>' : '') . '
  📧 Email: <strong>' . htmlspecialchars($recipientEmail ?? '') . '</strong></p>
</div>

<p><strong>Get started with your portal:</strong></p>
<ul style="color:#475569; line-height:2; padding-left:20px;">
  <li>Set up your facilities and courts</li>
  <li>Invite your team members</li>
  <li>Configure your booking schedule</li>
  <li>Connect your payment methods</li>
</ul>

<div style="text-align:center; margin: 32px 0;">
  <a href="' . htmlspecialchars($portalUrl ?? '#') . '" class="btn">Go to My Portal</a>
</div>

<hr class="divider">
<p><small>Need help getting started? Reply to this email or visit our documentation.</small></p>
';

include __DIR__ . '/layout.php';
