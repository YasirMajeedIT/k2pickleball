<?php
/**
 * Email: Verify Email Address
 * Variables: $firstName, $verifyUrl, $appUrl, $recipientEmail, $expiresIn
 */
$subject = 'Verify your email address — K2 Pickleball';
$bodyContent = '
<h1>Verify your email address</h1>
<p>Hi ' . htmlspecialchars($firstName ?? 'there') . ',</p>
<p>Thanks for signing up for K2 Pickleball! To get started, please verify your email address by clicking the button below.</p>

<div style="text-align:center; margin: 32px 0;">
  <a href="' . htmlspecialchars($verifyUrl ?? '#') . '" class="btn">Verify Email Address</a>
</div>

<p>Or paste this link into your browser:</p>
<div class="url-box">' . htmlspecialchars($verifyUrl ?? '') . '</div>

<div class="warning-box">
  <p>⏱ This link expires in ' . htmlspecialchars($expiresIn ?? '24 hours') . '. If it expires, you can request a new one from the login page.</p>
</div>

<hr class="divider">
<p><small>If you did not create an account, you can safely ignore this email.</small></p>
';

include __DIR__ . '/layout.php';
