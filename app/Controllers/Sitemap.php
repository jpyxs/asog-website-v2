<?php

namespace App\Controllers;

use App\Models\PostModel;

class Sitemap extends BaseController
{
    public function index()
    {
        $baseUrl = rtrim(base_url(), '/');

        $urls = [
            ['loc' => $baseUrl . '/', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/about', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/programs', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/services', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/facilities', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/incubatees', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/news', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/organization', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/contact', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/asog-tbi-website-app', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/privacy-policy', 'lastmod' => date('Y-m-d')],
            ['loc' => $baseUrl . '/terms-of-service', 'lastmod' => date('Y-m-d')],
        ];

        $postModel = new PostModel();
        $posts = $postModel->getPublished();

        foreach ($posts as $post) {
            if (empty($post['slug'])) {
                continue;
            }

            $lastmod = $post['updatedAt'] ?? $post['publishedAt'] ?? date('Y-m-d H:i:s');

            $urls[] = [
                'loc' => $baseUrl . '/news/' . $post['slug'],
                'lastmod' => date('Y-m-d', strtotime((string) $lastmod)),
            ];
        }

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($urls as $url) {
            $urlNode = $xml->addChild('url');
            $urlNode->addChild('loc', htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8'));
            $urlNode->addChild('lastmod', $url['lastmod']);
        }

        return $this->response
            ->setContentType('application/xml; charset=UTF-8')
            ->setBody($xml->asXML() ?: '');
    }
}
