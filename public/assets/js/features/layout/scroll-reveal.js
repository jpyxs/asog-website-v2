const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            io.unobserve(e.target);
        }
    });
}, {
    threshold: 0.18,
    rootMargin: '0px 0px -12% 0px'
});

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

requestAnimationFrame(() => {
    document.querySelectorAll('.reveal, .reveal-group').forEach(el => {
        if (prefersReducedMotion) {
            el.style.transition = 'none';
            el.querySelectorAll('.rc').forEach(rc => { rc.style.transition = 'none'; });
            el.classList.add('visible');
        } else {
            io.observe(el);
        }
    });
});
