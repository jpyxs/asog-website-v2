(() => {
    function parseIds(raw) {
        if (!raw) {
            return [];
        }

        const ids = String(raw)
            .split(',')
            .map((value) => Number.parseInt(value, 10))
            .filter((id) => Number.isInteger(id) && id >= 1 && id <= 17);

        return [...new Set(ids)].sort((a, b) => a - b);
    }

    function renderBadge(item) {
        const box = document.createElement('span');
        box.style.display = 'inline-flex';
        box.style.alignItems = 'center';
        box.style.gap = '6px';
        box.style.padding = '4px 7px';
        box.style.borderRadius = '4px';
        box.style.background = item.color;
        box.style.color = item.textColor || '#ffffff';
        box.style.fontSize = '.52rem';
        box.style.fontWeight = '700';
        box.style.letterSpacing = '.08em';
        box.style.textTransform = 'uppercase';
        box.style.lineHeight = '1';

        const number = document.createElement('strong');
        number.textContent = `SDG ${item.id}`;

        const name = document.createElement('span');
        name.textContent = item.name;
        name.style.fontWeight = '600';
        name.style.opacity = '.95';

        box.appendChild(number);
        box.appendChild(name);

        return box;
    }

    async function init() {
        const targets = [...document.querySelectorAll('[data-sdg-numbers]')];
        if (targets.length === 0) {
            return;
        }

        const allIds = new Set();
        const perTargetIds = new Map();

        targets.forEach((target) => {
            const ids = parseIds(target.getAttribute('data-sdg-numbers'));
            perTargetIds.set(target, ids);
            ids.forEach((id) => allIds.add(id));
        });

        if (allIds.size === 0) {
            return;
        }

        try {
            const query = [...allIds].sort((a, b) => a - b).join(',');
            const response = await fetch(`/api/sdgs?ids=${encodeURIComponent(query)}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            const items = Array.isArray(payload.data) ? payload.data : [];
            const byId = new Map(items.map((item) => [Number(item.id), item]));

            perTargetIds.forEach((ids, target) => {
                if (!ids.length) {
                    return;
                }

                target.style.display = 'flex';
                target.style.flexWrap = 'wrap';
                target.style.gap = '6px';
                target.style.alignItems = 'center';

                ids.forEach((id) => {
                    const item = byId.get(id);
                    if (item) {
                        target.appendChild(renderBadge(item));
                    }
                });
            });
        } catch (error) {
            // Silently ignore API failures so the page still renders normally.
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
