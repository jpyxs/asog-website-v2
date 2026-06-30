<?php

namespace App\Controllers;

use App\Models\FaqModel;
use App\Models\LandingSettingModel;

class Incubatees extends BaseController
{
    public function index()
    {
        $cohorts = $this->cohortModel->getActive();
        $allIncubatees = [];

        foreach ($cohorts as &$cohort) {
            $incubatees = $this->incubateeModel->getPublishedByCohort($cohort['name']);
            $cohort['_count'] = count($incubatees);
            $allIncubatees = array_merge($allIncubatees, $incubatees);
        }
        unset($cohort);

        $data = [
            'title'          => 'Incubatees - ASOG TBI',
            'heroSubtitle'   => 'Our Startups',
            'heroTitle'      => 'Incubatees',
            'heroDesc'       => 'Meet the startups and MSMEs building the future of food value chain management through engineering and AI.',
            'heroBg'         => 'bg-navy',
            'cohorts'        => $cohorts,
            'allIncubatees'  => $allIncubatees,
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('incubatees/index', $data)
            . view('templates/footer');
    }

    public function apply(): string
    {
        $faqModel = new FaqModel();
        $settings = new LandingSettingModel();

        $data = [
            'title'        => 'Be an Incubatee - ASOG TBI',
            'heroSubtitle' => 'Join the Program',
            'heroTitle'    => 'Be an Incubatee',
            'heroDesc'     => 'Apply to the ASOG TBI incubation program and turn your innovation into a market-ready solution.',
            'faqs'         => $faqModel->getPublished(),
            'faqTitle'     => $settings->getValue(
                LandingSettingModel::KEY_APPLY_FAQ_TITLE,
                'A few things you might be wondering.'
            ),
            'faqIntro'     => $settings->getValue(
                LandingSettingModel::KEY_APPLY_FAQ_INTRO,
                'Find quick answers about eligibility, requirements, and what happens after you submit your application.'
            ),
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('incubatees/apply', $data)
            . view('templates/footer');
    }

    public function cohort(int $num): string
    {
        $cohortLabel = 'Cohort ' . $num;

        // Cohort 1 shows all published incubatees
        $incubatees = ($num === 1)
            ? $this->incubateeModel->getPublished()
            : $this->incubateeModel->getPublishedByCohort($cohortLabel);

        $data = [
            'title'        => $cohortLabel . ' - ASOG TBI',
            'heroSubtitle' => 'Incubation Program',
            'heroTitle'    => $cohortLabel,
            'heroDesc'     => 'The startups and MSMEs in ' . $cohortLabel . ' of the ASOG TBI incubation program.',
            'incubatees'   => $incubatees,
            'cohortLabel'  => $cohortLabel,
            'cohortNum'    => $num,
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('incubatees/cohort', $data)
            . view('templates/footer');
    }

    public function applyForm(): string
    {
        $data = [
            'title'        => 'Application Form - ASOG TBI',
            'heroSubtitle' => 'Incubation Program',
            'heroTitle'    => 'Application Form',
            'heroDesc'     => 'Fill out the form below to apply for incubation at ASOG TBI.',
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('incubatees/apply_form', $data)
            . view('templates/footer');
    }

    public function applyFormStore(): \CodeIgniter\HTTP\ResponseInterface
    {
        $applicationModel = $this->applicationModel;
        
        $data = [
            'startupName'           => $this->request->getPost('startupName'),
            'startupDescription'    => $this->request->getPost('startupDescription'),
            'mainRisk'              => $this->request->getPost('mainRisk') ?? null,
            'shortTermGoals'        => $this->request->getPost('shortTermGoals') ?? null,
            'videoPresentationLink' => $this->request->getPost('videoPresentationLink'),
            'applicantName'         => $this->request->getPost('applicantName'),
            'applicantEmail'        => $this->request->getPost('applicantEmail'),
            'contactNumber'         => $this->request->getPost('contactNumber'),
            'applicationStatus'     => 'pending',
        ];

        if ($this->request->getPost('privacyAgreement') !== '1') {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['privacyAgreement' => 'Please confirm your privacy consent before continuing.']);
        }

        // Validate
        if (! $applicationModel->validate($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $applicationModel->errors());
        }

        // Handle file upload (Curriculum Vitae for team members)
        if ($this->request->getFileMultiple('teamCv')) {
            $files = $this->request->getFileMultiple('teamCv');
            $uploadedPaths = [];
            $seenFiles = [];

            if (count($files) > 10) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Only up to 10 CV files can be uploaded.');
            }

            foreach ($files as $file) {
                if ($file->isValid() && ! $file->hasMoved()) {
                    $fileKey = $file->getClientName() . '|' . $file->getSize();
                    if (isset($seenFiles[$fileKey])) {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Duplicate CV files are not allowed.');
                    }
                    $seenFiles[$fileKey] = true;

                    // Validate file type and size
                    if ($file->getSize() > 104857600) { // 100 MB
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'CV file exceeds 100 MB limit.');
                    }

                    if ($file->getMimeType() !== 'application/pdf') {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Only PDF files are accepted for CV.');
                    }

                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/applications', $newName);
                    $uploadedPaths[] = 'uploads/applications/' . $newName;
                }
            }

            if (! empty($uploadedPaths)) {
                $data['teamCvPath'] = implode(',', $uploadedPaths);
            }
        }

        // Handle Lean Canvas upload
        $leanCanvasFile = $this->request->getFile('leanCanvas');
        if ($leanCanvasFile && $leanCanvasFile->isValid() && ! $leanCanvasFile->hasMoved()) {
            $allowedMimes = [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];

            if ($leanCanvasFile->getSize() > 10485760) { // 10 MB
                return redirect()->back()
                    ->withInput()
                    ->with('errors', array_merge($applicationModel->errors(), ['leanCanvas' => 'Lean Canvas file exceeds the 10 MB limit.']));
            }

            if (! in_array($leanCanvasFile->getMimeType(), $allowedMimes)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', array_merge($applicationModel->errors(), ['leanCanvas' => 'Only PDF or Word (.docx) files are accepted for the Lean Canvas.']));
            }

            $newName = $leanCanvasFile->getRandomName();
            $leanCanvasFile->move(WRITEPATH . 'uploads/applications', $newName);
            $data['leanCanvasPath'] = 'uploads/applications/' . $newName;
        } else {
            // Lean Canvas is required
            return redirect()->back()
                ->withInput()
                ->with('errors', array_merge($applicationModel->errors(), ['leanCanvas' => 'Please upload your completed Lean Canvas (.docx or PDF).']));
        }

        // Save application
        if ($applicationModel->insert($data)) {
            // Send a copy of their responses via email
            $this->sendConfirmationEmail($data);

            return redirect()->to(site_url('apply/form/thank-you'))
                ->with('success', 'Your application has been submitted successfully!')
                ->with('application_submitted', true);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Unable to submit application. Please try again.');
    }

    // ──────────────────────────────────────────────
    // EMAIL — send applicant a copy of their responses
    // ──────────────────────────────────────────────
    private function sendConfirmationEmail(array $data): void
    {
        $email = \Config\Services::email();

        // Skip silently if SMTP is not configured
        $config = new \Config\Email();
        if (empty($config->SMTPUser) || $config->SMTPUser === 'your-email@gmail.com') {
            log_message('info', 'Confirmation email skipped — SMTP credentials not configured in .env');
            return;
        }

        $body = view('emails/application_confirmation', [
            'applicantName'         => $data['applicantName'],
            'applicantEmail'        => $data['applicantEmail'],
            'contactNumber'         => $data['contactNumber'],
            'startupName'           => $data['startupName'],
            'startupDescription'    => $data['startupDescription'],
            'mainRisk'              => $data['mainRisk'] ?? '',
            'shortTermGoals'        => $data['shortTermGoals'] ?? '',
            'videoPresentationLink' => $data['videoPresentationLink'] ?? '',
        ]);

        $email->setFrom($config->fromEmail, $config->fromName);
        $email->setTo($data['applicantEmail']);
        $email->setSubject('ASOG TBI — Application Received');
        $email->setMessage($body);
        $email->setMailType('html');

        if (! $email->send(false)) {
            log_message('error', 'Confirmation email failed: ' . $email->printDebugger(['headers']));
        } else {
            log_message('info', 'Confirmation email sent to: ' . $data['applicantEmail']);
        }
    }

    /**
     * AJAX endpoint — check if an applicant email already exists in the DB.
    * GET /apply/form/check-email?email=...
     */
    public function checkEmail(): \CodeIgniter\HTTP\ResponseInterface
    {
        $email = trim($this->request->getGet('email') ?? '');

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['exists' => false]);
        }

        $exists = $this->applicationModel->getByEmail($email) !== null;

        return $this->response->setJSON(['exists' => $exists]);
    }

    public function applyFormThankYou()
    {
        if (! session()->getFlashdata('application_submitted')) {
            return redirect()->to(site_url('apply/form'));
        }

        $data = [
            'title' => 'Application Submitted - ASOG TBI',
        ];

        return view('templates/header', $data)
            . view('incubatees/apply_thank_you', $data)
            . view('templates/footer');
    }

}
