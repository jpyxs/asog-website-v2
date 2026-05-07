<?php

namespace App\Controllers;

class News extends BaseController
{
    public function index(): string
    {
        $category = $this->request->getGet('category');
        $validCategories = ['news', 'features', 'opinions'];

        if ($category && in_array($category, $validCategories)) {
            $allPosts = $this->postModel->getByCategory($category);
            $activeCategory = $category;
        } else {
            $allPosts = $this->postModel->getPublished();
            $activeCategory = '';
        }

        $latestPost = ! empty($allPosts) ? array_shift($allPosts) : null;

        $data = [
            'title'           => 'News & Insights - ASOG TBI',
            'latestPost'      => $latestPost,
            'posts'           => $allPosts,
            'activeCategory'  => $activeCategory,
        ];

        $data['heroSubtitle'] = 'Stay Updated';
        $data['heroTitle']    = 'News & Insights';
        $data['heroDesc']     = 'The latest events, features, and stories from ASOG Technology Business Incubator.';

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('news/list', $data)
            . view('templates/footer');
    }

    /**
     * Display a single post by slug.
     */
    public function show(string $slug): string
    {
        $post = $this->postModel->getBySlug($slug);

        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Post not found.');
        }

        $rawContent = trim((string) ($post['content'] ?? ''));
        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($rawContent, ENT_QUOTES, 'UTF-8'))));
        $metaDescription = $plainContent !== ''
            ? (mb_strlen($plainContent) > 160 ? mb_substr($plainContent, 0, 160) . '…' : $plainContent)
            : 'Latest news, feature stories, and updates from ASOG TBI.';

        $metaImage = ! empty($post['imagePath']) ? base_url($post['imagePath']) : '';
        $metaImageAlt = trim((string) ($post['title'] ?? 'ASOG TBI story'));

        $data = [
            'title'       => trim((string) ($post['title'] ?? '')) . ' - ASOG TBI',
            'post'        => $post,
            'latestPosts' => $this->postModel->getPublished(10),
            'metaDescription' => $metaDescription,
            'metaImage'       => $metaImage,
            'metaImageAlt'    => $metaImageAlt,
            'metaType'        => 'article',
            'canonical'       => current_url(),
        ];

        return view('templates/header', $data)
            . view('news/detail', $data)
            . view('templates/footer');
    }
}