export function createLoaderTimeline({ scene, gsap, config, root, createWordMorphTimeline = null, onComplete }) {
    const s = scene.items;
    const scale = config.durationScale || 1;
    const runWordAnimation = config.skipWordAnimation !== true && typeof createWordMorphTimeline === 'function';
    const timelineOffset = runWordAnimation ? 0 : 4;
    const logoScale = scene.getLogoScale?.() || 0.9;
    const finalLockupScale = logoScale * 0.5;
    let completionStarted = false;
    const requestComplete = () => {
        if (completionStarted) {
            return;
        }
        completionStarted = true;
        onComplete?.();
    };
    const tl = gsap.timeline({
        defaults: { ease: 'power3.out' },
        onStart() {
            scene.start();
        },
        onComplete: requestComplete,
    });

    const at = (seconds) => Math.max(0, seconds - timelineOffset) * scale;
    const dur = (seconds) => seconds * scale;

    delete root.dataset.final;
    root.dataset.loaderPhase = runWordAnimation ? 'words' : 'prebuild';

    scene.camera.zoom = 0.98;
    scene.camera.updateProjectionMatrix();

    if (runWordAnimation) {
        tl.add(createWordMorphTimeline({ gsap, root, scale }), at(0));
    }
    tl.to(scene.camera, {
        zoom: 1.018,
        duration: dur(6.4),
        ease: 'sine.inOut',
        onUpdate: () => scene.camera.updateProjectionMatrix(),
    }, at(0));
    tl.to(s.grid.material, { opacity: 0.014, duration: dur(0.6), ease: 'sine.out' }, at(0.52));
    tl.to(s.grid.position, { x: 0.04, y: -0.035, duration: dur(6.5), ease: 'sine.inOut' }, at(0));

    tl.call(() => {
        root.dataset.loaderPhase = 'build';
    }, null, runWordAnimation ? at(4) : dur(0.12));
    tl.to(s.grid.material, { opacity: 0.028, duration: dur(0.42), ease: 'sine.out' }, at(4));

    tl.fromTo(s.logoGroup.position, { y: -0.02 }, { y: 0, duration: dur(1.8), ease: 'sine.inOut' }, at(4));
    tl.fromTo(s.logoGroup.scale, {
        x: logoScale * 0.92,
        y: logoScale * 0.92,
    }, {
        x: logoScale,
        y: logoScale,
        duration: dur(1.8),
        ease: 'power2.out',
    }, at(4));

    tl.fromTo(s.components.mountain.position, { y: -0.34 }, { y: -0.08, duration: dur(0.64), ease: 'power3.out' }, at(4.07));
    tl.fromTo(s.components.mountain.scale, { x: 0.86, y: 0.86 }, { x: 1.02, y: 1.02, duration: dur(0.72), ease: 'power2.out' }, at(4.07));
    tl.to(s.components.mountain.material, { opacity: 1, duration: dur(0.42), ease: 'power2.out' }, at(4.07));

    tl.fromTo(s.components.arc.scale, { x: 0.82, y: 0.82 }, { x: 1.02, y: 1.02, duration: dur(0.66), ease: 'power2.out' }, at(4.53));
    tl.fromTo(s.components.arc.rotation, { z: 0.1 }, { z: 0, duration: dur(0.7), ease: 'power2.out' }, at(4.53));
    tl.to(s.components.arc.material, { opacity: 1, duration: dur(0.4), ease: 'power2.out' }, at(4.49));

    tl.fromTo(s.components.gear.scale, { x: 0.82, y: 0.82 }, { x: 1.02, y: 1.02, duration: dur(0.74), ease: 'power2.out' }, at(4.89));
    tl.fromTo(s.components.gear.rotation, { z: -0.18 }, { z: 0.018, duration: dur(0.9), ease: 'power2.inOut' }, at(4.87));
    tl.to(s.components.gear.material, { opacity: 1, duration: dur(0.42), ease: 'power2.out' }, at(4.85));

    tl.fromTo(s.components.sparkle.scale, { x: 0.78, y: 0.78 }, { x: 1.04, y: 1.04, duration: dur(0.48), ease: 'back.out(1.1)' }, at(5.39));
    tl.to(s.components.sparkle.material, { opacity: 1, duration: dur(0.34), ease: 'power2.out' }, at(5.33));

    tl.to(s.logoGroup.scale, {
        x: logoScale * 1.012,
        y: logoScale * 1.012,
        duration: dur(0.28),
        ease: 'sine.out',
    }, at(5.77));
    tl.to(s.logoGroup.scale, {
        x: logoScale,
        y: logoScale,
        duration: dur(0.32),
        ease: 'sine.inOut',
    }, at(6.05));
    tl.to(s.componentList.map((part) => part.scale), {
        x: 0.94,
        y: 0.94,
        duration: dur(0.38),
        ease: 'power2.inOut',
    }, at(5.93));
    tl.to(s.componentList.map((part) => part.position), {
        y: -0.08,
        duration: dur(0.38),
        ease: 'power2.inOut',
    }, at(5.93));

    tl.call(() => {
        root.dataset.loaderPhase = 'final';
    }, null, at(6.27));
    tl.set(s.finalLogo.scale, { x: 0.94, y: 0.94 }, at(6.27));
    tl.set(s.finalFlash.scale, { x: 0.94, y: 0.94 }, at(6.27));
    tl.to(s.finalLogo.material, { opacity: 1, duration: dur(0.48), ease: 'power2.inOut' }, at(6.27));
    tl.to(s.componentList.map((part) => part.material), { opacity: 0, duration: dur(0.48), ease: 'power2.inOut' }, at(6.29));
    tl.to(s.finalFlash.material, { opacity: 0.36, duration: dur(0.14), ease: 'power2.out' }, at(6.51));
    tl.to(s.finalFlash.scale, { x: 0.985, y: 0.985, duration: dur(0.34), ease: 'sine.out' }, at(6.51));
    tl.to(s.finalFlash.material, { opacity: 0, duration: dur(0.36), ease: 'power2.out' }, at(6.67));
    tl.to(s.grid.material, { opacity: 0.015, duration: dur(0.64), ease: 'sine.out' }, at(6.49));

    tl.to(s.logoGroup.scale, {
        x: finalLockupScale,
        y: finalLockupScale,
        duration: dur(0.72),
        ease: 'power3.inOut',
    }, at(6.89));
    tl.to(s.logoGroup.position, {
        y: 0.78,
        duration: dur(0.72),
        ease: 'power3.inOut',
    }, at(6.89));

    tl.fromTo(s.subtext.position, { y: -3.22 }, {
        y: -3.02,
        duration: dur(0.68),
        ease: 'power3.out',
    }, at(7.47));
    tl.fromTo(s.subtext.scale, { x: 0.66, y: 0.66 }, {
        x: 0.7,
        y: 0.7,
        duration: dur(0.68),
        ease: 'power2.out',
    }, at(7.47));
    tl.to(s.subtext.material, {
        opacity: 1,
        duration: dur(0.42),
        ease: 'power2.out',
    }, at(7.55));

    tl.call(requestComplete, null, at(8.5));

    return tl;
}
