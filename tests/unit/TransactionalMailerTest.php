<?php

use App\Libraries\MailSenderInterface;
use App\Libraries\TransactionalMailer;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TransactionalMailerTest extends CIUnitTestCase
{
    public function testGmailSuccessDoesNotAttemptSmtp(): void
    {
        $gmail = new FakeMailSender(true, true);
        $smtp = new FakeMailSender(true, true);

        $mailer = new TransactionalMailer($gmail, $smtp);

        $this->assertTrue($mailer->send('to@example.test', 'Subject', '<p>Body</p>'));
        $this->assertSame(1, $gmail->sendCalls);
        $this->assertSame(0, $smtp->sendCalls);
    }

    public function testSmtpIsAttemptedWhenGmailUnavailable(): void
    {
        $gmail = new FakeMailSender(false, false);
        $smtp = new FakeMailSender(true, true);

        $mailer = new TransactionalMailer($gmail, $smtp);

        $this->assertTrue($mailer->send('to@example.test', 'Subject', '<p>Body</p>'));
        $this->assertSame(0, $gmail->sendCalls);
        $this->assertSame(1, $smtp->sendCalls);
    }

    public function testSmtpSuccessAfterGmailFailureReturnsTrue(): void
    {
        $gmail = new FakeMailSender(true, false);
        $smtp = new FakeMailSender(true, true);

        $mailer = new TransactionalMailer($gmail, $smtp);

        $this->assertTrue($mailer->send('to@example.test', 'Subject', '<p>Body</p>'));
        $this->assertSame(1, $gmail->sendCalls);
        $this->assertSame(1, $smtp->sendCalls);
    }

    public function testReturnsFalseWhenNoSenderCanSend(): void
    {
        $gmail = new FakeMailSender(false, false);
        $smtp = new FakeMailSender(false, false);

        $mailer = new TransactionalMailer($gmail, $smtp);

        $this->assertFalse($mailer->send('to@example.test', 'Subject', '<p>Body</p>'));
        $this->assertSame(0, $gmail->sendCalls);
        $this->assertSame(0, $smtp->sendCalls);
    }
}

final class FakeMailSender implements MailSenderInterface
{
    public int $sendCalls = 0;

    public function __construct(
        private bool $configured,
        private bool $sendResult
    ) {
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function send(string $to, string $subject, string $htmlBody, ?array $replyTo = null): bool
    {
        $this->sendCalls++;

        return $this->sendResult;
    }
}
