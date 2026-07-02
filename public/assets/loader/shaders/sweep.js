export function createSweepMaterial(THREE) {
    return new THREE.ShaderMaterial({
        transparent: true,
        depthWrite: false,
        uniforms: {
            uProgress: { value: -0.6 },
            uOpacity: { value: 0 },
        },
        vertexShader: `
            varying vec2 vUv;

            void main() {
                vUv = uv;
                gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }
        `,
        fragmentShader: `
            varying vec2 vUv;
            uniform float uProgress;
            uniform float uOpacity;

            void main() {
                float diagonal = vUv.x + vUv.y * 0.65;
                float band = smoothstep(uProgress - 0.08, uProgress, diagonal) - smoothstep(uProgress, uProgress + 0.12, diagonal);
                gl_FragColor = vec4(vec3(1.0), band * uOpacity * 0.72);
            }
        `,
    });
}
