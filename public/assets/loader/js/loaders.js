import { loadImage } from './utils.js';

export async function preloadLoaderAssets(config) {
    const [logo, stageLogo, subtext, gear, arc, mountain, sparkle] = await Promise.all([
        loadImage(config.assets.logo),
        loadImage(config.assets.stageLogo),
        loadImage(config.assets.subtext),
        loadImage(config.assets.components.gear),
        loadImage(config.assets.components.arc),
        loadImage(config.assets.components.mountain),
        loadImage(config.assets.components.sparkle),
    ]);

    return {
        logo,
        stageLogo,
        subtext,
        components: {
            gear,
            arc,
            mountain,
            sparkle,
        },
    };
}
