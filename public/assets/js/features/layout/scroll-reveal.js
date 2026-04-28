const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
        } else {
            e.target.classList.remove('visible');
        }
    });
}, {
    threshold: 0.18,
    rootMargin: '0px 0px -12% 0px'
});

requestAnimationFrame(() => {
    document.querySelectorAll('.reveal, .reveal-group').forEach(el => io.observe(el));
});
