(function () {
    function initServicesExperience() {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
            return;
        }

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        gsap.registerPlugin(ScrollTrigger);

        gsap.set('.svc-panel', { opacity: 1 });

        var heroTl = gsap.timeline();
        heroTl
            .from('.svc-kicker-line', {
                scaleX: 0,
                opacity: 0,
                duration: 0.45,
                stagger: 0.08,
                ease: 'power2.out'
            })
            .from('.svc-hero-title', {
                yPercent: 10,
                opacity: 0,
                duration: 0.9,
                ease: 'power3.out'
            }, '-=0.16')
            .from('.svc-hero-copy', {
                y: 16,
                opacity: 0,
                duration: 0.7,
                ease: 'power2.out'
            }, '-=0.68')
            .add(function () {
                gsap.set('.svc-kicker-line, .svc-hero-title, .svc-hero-copy', {
                    clearProps: 'transform,opacity'
                });
            });

        gsap.utils.toArray('.svc-panel').forEach(function (panel, idx) {
            var row = panel.querySelector('.svc-row');
            var media = panel.querySelector('.svc-media');
            var copy = panel.querySelector('.svc-copy');
            var bullets = panel.querySelectorAll('.svc-bullet');

            gsap.timeline({
                defaults: { ease: 'power3.out' },
                scrollTrigger: {
                    trigger: panel,
                    start: 'top 82%',
                    end: 'bottom 26%',
                    scrub: 0.45
                }
            })
                .fromTo(row, {
                    y: 20,
                    opacity: 0.45
                }, {
                    y: 0,
                    opacity: 1
                }, 0)
                .fromTo(media, {
                    clipPath: 'inset(4% 4% 4% 4% round 16px)',
                    scale: 1.03,
                    rotation: 0,
                    filter: 'saturate(90%) brightness(0.95)'
                }, {
                    clipPath: 'inset(0% 0% 0% 0% round 16px)',
                    scale: 1,
                    rotation: 0,
                    filter: 'saturate(100%) brightness(1)'
                }, 0.02)
                .fromTo(copy, {
                    y: 14,
                    opacity: 0
                }, {
                    y: 0,
                    opacity: 1
                }, 0.12)
                .fromTo(bullets, {
                    y: 8,
                    opacity: 0
                }, {
                    y: 0,
                    opacity: 1,
                    stagger: 0.06,
                    ease: 'power2.out'
                }, 0.22);

            gsap.to(media, {
                yPercent: idx % 2 === 0 ? -3 : 3,
                ease: 'none',
                scrollTrigger: {
                    trigger: panel,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: true
                }
            });
        });

        ScrollTrigger.refresh();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initServicesExperience);
    } else {
        initServicesExperience();
    }
})();
