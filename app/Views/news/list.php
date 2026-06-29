<?php
$activeCategories = $activeCategories ?? [];
$activeSort       = $activeSort ?? 'newest';
$currentPage      = (int) ($currentPage ?? 1);
$totalPages       = (int) ($totalPages ?? 1);
$totalPosts       = (int) ($totalPosts ?? 0);
$categories       = $categories ?? \Config\PostCategories::all();

$buildUrl = function(array $cats, string $srt, int $pg = 1): string {
    $p = [];
    if (!empty($cats)) $p['categories'] = implode(',', $cats);
    if ($srt !== 'newest') $p['sort'] = $srt;
    if ($pg > 1) $p['page'] = $pg;
    return site_url('news') . ($p ? '?' . http_build_query($p) : '');
};

$pageUrl = fn(int $p): string => $buildUrl($activeCategories, $activeSort, $p);

$sortUrl = function(string $srt) use ($activeCategories, $buildUrl): string {
    return $buildUrl($activeCategories, $srt);
};

$activeCatLabel = empty($activeCategories)
    ? 'All Posts'
    : implode(', ', array_map(fn($c) => $categories[$c] ?? ucfirst($c), $activeCategories));
?>
<style>
.nf-wrap{position:relative;display:inline-block}
.nf-btn{display:inline-flex;align-items:center;gap:7px;background:#fff;border:1px solid rgba(2,13,24,.12);border-radius:2px;padding:7px 10px;cursor:pointer;font-family:inherit;transition:border-color .2s;outline:none}
.nf-btn:hover,.nf-wrap.open .nf-btn{border-color:rgba(2,13,24,.28)}
.nf-icon{color:rgba(2,13,24,.28);flex-shrink:0}
.nf-label{font-size:.5rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:rgba(2,13,24,.28);white-space:nowrap;user-select:none}
.nf-sep{width:1px;height:12px;background:rgba(2,13,24,.08);flex-shrink:0}
.nf-val{font-size:.58rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:rgba(2,13,24,.7);white-space:nowrap}
.nf-arrow{color:rgba(2,13,24,.3);flex-shrink:0;transition:transform .18s}
.nf-wrap.open .nf-arrow{transform:rotate(180deg)}
.nf-panel{display:none;position:absolute;top:calc(100% + 5px);left:0;min-width:160px;background:#fff;border:1px solid rgba(2,13,24,.12);border-radius:3px;box-shadow:0 6px 20px rgba(2,13,24,.09);z-index:200;overflow:hidden}
.nf-wrap.open .nf-panel{display:block}
.nf-opt{display:flex;align-items:center;gap:9px;padding:8px 12px;font-size:.56rem;font-weight:600;letter-spacing:.09em;text-transform:uppercase;color:rgba(2,13,24,.55);text-decoration:none;transition:background .12s;cursor:pointer;white-space:nowrap;width:100%;background:none;border:none;text-align:left;font-family:inherit}
.nf-opt:hover{background:rgba(2,13,24,.04);color:rgba(2,13,24,.8);text-decoration:none}
.nf-chk{width:14px;height:14px;flex-shrink:0;border:1.5px solid rgba(2,13,24,.2);border-radius:2px;display:inline-flex;align-items:center;justify-content:center;font-size:.55rem;transition:background .12s,border-color .12s}
.nf-opt.nf-active{color:#03355a}
.nf-opt.nf-active .nf-chk{background:#03355a;border-color:#03355a;color:#fff}
.nf-opt-radio{display:flex;align-items:center;padding:8px 12px;font-size:.56rem;font-weight:600;letter-spacing:.09em;text-transform:uppercase;color:rgba(2,13,24,.55);text-decoration:none;transition:background .12s;white-space:nowrap}
.nf-opt-radio:hover{background:rgba(2,13,24,.04);color:rgba(2,13,24,.8);text-decoration:none}
.nf-opt-radio.nf-active{color:#03355a;font-weight:700}
</style>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var baseUrl = '<?= site_url('news') ?>';

    function getParams(){
        return new URLSearchParams(window.location.search);
    }

    function navigate(cats, sort){
        var p = new URLSearchParams();
        if (cats.length) p.set('categories', cats.join(','));
        var currentSort = getParams().get('sort') || 'newest';
        if (sort !== undefined) currentSort = sort;
        if (currentSort !== 'newest') p.set('sort', currentSort);
        window.location.href = baseUrl + (p.toString() ? '?' + p.toString() : '');
    }

    var catWrap = document.getElementById('nfCatWrap');
    var catBtn  = document.getElementById('nfCatBtn');
    var catVal  = document.querySelector('#nfCatWrap .nf-val');

    var sortWrap = document.getElementById('nfSortWrap');
    var sortBtn  = document.getElementById('nfSortBtn');

    var catParam = getParams().get('categories') || '';
    var selected = catParam ? catParam.split(',').filter(Boolean) : [];
    var dirty = false;

    function updateCatUI(){
        catWrap.querySelectorAll('.nf-opt[data-cat]').forEach(function(btn){
            var c = btn.getAttribute('data-cat');
            var active = c === '' ? selected.length === 0 : selected.indexOf(c) > -1;
            btn.classList.toggle('nf-active', active);
            var chk = btn.querySelector('.nf-chk');
            if (chk) chk.textContent = active ? '✓' : '';
        });
        var label = selected.length === 0 ? 'All Posts' : selected.map(function(c){
            var opts = catWrap.querySelectorAll('.nf-opt[data-cat]');
            for (var i = 0; i < opts.length; i++){
                if (opts[i].getAttribute('data-cat') === c){
                    var optLbl = opts[i].querySelector('.nf-opt-label');
                    return optLbl ? optLbl.textContent.trim() : c;
                }
            }
            return c;
        }).join(', ');
        if (catVal) catVal.textContent = label;
    }

    if (catWrap && catBtn){
        updateCatUI();

        catBtn.addEventListener('click', function(e){
            e.stopPropagation();
            var isOpen = catWrap.classList.toggle('open');
            catBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (!isOpen && dirty){
                dirty = false;
                navigate(selected);
            }
            if (sortWrap) sortWrap.classList.remove('open');
        });

        catWrap.querySelectorAll('.nf-opt[data-cat]').forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.stopPropagation();
                var c = this.getAttribute('data-cat');
                if (c === ''){
                    selected = [];
                } else {
                    var idx = selected.indexOf(c);
                    if (idx > -1) selected.splice(idx, 1);
                    else selected.push(c);
                }
                dirty = true;
                updateCatUI();
            });
        });
    }

    if (sortWrap && sortBtn){
        sortBtn.addEventListener('click', function(e){
            e.stopPropagation();
            var isOpen = sortWrap.classList.toggle('open');
            sortBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (catWrap) catWrap.classList.remove('open');
        });
    }

    document.addEventListener('click', function(){
        if (catWrap && catWrap.classList.contains('open')){
            catWrap.classList.remove('open');
            catBtn.setAttribute('aria-expanded','false');
            if (dirty){
                dirty = false;
                navigate(selected);
            }
        }
        if (sortWrap && sortWrap.classList.contains('open')){
            sortWrap.classList.remove('open');
            sortBtn.setAttribute('aria-expanded','false');
        }
    });
});
</script>
<section class="relative bg-off py-16 md:py-24 px-6 md:px-10 lg:px-14">
    <div class="max-w-[1100px] mx-auto relative z-[2]">

        <div class="flex flex-wrap items-center gap-3 mb-10">

            <div class="nf-wrap" id="nfCatWrap">
                <button type="button" class="nf-btn" id="nfCatBtn" aria-haspopup="listbox" aria-expanded="false">
                    <svg class="nf-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    <span class="nf-label">Filter</span>
                    <div class="nf-sep"></div>
                    <span class="nf-val"><?= esc($activeCatLabel) ?></span>
                    <svg class="nf-arrow" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="nf-panel" id="nfCatPanel" role="listbox" aria-multiselectable="true">
                    <button type="button" class="nf-opt<?= empty($activeCategories) ? ' nf-active' : '' ?>" data-cat="" role="option">
                        <span class="nf-chk" aria-hidden="true"><?= empty($activeCategories) ? '✓' : '' ?></span>
                        <span class="nf-opt-label">All Posts</span>
                    </button>
                    <?php foreach ($categories as $catVal => $catLabel): ?>
                    <?php $isActive = in_array($catVal, $activeCategories, true); ?>
                    <button type="button" class="nf-opt<?= $isActive ? ' nf-active' : '' ?>" data-cat="<?= esc($catVal) ?>" role="option">
                        <span class="nf-chk" aria-hidden="true"><?= $isActive ? '✓' : '' ?></span>
                        <span class="nf-opt-label"><?= esc($catLabel) ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="nf-wrap" id="nfSortWrap">
                <button type="button" class="nf-btn" id="nfSortBtn" aria-haspopup="listbox" aria-expanded="false">
                    <svg class="nf-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 9l4-4 4 4M7 5v14M21 15l-4 4-4-4M17 19V5"/>
                    </svg>
                    <span class="nf-label">Sort</span>
                    <div class="nf-sep"></div>
                    <span class="nf-val"><?= $activeSort === 'oldest' ? 'Oldest First' : 'Newest First' ?></span>
                    <svg class="nf-arrow" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="nf-panel" id="nfSortPanel" role="listbox">
                    <a href="<?= $sortUrl('newest') ?>" class="nf-opt-radio<?= $activeSort === 'newest' ? ' nf-active' : '' ?>" role="option">Newest First</a>
                    <a href="<?= $sortUrl('oldest') ?>" class="nf-opt-radio<?= $activeSort === 'oldest' ? ' nf-active' : '' ?>" role="option">Oldest First</a>
                </div>
            </div>

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
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.55);background:#fff;">
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
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.5);background:#fff;">1</a>
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
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.5);background:#fff;"><?= $i ?></a>
                <?php endif; ?>
                <?php endfor; ?>

                <?php if ($rangeEnd < $totalPages): ?>
                <?php if ($rangeEnd < $totalPages - 1): ?>
                <span class="inline-flex items-center justify-center h-8 text-[.55rem]" style="color:rgba(2,13,24,.25)">…</span>
                <?php endif; ?>
                <a href="<?= $pageUrl($totalPages) ?>"
                    class="inline-flex items-center justify-center h-8 w-8 text-[.52rem] font-semibold no-underline border rounded-sm transition-colors duration-150"
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.5);background:#fff;"><?= $totalPages ?></a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                <a href="<?= $pageUrl($currentPage + 1) ?>"
                    class="inline-flex items-center gap-1 h-8 px-3 text-[.52rem] font-semibold tracking-[.1em] uppercase no-underline border rounded-sm transition-colors duration-150"
                    style="border-color:rgba(2,13,24,.12);color:rgba(2,13,24,.55);background:#fff;">
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