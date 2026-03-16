<?php
/**
 * Email: Subscription Confirmation
 * Variables: $firstName, $planName, $billingCycle, $price, $periodEnd, $portalUrl, $appUrl, $recipientEmail
 */
$subject = 'Subscription confirmed — ' . ($planName ?? 'K2 Pickleball Plan');
$bodyContent = '
<h1>Subscription confirmed ✅</h1>
<p>Hi ' . htmlspecialchars($firstName ?? 'there') . ',</p>
<p>Your K2 Pickleball subscription has been activated successfully.</p>

<div class="info-box">
  <p>
  📦 Plan: <strong>' . htmlspecialchars($planName ?? '') . '</strong><br>
  💳 Billing: <strong>' . htmlspecialchars(ucfirst($billingCycle ?? 'monthly')) . '</strong><br>
  💰 Amount: <strong>$' . number_format((float)($price ?? 0), 2) . '</strong><br>
  📅 Next renewal: <strong>' . htmlspecialchars($periodEnd ?? '') . '</strong>
  </p>
</div>

<p>You can manage your subscription, view invoices, and upgrade your plan at any time from your portal.</p>

<div style="text-align:center; margin: 32px 0;">
  <a href="' . htmlspecialchars($portalUrl ?? '#') . '/subscription" class="btn">Manage Subscription</a>
</div>

<hr class="divider">
<p><small>Questions about billing? Reply to this email and we\'ll help you out.</small></p>
';

include __DIR__ . '/layout.php';
