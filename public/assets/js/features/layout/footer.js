document.addEventListener('DOMContentLoaded', function () {
    // Check for reduced motion preference
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    if (prefersReducedMotion.matches) {
        var footer = document.querySelector('.site-footer');
        if (footer) {
            var elements = footer.querySelectorAll('.ft-col, .ft-social-link, .ft-bottom');
            elements.forEach(function(el) {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            });
        }
        return;
    }

    if (typeof gsap === 'undefined') return;

    var footer = document.querySelector('.site-footer');
    if (!footer) return;

    // Get elements
    var cols = footer.querySelectorAll('.ft-col');
    var links = footer.querySelectorAll('.ft-social-link');
    var bottom = footer.querySelector('.ft-bottom');

    if (!cols.length && !links.length && !bottom) return;

    /* ── Ensure they start hidden ── */
    gsap.set(cols, { opacity: 0, y: 20 });
    gsap.set(links, { opacity: 0, y: 8 });
    if (bottom) gsap.set(bottom, { opacity: 0, y: 8 });

    /* ── Unified timeline - matches header loading timing ── */
    var tl = gsap.timeline({
        defaults: { ease: 'power2.out' },
        delay: 0.1 // Match header delay
    });

    // Columns stagger
    if (cols.length) {
        tl.to(cols, {
            opacity: 1,
            y: 0,
            duration: 0.35,
            stagger: 0.06,
            onComplete: function() {
                cols.forEach(function(col) {
                    col.classList.add('ft-col-visible');
                    col.style.opacity = '';
                    col.style.transform = '';
                });
            }
        });
    }

    // Social links stagger (overlap with columns)
    if (links.length) {
        tl.to(links, {
            opacity: 1,
            y: 0,
            duration: 0.3,
            stagger: 0.04
        }, '-=0.2');
    }

    // Bottom bar
    if (bottom) {
        tl.to(bottom, {
            opacity: 1,
            y: 0,
            duration: 0.3,
            onComplete: function() {
                bottom.classList.add('ft-bottom-visible');
                bottom.style.opacity = '';
                bottom.style.transform = '';
            }
        }, '-=0.1');
    }

    /* ── Ensure footer section is visible ── */
    if (footer) {
        footer.style.opacity = '1';
    }
});