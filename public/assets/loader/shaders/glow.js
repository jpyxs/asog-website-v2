export function createGlowMaterial(THREE, color, opacity = 0.2) {
    return new THREE.MeshBasicMaterial({
        color,
        transparent: true,
        opacity,
        blending: THREE.AdditiveBlending,
        depthWrite: false,
    });
}
