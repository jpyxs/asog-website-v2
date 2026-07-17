<?php

namespace App\Libraries;

use CodeIgniter\Email\Email as CodeIgniterEmail;
use Config\Email as EmailConfig;
use Config\Services;
use Throwable;

class SmtpMailer implements MailSenderInterface
{
    public function __construct(
        private ?EmailConfig $config = null,
        private ?CodeIgniterEmail $email = null
    ) {
        $this->config ??= config('Email');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config->smtpEnabled)
            && trim($this->config->fromEmail) !== ''
            && trim($this->config->SMTPHost) !== ''
            && trim($this->config->SMTPUser) !== ''
            && trim($this->config->SMTPPass) !== ''
            && $this->config->SMTPPort > 0;
    }

    public function send(string $to, string $subject, string $htmlBody, ?array $replyTo = null): bool
    {
        if (! $this->isConfigured()) {
            log_message('info', 'SMTP email skipped - SMTP fallback is not configured.');
            return false;
        }

        $email = $this->email ?? Services::email($this->config, false);
        $email->clear(true);
        $email->setFrom($this->config->fromEmail, $this->config->fromName);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($htmlBody);
        $email->setMailType('html');

        if ($replyTo !== null && ! empty($replyTo['email'])) {
            $email->setReplyTo((string) $replyTo['email'], (string) ($replyTo['name'] ?? ''));
        }

        try {
            if ($email->send(false)) {
                return true;
            }

            log_message('error', 'SMTP email failed: ' . $this->safeDebug($email->printDebugger(['headers', 'subject'])));
            return false;
        } catch (Throwable $e) {
            log_message('error', 'SMTP email exception: ' . $this->safeDebug($e->getMessage()));
            return false;
        } finally {
            $email->clear(true);
        }
    }

    private function safeDebug(string $debug): string
    {
        $values = [
            $this->config->SMTPPass,
            $this->config->SMTPUser,
        ];

        foreach ($values as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                $debug = str_replace($value, '[redacted]', $debug);
            }
        }

        return preg_replace('/(password|pass|user|username|auth)["\':=\s]+[^,\s"<]+/i', '$1=[redacted]', $debug)
            ?? 'Unknown SMTP error';
    }
}
