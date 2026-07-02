export function prefersReducedMotion() {
    return window.matchMedia?.('(prefers-reduced-motion: reduce)').matches === true;
}

export function supportsWebGL() {
    try {
        const canvas = document.createElement('canvas');
        return !!(window.WebGLRenderingContext && (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
    } catch (error) {
        return false;
    }
}

export function sessionGet(key) {
    try {
        return window.sessionStorage.getItem(key);
    } catch (error) {
        return null;
    }
}

export function sessionSet(key, value) {
    try {
        window.sessionStorage.setItem(key, value);
    } catch (error) {
        // Storage can be blocked in private modes; the loader should still finish.
    }
}

export function sessionRemove(key) {
    try {
        window.sessionStorage.removeItem(key);
    } catch (error) {
        // Storage can be blocked in private modes; the loader should still finish.
    }
}

export function loadScript(src) {
    return new Promise((resolve, reject) => {
        if (!src) {
            reject(new Error('Missing script source.'));
            return;
        }

        const existing = document.querySelector(`script[src="${CSS.escape(src)}"]`);
        if (existing) {
            if (window.gsap) {
                resolve(window.gsap);
                return;
            }
            existing.addEventListener('load', () => resolve(window.gsap), { once: true });
            existing.addEventListener('error', reject, { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.onload = () => resolve(window.gsap);
        script.onerror = () => reject(new Error(`Failed to load ${src}`));
        document.head.appendChild(script);
    });
}

export function loadImage(src) {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.decoding = 'async';
        image.onload = () => resolve(image);
        image.onerror = () => reject(new Error(`Failed to load image ${src}`));
        image.src = src;
    });
}

export function createTextTexture(THREE, text, options = {}) {
    const {
        color = '#0A5C8E',
        fontSize = 92,
        fontWeight = 800,
        fontStyle = 'normal',
        letterSpacing = 0,
        paddingX = 64,
        paddingY = 34,
        fontFamily = 'Arial, Helvetica, sans-serif',
    } = options;

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const font = `${fontStyle} ${fontWeight} ${fontSize}px ${fontFamily}`;
    context.font = font;
    const metrics = context.measureText(text);
    canvas.width = Math.ceil(metrics.width + paddingX * 2 + letterSpacing * Math.max(0, text.length - 1));
    canvas.height = Math.ceil(fontSize + paddingY * 2);

    context.clearRect(0, 0, canvas.width, canvas.height);
    context.font = font;
    context.textBaseline = 'middle';
    context.fillStyle = color;

    if (letterSpacing > 0) {
        let x = paddingX;
        for (const char of text) {
            context.fillText(char, x, canvas.height / 2);
            x += context.measureText(char).width + letterSpacing;
        }
    } else {
        context.fillText(text, paddingX, canvas.height / 2);
    }

    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.needsUpdate = true;

    return {
        texture,
        width: canvas.width,
        height: canvas.height,
        aspect: canvas.width / canvas.height,
    };
}

export function createMixedWordTexture(THREE, initial, suffix, options = {}) {
    const {
        initialColor = '#0A5C8E',
        suffixColor = '#F9A51A',
        initialFontSize = 116,
        suffixFontSize = 74,
        initialFontWeight = 900,
        suffixFontWeight = 400,
        initialFontFamily = '"ASOG Loader Monas", Arial, sans-serif',
        suffixFontFamily = '"DM Serif Display", Georgia, serif',
        suffixFontStyle = 'italic',
        gap = -3,
        paddingX = 58,
        paddingY = 34,
    } = options;

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const initialFont = `normal ${initialFontWeight} ${initialFontSize}px ${initialFontFamily}`;
    const suffixFont = `${suffixFontStyle} ${suffixFontWeight} ${suffixFontSize}px ${suffixFontFamily}`;

    context.font = initialFont;
    const initialWidth = context.measureText(initial).width;
    context.font = suffixFont;
    const suffixWidth = context.measureText(suffix).width;

    const height = Math.ceil(Math.max(initialFontSize, suffixFontSize) + paddingY * 2);
    const width = Math.ceil(initialWidth + suffixWidth + gap + paddingX * 2);
    canvas.width = width;
    canvas.height = height;

    context.clearRect(0, 0, width, height);
    context.textBaseline = 'middle';

    const centerY = height / 2;
    context.font = initialFont;
    context.fillStyle = initialColor;
    context.fillText(initial, paddingX, centerY);

    context.font = suffixFont;
    context.fillStyle = suffixColor;
    context.fillText(suffix, paddingX + initialWidth + gap, centerY + 1);

    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.needsUpdate = true;

    return {
        texture,
        width,
        height,
        aspect: width / height,
        initialOffsetX: (paddingX + initialWidth / 2 - width / 2) / height,
        suffixCenterOffsetX: (paddingX + initialWidth + gap + suffixWidth / 2 - width / 2) / height,
        suffixWidthRatio: suffixWidth / height,
    };
}

export function disposeObject3D(object) {
    object?.traverse?.((child) => {
        if (child.geometry) {
            child.geometry.dispose();
        }

        const materials = Array.isArray(child.material) ? child.material : [child.material];
        materials.filter(Boolean).forEach((material) => {
            Object.keys(material).forEach((key) => {
                const value = material[key];
                if (value && typeof value.dispose === 'function') {
                    value.dispose();
                }
            });
            material.dispose?.();
        });
    });
}

export function wait(ms) {
    return new Promise((resolve) => window.setTimeout(resolve, ms));
}
