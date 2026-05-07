<?php

namespace App\Controllers;

class Contact extends BaseController
{
    public function index(): string
    {
        $data = [
            'title'        => 'Contact - ASOG TBI',
            'heroSubtitle' => 'Reach Out',
            'heroTitle'    => 'Contact Us',
            'heroDesc'     => 'Have questions or want to collaborate? Get in touch with the ASOG TBI team.',
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('contact/index', $data)
            . view('templates/footer');
    }

    /**
     * Handle contact form submission.
     *
     * 1. Validate input
     * 2. Persist to `contact_messages` table
     * 3. Send email notification to admin (best-effort)
     */
    
    public function send()
    {
        $data = [
            'name'    => $this->request->getPost('name'),
            'email'   => $this->request->getPost('email'),
            'message' => $this->request->getPost('message'),
        ];

        if (! $this->contactModel->validate($data)) {
            setToast('error', 'Please fill in all fields correctly.');
            return redirect()->back()->withInput();
        }

        if (! $this->contactModel->insert($data)) {
            log_message('error', 'Contact message DB insert failed.');
            setToast('error', 'Something went wrong. Please try again.');
            return redirect()->back()->withInput();
        }

        $this->notifyAdmin($data);

        setToast('success', 'Your message has been sent! We\'ll get back to you soon.');
        return redirect()->to(site_url('contact'));
    }

    // ──────────────────────────────────────────────
    // EMAIL — notify admin of new contact message
    // ──────────────────────────────────────────────
    private function notifyAdmin(array $data): void
    {
        $emailService = \Config\Services::email();
        $config       = new \Config\Email();

        // Skip silently when SMTP is not configured
        if (empty($config->SMTPUser) || $config->SMTPUser === 'your-email@gmail.com') {
            log_message('info', 'Contact notification skipped — SMTP not configured.');
            return;
        }

        $body = view('emails/contact_notification', [
            'name'    => $data['name'],
            'email'   => $data['email'],
            'message' => $data['message'],
            'sentAt'  => date('F j, Y \a\t g:i A'),
        ]);

        $emailService->setFrom($config->fromEmail, $config->fromName);
        $emailService->setTo($config->SMTPUser);          // send to the admin's own inbox
        $emailService->setReplyTo($data['email'], $data['name']);
        $emailService->setSubject('ASOG TBI — New Contact Message from ' . $data['name']);
        $emailService->setMessage($body);
        $emailService->setMailType('html');

        if (! $emailService->send(false)) {
            log_message('error', 'Contact notification email failed: ' . $emailService->printDebugger(['headers']));
        } else {
            log_message('info', 'Contact notification sent for: ' . $data['email']);
        }
    }
}