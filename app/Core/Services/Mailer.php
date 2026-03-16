<?php

declare(strict_types=1);

namespace App\Core\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

/**
 * Email service wrapping PHPMailer.
 * Reads config from .env MAIL_* keys.
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
     * Send an email.
     *
     * @param string $toEmail  Recipient email
     * @param string $toName   Recipient name
     * @param string $subject  Email subject
     * @param string $htmlBody HTML email body
     * @param string $textBody Optional plain-text fallback
     *
     * @throws \RuntimeException on failure
     */
    public function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $textBody = ''
    ): void {
        // If mail not configured, log and silently skip (dev mode)
        if (!$this->enabled) {
            $this->logFallback($toEmail, $subject, $htmlBody);
            return;
        }

        $mail = new PHPMailer(true);

        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->SMTPSecure = $this->encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->port;
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 10;

            // From
            $mail->setFrom($this->fromAddress, $this->fromName);
            $mail->addReplyTo($this->fromAddress, $this->fromName);

            // Recipients
            $mail->addAddress($toEmail, $toName);

            // Content
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
