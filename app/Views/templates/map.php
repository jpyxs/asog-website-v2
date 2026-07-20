<!-- ╔══════════════════════════════════════════════════════════════════════╗
     ║  SECTION: MAP                                                      ║
     ║  Full-width Google Maps embed with blue overlay + info card        ║
     ╚══════════════════════════════════════════════════════════════════════╝ -->
<div id="map" class="relative h-[400px] md:h-[500px] lg:h-[600px]">
    <div
        class="absolute inset-0 bg-[#dce8ef]"
        data-lazy-map
        data-map-title="Find ASOG Technology Business Incubator on Google Maps"
        data-map-label="Map showing the location of ASOG Technology Business Incubator in San Miguel, Nabua, Camarines Sur"
        data-map-src="https://maps.google.com/maps?q=ASOG+Technology+Business+Incubator,+San+Miguel,+Nabua,+Camarines+Sur&t=&z=18&ie=UTF8&iwloc=&output=embed">
        <noscript>
            <iframe
                title="Find ASOG Technology Business Incubator on Google Maps"
                aria-label="Map showing the location of ASOG Technology Business Incubator in San Miguel, Nabua, Camarines Sur"
                src="https://maps.google.com/maps?q=ASOG+Technology+Business+Incubator,+San+Miguel,+Nabua,+Camarines+Sur&t=&z=18&ie=UTF8&iwloc=&output=embed"
                class="absolute inset-0 w-full h-full" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </noscript>
    </div>

    <!-- Navy blue overlay -->
    <div class="absolute inset-0 bg-navy/40 pointer-events-none z-[1]"></div>

    <!-- Info card — bottom-center mobile, center-left desktop -->
    <div
        class="absolute inset-0 z-10 flex items-end justify-center p-4 md:items-center md:justify-start md:pl-10 lg:pl-14 pointer-events-none">
        <div
            class="bg-dark/95 backdrop-blur-md border border-white/[.08] rounded-lg p-5 md:p-8 max-w-[480px] w-full pointer-events-auto">
            <span class="text-[.5rem] font-semibold tracking-[.24em] uppercase text-gold block mb-3 md:mb-4">Our
                Location</span>
            <h3 class="font-display text-lg md:text-[1.8rem] leading-tight text-off mb-3">ASOG Technology Business
                Incubator</h3>
            <div class="space-y-2 mb-5 md:mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-gold/60 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                        <span class="text-[.82rem] md:text-[.85rem] text-white/80 font-light block">Camarines Sur
                            Polytechnic Colleges</span>
                        <span class="text-[.72rem] md:text-[.75rem] text-white/50 font-light">San Miguel, Nabua,
                            Camarines Sur, Philippines 4434</span>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-gold/60 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-[.82rem] md:text-[.85rem] text-white/80 font-light">asog.tbi@cspc.edu.ph</span>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-gold/60 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="text-[.82rem] md:text-[.85rem] text-white/80 font-light">+63 (054) 123-4567</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/features/layout/lazy-map.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/features/layout/lazy-map.js') ?>" defer></script>
