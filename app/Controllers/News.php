<?php

namespace App\Controllers;

class News extends BaseController
{
    public function index(): string
    {
        $validCategories = ['news', 'features', 'opinions'];
        $validSorts      = ['newest', 'oldest'];

        $category = (string) ($this->request->getGet('category') ?? '');
        $sort     = (string) ($this->request->getGet('sort') ?? 'newest');
        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));

        if (!in_array($category, $validCategories, true)) {
            $category = '';
        }
        if (!in_array($sort, $validSorts, true)) {
            $sort = 'newest';
        }

        $result   = $this->postModel->getPublishedPage($category, $sort, 10, $page);
        $allPosts = $result['posts'];

        $latestPost = null;
        if ($page === 1 && $category === '' && $sort === 'newest' && !empty($allPosts)) {
            $latestPost = array_shift($allPosts);
        }

        $data = [
            'title'          => 'News & Insights - ASOG TBI',
            'latestPost'     => $latestPost,
            'posts'          => $allPosts,
            'activeCategory' => $category,
            'activeSort'     => $sort,
            'currentPage'    => $page,
            'totalPages'     => $result['pages'],
            'totalPosts'     => $result['total'],
            'heroSubtitle'   => 'Stay Updated',
            'heroTitle'      => 'News & Insights',
            'heroDesc'       => 'The latest events, features, and stories from ASOG Technology Business Incubator.',
        ];

        return view('templates/header', $data)
            . view('templates/page_hero', $data)
            . view('news/list', $data)
            . view('templates/footer');
    }

    /**
     * Display a single post by slug.
     */
    public function show(string $slug)
    {
        $post = $this->postModel->getBySlug($slug);

        if (! $post) {
            $redirectPost = $this->postModel->resolveSlugRedirect($slug);

            if ($redirectPost) {
                return redirect()->to(site_url('news/' . $redirectPost['slug']))->setStatusCode(301);
            }

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