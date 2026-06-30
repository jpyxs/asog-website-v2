<!-- ╔══════════════════════════════════════════════════════════════════════╗
     ║  SECTION: PROGRAMS & SERVICES                                      ║
     ║  Dark bg · 8-card paged GSAP slider                                ║
     ╚══════════════════════════════════════════════════════════════════════╝ -->
<section id="programs" class="relative bg-navy py-16 md:py-24 px-6 md:px-10 lg:px-14">
    <div class="max-w-[1200px] mx-auto relative z-[2]">

        <!-- Header row: heading left, "View All" right -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10 md:mb-14 reveal">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="block w-[18px] h-[1.5px] bg-gold"></span>
                    <span class="text-[.58rem] font-semibold tracking-[.2em] uppercase text-gold">What We Offer</span>
                </div>
                <h2 class="font-display text-3xl md:text-[2.1rem] leading-[1.12] text-off"><em
                        class="italic text-gold">Services</em> offered </h2>
            </div>
            <a href="<?= site_url('programs') ?>"
                class="text-[.6rem] font-semibold tracking-[.13em] uppercase text-white/[.55] no-underline border-b border-white/[.24] pb-0.5 transition-colors duration-200 hover:text-gold hover:border-gold shrink-0">
                View Programs and Services →</a>
        </div>

        <?php
        $programs = [
            [
                'title' => 'Mentorship from experts in AI, engineering, &amp; business',
                'desc'  => 'Bridge the gap between academic research and market-ready innovations with our tech transfer partnerships.'
            ],
            [
                'title' => 'Startup Bootcamps &amp; Training',
                'desc'  => 'Industry experts and academic mentors deliver hands-on workshops and tailored training for founders.'
            ],
            [
                'title' => 'Prototyping &amp; product development',
                'desc'  => 'Leverage prototyping labs and technical expertise to refine your MVP and iterate toward product-market fit.'
            ],
            [
                'title' => 'IP assistance (patents, trademarks, etc.)',
                'desc'  => 'Navigate patents, trademarks, and IP strategy with our dedicated Intellectual Property Management Unit.'
            ],
            [
                'title' => 'Market validation support',
                'desc'  => 'Connect with industry partners, pilot customers, and distribution channels to accelerate go-to-market strategies.'
            ],
            [
                'title' => 'Access to funding networks',
                'desc'  => 'Access seed capital opportunities, investor matchmaking, and grant writing support for early-stage ventures.'
            ],
            [
                'title' => 'Free co-working space',
                'desc'  => 'End-to-end startup support &mdash; from co-working spaces and prototyping labs to seed funding guidance.'
            ],
            [
                'title' => 'Pitching opportunities &amp; networking',
                'desc'  => 'Join pitch nights, demo days, and founder meetups that build lasting connections across the startup ecosystem.'
            ],
        ];
        $total = count($programs);
        ?>
 
        <!-- Card track -->
        <div class="overflow-hidden reveal-group">
            <div id="progSlider" class="flex" data-total="<?= $total ?>">
                <?php foreach ($programs as $i => $prog): ?>
                <div class="prog-card shrink-0 box-border py-2" data-ix="<?= $i ?>">
                    <div class="h-full px-6 md:px-7<?= $i > 0 ? ' border-l border-white/[.08]' : '' ?>">
                        <span
                            class="block text-[.7rem] font-semibold tracking-[.22em] uppercase text-gold/80 mb-4"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
                        <h3 class="font-display text-[1.05rem] text-off mb-3 leading-snug"><?= $prog['title'] ?></h3>
                        <p class="text-[.78rem] font-light leading-[1.8] text-white/60"><?= $prog['desc'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Bottom nav: arrows + page indicator -->
        <div class="flex items-center justify-center gap-4 mt-14 md:mt-16 reveal">
            <button id="progPrev" aria-label="Previous"
                class="group/btn relative w-11 h-11 md:w-12 md:h-12 rounded-full border border-white/30 flex items-center justify-center text-white/50 cursor-pointer opacity-50 pointer-events-none overflow-hidden"
                style="transition:border-color .3s,color .3s,background .3s,opacity .3s,transform .2s">
                <span
                    class="absolute inset-0 rounded-full bg-gold/0 group-hover/btn:bg-gold/[.12] transition-all duration-300"></span>
                <svg class="relative z-[1] transition-transform duration-200 group-hover/btn:-translate-x-0.5"
                    width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <span class="block w-3 h-[1px] bg-gold/50"></span>
                <span id="progPage"
                    class="text-[.65rem] font-semibold tracking-[.2em] text-white/50 min-w-[2.5rem] text-center select-none"
                    style="font-variant-numeric:tabular-nums"></span>
                <span class="block w-3 h-[1px] bg-gold/50"></span>
            </div>
            <button id="progNext" aria-label="Next"
                class="group/btn relative w-11 h-11 md:w-12 md:h-12 rounded-full border border-white/30 flex items-center justify-center text-white/50 cursor-pointer overflow-hidden"
                style="transition:border-color .3s,color .3s,background .3s,opacity .3s,transform .2s">
                <span
                    class="absolute inset-0 rounded-full bg-gold/0 group-hover/btn:bg-gold/[.12] transition-all duration-300"></span>
                <svg class="relative z-[1] transition-transform duration-200 group-hover/btn:translate-x-0.5" width="16"
                    height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <script src="<?= base_url('assets/js/features/carousel/programSlider.js') ?>" defer></script>
</section>