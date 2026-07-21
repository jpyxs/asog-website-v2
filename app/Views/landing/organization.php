<!-- ╔══════════════════════════════════════════════════════════════════════╗
     ║  SECTION: ORGANIZATION                                             ║
     ║  Dark bg · 4-column team grid                                      ║
     ╚══════════════════════════════════════════════════════════════════════╝ -->
<section id="organization" data-navhint="blue"
    class="relative overflow-hidden bg-[#03558c] py-16 md:py-24 px-6 md:px-10 lg:px-14">
    <div class="max-w-[1400px] mx-auto relative z-[2]">
        <div class="text-center reveal mb-10 md:mb-14">
            <div class="flex items-center justify-center gap-2 mb-3">
                <span class="block w-[18px] h-[1.5px] bg-gold"></span>
                <span class="text-[.58rem] font-semibold tracking-[.2em] uppercase text-gold">The Team</span>
                <span class="block w-[18px] h-[1.5px] bg-gold"></span>
            </div>
            <h2 class="font-display text-3xl md:text-[2.1rem] leading-[1.12] text-off">Our <em
                    class="italic text-gold">Organization</em></h2>
        </div>

        <?php if (! empty($coreTeamMembers)): ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 reveal-group">
            <?php foreach ($coreTeamMembers as $member): ?>
                <?= view('organization/_landing_card', ['member' => $member]) ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- See More -->
        <div class="text-center mt-10 md:mt-14 reveal">
            <a href="<?= site_url('organization') ?>"
                class="inline-flex items-center gap-2 font-body text-[.62rem] font-bold tracking-[.2em] uppercase text-gold no-underline border border-gold/40 px-6 py-3 rounded-sm transition-all duration-200 hover:bg-gold/10 hover:border-gold">
                View ASOG Team <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>
