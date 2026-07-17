<?php

use CodeIgniter\Test\CIUnitTestCase;
use Config\Email as EmailConfig;

/**
 * @internal
 */
final class EmailConfigTest extends CIUnitTestCase
{
    public function testBlankSmtpSenderFallsBackToGmailSender(): void
    {
        $this->withEnv([
            'smtp.enabled' => 'true',
            'smtp.fromEmail' => '',
            'smtp.fromName' => '',
            'smtp.host' => 'smtp.example.test',
            'smtp.user' => 'smtp-user',
            'smtp.pass' => 'smtp-pass',
            'smtp.port' => '2525',
            'smtp.crypto' => 'ssl',
            'smtp.timeout' => '15',
            'gmailApi.senderEmail' => 'gmail-sender@example.test',
            'gmailApi.senderName' => 'Gmail Sender',
        ], function (): void {
            $config = new EmailConfig();

            $this->assertTrue($config->smtpEnabled);
            $this->assertSame('gmail-sender@example.test', $config->fromEmail);
            $this->assertSame('Gmail Sender', $config->fromName);
            $this->assertSame('smtp.example.test', $config->SMTPHost);
            $this->assertSame('smtp-user', $config->SMTPUser);
            $this->assertSame('smtp-pass', $config->SMTPPass);
            $this->assertSame(2525, $config->SMTPPort);
            $this->assertSame('ssl', $config->SMTPCrypto);
            $this->assertSame(15, $config->SMTPTimeout);
        });
    }

    public function testCompleteSmtpSenderOverridesGmailSender(): void
    {
        $this->withEnv([
            'smtp.enabled' => 'true',
            'smtp.fromEmail' => 'smtp-sender@example.test',
            'smtp.fromName' => 'SMTP Sender',
            'smtp.host' => 'smtp.example.test',
            'smtp.user' => 'smtp-user',
            'smtp.pass' => 'smtp-pass',
            'smtp.port' => '587',
            'smtp.crypto' => 'tls',
            'smtp.timeout' => '20',
            'gmailApi.senderEmail' => 'gmail-sender@example.test',
            'gmailApi.senderName' => 'Gmail Sender',
        ], function (): void {
            $config = new EmailConfig();

            $this->assertSame('smtp-sender@example.test', $config->fromEmail);
            $this->assertSame('SMTP Sender', $config->fromName);
            $this->assertSame(587, $config->SMTPPort);
            $this->assertSame('tls', $config->SMTPCrypto);
        });
    }

    /**
     * @param array<string, string> $values
     */
    private function withEnv(array $values, callable $callback): void
    {
        $previous = [];

        foreach ($values as $key => $value) {
            $previous[$key] = [
                'envExists' => array_key_exists($key, $_ENV),
                'serverExists' => array_key_exists($key, $_SERVER),
                'env' => $_ENV[$key] ?? null,
                'server' => $_SERVER[$key] ?? null,
            ];
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        try {
            $callback();
        } finally {
            foreach ($previous as $key => $state) {
                if ($state['envExists']) {
                    $_ENV[$key] = $state['env'];
                } else {
                    unset($_ENV[$key]);
                }

                if ($state['serverExists']) {
                    $_SERVER[$key] = $state['server'];
                } else {
                    unset($_SERVER[$key]);
                }
            }
        }
    }
}
