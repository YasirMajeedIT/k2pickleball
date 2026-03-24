<?php

declare(strict_types=1);

namespace App\Core\Services;

/**
 * Lightweight Slack webhook integration.
 * Sends messages to a configured Slack channel via incoming webhook.
 */
final class SlackNotifier
{
    private static ?self $instance = null;

    private ?string $webhookUrl;

    private function __construct()
    {
        $this->webhookUrl = $_ENV['SLACK_WEBHOOK_URL'] ?? null;
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Send a message to the configured Slack webhook.
     *
     * @param string $text  Plain-text fallback / notification text
     * @param array  $blocks  Optional Block Kit blocks for rich formatting
     * @return bool  True if sent successfully (or webhook not configured)
     */
    public function send(string $text, array $blocks = []): bool
    {
        if (empty($this->webhookUrl)) {
            // Log instead of failing — Slack is not critical path
            error_log('[SlackNotifier] SLACK_WEBHOOK_URL not configured, skipping notification');
            return false;
        }

        $payload = ['text' => $text];
        if (!empty($blocks)) {
            $payload['blocks'] = $blocks;
        }

        $ch = curl_init($this->webhookUrl);
        if ($ch === false) {
            error_log('[SlackNotifier] Failed to initialize cURL');
            return false;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            error_log("[SlackNotifier] Failed to send: HTTP {$httpCode}, error: {$error}");
            return false;
        }

        return true;
    }

    /**
     * Send a consultation notification with rich formatting.
     */
    public function sendConsultationNotification(array $consultation): bool
    {
        $type = ucwords(str_replace('_', ' ', $consultation['consultation_type'] ?? 'general'));
        $name = ($consultation['first_name'] ?? '') . ' ' . ($consultation['last_name'] ?? '');
        $email = $consultation['email'] ?? '—';
        $phone = $consultation['phone'] ?? '—';
        $stage = ucwords(str_replace('_', ' ', $consultation['facility_stage'] ?? '—'));
        $location = $consultation['planned_location'] ?? '—';
        $courts = $consultation['number_of_courts'] ?? '—';
        $software = $consultation['software_interest'] ?? '—';
        $message = $consultation['message'] ?? '—';

        $text = "New {$type} consultation request from {$name} ({$email})";

        $blocks = [
            [
                'type' => 'header',
                'text' => ['type' => 'plain_text', 'text' => "📋 New Consultation Request", 'emoji' => true],
            ],
            [
                'type' => 'section',
                'fields' => [
                    ['type' => 'mrkdwn', 'text' => "*Type:*\n{$type}"],
                    ['type' => 'mrkdwn', 'text' => "*Name:*\n{$name}"],
                    ['type' => 'mrkdwn', 'text' => "*Email:*\n{$email}"],
                    ['type' => 'mrkdwn', 'text' => "*Phone:*\n{$phone}"],
                ],
            ],
            [
                'type' => 'section',
                'fields' => [
                    ['type' => 'mrkdwn', 'text' => "*Facility Stage:*\n{$stage}"],
                    ['type' => 'mrkdwn', 'text' => "*Location:*\n{$location}"],
                    ['type' => 'mrkdwn', 'text' => "*Courts:*\n{$courts}"],
                    ['type' => 'mrkdwn', 'text' => "*Software Interest:*\n{$software}"],
                ],
            ],
            [
                'type' => 'section',
                'text' => ['type' => 'mrkdwn', 'text' => "*Message:*\n{$message}"],
            ],
            ['type' => 'divider'],
        ];

        return $this->send($text, $blocks);
    }

    /**
     * Send a contact form notification with rich formatting.
     */
    public function sendContactNotification(array $contact): bool
    {
        $subject = ucwords(str_replace('_', ' ', $contact['subject'] ?? 'other'));
        $name    = ($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? '');
        $email   = $contact['email'] ?? '—';
        $message = $contact['message'] ?? '—';

        $text = "New contact message ({$subject}) from {$name} ({$email})";

        $blocks = [
            [
                'type' => 'header',
                'text' => ['type' => 'plain_text', 'text' => "\u2709\uFE0F New Contact Submission", 'emoji' => true],
            ],
            [
                'type' => 'section',
                'fields' => [
                    ['type' => 'mrkdwn', 'text' => "*Subject:*\n{$subject}"],
                    ['type' => 'mrkdwn', 'text' => "*Name:*\n{$name}"],
                    ['type' => 'mrkdwn', 'text' => "*Email:*\n{$email}"],
                ],
            ],
            [
                'type' => 'section',
                'text' => ['type' => 'mrkdwn', 'text' => "*Message:*\n{$message}"],
            ],
            ['type' => 'divider'],
        ];

        return $this->send($text, $blocks);
    }
}
