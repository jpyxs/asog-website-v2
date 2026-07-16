<?php

namespace App\Controllers;

class Legal extends BaseController
{
    public function appInfo(): string
    {
        $data = [
            'title'           => 'ASOG TBI Website | App Information',
            'metaDescription' => 'App information for ASOG TBI Website, including its purpose and Google data usage for OAuth verification.',
            'canonical'       => site_url('asog-tbi-website-app'),
            'bodyClass'       => 'bg-off text-dark',
        ];

        return view('templates/header', $data)
            . view('legal/app_info', $data)
            . view('templates/footer', $data);
    }

    public function privacyPolicy(): string
    {
        $data = [
            'title'           => 'Privacy Policy | ASOG TBI Website',
            'metaDescription' => 'Privacy policy for the ASOG TBI Website, including how Google user data is used for account sign-in and Gmail API email notifications.',
            'canonical'       => site_url('privacy-policy'),
            'bodyClass'       => 'bg-off text-dark',
        ];

        return view('templates/header', $data)
            . view('legal/privacy_policy', $data)
            . view('templates/footer', $data);
    }

    public function termsOfService(): string
    {
        $data = [
            'title'           => 'Terms of Service | ASOG TBI Website',
            'metaDescription' => 'Terms of Service for use of the ASOG TBI Website.',
            'canonical'       => site_url('terms-of-service'),
            'bodyClass'       => 'bg-off text-dark',
        ];

        return view('templates/header', $data)
            . view('legal/terms_of_service', $data)
            . view('templates/footer', $data);
    }
}
