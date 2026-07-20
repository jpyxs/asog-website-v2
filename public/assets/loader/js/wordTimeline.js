function renderMorphTbi() {
    return [
        '<span class="asog-loader-final-lockup">',
        '<span class="asog-loader-final-asog" data-asog-loader-final-asog>',
        ['A', 'S', 'O', 'G'].map((letter) => [
            '<span class="asog-loader-final-letter" data-asog-loader-final-letter>',
            letter,
            '</span>',
        ].join('')).join(''),
        '</span>',
        '<span class="asog-loader-word-tbi" data-asog-loader-final-tbi>TBI</span>',
        '</span>',
    ].join('');
}

function createFlyingInitials({ morphRoot, initials, finalLetters }) {
    morphRoot.querySelector('[data-asog-loader-flying-layer]')?.remove();

    const rootBounds = morphRoot.getBoundingClientRect();
    const layer = document.createElement('span');
    layer.className = 'asog-loader-flying-layer';
    layer.setAttribute('data-asog-loader-flying-layer', '');
    morphRoot.appendChild(layer);

    const clones = Array.from(initials).map((initial, index) => {
        const target = finalLetters[index];
        if (!target) {
            return null;
        }

        const sourceBounds = initial.getBoundingClientRect();
        const targetBounds = target.getBoundingClientRect();
        const computed = window.getComputedStyle(initial);
        const clone = document.createElement('span');
        clone.className = 'asog-loader-flying-letter';
        clone.textContent = initial.textContent || '';
        clone.style.left = `${sourceBounds.left - rootBounds.left}px`;
        clone.style.top = `${sourceBounds.top - rootBounds.top}px`;
        clone.style.width = `${sourceBounds.width}px`;
        clone.style.height = `${sourceBounds.height}px`;
        clone.style.fontSize = computed.fontSize;
        clone.style.lineHeight = computed.lineHeight;
        clone.dataset.targetX = String((targetBounds.left + (targetBounds.width / 2)) - (sourceBounds.left + (sourceBounds.width / 2)));
        clone.dataset.targetY = String((targetBounds.top + (targetBounds.height / 2)) - (sourceBounds.top + (sourceBounds.height / 2)));
        layer.appendChild(clone);
        return clone;
    }).filter(Boolean);

    return { layer, clones };
}

function renderAcronymCluster() {
    return [
        ['A', 'CADEME'],
        ['S', 'OCIETY'],
        ['O', 'RGANIZATION'],
        ['G', 'OVERNMENT'],
    ].map(([initial, rest]) => [
        '<span class="asog-loader-word-row">',
        '<span class="asog-loader-word-initial">',
        initial,
        '</span><span class="asog-loader-word-rest">',
        rest,
        '</span></span>',
    ].join('')).join('');
}

export function createWordMorphTimeline({ gsap, root, scale }) {
    const morphRoot = root.querySelector('[data-asog-loader-word-morph]');
    const wordA = root.querySelector('[data-asog-loader-word-a]');
    const wordB = root.querySelector('[data-asog-loader-word-b]');

    if (!morphRoot || !wordA || !wordB) {
        return gsap.timeline();
    }

    const dur = (seconds) => seconds * scale;
    const tl = gsap.timeline();
    wordA.innerHTML = renderAcronymCluster();
    wordB.innerHTML = renderMorphTbi();
    const rows = wordA.querySelectorAll('.asog-loader-word-row');
    const initials = wordA.querySelectorAll('.asog-loader-word-initial');
    const suffixes = wordA.querySelectorAll('.asog-loader-word-rest');
    const finalAsog = wordB.querySelector('[data-asog-loader-final-asog]');
    const finalTbi = wordB.querySelector('[data-asog-loader-final-tbi]');
    const finalLetters = wordB.querySelectorAll('[data-asog-loader-final-letter]');
    let flying = { layer: null, clones: [] };

    tl.call(() => {
        root.dataset.loaderPhase = 'words';
        morphRoot.querySelector('[data-asog-loader-flying-layer]')?.remove();
        gsap.set(morphRoot, { autoAlpha: 1, xPercent: -50, yPercent: -50, x: 0, y: 0, scale: 1 });
        gsap.set(wordA, { opacity: 1, filter: 'blur(0px)' });
        gsap.set(wordB, { opacity: 1, x: 0, y: 0, scale: 1, filter: 'blur(0px)' });
        gsap.set(finalAsog, { opacity: 0, scale: 1, filter: 'blur(0px)' });
        gsap.set(finalTbi, { opacity: 0, x: -10, filter: 'blur(12px)' });
        gsap.set(rows, {
            opacity: 0,
            x: -18,
            y: 12,
            filter: 'blur(10px)',
        });
        gsap.set(initials, { x: 0, y: 0, scale: 1 });
        gsap.set(suffixes, { display: 'inline-block', opacity: 1, x: 0, filter: 'blur(0px)' });
    }, null, 0);

    tl.to(rows, {
        opacity: 1,
        x: 0,
        y: 0,
        filter: 'blur(0px)',
        duration: dur(0.68),
        stagger: dur(0.105),
        ease: 'power3.out',
    }, dur(0.12));
    tl.to(suffixes, {
        opacity: 0,
        x: 8,
        filter: 'blur(10px)',
        duration: dur(0.74),
        stagger: dur(0.045),
        ease: 'power2.inOut',
    }, dur(1.48));
    tl.call(() => {
        flying = createFlyingInitials({ morphRoot, initials, finalLetters });
        gsap.set(initials, { opacity: 0 });
        gsap.set(flying.clones, { opacity: 1, scale: 1.02, filter: 'blur(0px)' });
        gsap.to(flying.clones, {
            x: (index, target) => Number(target.dataset.targetX || 0),
            y: (index, target) => Number(target.dataset.targetY || 0),
            scale: 1,
            duration: dur(1.02),
            stagger: dur(0.018),
            ease: 'power3.inOut',
        });
    }, null, dur(1.94));
    tl.to(rows, {
        opacity: 0,
        duration: dur(0.32),
        ease: 'sine.out',
    }, dur(2.18));
    tl.to(finalTbi, {
        opacity: 1,
        x: 0,
        filter: 'blur(0px)',
        duration: dur(0.46),
        ease: 'power3.out',
    }, dur(3.24));

    tl.to(morphRoot, {
        scale: 0.985,
        duration: dur(0.5),
        ease: 'sine.inOut',
    }, dur(3.24));
    tl.to(morphRoot, {
        autoAlpha: 0,
        y: -8,
        duration: dur(0.5),
        ease: 'power2.inOut',
    }, dur(3.68));
    tl.call(() => {
        flying.layer?.remove();
    }, null, dur(4.24));

    return tl;
}
