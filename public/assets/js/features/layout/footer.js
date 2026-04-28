document.addEventListener('DOMContentLoaded', function () {
    if (typeof gsap === 'undefined') return;

    var footer = document.querySelector('.site-footer');
    if (!footer) return;

    /* ── Entrance animation ───────────────── */
    function playFooterIntro() {
        var tl = gsap.timeline({ defaults: { ease: 'power2.out' } });

        // Columns stagger
        var cols = footer.querySelectorAll('.ft-col');
        if (cols.length) {
            tl.from(cols, { y: 20, opacity: 0, duration: 0.4, stagger: 0.1 });
        }

        // Social links stagger
        var links = footer.querySelectorAll('.ft-social-link');
        if (links.length) {
            tl.from(links, {
                y: 8, opacity: 0,
                duration: 0.3, stagger: 0.06
            }, '-=0.2');
        }

        // Bottom bar
        var bottom = footer.querySelector('.ft-bottom');
        if (bottom) {
            tl.from(bottom, { y: 8, opacity: 0, duration: 0.35 }, '-=0.05');
        }
    }

    /* ── Observer trigger ─────────────────── */
    var played = false;
    var io = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
            if (!played && entry.isIntersecting) {
                played = true;
                playFooterIntro();
                io.disconnect();
            }
        });
    }, { threshold: 0.15 });
    io.observe(footer);
});
