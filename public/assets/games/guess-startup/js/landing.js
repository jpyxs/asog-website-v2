(function () {
    const root = document.querySelector('.gsl-root');
    if (!root) {
        return;
    }

    const navbar = document.getElementById('navbar');
    const navCloudMask = document.querySelector('.gsl-nav-cloud-mask');
    const navMaskClouds = Array.from(document.querySelectorAll('.gsl-mask-cloud'));
    const navLinks = Array.from(document.querySelectorAll('.gsl-nav-link'));
    const modeItems = Array.from(document.querySelectorAll('.gsl-mode-item'));
    const scoreItems = Array.from(document.querySelectorAll('.gsl-score-list li'));
    const flowItems = Array.from(document.querySelectorAll('.gsl-flow-item'));
    const infoModal = document.getElementById('gslInfoModal');
    const infoClose = document.getElementById('gslInfoClose');
    const infoBackdrop = document.querySelector('.gsl-info-modal-backdrop');

    function revealNavbarWithClouds() {
        if (!navbar) {
            return;
        }

        navbar.classList.add('gsl-nav-reveal');

        const finishReveal = function () {
            navbar.classList.add('is-visible');
            if (navCloudMask) {
                navCloudMask.classList.add('is-clearing');
            }
        };

        if (!window.gsap) {
            requestAnimationFrame(function () {
                finishReveal();
            });
            return;
        }

        if (navMaskClouds.length === 0) {
            finishReveal();
            return;
        }

        const tl = window.gsap.timeline({ defaults: { ease: 'power2.out' } });

        tl.fromTo(
            navMaskClouds,
            { y: 0, opacity: 0.95 },
            {
                y: -68,
                opacity: 0,
                duration: 0.9,
                stagger: 0.06,
                onStart: finishReveal
            }
        );
    }

    function introAnimate() {
        if (!window.gsap) {
            return;
        }

        const tl = window.gsap.timeline({ defaults: { ease: 'power2.out' } });
        tl.from('.gsl-title', { y: 22, autoAlpha: 0, duration: 0.5 })
            .from('.gsl-subtitle', { y: 10, autoAlpha: 0, duration: 0.32 }, '-=0.2')
            .from('.gsl-hero-actions > *', { y: 8, autoAlpha: 0, duration: 0.24, stagger: 0.08 }, '-=0.14')
            .from('.gsl-panel', { y: 12, autoAlpha: 0, duration: 0.24, stagger: 0.06 }, '-=0.08')
            .from(modeItems, { y: 8, autoAlpha: 0, duration: 0.2, stagger: 0.04 }, '-=0.1')
            .from(scoreItems, { y: 8, autoAlpha: 0, duration: 0.2, stagger: 0.04 }, '-=0.15')
            .from(flowItems, { y: 8, autoAlpha: 0, duration: 0.2, stagger: 0.04 }, '-=0.16');
    }

    function wireTransitionLink() {
        if (navLinks.length === 0) {
            return;
        }

        const layer = document.createElement('div');
        layer.className = 'gsl-page-wipe';
        layer.innerHTML = '<div class="gsl-page-wipe-band"></div>';
        document.body.appendChild(layer);

        const band = layer.querySelector('.gsl-page-wipe-band');

        navLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                const targetHref = link.getAttribute('href') || '';
                if (!targetHref) {
                    return;
                }

                if (!window.gsap) {
                    return;
                }

                event.preventDefault();

                const tl = window.gsap.timeline({
                    onComplete: function () {
                        window.location.href = targetHref;
                    }
                });

                tl.to(layer, {
                    scaleX: 1,
                    duration: 0.44,
                    ease: 'power3.inOut'
                }).fromTo(
                    band,
                    { xPercent: -40, opacity: 0 },
                    { xPercent: 130, opacity: 1, duration: 0.32, ease: 'power2.out' },
                    0.08
                );
            });
        });
    }



    revealNavbarWithClouds();
    introAnimate();
    wireTransitionLink();
})();
