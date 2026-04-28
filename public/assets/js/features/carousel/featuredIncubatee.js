/* ── Featured Incubatee — card flip ──────────────────────── */
(function(){
    var card  = document.getElementById('ficCard');
    var inner = document.getElementById('ficInner');
    if (!card || !inner) return;

    var flipped = false;

    card.addEventListener('click', function(){
        flipped = !flipped;
        gsap.to(inner, {
            rotateY: flipped ? -180 : 0,
            duration: .65,
            ease: 'power2.inOut'
        });
    });

    /* Subtle idle floating animation */
    gsap.to(inner, {
        y: -6,
        duration: 2.4,
        ease: 'sine.inOut',
        yoyo: true,
        repeat: -1
    });
})();
