<link rel="stylesheet" href="<?= base_url('assets/css/adminGames.css') ?>">

<div class="games-card">
    <div class="games-head">
        <p class="games-kicker">Landing & Gameplay Control</p>
        <h2>Guess The Startup Availability</h2>
        <p class="games-copy">Use this switch to pause or resume public gameplay. When disabled, visitors can still view the lobby and leaderboard, but they cannot start a new round or submit answers.</p>
    </div>

    <form method="POST" action="<?= site_url('admin/games/guess-startup/availability') ?>" class="games-form">
        <?= csrf_field() ?>

        <div class="games-toggle-row">
            <div class="games-toggle-copy">
                <strong>Guess The Startup</strong>
                <span><?= ! empty($isGuessStartupEnabled) ? 'Currently available to visitors' : 'Currently unavailable to visitors' ?></span>
            </div>

            <label class="games-switch" for="guessStartupEnabled">
                <input type="hidden" name="guessStartupEnabled" value="0">
                <input
                    id="guessStartupEnabled"
                    type="checkbox"
                    name="guessStartupEnabled"
                    value="1"
                    <?= ! empty($isGuessStartupEnabled) ? 'checked' : '' ?>
                >
                <span class="games-slider" aria-hidden="true"></span>
                <span class="games-switch-label"><?= ! empty($isGuessStartupEnabled) ? 'ON' : 'OFF' ?></span>
            </label>
        </div>

        <div class="games-actions">
            <button type="submit" class="btn btn-p">Save Availability</button>
        </div>
    </form>
</div>

<script>
(() => {
    const checkbox = document.getElementById('guessStartupEnabled');
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
