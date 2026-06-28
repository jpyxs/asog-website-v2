<link rel="stylesheet" href="<?= base_url('assets/css/adminGames.css') ?>">

<div class="games-card">
    <div class="games-head">
        <p class="games-kicker">Organization Page Control</p>
        <h2>Interns Section Visibility</h2>
        <p class="games-copy">Use this switch to show or hide the interns section on the public Organization page.</p>
    </div>

    <form method="POST" action="<?= site_url('admin/organization/interns-visibility') ?>" class="games-form">
        <?= csrf_field() ?>

        <div class="games-toggle-row">
            <div class="games-toggle-copy">
                <strong>Show Interns Section</strong>
                <span><?= ! empty($showInternsSection) ? 'Currently visible on the Organization page' : 'Currently hidden on the Organization page' ?></span>
            </div>

            <label class="games-switch" for="showInternsSection">
                <input type="hidden" name="showInternsSection" value="0">
                <input
                    id="showInternsSection"
                    type="checkbox"
                    name="showInternsSection"
                    value="1"
                    <?= ! empty($showInternsSection) ? 'checked' : '' ?>
                >
                <span class="games-slider" aria-hidden="true"></span>
                <span class="games-switch-label"><?= ! empty($showInternsSection) ? 'ON' : 'OFF' ?></span>
            </label>
        </div>

        <div class="games-actions">
            <button type="submit" class="btn btn-p">Save Visibility</button>
        </div>
    </form>
</div>

<script>
(() => {
    const checkbox = document.getElementById('showInternsSection');
    const stateLabel = document.querySelector('.games-switch-label');
    if (!checkbox || !stateLabel) {
        return;
    }

    const updateLabel = () => {
        stateLabel.textContent = checkbox.checked ? 'ON' : 'OFF';
    };

    checkbox.addEventListener('change', updateLabel);
    updateLabel();
})();
</script>
