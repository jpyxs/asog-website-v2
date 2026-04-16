(function () {
    const canvas = document.getElementById('gsThreeCanvas');
    if (!canvas || !window.THREE) {
        return;
    }

    const renderer = new THREE.WebGLRenderer({
        canvas: canvas,
        alpha: true,
        antialias: true,
        powerPreference: 'low-power'
    });

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(50, 1, 0.1, 100);
    camera.position.z = 7.5;

    const dotsCount = 180;
    const positions = new Float32Array(dotsCount * 3);
    const colors = new Float32Array(dotsCount * 3);

    const palette = [
        new THREE.Color('#0f5d99'),
        new THREE.Color('#4ba6de'),
        new THREE.Color('#f3b33a'),
        new THREE.Color('#1f4063')
    ];

    for (let i = 0; i < dotsCount; i++) {
        const i3 = i * 3;
        positions[i3] = (Math.random() - 0.5) * 14;
        positions[i3 + 1] = (Math.random() - 0.5) * 9;
        positions[i3 + 2] = (Math.random() - 0.5) * 6;

        const color = palette[Math.floor(Math.random() * palette.length)];
        colors[i3] = color.r;
        colors[i3 + 1] = color.g;
        colors[i3 + 2] = color.b;
    }

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

    const material = new THREE.PointsMaterial({
        size: 0.055,
        vertexColors: true,
        transparent: true,
        opacity: 0.42,
        depthWrite: false
    });

    const points = new THREE.Points(geometry, material);
    scene.add(points);

    const ambient = new THREE.AmbientLight(0xffffff, 0.8);
    scene.add(ambient);

    const clock = new THREE.Clock();
    let rafId = null;

    function resize() {
        const width = window.innerWidth;
        const height = Math.max(window.innerHeight, 520);

        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
    }

    function tick() {
        const t = clock.getElapsedTime();
        points.rotation.y = t * 0.03;
        points.rotation.x = Math.sin(t * 0.18) * 0.03;
        renderer.render(scene, camera);
        rafId = requestAnimationFrame(tick);
    }

    function handleVisibility() {
        if (document.hidden) {
            if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
        } else if (!rafId) {
            tick();
        }
    }

    resize();
    tick();

    window.addEventListener('resize', resize);
    document.addEventListener('visibilitychange', handleVisibility);
})();
