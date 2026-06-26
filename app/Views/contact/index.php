<!-- ╔══════════════════════════════════════════════════════════════════════╗
     ║  CONTACT PAGE — Matches landing page layout                          ║
     ║  Centered header · Big map left · Form right                         ║
     ╚══════════════════════════════════════════════════════════════════════╝ -->
<?php
    $contactEmail = 'asogtbi@cspc.edu.ph';
    $contactAddr  = 'Camarines Sur Polytechnic Colleges, San Miguel, Nabua, Camarines Sur, Philippines 4434';
    $fbUrl        = 'https://www.facebook.com/asogtbi';
    $igUrl        = 'https://www.instagram.com/asogtbi';
    $xUrl         = 'https://x.com/asogtbi';
    $threadsUrl   = 'https://www.threads.com/@asogtbi';
?>
<section class="relative overflow-hidden bg-off py-16 md:py-20 px-6 md:px-10 lg:px-14">
    <div class="max-w-[1400px] mx-auto relative z-[2]">

        <!-- ═══════ CENTERED HEADER ═══════ -->
        <div class="text-center mb-10 md:mb-14 reveal">
            <div class="flex items-center justify-center gap-2 mb-3">
                <span class="block w-[18px] h-[1.5px] bg-gold"></span>
                <span class="text-[.58rem] font-semibold tracking-[.2em] uppercase text-gold">Get In Touch</span>
                <span class="block w-[18px] h-[1.5px] bg-gold"></span>
            </div>
            <h2 class="font-display text-3xl md:text-[2.4rem] leading-[1.12] text-dark">
                Reach <em class="italic text-gold">our Team</em>
            </h2>
        </div>

        <!-- ═══════ MAP LEFT (wider) + FORM RIGHT ═══════ -->
        <div class="grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-8 lg:gap-12">

            <!-- LEFT — Map + Floating Card + Social -->
            <div class="reveal">
                <!-- Map container — maximised height -->
                <div class="relative rounded-lg overflow-hidden mb-5" style="height:420px;">
                    <iframe
                        title="Find ASOG Technology Business Incubator on Google Maps"
                        aria-label="Map showing the location of ASOG Technology Business Incubator in San Miguel, Nabua, Camarines Sur"
                        src="https://maps.google.com/maps?q=ASOG+Technology+Business+Incubator,+San+Miguel,+Nabua,+Camarines+Sur&t=&z=15&ie=UTF8&iwloc=&output=embed"
                        class="absolute inset-0 w-full h-full" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>

                    <!-- Floating gold detail card -->
                    <!-- <div
                        class="absolute bottom-4 left-4 right-4 md:right-auto md:max-w-[320px] z-10 bg-gold rounded-lg p-4 md:p-5 shadow-lg">
                        <h3 class="font-display text-[1rem] md:text-[1.1rem] text-white leading-tight mb-3">ASOG
                            Technology Business Incubator</h3>
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-black shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:<?= esc($contactEmail) ?>"
                                class="text-[.78rem] text-black font-medium no-underline hover:text-black transition-colors"><?= esc($contactEmail) ?></a>
                        </div>
                    </div> -->
                </div>

                <!-- Social — plain icons, no boxes -->
                <!-- <div class="flex items-center gap-1">
                    <span class="text-[.52rem] font-bold tracking-[.18em] uppercase text-dark mr-3">Follow Us</span>
                    <a href="<?= esc($fbUrl) ?>" target="_blank" rel="noopener" title="Facebook"
                        class="inline-flex items-center justify-center w-9 h-9 no-underline transition-colors duration-200 hover:text-gold"
                        style="color:rgba(2,13,24,.4)">
                        <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="<?= esc($igUrl) ?>" target="_blank" rel="noopener" title="Instagram"
                        class="inline-flex items-center justify-center w-9 h-9 no-underline transition-colors duration-200 hover:text-gold"
                        style="color:rgba(2,13,24,.4)">
                        <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                        </svg>
                    </a>
                    <a href="<?= esc($xUrl) ?>" target="_blank" rel="noopener" title="X"
                        class="inline-flex items-center justify-center w-9 h-9 no-underline transition-colors duration-200 hover:text-gold"
                        style="color:rgba(2,13,24,.4)">
                        <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                    <a href="<?= esc($threadsUrl) ?>" target="_blank" rel="noopener" title="Threads"
                        class="inline-flex items-center justify-center w-9 h-9 no-underline transition-colors duration-200 hover:text-gold"
                        style="color:rgba(2,13,24,.4)">
                        <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.59 12c.025 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.8-2.045 1.647-1.613 1.618-3.593 1.09-4.798-.31-.71-.873-1.3-1.634-1.75-.192 1.352-.622 2.446-1.29 3.276-.776.965-1.872 1.599-3.263 1.885-1.254.258-2.47.148-3.43-.312-1.09-.522-1.815-1.396-2.04-2.46-.346-1.636.307-3.217 1.743-4.227 1.108-.78 2.558-1.168 4.312-1.156.888.006 1.739.096 2.547.27-.062-.784-.225-1.44-.493-1.96-.384-.743-1.009-1.189-1.857-1.327-1.237-.2-2.646.152-3.322.95l-1.54-1.317c1.046-1.232 2.97-1.793 4.746-1.505 1.34.217 2.412.914 3.1 2.015.53.85.838 1.89.935 3.117.516.17.993.394 1.424.674 1.206.783 2.073 1.933 2.51 3.332.587 1.885.382 4.532-1.694 6.565-1.842 1.804-4.15 2.619-7.268 2.643zM11.29 12.666c-1.16-.007-2.136.244-2.833.73-.787.55-1.066 1.29-.93 1.932.122.575.558 1.025 1.194 1.33.72.345 1.62.413 2.544.222 1.017-.21 1.8-.654 2.327-1.31.42-.524.725-1.203.893-2.03-.7-.267-1.497-.423-2.385-.475-.266-.02-.537-.025-.81-.025v-.374z" />
                        </svg>
                    </a>
                </div> -->
            </div>

            <!-- RIGHT — Contact Form -->
            <div class="reveal reveal-d2">
                <form action="<?= site_url('contact/send') ?>" method="post" class="space-y-5">
                    <?= csrf_field() ?>
                    <div>
                        <label
                            class="text-[.54rem] font-bold tracking-[.16em] uppercase text-dark/60 block mb-2">Name</label>
                        <input type="text" name="name"
                            class="w-full bg-white border border-dark/[.12] rounded-sm px-4 py-3 text-[.85rem] text-dark font-light outline-none transition-colors duration-200 focus:border-gold/50 focus:shadow-sm focus:shadow-gold/10 placeholder:text-dark/30"
                            placeholder="Your full name" required>
                    </div>
                    <div>
                        <label
                            class="text-[.54rem] font-bold tracking-[.16em] uppercase text-dark/60 block mb-2">Email</label>
                        <input type="email" name="email"
                            class="w-full bg-white border border-dark/[.12] rounded-sm px-4 py-3 text-[.85rem] text-dark font-light outline-none transition-colors duration-200 focus:border-gold/50 focus:shadow-sm focus:shadow-gold/10 placeholder:text-dark/30"
                            placeholder="your@email.com" required>
                    </div>
                    <div>
                        <label
                            class="text-[.54rem] font-bold tracking-[.16em] uppercase text-dark/60 block mb-2">Message</label>
                        <textarea rows="5" name="message"
                            class="w-full bg-white border border-dark/[.12] rounded-sm px-4 py-3 text-[.85rem] text-dark font-light outline-none resize-none transition-colors duration-200 focus:border-gold/50 focus:shadow-sm focus:shadow-gold/10 placeholder:text-dark/30"
                            placeholder="How can we help?" required></textarea>
                    </div>
                    <button type="submit"
                        class="font-body text-[.72rem] font-medium tracking-[.14em] uppercase text-white bg-sky border border-sky px-8 md:px-10 py-4 rounded-sm cursor-pointer no-underline transition-all duration-200 hover:bg-sky/80 hover:-translate-y-0.5">Send
                        Message</button>
                </form>
            </div>
        </div>
    </div>
</section>
