<?php
/**
 * Email: Invitation — set your password and activate your account
 * Variables: $firstName, $inviteUrl, $appUrl, $recipientEmail, $expiresIn
 */
$subject = "You're invited to K2 Pickleball!";
$bodyContent = '
<h1>You\'ve been invited!</h1>
<p>Hi ' . htmlspecialchars($firstName ?? 'there') . ',</p>
<p>An administrator has created an account for you at <strong>K2 Pickleball</strong>. To get started, click the button below to set your password and activate your account.</p>

<div style="text-align:center; margin: 32px 0;">
  <a href="' . htmlspecialchars($inviteUrl ?? '#') . '" class="btn">Set Password &amp; Activate</a>
</div>

<p>Or paste this link into your browser:</p>
<div class="url-box">' . htmlspecialchars($inviteUrl ?? '') . '</div>

<div class="warning-box">
  <p>⏱ This link expires in ' . htmlspecialchars($expiresIn ?? '24 hours') . '. If it expires, please ask your administrator to resend the invitation.</p>
</div>

<hr class="divider">
<p><small>If you weren\'t expecting this invitation, you can safely ignore this email.</small></p>
';

include __DIR__ . '/layout.php';
