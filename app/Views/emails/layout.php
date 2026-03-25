<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($subject ?? 'K2 Pickleball') ?></title>
<style>
  body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #334155; }
  .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
  .header { background: linear-gradient(135deg, #0b1629 0%, #162844 50%, #1e3658 100%); padding: 36px 40px; text-align: center; }
  .header-logo { display: inline-flex; align-items: center; gap: 12px; text-decoration: none; }
  .header-icon { width: 44px; height: 44px; background: rgba(212,175,55,0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; }
  .header-name { font-size: 22px; font-weight: 700; color: #ffffff; }
  .header-accent { color: #d4af37; }
  .body { padding: 40px; }
  .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
  .footer p { margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.6; }
  .footer a { color: #d4af37; text-decoration: none; }
  h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 8px; }
  p { font-size: 15px; line-height: 1.7; color: #475569; margin: 0 0 16px; }
  .btn { display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #f0d878, #d4af37, #b8952d); color: #0b1629 !important; text-decoration: none; border-radius: 10px; font-size: 15px; font-weight: 600; margin: 8px 0 24px; }
  .btn:hover { background: #b8952d; }
  .divider { border: none; border-top: 1px solid #e2e8f0; margin: 24px 0; }
  .info-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px 20px; margin: 16px 0; }
  .info-box p { margin: 0; color: #166534; font-size: 14px; }
  .warning-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; padding: 16px 20px; margin: 16px 0; }
  .warning-box p { margin: 0; color: #9a3412; font-size: 14px; }
  small { font-size: 13px; color: #94a3b8; }
  .url-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; word-break: break-all; font-family: monospace; font-size: 13px; color: #475569; margin: 8px 0 16px; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div class="header-logo">
      <div class="header-icon">
        <svg width="24" height="24" viewBox="0 0 40 40" fill="#d4af37">
          <path d="M20 2L23 14L35 14L25 22L28 34L20 27L12 34L15 22L5 14L17 14Z"/>
        </svg>
      </div>
      <span class="header-name">K2 <span class="header-accent">Pickleball</span></span>
    </div>
  </div>

  <div class="body">
    <?= $bodyContent ?? '' ?>
  </div>

  <div class="footer">
    <p>
      © <?= date('Y') ?> K2 Pickleball SaaS Platform. All rights reserved.<br>
      <a href="<?= htmlspecialchars($appUrl ?? '#') ?>">Visit Website</a> &nbsp;·&nbsp;
      <a href="<?= htmlspecialchars($appUrl ?? '#') ?>/portal">My Portal</a>
    </p>
    <p style="margin-top:8px;">This email was sent to <strong><?= htmlspecialchars($recipientEmail ?? '') ?></strong></p>
  </div>
</div>
</body>
</html>
