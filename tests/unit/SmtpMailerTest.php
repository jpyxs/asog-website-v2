<?php

use App\Libraries\SmtpMailer;
use CodeIgniter\Email\Email as CodeIgniterEmail;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Email as EmailConfig;

/**
 * @internal
 */
final class SmtpMailerTest extends CIUnitTestCase
{
    public function testReplyToIsAppliedWhenSendingContactNotification(): void
    {
        $config = new EmailConfig();
        $config->smtpEnabled = true;
        $config->fromEmail = 'sender@example.test';
        $config->fromName = 'ASOG TBI';
        $config->SMTPHost = 'smtp.example.test';
        $config->SMTPUser = 'smtp-user';
        $config->SMTPPass = 'smtp-pass';
        $config->SMTPPort = 587;

        $email = $this->createMock(CodeIgniterEmail::class);
        $email->expects($this->exactly(2))
            ->method('clear')
            ->with(true)
            ->willReturnSelf();
        $email->expects($this->once())
            ->method('setFrom')
            ->with('sender@example.test', 'ASOG TBI')
            ->willReturnSelf();
        $email->expects($this->once())
            ->method('setTo')
            ->with('admin@example.test')
            ->willReturnSelf();
        $email->expects($this->once())
            ->method('setSubject')
            ->with('Subject')
            ->willReturnSelf();
        $email->expects($this->once())
            ->method('setMessage')
            ->with('<p>Body</p>')
            ->willReturnSelf();
        $email->expects($this->once())
            ->method('setMailType')
            ->with('html')
            ->willReturnSelf();
        $email->expects($this->once())
            ->method('setReplyTo')
            ->with('visitor@example.test', 'Visitor')
            ->willReturnSelf();
        $email->expects($this->once())
            ->method('send')
            ->with(false)
            ->willReturn(true);

        $mailer = new SmtpMailer($config, $email);

        $this->assertTrue($mailer->send('admin@example.test', 'Subject', '<p>Body</p>', [
            'email' => 'visitor@example.test',
            'name' => 'Visitor',
        ]));
    }
}
