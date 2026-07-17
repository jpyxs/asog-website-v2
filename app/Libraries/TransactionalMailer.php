<?php

namespace App\Libraries;

class TransactionalMailer implements MailSenderInterface
{
    public function __construct(
        private ?MailSenderInterface $gmail = null,
        private ?MailSenderInterface $smtp = null
    ) {
        $this->gmail ??= new GmailMailer();
        $this->smtp ??= new SmtpMailer();
    }

    public function isConfigured(): bool
    {
        return $this->gmail->isConfigured() || $this->smtp->isConfigured();
    }

    public function send(string $to, string $subject, string $htmlBody, ?array $replyTo = null): bool
    {
        if ($this->gmail->isConfigured()) {
            if ($this->gmail->send($to, $subject, $htmlBody, $replyTo)) {
                return true;
            }

            log_message('error', 'Gmail API email failed; attempting SMTP fallback.');
        } else {
            log_message('info', 'Gmail API email skipped by transactional mailer - Gmail API is not configured.');
        }

        if ($this->smtp->isConfigured()) {
            if ($this->smtp->send($to, $subject, $htmlBody, $replyTo)) {
                return true;
            }

            log_message('error', 'SMTP fallback email failed.');
        } else {
            log_message('info', 'SMTP fallback email skipped - SMTP fallback is not configured.');
        }

        return false;
    }
}
