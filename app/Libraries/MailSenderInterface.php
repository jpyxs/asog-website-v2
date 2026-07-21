<?php

namespace App\Libraries;

interface MailSenderInterface
{
    public function isConfigured(): bool;

    public function send(string $to, string $subject, string $htmlBody, ?array $replyTo = null): bool;
}
