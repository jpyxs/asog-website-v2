<!-- ╔══════════════════════════════════════════════════════════════════════╗
     ║  SECTION: HERO                                                       ║
     ║  Full-screen slideshow — slides built from published posts with      ║
     ║  a cover image. Shows title, description & "Read Article" per slide. ║
     ╚══════════════════════════════════════════════════════════════════════╝ -->
<?php
$heroSlides = $heroSlides ?? [];
$hasSlides  = ! empty($heroSlides);
$dotCount   = $hasSlides ? count($heroSlides) : 3;
?>
<section id="hero" class="hero-rect-mobile relative w-full overflow-hidden" data-navhint="blue">

    <!-- ── Background Slides ──────────────────────────────────── -->
    <?php if ($hasSlides): ?>
    <?php foreach ($heroSlides as $i => $s): ?>
    <div class="slide slide-post slide-idx-<?= $i ?> <?= $i === 0 ? 'active' : '' ?>"
        data-bg="<?= esc(base_url($s['imagePath'])) ?>"
        <?= $i === 0 ? ' style="background-image:url(\'' . esc(base_url($s['imagePath'])) . '\');"' : '' ?>>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="slide sl1 active"></div>
    <div class="slide sl2"></div>
    <div class="slide sl3"></div>
    <?php endif; ?>

    <!-- ── Overlay: heavy bottom + left fade so text is always readable ── -->
    <div class="hero-overlay absolute inset-0 z-[2]">
    </div>

    <div class="hero-mobile-gradient" aria-hidden="true"></div>

    <!-- ── Content: pinned to bottom-left on desktop, moved below image on mobile ── -->
    <div class="hero-content absolute bottom-0 left-0 z-[3] px-8 md:px-14 lg:px-20 pb-2 md:pb-12 w-full max-w-[960px]">

        <!-- Gold accent rule -->
        <div class="hero-kicker hidden md:flex items-center gap-10 mb-6">
            <div class="w-10 h-[2px] bg-gold shrink-0"></div>
            <span class="text-[.56rem] font-bold tracking-[.28em] uppercase text-gold/80">
                <?= $hasSlides ? 'Featured Story' : 'Bicol Region\'s Premier Incubator' ?>
            </span>
        </div>

        <!-- ── Headline stack ── -->
        <div id="heroTitleWrap" class="relative min-h-[10px] md:min-h-[50px] mb-1 lg:mb-2">
            <?php if ($hasSlides): ?>
            <?php foreach ($heroSlides as $i => $s): ?>
            <h1
                class="hl <?= $i === 0 ? 'active' : '' ?> font-display text-[clamp(1.6rem,2.8vw,2.8rem)] leading-[1.18] text-off max-w-[720px]">
                <?= esc($s['title']) ?>
            </h1>
            <?php endforeach; ?>
            <?php else: ?>
            <h1 class="hl active font-display text-[clamp(1.6rem,2.8vw,2.8rem)] leading-[1.18] text-off max-w-[720px]">
                Empowering Startups Through <em class="italic text-gold">Cutting-Edge</em> Technology</h1>
            <h1 class="hl font-display text-[clamp(1.6rem,2.8vw,2.8rem)] leading-[1.18] text-off max-w-[720px]">
                Engineering &amp; <em class="italic text-gold">AI-Driven Innovations</em> for the Food Value Chain</h1>
            <h1 class="hl font-display text-[clamp(1.6rem,2.8vw,2.8rem)] leading-[1.18] text-off max-w-[720px]">
                From <em class="italic text-gold">Concept</em> to Market-Ready Solutions</h1>
            <?php endif; ?>
        </div>

        <!-- ── CTA + Dots (stacked vertically) ── -->
        <div class="hero-actions flex flex-col gap-1">
            <!-- Read Article links (one per slide, stack-animated) -->
            <?php if ($hasSlides): ?>
            <div class="hero-read-wrap relative h-7 w-40">
                <?php foreach ($heroSlides as $i => $s): ?>
                <a href="<?= site_url('news/' . esc($s['slug'])) ?>"
                    class="hl-link <?= $i === 0 ? 'active' : '' ?> flex items-center gap-2 md:gap-3 text-[.62rem] font-bold tracking-[.2em] uppercase text-gold no-underline whitespace-nowrap transition-[gap] duration-300 md:hover:gap-4">
                    Read Article <span aria-hidden="true">→</span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Dots — below the CTA -->
            <div class="hero-dots flex items-center justify-center md:justify-start gap-1.5">
                <?php for ($d = 0; $d < $dotCount; $d++): ?>
                <button class="ind <?= $d === 0 ? 'active' : '' ?> border-none p-0 cursor-pointer"
                    onclick="goTo(<?= $d ?>)"></button>
                <?php endfor; ?>
            </div>
        </div>

    </div><!-- /content -->
</section>