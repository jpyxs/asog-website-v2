<link rel="stylesheet" href="<?= base_url('assets/css/adminSettings.css') ?>">

<div class="settings-stack">
    <div class="settings-card">
        <div class="settings-head">
            <p class="settings-kicker">Landing & Gameplay Control</p>
            <h2>Guess The Startup Availability</h2>
            <p class="settings-copy">Use this switch to pause or resume public gameplay. When disabled, visitors can still view the lobby and leaderboard, but they cannot start a new round or submit answers.</p>
        </div>

        <form method="POST" action="<?= site_url('admin/settings/guess-startup/availability') ?>" class="settings-form" data-toggle-form>
            <?= csrf_field() ?>

            <div class="settings-toggle-row">
                <div class="settings-toggle-copy">
                    <strong>Guess The Startup</strong>
                    <span><?= ! empty($isGuessStartupEnabled) ? 'Currently available to visitors' : 'Currently unavailable to visitors' ?></span>
                </div>

                <label class="settings-switch" for="guessStartupEnabled">
                    <input type="hidden" name="guessStartupEnabled" value="0">
                    <input
                        id="guessStartupEnabled"
                        type="checkbox"
                        name="guessStartupEnabled"
                        value="1"
                        <?= ! empty($isGuessStartupEnabled) ? 'checked' : '' ?>
                    >
                    <span class="settings-slider" aria-hidden="true"></span>
                    <span class="settings-switch-label"><?= ! empty($isGuessStartupEnabled) ? 'ON' : 'OFF' ?></span>
                </label>
            </div>

            <div class="settings-actions">
                <button type="submit" class="btn btn-p">Save Availability</button>
            </div>
        </form>
    </div>

    <div class="settings-card">
        <div class="settings-head">
            <p class="settings-kicker">Organization Page Control</p>
            <h2>Interns Section Visibility</h2>
            <p class="settings-copy">Use this switch to show or hide the interns section on the public Organization page.</p>
        </div>

        <form method="POST" action="<?= site_url('admin/settings/interns-visibility') ?>" class="settings-form" data-toggle-form>
            <?= csrf_field() ?>

            <div class="settings-toggle-row">
                <div class="settings-toggle-copy">
                    <strong>Show Interns Section</strong>
                    <span><?= ! empty($showInternsSection) ? 'Currently visible on the Organization page' : 'Currently hidden on the Organization page' ?></span>
                </div>

                <label class="settings-switch" for="showInternsSection">
                    <input type="hidden" name="showInternsSection" value="0">
                    <input
                        id="showInternsSection"
                        type="checkbox"
                        name="showInternsSection"
                        value="1"
                        <?= ! empty($showInternsSection) ? 'checked' : '' ?>
                    >
                    <span class="settings-slider" aria-hidden="true"></span>
                    <span class="settings-switch-label"><?= ! empty($showInternsSection) ? 'ON' : 'OFF' ?></span>
                </label>
            </div>

            <div class="settings-actions">
                <button type="submit" class="btn btn-p">Save Visibility</button>
            </div>
        </form>
    </div>

    <div class="settings-card lf-panel">
        <div class="lf-wrap">
            <div class="lf-copy">
                <p class="lf-kicker">Homepage Incubatees</p>
                <h3 class="lf-title">Display Filter</h3>
                <p class="lf-desc">Choose one cohort or all cohorts for the landing section. If the selected cohort has no published startups yet, the site shows "Will be announced soon".</p>
            </div>
            <form method="POST" action="<?= site_url('admin/settings/homepage-incubatees-filter') ?>" class="lf-form">
                <?= csrf_field() ?>
                <div class="lf-field">
                    <label class="lf-label" for="landingCohortFilter">Cohort</label>
                    <select id="landingCohortFilter" name="landingCohortFilter" class="lf-select">
                        <option value="all" <?= ($selectedLandingFilter ?? 'all') === 'all' ? 'selected' : '' ?>>All Cohorts</option>
                        <?php foreach (($landingFilterOptions ?? []) as $cohortName): ?>
                            <option value="<?= esc($cohortName) ?>" <?= ($selectedLandingFilter ?? 'all') === $cohortName ? 'selected' : '' ?>>
                                <?= esc($cohortName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-p">Save</button>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    document.querySelectorAll('[data-toggle-form]').forEach((form) => {
        const checkbox = form.querySelector('input[type="checkbox"]');
        const stateLabel = form.querySelector('.settings-switch-label');
        if (!checkbox || !stateLabel) {
            return;
        }

        const updateLabel = () => {
            stateLabel.textContent = checkbox.checked ? 'ON' : 'OFF';
        };

        checkbox.addEventListener('change', updateLabel);
        updateLabel();
    });
})();
</script>
