const revealQuery = window.matchMedia('(max-width: 767px)');
const revealOptions = revealQuery.matches
    ? { threshold: 0.04, rootMargin: '0px 0px -4% 0px' }
    : { threshold: 0.18, rootMargin: '0px 0px -12% 0px' };

const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            io.unobserve(e.target);
        }
    });
}, revealOptions);

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function isVisibleInMobileViewport(el) {
    if (!revealQuery.matches || !el.matches('[data-reveal-mobile-initial]')) {
        return false;
    }

    const rect = el.getBoundingClientRect();
    const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
    const visibleTop = Math.max(rect.top, 0);
    const visibleBottom = Math.min(rect.bottom, viewportHeight);

    return visibleBottom - visibleTop >= 24;
}

requestAnimationFrame(() => {
    document.querySelectorAll('.reveal, .reveal-group').forEach(el => {
        if (prefersReducedMotion) {
            el.style.transition = 'none';
            el.querySelectorAll('.rc').forEach(rc => { rc.style.transition = 'none'; });
            el.classList.add('visible');
        } else if (isVisibleInMobileViewport(el)) {
            el.classList.add('visible');
        } else {
            io.observe(el);
        }
    });
});
