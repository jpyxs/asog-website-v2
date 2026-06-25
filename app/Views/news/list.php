<!-- ╔══════════════════════════════════════════════════════════════════════╗
     ║  NEWS LIST — Editorial layout                                      ║
     ╚══════════════════════════════════════════════════════════════════════╝ -->
<section class="relative bg-off py-16 md:py-24 px-6 md:px-10 lg:px-14">
    <div class="max-w-[1100px] mx-auto relative z-[2]">

        <!-- Category Filter — custom dropdown (replaces native <select> for full style control) -->
        <?php
            // Map category slug → display label
            $categoryLabels = [
                ''         => 'All Posts',
                'news'     => 'News',
                'features' => 'Features',
                'opinions' => 'Stories',
            ];
            $activeSlug  = $activeCategory ?? '';
            $activeLabel = $categoryLabels[$activeSlug] ?? ucfirst($activeSlug);
        ?>
        <style>
            /* ── News filter custom dropdown ─────────────────────────────── */
            #nf-trigger {
                position: relative;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #fff;
                border: 1px solid rgba(2,13,24,.12);
                border-radius: 3px;
                padding: 7px 32px 7px 10px;
                cursor: pointer;
                user-select: none;
                transition: border-color .18s ease, box-shadow .18s ease;
                white-space: nowrap;
            }
            #nf-trigger:hover  { border-color: rgba(2,13,24,.25); }
            #nf-trigger.is-open { border-color: rgba(2,13,24,.3); box-shadow: 0 0 0 3px rgba(2,13,24,.04); }
            #nf-trigger .nf-sep { width: 1px; height: 12px; background: rgba(2,13,24,.08); flex-shrink: 0; }
            #nf-trigger .nf-label {
                font-size: .60rem;
                font-weight: 600;
                letter-spacing: .1em;
                text-transform: uppercase;
                color: rgba(2,13,24,.7);
                min-width: 60px;
            }
            #nf-caret {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                color: rgba(2,13,24,.3);
                transition: transform .18s ease, color .18s ease;
                pointer-events: none;
            }
            #nf-trigger.is-open #nf-caret {
                transform: translateY(-50%) rotate(-180deg);
                color: rgba(2,13,24,.55);
            }

            #nf-panel {
                display: none;
                position: absolute;
                top: calc(100% + 4px);
                left: 0;
                min-width: 100%;
                background: #fff;
                border: 1px solid rgba(2,13,24,.1);
                border-radius: 4px;
                box-shadow: 0 6px 20px rgba(2,13,24,.09), 0 1px 4px rgba(2,13,24,.06);
                overflow: hidden;
                z-index: 50;
                animation: nfFadeIn .14s ease;
            }
            #nf-panel.is-open { display: block; }
            @keyframes nfFadeIn {
                from { opacity: 0; transform: translateY(-4px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            .nf-option {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 9px 14px;
                font-size: .56rem;
                font-weight: 600;
                letter-spacing: .1em;
                text-transform: uppercase;
                color: rgba(2,13,24,.6);
                cursor: pointer;
                transition: background .12s ease, color .12s ease;
                white-space: nowrap;
                border-bottom: 1px solid rgba(2,13,24,.05);
            }
            .nf-option:last-child { border-bottom: none; }
            .nf-option:hover { background: rgba(2,13,24,.04); color: rgba(2,13,24,.9); }
            .nf-option.is-active { color: #03355a; background: rgba(3,53,90,.05); }
            .nf-option .nf-tick {
                width: 10px; height: 10px;
                flex-shrink: 0;
                opacity: 0;
                color: #03355a;
                transition: opacity .1s;
            }
            .nf-option.is-active .nf-tick { opacity: 1; }
        </style>

        <div class="flex items-center gap-4 mb-10">
            <!-- Trigger pill -->
            <div id="nf-wrapper" style="position:relative">
                <div id="nf-trigger" role="combobox" aria-haspopup="listbox" aria-expanded="false"
                     aria-controls="nf-panel" aria-label="Filter articles by category" tabindex="0">
                    <!-- Funnel icon -->
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" style="color:rgba(2,13,24,.28);flex-shrink:0" aria-hidden="true">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    <span style="font-size:.5rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:rgba(2,13,24,.28)">Filter</span>
                    <span class="nf-sep"></span>
                    <span id="nf-selected-label" class="nf-label"><?= esc($activeLabel) ?></span>
                    <!-- Caret -->
                    <svg id="nf-caret" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>

                <!-- Dropdown panel -->
                <div id="nf-panel" role="listbox" aria-label="Category options">
                    <?php
                    $filterOptions = [
                        ['slug' => '',         'label' => 'All Posts', 'url' => site_url('news')],
                        ['slug' => 'news',     'label' => 'News',      'url' => site_url('news?category=news')],
                        ['slug' => 'features', 'label' => 'Features',  'url' => site_url('news?category=features')],
                        ['slug' => 'opinions', 'label' => 'Stories',   'url' => site_url('news?category=opinions')],
                    ];
                    foreach ($filterOptions as $opt):
                        $isActive = ($opt['slug'] === $activeSlug);
                    ?>
                    <div class="nf-option <?= $isActive ? 'is-active' : '' ?>"
                         role="option" aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                         data-url="<?= esc($opt['url']) ?>">
                        <!-- Checkmark tick -->
                        <svg class="nf-tick" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?= esc($opt['label']) ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <?php if (! empty($latestPost)): ?>
        <!-- ─── LATEST RELEASE ─── -->
        <div class="mb-14 md:mb-18">
            <div class="flex items-center gap-2 mb-6">
                <span class="block w-[18px] h-[1.5px] bg-gold"></span>
                <span id="latest-release"
                    class="text-[.55rem] font-semibold tracking-[.18em] uppercase text-gold scroll-mt-28">Latest
                    Release</span>
            </div>

            <a href="<?= site_url('news/' . $latestPost['slug']) ?>" class="group no-underline block">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                    <!-- Image -->
                    <div class="aspect-[16/11] lg:aspect-auto lg:h-full bg-[#e5e2dc] overflow-hidden">
                        <?php if (! empty($latestPost['imagePath'])): ?>
                        <img src="<?= site_url($latestPost['imagePath']) ?>" alt="<?= esc($latestPost['title']) ?>"
                            class="w-full h-full object-cover" />
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center min-h-[280px]">
                            <span class="text-[.55rem] font-semibold tracking-[.2em] uppercase text-dark/12">Cover
                                Image</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Content -->
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
                        <?php if (! empty($latestPost['shortDescription'])): ?>
                        <p class="text-[.9rem] font-light leading-[1.6] mb-3 transition-colors duration-200"
                            style="color:#1a1a1a;">
                            <?= html_entity_decode(esc(character_limiter($latestPost['shortDescription'], 180))) ?></p>
                        <?php endif; ?>
                        <?php if (! empty($latestPost['authorName'])): ?>
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

        <?php if (! empty($posts)): ?>
        <!-- ─── MORE ARTICLES ─── -->
        <div>
            <div class="flex items-center gap-2 mb-8">
                <span class="block w-[18px] h-[1.5px] bg-navy"></span>
                <span id="more-articles"
                    class="text-[.55rem] font-semibold tracking-[.18em] uppercase text-navy scroll-mt-28">More
                    Articles</span>
            </div>

            <div class="space-y-0">
                <?php foreach ($posts as $post): ?>
                <a href="<?= site_url('news/' . $post['slug']) ?>"
                    class="group no-underline flex gap-6 py-6 border-t border-dark/[.06] last:border-b last:border-dark/[.06]">
                    <!-- Thumbnail -->
                    <div
                        class="w-[100px] md:w-[140px] lg:w-[180px] h-[80px] md:h-[100px] lg:h-[120px] shrink-0 bg-[#e5e2dc] overflow-hidden">
                        <?php if (! empty($post['imagePath'])): ?>
                        <img src="<?= site_url($post['imagePath']) ?>" alt="<?= esc($post['title']) ?>"
                            class="w-full h-full object-cover" />
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-[.45rem] font-semibold tracking-[.18em] uppercase text-dark/10">IMG</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- Body -->
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
                        <?php if (! empty($post['shortDescription'])): ?>
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

        <?php if (empty($latestPost) && empty($posts)): ?>
        <!-- ─── EMPTY STATE ─── -->
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

    </div><!-- end max-w -->
</section>

<script>
(function () {
    const trigger  = document.getElementById('nf-trigger');
    const panel    = document.getElementById('nf-panel');
    const options  = panel ? Array.from(panel.querySelectorAll('.nf-option')) : [];

    if (!trigger || !panel) return;

    function open() {
        trigger.classList.add('is-open');
        panel.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        // Focus first (or active) option for keyboard nav
        const active = panel.querySelector('.nf-option.is-active') || options[0];
        if (active) active.focus();
    }

    function close() {
        trigger.classList.remove('is-open');
        panel.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
    }

    function toggle() {
        trigger.classList.contains('is-open') ? close() : open();
    }

    // Make options focusable for keyboard nav
    options.forEach(function (opt) {
        opt.setAttribute('tabindex', '-1');
        opt.addEventListener('click', function () {
            window.location.href = opt.dataset.url;
        });
        opt.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                window.location.href = opt.dataset.url;
            }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const next = options[options.indexOf(opt) + 1];
                if (next) next.focus();
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prev = options[options.indexOf(opt) - 1];
                if (prev) prev.focus();
                else trigger.focus();
            }
            if (e.key === 'Escape') { close(); trigger.focus(); }
        });
    });

    // Trigger interactions
    trigger.addEventListener('click', toggle);
    trigger.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
            e.preventDefault(); open();
        }
        if (e.key === 'Escape') close();
    });

    // Click outside to close
    document.addEventListener('click', function (e) {
        if (!trigger.contains(e.target) && !panel.contains(e.target)) close();
    });
})();
</script>
