/* ═══ NAVBAR SCROLL TOGGLE (desktop) ═══ */
const navbar = document.getElementById('navbar');
const hasHero = !!document.getElementById('hero');
let lastScrollY = window.scrollY;
let ticking = false;
const SCROLL_THRESHOLD = 60;

function getViewportWidth() {
    return (window.visualViewport && window.visualViewport.width)
        || document.documentElement.clientWidth
        || window.innerWidth;
}

const isMobile = () => getViewportWidth() < 1024;

// Detect if the section behind the navbar has a light background
function getNavTheme() {
    const navBottom = navbar.getBoundingClientRect().bottom;

    const hinted = document.querySelectorAll('[data-navhint]');
    for (const el of hinted) {
        const r = el.getBoundingClientRect();
        if (r.top <= navBottom && r.bottom >= navBottom) {
            const hint = (el.dataset.navhint ?? '').trim();
            if (hint) {
                return hint;
            }
        }
    }

    const candidates = document.querySelectorAll('section, main, div[class*="bg-"]');
    let bestTheme = 'dark';
    let bestZ = -Infinity;
    for (const el of candidates) {
        const r = el.getBoundingClientRect();
        if (r.top <= navBottom && r.bottom >= navBottom) {
            const bg = window.getComputedStyle(el).backgroundColor;
            const m = bg.match(/rgba?\(([\d.]+),\s*([\d.]+),\s*([\d.]+)(?:,\s*([\d.]+))?\)/);
            if (m) {
                const alpha = m[4] !== undefined ? parseFloat(m[4]) : 1;
                if (alpha < 0.15) continue;
                const depth = getDepth(el);
                if (depth > bestZ) {
                    bestZ = depth;
                    const rVal = parseFloat(m[1]);
                    const gVal = parseFloat(m[2]);
                    const bVal = parseFloat(m[3]);
                    const lum = (0.299 * rVal + 0.587 * gVal + 0.114 * bVal) / 255;
                    const isBlue = bVal > 110 && gVal < 130 && rVal < 90;
                    if (isBlue) {
                        bestTheme = 'blue';
                    } else {
                        bestTheme = lum > 0.55 ? 'light' : 'dark';
                    }
                }
            }
        }
    }
    return bestTheme;
}

function getDepth(el) {
    let d = 0;
    while (el.parentElement) { d++; el = el.parentElement; }
    return d;
}

function updateNavbar() {
    if (!navbar) return;
    const currentY = window.scrollY;
    const delta = currentY - lastScrollY;
    const navTheme = getNavTheme();

    if (isMobile()) {
        if (currentY <= SCROLL_THRESHOLD) {
            navbar.classList.remove('scrolled', 'nav-hidden');
        } else if (delta < -3) {
            // Scrolling up: reveal compact navbar
            navbar.classList.add('scrolled');
            navbar.classList.remove('nav-hidden');
        } else if (delta > 3) {
            // Scrolling down: hide navbar
            navbar.classList.add('scrolled');
            navbar.classList.add('nav-hidden');
        } else if (!navbar.classList.contains('scrolled') && !navbar.classList.contains('nav-hidden')) {
            navbar.classList.add('scrolled');
            navbar.classList.remove('nav-hidden');
        }

        navbar.classList.toggle('on-light', navTheme === 'light');
        navbar.classList.toggle('on-blue', navTheme === 'blue');
        lastScrollY = currentY;
        return;
    }

    if (currentY <= SCROLL_THRESHOLD) {
        // At the very top: full navbar, no hide
        navbar.classList.remove('scrolled', 'nav-hidden');
    } else if (delta < -3) {
        // Scrolling up: show compact navbar
        navbar.classList.add('scrolled');
        navbar.classList.remove('nav-hidden');
    } else if (delta > 3) {
        // Scrolling down: hide navbar
        navbar.classList.add('nav-hidden');
    } else if (!navbar.classList.contains('scrolled') && !navbar.classList.contains('nav-hidden')) {
        // Initial / low-movement fallback when already below threshold
        navbar.classList.add('scrolled');
        navbar.classList.remove('nav-hidden');
    }

    navbar.classList.toggle('on-light', navTheme === 'light');
    navbar.classList.toggle('on-blue', navTheme === 'blue');
    lastScrollY = currentY;
}

window.addEventListener('scroll', function () {
    if (!ticking) {
        requestAnimationFrame(function () {
            updateNavbar();
            ticking = false;
        });
        ticking = true;
    }
}, { passive: true });
updateNavbar();

if (window.visualViewport) {
    window.visualViewport.addEventListener('resize', updateNavbar);
}

/* ═══ MOBILE MENU TOGGLE ═══ */
const menuBtn = document.getElementById('menuBtn');
const mobileMenu = document.getElementById('mobileMenu');
const bar1 = document.getElementById('bar1');
const bar2 = document.getElementById('bar2');
const bar3 = document.getElementById('bar3');
let menuOpen = false;

function toggleMenu(open) {
    if (!mobileMenu || !bar1 || !bar2 || !bar3) return;
    menuOpen = open;
    mobileMenu.classList.toggle('open', menuOpen);
    if (menuOpen) {
        bar1.style.transform = 'rotate(45deg) translateY(10px)';
        bar2.style.opacity = '0';
        bar3.style.transform = 'rotate(-45deg) translateY(-10px)';
        document.body.style.overflow = 'hidden';
    } else {
        bar1.style.transform = 'none';
        bar2.style.opacity = '1';
        bar3.style.transform = 'none';
        document.body.style.overflow = 'auto';
    }
}

if (menuBtn && mobileMenu) {
    menuBtn.addEventListener('click', () => toggleMenu(!menuOpen));

    const closeMenuBtn = document.getElementById('closeMenuBtn');
    if (closeMenuBtn) {
        closeMenuBtn.addEventListener('click', () => toggleMenu(false));
    }

    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => toggleMenu(false));
    });

    const navLogo = document.getElementById('navLogo');
    if (navLogo) {
        navLogo.addEventListener('click', () => {
            if (menuOpen) toggleMenu(false);
        });
    }
}

/* ═══ MOBILE PROGRAMS & SERVICES COLLAPSIBLE ═══ */
const psToggle = document.getElementById('mobPsToggle');
const psSub = document.getElementById('mobPsSub');
const psChevron = document.getElementById('mobPsChevron');
let psOpen = false;

if (psToggle && psSub) {
    psToggle.addEventListener('click', () => {
        psOpen = !psOpen;
        if (psOpen) {
            psSub.style.maxHeight = psSub.scrollHeight + 'px';
            if (psChevron) psChevron.style.transform = 'rotate(180deg)';
        } else {
            psSub.style.maxHeight = '0';
            if (psChevron) psChevron.style.transform = 'rotate(0)';
        }
    });
}

/* ═══ MOBILE ABOUT COLLAPSIBLE ═══ */
const aboutToggle = document.getElementById('mobAboutToggle');
const aboutSub = document.getElementById('mobAboutSub');
const aboutChevron = document.getElementById('mobAboutChevron');
let aboutOpen = false;

if (aboutToggle && aboutSub) {
    aboutToggle.addEventListener('click', () => {
        aboutOpen = !aboutOpen;
        if (aboutOpen) {
            aboutSub.style.maxHeight = aboutSub.scrollHeight + 'px';
            if (aboutChevron) aboutChevron.style.transform = 'rotate(180deg)';
        } else {
            aboutSub.style.maxHeight = '0';
            if (aboutChevron) aboutChevron.style.transform = 'rotate(0)';
        }
    });
}

/* ═══ DESKTOP DROPDOWN FALLBACK (Safari-friendly click/keyboard) ═══ */
const desktopDropdowns = Array.from(document.querySelectorAll('#navbar .nav-dd'));

function isDesktopWidth() {
    return window.innerWidth >= 1024;
}

function closeDesktopDropdowns(except = null) {
    desktopDropdowns.forEach(function (dd) {
        if (except && dd === except) return;
        dd.classList.remove('show');
    });
}

desktopDropdowns.forEach(function (dd) {
    const trigger = dd.querySelector('[role="button"]');
    if (!trigger) return;

    trigger.addEventListener('click', function (e) {
        if (!isDesktopWidth()) return;
        e.preventDefault();
        e.stopPropagation();

        const willOpen = !dd.classList.contains('show');
        closeDesktopDropdowns(dd);
        dd.classList.toggle('show', willOpen);
    });

    trigger.addEventListener('keydown', function (e) {
        if (!isDesktopWidth()) return;

        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const willOpen = !dd.classList.contains('show');
            closeDesktopDropdowns(dd);
            dd.classList.toggle('show', willOpen);
        } else if (e.key === 'Escape') {
            dd.classList.remove('show');
            trigger.blur();
        }
    });

    dd.addEventListener('focusout', function () {
        if (!isDesktopWidth()) return;
        window.setTimeout(function () {
            if (!dd.contains(document.activeElement)) {
                dd.classList.remove('show');
            }
        }, 0);
    });
});

document.addEventListener('click', function (e) {
    if (!isDesktopWidth()) return;
    if (!e.target.closest('#navbar .nav-dd')) {
        closeDesktopDropdowns();
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeDesktopDropdowns();
    }
});

window.addEventListener('resize', function () {
    if (!isDesktopWidth()) {
        closeDesktopDropdowns();
    }
});
