<?php
$activeCategory = $activeCategory ?? '';
$activeSort     = $activeSort ?? 'newest';
$currentPage    = (int) ($currentPage ?? 1);
$totalPages     = (int) ($totalPages ?? 1);
$totalPosts     = (int) ($totalPosts ?? 0);
$categories     = $categories ?? \Config\PostCategories::all();

$qp = [];
if ($activeCategory !== '') $qp['category'] = $activeCategory;
if ($activeSort !== 'newest') $qp['sort']    = $activeSort;
$qBase   = site_url('news') . ($qp ? '?' . http_build_query($qp) . '&' : '?');
$pageUrl = fn(int $p): string => $qBase . 'page=' . $p;

$catUrl = function(string $cat) use ($activeSort): string {
    $p = [];
    if ($cat !== '') $p['category'] = $cat;
    if ($activeSort !== 'newest') $p['sort'] = $activeSort;
    return site_url('news') . ($p ? '?' . http_build_query($p) : '');
};

$sortUrl = function(string $srt) use ($activeCategory): string {
    $p = [];
    if ($activeCategory !== '') $p['category'] = $activeCategory;
    if ($srt !== 'newest') $p['sort'] = $srt;
    return site_url('news') . ($p ? '?' . http_build_query($p) : '');
};
?>
<section class="relative bg-off py-16 md:py-24 px-6 md:px-10 lg:px-14">
    <div class="max-w-[1100px] mx-auto relative z-[2]">

        <div class="flex flex-wrap items-center gap-3 mb-10">

            <label
                class="relative flex items-center gap-2 bg-white border rounded-sm pl-3 pr-8 py-2 cursor-pointer transition-colors duration-200 hover:border-dark/25 focus-within:border-dark/30"
                style="border-color:rgba(2,13,24,.12)">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" style="color:rgba(2,13,24,.28);flex-shrink:0"
                    aria-hidden="true">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                </svg>
                <span class="text-[.5rem] font-bold tracking-[.18em] uppercase select-none"
                    style="color:rgba(2,13,24,.28);white-space:nowrap">Filter</span>
                <div class="w-px h-3 shrink-0" style="background:rgba(2,13,24,.08)"></div>
                <select id="newsFilter" onchange="window.location=this.value"
                    class="appearance-none bg-transparent text-[.58rem] font-semibold tracking-[.1em] uppercase focus:outline-none cursor-pointer border-0 min-w-[80px]"
                    style="color:rgba(2,13,24,.7)">
                    <option value="<?= $catUrl('') ?>" <?= $activeCategory === '' ? 'selected' : '' ?>>All Posts</option>
                    <?php foreach ($categories as $catVal => $catLabel): ?>
                    <option value="<?= $catUrl($catVal) ?>" <?= $activeCategory === $catVal ? 'selected' : '' ?>><?= $catLabel ?></option>
                    <?php endforeach; ?>
                </select>
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:rgba(2,13,24,.3);pointer-events:none"
                    aria-hidden="true">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </label>

            <label
                class="relative flex items-center gap-2 bg-white border rounded-sm pl-3 pr-8 py-2 cursor-pointer transition-colors duration-200 hover:border-dark/25 focus-within:border-dark/30"
                style="border-color:rgba(2,13,24,.12)">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" style="color:rgba(2,13,24,.28);flex-shrink:0"
                    aria-hidden="true">
                    <path d="M3 9l4-4 4 4M7 5v14M21 15l-4 4-4-4M17 19V5" />
                </svg>
                <span class="text-[.5rem] font-bold tracking-[.18em] uppercase select-none"
                    style="color:rgba(2,13,24,.28);white-space:nowrap">Sort</span>
                <div class="w-px h-3 shrink-0" style="background:rgba(2,13,24,.08)"></div>
                <select id="newsSort" onchange="window.location=this.value"
                    class="appearance-none bg-transparent text-[.58rem] font-semibold tracking-[.1em] uppercase focus:outline-none cursor-pointer border-0 min-w-[80px]"
                    style="color:rgba(2,13,24,.7)">
                    <option value="<?= $sortUrl('newest') ?>" <?= $activeSort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                    <option value="<?= $sortUrl('oldest') ?>" <?= $activeSort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                </select>
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:rgba(2,13,24,.3);pointer-events:none"
                    aria-hidden="true">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </label>

            <?php if ($activeCategory !== ''): ?>
            <div class="flex items-center gap-2 px-3 py-2 rounded-sm" style="background:rgba(3,53,90,.06)">
                <span class="text-[.56rem] font-semibold tracking-[.1em] uppercase"
                    style="color:#03355a"><?= esc($categories[$activeCategory] ?? ucfirst($activeCategory)) ?></span>
                <a href="<?= $catUrl('') ?>" class="no-underline transition-colors" style="color:rgba(2,13,24,.3)"
                    title="Clear filter">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </a>
            </div>
            <?php endif; ?>

            <?php if ($activeSort !== 'newest'): ?>
            <div class="flex items-center gap-2 px-3 py-2 rounded-sm" style="background:rgba(3,53,90,.06)">
                <span class="text-[.56rem] font-semibold tracking-[.1em] uppercase"
                    style="color:#03355a">Oldest First</span>
                <a href="<?= $sortUrl('newest') ?>" class="no-underline transition-colors" style="color:rgba(2,13,24,.3)"
                    title="Clear sort">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </a>
            </div>
            <?php endif; ?>

        </div>

        <?php if (!empty($latestPost)): ?>
        <div class="mb-14 md:mb-18">
            <div class="flex items-center gap-2 mb-6">
                <span class="block w-[18px] h-[1.5px] bg-gold"></span>
                <span id="latest-release"
                    class="text-[.55rem] font-semibold tracking-[.18em] uppercase text-gold scroll-mt-28">Latest
                    Release</span>
            </div>

            <a href="<?= site_url('news/' . $latestPost['slug']) ?>" class="group no-underline block">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                    <div class="aspect-[16/11] lg:aspect-auto lg:h-full bg-[#e5e2dc] overflow-hidden">
                        <?php if (!empty($latestPost['imagePath'])): ?>
                        <img src="<?= site_url($latestPost['imagePath']) ?>" alt="<?= esc($latestPost['title']) ?>"
                            class="w-full h-full object-cover" />
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center min-h-[280px]">
                            <span class="text-[.55rem] font-semibold tracking-[.2em] uppercase text-dark/12">Cover
                                Image</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div
                        class="bg-white border-b-2 border-dark/[.06] group-hover:border-dark/20 transition-colors duration-300 p-7 md:p-10 lg:p-12 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-4">
                            <span
                                class="text-[.5rem] font-bold tracking-[.18em] uppercase text-navy/40"><?= esc(ucfirst($latestPost['category'])) ?></span>
                            <?php if ($latestPost['publishedAt']): ?>
                            <span class="text-dark/12">·</span>
                            <span
                                class="text-[.5rem] font-medium tracking-[.06em] text-dark/40"><?= date('F j, Y', strtotime($latestPost['publishedAt'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <h2
                            class="font-display text-[1.3rem] md:text-[1.6rem] lg:text-[1.8rem] leading-[1.18] text-dark mb-4">
                            <?= esc($latestPost['title']) ?></h2>
                        <?php if (!empty($latestPost['shortDescription'])): ?>
                        <p class="text-[.9rem] font-light leading-[1.6] mb-3 transition-colors duration-200"
                            style="color:#1a1a1a;">
                            <?= html_entity_decode(esc(character_limiter($latestPost['shortDescription'], 180))) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($latestPost['authorName'])): ?>
                        <span class="text-[.68rem] font-medium text-dark/35 mb-4">By
                            <?= esc($latestPost['authorName']) ?></span>
                        <?php endif; ?>
                        <span
                            class="text-[.56rem] font-bold tracking-[.14em] uppercase text-dark/30 border-b border-dark/10 self-start pb-0.5 group-hover:text-dark/50 group-hover:border-dark/25 transition-colors duration-200">
                            Read Article →
                        </span>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if (!empty($posts)): ?>
        <div>
            <?php if (!empty($latestPost)): ?>
            <div class="flex items-center gap-2 mb-8">
                <span class="block w-[18px] h-[1.5px] bg-navy"></span>
                <span id="more-articles"
                    class="text-[.55rem] font-semibold tracking-[.18em] uppercase text-navy scroll-mt-28">More
                    Articles</span>
            </div>
            <?php endif; ?>

            <div class="space-y-0">
                <?php foreach ($posts as $post): ?>
                <a href="<?= site_url('news/' . $post['slug']) ?>"
                    class="group no-underline flex gap-6 py-6 border-t border-dark/[.06] last:border-b last:border-dark/[.06]">
                    <div
                        class="w-[100px] md:w-[140px] lg:w-[180px] h-[80px] md:h-[100px] lg:h-[120px] shrink-0 bg-[#e5e2dc] overflow-hidden">
                        <?php if (!empty($post['imagePath'])): ?>
                        <img src="<?= site_url($post['imagePath']) ?>" alt="<?= esc($post['title']) ?>"
                            class="w-full h-full object-cover" />
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-[.45rem] font-semibold tracking-[.18em] uppercase text-dark/10">IMG</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col justify-center">
                        <div class="flex items-center gap-2.5 mb-2">
                            <span
                                class="text-[.46rem] font-bold tracking-[.16em] uppercase text-navy/40"><?= esc(ucfirst($post['category'])) ?></span>
                            <?php if ($post['publishedAt']): ?>
                            <span class="text-dark/10">·</span>
                            <span
                                class="text-[.46rem] font-medium text-dark/35"><?= date('M j, Y', strtotime($post['publishedAt'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-display text-[1rem] md:text-[1.08rem] text-dark leading-snug mb-1.5">
                            <?= esc($post['title']) ?></h3>
                        <?php if (!empty($post['shortDescription'])): ?>
                        <p class="text-[.88rem] font-light leading-[1.7] transition-colors duration-200 line-clamp-2"
                            style="color:#1a1a1a;">
                            <?= html_entity_decode(esc(character_limiter($post['shortDescription'], 120))) ?></p>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
        <?php
        $rangeStart = max(1, $currentPage - 2);
        $rangeEnd   = min($totalPages, $currentPage + 2);
        ?>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" style="margin-top:2.5rem;margin-bottom:0.5rem;">
            <span class="text-[.55rem] font-medium tracking-[.08em] uppercase" style="color:rgba(2,13,24,.3)">
                Page <?= $currentPage ?> of <?= $totalPages ?> &middot; <?= $totalPosts ?> articles
            </span>
            <nav class="flex items-center gap-1" aria-label="Pagination">
                <?php if ($currentPage > 1): ?>
                <a href="<?= $pageUrl($currentPage - 1) ?>"
                    class="inline-flex items-center gap-1 h-8 px-3 text-[.52rem] font-semibold tracking-[.1em] uppercase no-underline border rounded-sm transition-colors duration-150"
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.55)">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="15 18 9 12 15 6" />
                    </svg>
                    Prev
                </a>
                <?php endif; ?>

                <?php if ($rangeStart > 1): ?>
                <a href="<?= $pageUrl(1) ?>"
                    class="inline-flex items-center justify-center h-8 w-8 text-[.52rem] font-semibold no-underline border rounded-sm transition-colors duration-150"
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.5)">1</a>
                <?php if ($rangeStart > 2): ?>
                <span class="inline-flex items-center justify-center h-8 text-[.55rem]" style="color:rgba(2,13,24,.25)">…</span>
                <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $rangeStart; $i <= $rangeEnd; $i++): ?>
                <?php if ($i === $currentPage): ?>
                <span
                    class="inline-flex items-center justify-center h-8 w-8 text-[.52rem] font-bold border rounded-sm"
                    style="border-color:#03355a;background:rgba(3,53,90,.06);color:#03355a"
                    aria-current="page"><?= $i ?></span>
                <?php else: ?>
                <a href="<?= $pageUrl($i) ?>"
                    class="inline-flex items-center justify-center h-8 w-8 text-[.52rem] font-semibold no-underline border rounded-sm transition-colors duration-150"
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.5)"><?= $i ?></a>
                <?php endif; ?>
                <?php endfor; ?>

                <?php if ($rangeEnd < $totalPages): ?>
                <?php if ($rangeEnd < $totalPages - 1): ?>
                <span class="inline-flex items-center justify-center h-8 text-[.55rem]" style="color:rgba(2,13,24,.25)">…</span>
                <?php endif; ?>
                <a href="<?= $pageUrl($totalPages) ?>"
                    class="inline-flex items-center justify-center h-8 w-8 text-[.52rem] font-semibold no-underline border rounded-sm transition-colors duration-150"
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.5)"><?= $totalPages ?></a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                <a href="<?= $pageUrl($currentPage + 1) ?>"
                    class="inline-flex items-center gap-1 h-8 px-3 text-[.52rem] font-semibold tracking-[.1em] uppercase no-underline border rounded-sm transition-colors duration-150"
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.55)">
                    Next
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>

        <?php if (empty($latestPost) && empty($posts)): ?>
        <div class="text-center py-20">
            <div class="w-16 h-16 mx-auto rounded-full border border-dark/[.08] flex items-center justify-center mb-5">
                <svg class="w-6 h-6 text-dark/15" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                </svg>
            </div>
            <h3 class="font-display text-lg text-dark/40 mb-2">No Articles Yet</h3>
            <p class="text-[.82rem] text-dark/25 font-light">News and insights will appear here once published through
                the admin panel.</p>
        </div>
        <?php endif; ?>

    </div>
</section>