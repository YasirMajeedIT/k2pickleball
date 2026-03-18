<?php

declare(strict_types=1);

namespace App\Core\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

/**
 * Email service wrapping PHPMailer.
 * Supports facility-level SMTP config with env fallback.
 */
final class Mailer
{
    private static ?self $instance = null;

    private string $host;
    private int    $port;
    private string $username;
    private string $password;
    private string $encryption;
    private string $fromAddress;
    private string $fromName;
    private bool   $enabled;

    private function __construct()
    {
        $this->host        = $_ENV['MAIL_HOST']         ?? 'smtp.gmail.com';
        $this->port        = (int) ($_ENV['MAIL_PORT']  ?? 587);
        $this->username    = $_ENV['MAIL_USERNAME']     ?? '';
        $this->password    = $_ENV['MAIL_PASSWORD']     ?? '';
        $this->encryption  = $_ENV['MAIL_ENCRYPTION']  ?? 'tls';
        $this->fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@k2pickleball.com';
        $this->fromName    = $_ENV['MAIL_FROM_NAME']    ?? 'K2 Pickleball';
        $this->enabled     = !empty($this->username) && !empty($this->password);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Send an email, optionally using facility-level SMTP config.
     * Falls back to .env SMTP when facility SMTP is not configured.
     */
    public function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $textBody = '',
        ?array $facilitySmtp = null
    ): void {
        $smtp = $this->resolveSmtp($facilitySmtp);

        if (!$smtp['enabled']) {
            $this->logFallback($toEmail, $subject, $htmlBody);
            return;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $smtp['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp['username'];
            $mail->Password   = $smtp['password'];
            $mail->SMTPSecure = $smtp['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtp['port'];
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 10;

            $mail->setFrom($smtp['from_address'], $smtp['from_name']);
            $mail->addReplyTo($smtp['from_address'], $smtp['from_name']);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));

            $mail->send();
        } catch (MailerException $e) {
            error_log('[Mailer] Failed to send email to ' . $toEmail . ': ' . $mail->ErrorInfo);
            throw new \RuntimeException('Email could not be sent: ' . $mail->ErrorInfo);
        }
    }

    /**
     * Resolve SMTP config: facility overrides env when available.
     */
    private function resolveSmtp(?array $facilitySmtp): array
    {
        if ($facilitySmtp
            && !empty($facilitySmtp['smtp_host'])
            && !empty($facilitySmtp['smtp_username'])
            && !empty($facilitySmtp['smtp_password'])
        ) {
            return [
                'host'         => $facilitySmtp['smtp_host'],
                'port'         => (int) ($facilitySmtp['smtp_port'] ?? 587),
                'username'     => $facilitySmtp['smtp_username'],
                'password'     => $facilitySmtp['smtp_password'],
                'encryption'   => $facilitySmtp['smtp_encryption'] ?? 'tls',
                'from_address' => $facilitySmtp['smtp_from_email'] ?? $this->fromAddress,
                'from_name'    => $facilitySmtp['smtp_from_name'] ?? $this->fromName,
                'enabled'      => true,
            ];
        }

        return [
            'host'         => $this->host,
            'port'         => $this->port,
            'username'     => $this->username,
            'password'     => $this->password,
            'encryption'   => $this->encryption,
            'from_address' => $this->fromAddress,
            'from_name'    => $this->fromName,
            'enabled'      => $this->enabled,
        ];
    }

    /**
     * Build an HTML email from a view template.
     * Template receives $data as extracted variables.
     */
    public function renderTemplate(string $templateName, array $data = []): string
    {
        $templatePath = dirname(__DIR__, 2) . '/Views/emails/' . $templateName . '.php';
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Email template not found: {$templateName}");
        }
        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    /**
     * In dev mode, write email to log file instead of sending.
     */
    private function logFallback(string $toEmail, string $subject, string $htmlBody): void
    {
        $logDir  = dirname(__DIR__, 2) . '/storage/logs';
        $logFile = $logDir . '/emails.log';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $entry = sprintf(
            "[%s] TO: %s | SUBJECT: %s\n---\n%s\n===\n",
            date('Y-m-d H:i:s'),
            $toEmail,
            $subject,
            strip_tags($htmlBody)
        );
        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }
}
