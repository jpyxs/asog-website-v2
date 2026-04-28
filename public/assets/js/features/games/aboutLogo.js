(() => {
    const points = document.querySelectorAll('.logo-point');
    const backdrop = document.getElementById('logoPartBackdrop');
    const modal = document.getElementById('logoPartModal');
    const closeBtn = document.getElementById('logoPartClose');
    const titleEl = document.getElementById('logoPartTitle');
    const textEl = document.getElementById('logoPartText');

    if (!points.length || !backdrop || !modal || !closeBtn || !titleEl || !textEl) {
        return;
    }

    const content = {
        gear: {
            title: 'Gear',
            text: 'Gear details coming soon.'
        },
        star: {
            title: 'Star',
            text: 'Star details coming soon.'
        },
        mountain: {
            title: 'Mountain',
            text: 'Mountain details coming soon.'
        }
    };

    function openModal(part) {
        const data = content[part] || { title: 'Logo', text: 'Details coming soon.' };
        titleEl.textContent = data.title;
        textEl.textContent = data.text;

        backdrop.classList.remove('hidden');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        backdrop.classList.add('hidden');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    points.forEach((point) => {
        point.addEventListener('click', () => openModal(point.dataset.logoPart));
    });

    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
})();
