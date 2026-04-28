/**
 * TOC Sidebar — Scroll-spy + mobile toggle
 * Highlights the active section in the sidebar as user scrolls.
 */
(function () {
    'use strict';

    /* ── Mobile toggle ── */
    const toggle = document.getElementById('tocToggle');
    const list   = document.getElementById('tocList');
    const chevron = document.getElementById('tocChevron');

    if (toggle && list) {
        toggle.addEventListener('click', () => {
            const open = list.classList.toggle('hidden');
            toggle.setAttribute('aria-expanded', !open);
            if (chevron) chevron.style.transform = open ? '' : 'rotate(180deg)';
        });

        /* Close mobile TOC when a link is clicked */
        list.querySelectorAll('.toc-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    list.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                    if (chevron) chevron.style.transform = '';
                }
            });
        });
    }

    /* ── TOC Tabs Mode (show one panel at a time) ── */
    const links = document.querySelectorAll('.toc-link[data-toc-target]');
    if (!links.length) return;

    const tabsRoot = document.querySelector('[data-toc-mode="tabs"]');
    const ACTIVE = 'toc-active';

    if (tabsRoot) {
        const ids = Array.from(links).map(l => l.getAttribute('data-toc-target'));
        const panels = ids
            .map(id => document.getElementById(id))
            .filter(panel => panel && panel.hasAttribute('data-toc-panel'));

        if (panels.length) {
            const byId = Object.fromEntries(panels.map(panel => [panel.id, panel]));

            const setActivePanel = (targetId) => {
                if (!byId[targetId]) return;

                panels.forEach(panel => panel.classList.add('hidden'));
                byId[targetId].classList.remove('hidden');

                links.forEach(link => {
                    const isActive = link.getAttribute('data-toc-target') === targetId;
                    link.classList.toggle(ACTIVE, isActive);
                });

                if (window.location.hash !== `#${targetId}`) {
                    history.replaceState(null, '', `#${targetId}`);
                }
            };

            links.forEach(link => {
                link.addEventListener('click', (event) => {
                    const targetId = link.getAttribute('data-toc-target');
                    if (!byId[targetId]) return;

                    event.preventDefault();
                    setActivePanel(targetId);

                    if (window.innerWidth < 1024 && list && toggle) {
                        list.classList.add('hidden');
                        toggle.setAttribute('aria-expanded', 'false');
                        if (chevron) chevron.style.transform = '';
                    }
                });
            });

            const hashTarget = (window.location.hash || '').replace('#', '');
            const firstId = panels[0].id;
            setActivePanel(byId[hashTarget] ? hashTarget : firstId);
            return;
        }
    }

    /* ── Scroll-spy ── */

    const ids = Array.from(links).map(l => l.getAttribute('data-toc-target'));
    const sections = ids.map(id => document.getElementById(id)).filter(Boolean);

    if (!sections.length) return;

    const OFFSET   = 160; // px from top to consider "in view"

    /* Build a map of child-id → parent-link for nested TOC items */
    const childToParent = {};
    document.querySelectorAll('.toc-children .toc-link[data-toc-target]').forEach(childLink => {
        const parentLi = childLink.closest('.toc-children')?.closest('li');
        if (parentLi) {
            const parentLink = parentLi.querySelector(':scope > .toc-link[data-toc-target]');
            if (parentLink) {
                childToParent[childLink.getAttribute('data-toc-target')] = parentLink;
            }
        }
    });

    let ticking = false;

    function update() {
        const scrollY = window.scrollY || document.documentElement.scrollTop;
        let currentId = '';

        /* Find the deepest section whose top has scrolled past the offset */
        for (const sec of sections) {
            if (sec.getBoundingClientRect().top <= OFFSET) {
                currentId = sec.id;
            }
        }

        /* Fallback: if nothing is past the offset yet, highlight the first */
        if (!currentId && sections.length) {
            currentId = sections[0].id;
        }

        /* Remove all active states first */
        links.forEach(link => link.classList.remove(ACTIVE));

        /* Activate the current link */
        links.forEach(link => {
            if (link.getAttribute('data-toc-target') === currentId) {
                link.classList.add(ACTIVE);
            }
        });

        /* Also highlight the parent item if a child is active */
        if (childToParent[currentId]) {
            childToParent[currentId].classList.add(ACTIVE);
        }

        ticking = false;
    }

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(update);
            ticking = true;
        }
    }, { passive: true });

    /* Initial highlight */
    update();
})();
