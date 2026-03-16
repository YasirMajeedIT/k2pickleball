<?php
/**
 * Email: Forgot Password / Reset Link
 * Variables: $firstName, $resetUrl, $appUrl, $recipientEmail, $expiresIn
 */
$subject = 'Reset your K2 Pickleball password';
$bodyContent = '
<h1>Reset your password</h1>
<p>Hi ' . htmlspecialchars($firstName ?? 'there') . ',</p>
<p>We received a request to reset the password for your K2 Pickleball account. Click the button below to set a new password.</p>

<div style="text-align:center; margin: 32px 0;">
  <a href="' . htmlspecialchars($resetUrl ?? '#') . '" class="btn">Reset Password</a>
</div>

<p>Or paste this link into your browser:</p>
<div class="url-box">' . htmlspecialchars($resetUrl ?? '') . '</div>

<div class="warning-box">
  <p>⏱ This link expires in ' . htmlspecialchars($expiresIn ?? '1 hour') . '. If it has expired, please request a new reset link.</p>
</div>

<hr class="divider">
<p><small>If you did not request a password reset, you can safely ignore this email. Your password will not be changed.</small></p>
';

include __DIR__ . '/layout.php';
