<!-- ╔══════════════════════════════════════════════════════════════════════╗
     ║  CALL FOR STARTUPS GUIDELINES MODAL — Reusable partial               ║
     ║  Include with: view('incubatees/partials/_guidelines_modal')         ║
     ╚══════════════════════════════════════════════════════════════════════╝ -->
<link rel="stylesheet" href="<?= base_url('assets/css/guidelinesModal.css') ?>">

<div id="guidelinesModal"
    class="fixed inset-0 z-[9998] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300">
    <!-- Backdrop -->
    <div id="guidelinesBackdrop" class="absolute inset-0 bg-dark/60 backdrop-blur-sm"></div>

    <!-- Modal body -->
    <div id="guidelinesBody"
        class="relative w-full max-w-[720px] max-h-[85vh] overflow-y-auto bg-white rounded-lg shadow-2xl border border-navy/10 transform scale-95 transition-transform duration-300 guidelines-scroll">

        <!-- Header -->
        <div
            class="sticky top-0 bg-white/95 backdrop-blur-sm z-10 flex items-center justify-between px-7 py-5 border-b border-navy/[.08]">
            <div>
                <span class="text-[.48rem] font-bold tracking-[.22em] uppercase text-gold block mb-0.5">ASOG TBI</span>
                <h2 class="text-[1rem] font-display text-dark m-0">Call for Startups — Guidelines</h2>
            </div>
            <button id="btnCloseGuidelines"
                class="w-9 h-9 rounded-md bg-transparent flex items-center justify-center text-dark/30 hover:text-gold hover:bg-gold/8 transition-all cursor-pointer border border-navy/8">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="px-7 py-6 space-y-7">

            <!-- Eligibility Criteria -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="block w-3 h-[2px] bg-gold"></span>
                    <span class="text-[.52rem] font-bold tracking-[.2em] uppercase text-navy">Eligibility
                        Criteria</span>
                </div>
                <ul class="space-y-2 text-[.85rem] text-dark leading-[1.7] list-none p-0 m-0">
                    <li class="flex items-start gap-2">
                        <span class="text-gold mt-1 shrink-0">•</span>
                        <span>Early-stage startups or MSMEs with innovative food value chain solutions (agriculture,
                            fisheries, food tech, food processing, etc.)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-gold mt-1 shrink-0">•</span>
                        <span>Must be based in or willing to operate in the Bicol Region</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-gold mt-1 shrink-0">•</span>
                        <span>Must have at least a working prototype or proof of concept</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-gold mt-1 shrink-0">•</span>
                        <span>Team must be willing to participate in the full ALTITUDE incubation program</span>
                    </li>
                </ul>
            </div>

            <div class="h-px bg-navy/[.06]"></div>

            <!-- Startup Categories -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="block w-3 h-[2px] bg-gold"></span>
                    <span class="text-[.52rem] font-bold tracking-[.2em] uppercase text-navy">Startup Categories</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-[.65rem] text-navy font-bold mt-0.5">a.</span>
                        <span>Agricultural Technology (AgriTech)</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-[.65rem] text-navy font-bold mt-0.5">b.</span>
                        <span>Aquaculture &amp; Fisheries Tech</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-[.65rem] text-navy font-bold mt-0.5">c.</span>
                        <span>Food Processing &amp; Safety</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-[.65rem] text-navy font-bold mt-0.5">d.</span>
                        <span>Supply Chain &amp; Logistics</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-[.65rem] text-navy font-bold mt-0.5">e.</span>
                        <span>AI &amp; Data for Food Systems</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-[.65rem] text-navy font-bold mt-0.5">f.</span>
                        <span>Sustainable Packaging</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-[.65rem] text-navy font-bold mt-0.5">g.</span>
                        <span>Nutrition &amp; Health Tech</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-[.65rem] text-navy font-bold mt-0.5">h.</span>
                        <span>Environmental Engineering</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-[.65rem] text-navy font-bold mt-0.5">i.</span>
                        <span>Other Food Value Chain Innovations</span>
                    </div>
                </div>
            </div>

            <div class="h-px bg-navy/[.06]"></div>

            <!-- Evaluation Criteria -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="block w-3 h-[2px] bg-gold"></span>
                    <span class="text-[.52rem] font-bold tracking-[.2em] uppercase text-navy">Evaluation Criteria</span>
                </div>
                <div class="space-y-2">
                    <div
                        class="flex items-center justify-between border border-navy/[.10] rounded px-4 py-2.5 bg-off/50">
                        <span class="text-[.84rem] text-dark font-medium">Innovation &amp; Technology</span>
                        <span class="text-[.75rem] font-bold text-navy">30%</span>
                    </div>
                    <div
                        class="flex items-center justify-between border border-navy/[.10] rounded px-4 py-2.5 bg-off/50">
                        <span class="text-[.84rem] text-dark font-medium">Market Potential &amp; Scalability</span>
                        <span class="text-[.75rem] font-bold text-navy">25%</span>
                    </div>
                    <div
                        class="flex items-center justify-between border border-navy/[.10] rounded px-4 py-2.5 bg-off/50">
                        <span class="text-[.84rem] text-dark font-medium">Team Capability</span>
                        <span class="text-[.75rem] font-bold text-navy">20%</span>
                    </div>
                    <div
                        class="flex items-center justify-between border border-navy/[.10] rounded px-4 py-2.5 bg-off/50">
                        <span class="text-[.84rem] text-dark font-medium">Social &amp; Environmental Impact</span>
                        <span class="text-[.75rem] font-bold text-navy">15%</span>
                    </div>
                    <div
                        class="flex items-center justify-between border border-navy/[.10] rounded px-4 py-2.5 bg-off/50">
                        <span class="text-[.84rem] text-dark font-medium">Feasibility &amp; Readiness</span>
                        <span class="text-[.75rem] font-bold text-navy">10%</span>
                    </div>
                </div>
            </div>

            <div class="h-px bg-navy/[.06]"></div>

            <!-- Application Process -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="block w-3 h-[2px] bg-gold"></span>
                    <span class="text-[.52rem] font-bold tracking-[.2em] uppercase text-navy">Application Process</span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span
                            class="w-7 h-7 shrink-0 rounded-full bg-navy flex items-center justify-center text-[.6rem] font-bold text-white">1</span>
                        <div>
                            <p class="text-[.85rem] text-dark m-0 font-semibold">Submit Online Application</p>
                            <p class="text-[.76rem] text-dark/75 m-0 mt-0.5">Complete the application form with your
                                startup details, team info, and pitch video.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="w-7 h-7 shrink-0 rounded-full bg-navy flex items-center justify-center text-[.6rem] font-bold text-white">2</span>
                        <div>
                            <p class="text-[.85rem] text-dark m-0 font-semibold">Screening &amp; Shortlisting</p>
                            <p class="text-[.76rem] text-dark/75 m-0 mt-0.5">Applications are reviewed and scored by the
                                evaluation panel.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="w-7 h-7 shrink-0 rounded-full bg-navy flex items-center justify-center text-[.6rem] font-bold text-white">3</span>
                        <div>
                            <p class="text-[.85rem] text-dark m-0 font-semibold">Pitch Day &amp; Interview</p>
                            <p class="text-[.76rem] text-dark/75 m-0 mt-0.5">Shortlisted applicants present their
                                startup to the ASOG TBI evaluation team.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="w-7 h-7 shrink-0 rounded-full bg-navy flex items-center justify-center text-[.6rem] font-bold text-white">4</span>
                        <div>
                            <p class="text-[.85rem] text-dark m-0 font-semibold">Acceptance &amp; Onboarding</p>
                            <p class="text-[.76rem] text-dark/75 m-0 mt-0.5">Selected startups begin the ALTITUDE
                                incubation program.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-px bg-navy/[.06]"></div>

            <!-- Benefits -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="block w-3 h-[2px] bg-gold"></span>
                    <span class="text-[.52rem] font-bold tracking-[.2em] uppercase text-navy">What You'll Receive</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-gold mt-0.5 shrink-0 font-bold">✓</span>
                        <span>Co-working space &amp; lab access</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-gold mt-0.5 shrink-0 font-bold">✓</span>
                        <span>Technical mentorship &amp; advisory</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-gold mt-0.5 shrink-0 font-bold">✓</span>
                        <span>Business model development support</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-gold mt-0.5 shrink-0 font-bold">✓</span>
                        <span>IP &amp; legal guidance</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-gold mt-0.5 shrink-0 font-bold">✓</span>
                        <span>Investor readiness &amp; pitch coaching</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-gold mt-0.5 shrink-0 font-bold">✓</span>
                        <span>Networking &amp; partnership opportunities</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-gold mt-0.5 shrink-0 font-bold">✓</span>
                        <span>Training workshops &amp; seminars</span>
                    </div>
                    <div class="flex items-start gap-2 text-[.84rem] text-dark leading-[1.7]">
                        <span class="text-gold mt-0.5 shrink-0 font-bold">✓</span>
                        <span>Demo day &amp; showcase events</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/features/modals/guidelinesModal.js') ?>" defer></script>