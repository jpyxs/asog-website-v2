/* ── Featured Incubatee — card flip ──────────────────────── */
(function(){
    var card  = document.getElementById('ficCard');
    var inner = document.getElementById('ficInner');
    if (!card || !inner) return;

    var flipped = false;

    if (!window.gsap) {
        inner.style.transition = 'transform .65s ease';
        inner.style.transformStyle = 'preserve-3d';
    }

    card.addEventListener('click', function(){
        flipped = !flipped;
        if (!window.gsap) {
            inner.style.transform = 'rotateY(' + (flipped ? -180 : 0) + 'deg)';
            return;
        }

        window.gsap.to(inner, {
            rotateY: flipped ? -180 : 0,
            duration: .65,
            ease: 'power2.inOut'
        });
    });

    if (!window.gsap) return;

    /* Subtle idle floating animation */
    window.gsap.to(inner, {
        y: -6,
        duration: 2.4,
        ease: 'sine.inOut',
        yoyo: true,
        repeat: -1
    });
})();
