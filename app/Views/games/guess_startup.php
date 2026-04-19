<link rel="stylesheet" href="<?= base_url('assets/games/guess-startup/css/landing.css') ?>">

<?php
$player = $player ?? null;
$isProfileComplete = (bool) ($isProfileComplete ?? false);
$todayPlay = is_array($todayPlay ?? null) ? $todayPlay : null;
$todayRank = is_array($todayRank ?? null) ? $todayRank : null;
$playStatus = (string) ($todayPlay['status'] ?? '');
$playAttemptsUsed = (int) ($todayPlay['attemptsUsed'] ?? 0);
$hasPlayedToday = in_array($playStatus, ['solved', 'forfeited'], true);
$isTopThreeWinner = (bool) ($isTopThreeWinner ?? false);
$leaderboardRows = is_array($leaderboardRows ?? null) ? $leaderboardRows : [];
$leaderboardDate = (string) ($leaderboardDate ?? date('Y-m-d'));

$statusLabel = match ($playStatus) {
    'solved' => 'Completed',
    'forfeited' => 'Forfeited',
    'in_progress' => 'In Progress',
    default => 'Not Played',
};

$flashError = session('gs_error');
$flashNotice = session('gs_notice');
$flashSuccess = session('gs_success');
$playerAvatar = $player ? trim((string) ($player['avatarUrl'] ?? '')) : '';

$formatElapsed = static function (int $elapsedMs): string {
    $seconds = max(0, (int) floor($elapsedMs / 1000));
    $minutes = intdiv($seconds, 60);
    $remain = $seconds % 60;
    return str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $remain, 2, '0', STR_PAD_LEFT);
};
?>

<main id="main">
    <section class="gsl-root" data-navhint="light">
        <div class="gsl-nav-cloud-mask" aria-hidden="true">
            <span class="gsl-mask-cloud c1"></span>
            <span class="gsl-mask-cloud c2"></span>
            <span class="gsl-mask-cloud c3"></span>
            <span class="gsl-mask-cloud c4"></span>
        </div>

        <canvas id="gsThreeCanvas" class="gsl-canvas" aria-hidden="true"></canvas>

        <div class="gsl-scenery" aria-hidden="true">
            <div class="gsl-sun"></div>
            <div class="gsl-cloud-layer top">
                <span class="gsl-cloud c1"></span>
                <span class="gsl-cloud c2"></span>
                <span class="gsl-cloud c3"></span>
            </div>
            <div class="gsl-cloud-layer mid">
                <span class="gsl-cloud c4"></span>
                <span class="gsl-cloud c5"></span>
                <span class="gsl-cloud c6"></span>
            </div>
            <div class="gsl-mountain m1"></div>
            <div class="gsl-mountain m2"></div>
            <div class="gsl-mountain m3"></div>
            <div class="gsl-ground"></div>
        </div>

        <div class="gsl-shell">
            <header class="gsl-hero-copy">
                <h1 class="gsl-title">Startup Hunt</h1>
                <p class="gsl-subtitle">Tap Play, complete your profile, then compete on today&apos;s leaderboard.</p>

                <?php if (! empty($flashError)): ?>
                <p class="gsl-flash gsl-flash-bad"><?= esc($flashError) ?></p>
                <?php endif; ?>
                <?php if (! empty($flashNotice)): ?>
                <p class="gsl-flash gsl-flash-note"><?= esc($flashNotice) ?></p>
                <?php endif; ?>
                <?php if (! empty($flashSuccess)): ?>
                <p class="gsl-flash gsl-flash-good"><?= esc($flashSuccess) ?></p>
                <?php endif; ?>

                <div class="gsl-hero-actions">
                    <?php if ($isTopThreeWinner): ?>
                    <button type="button" class="gsl-btn gsl-btn-primary gsl-btn-disabled" disabled
                        aria-disabled="true">Top 3 Winner - No Longer Eligible</button>
                    <?php elseif ($hasPlayedToday): ?>
                    <button type="button" class="gsl-btn gsl-btn-primary gsl-btn-disabled" disabled
                        aria-disabled="true">Maxed Out Today</button>
                    <?php else: ?>
                    <a id="gslEnterBtn" href="<?= site_url('games/guess-the-startup/play?autostart=1') ?>"
                        class="gsl-btn gsl-btn-primary gsl-nav-link">Play</a>
                    <?php endif; ?>
                    <a href="<?= site_url('games/guess-the-startup/leaderboard') ?>"
                        class="gsl-btn gsl-btn-ghost gsl-nav-link">Leaderboard</a>
                    <?php if ($player !== null): ?>
                    <a href="<?= site_url('games/guess-the-startup/sign-out') ?>" class="gsl-btn gsl-btn-ghost">Reset
                        Profile</a>
                    <?php endif; ?>
                </div>
            </header>

            <section class="gsl-info-grid" aria-label="Game details">
                <article class="gsl-panel">
                    <h2 class="gsl-block-title">Player Status</h2>
                    <ul class="gsl-mode-list">
                        <li class="gsl-mode-item">
                            <span class="gsl-mode-icon"><i class="fa-solid fa-user" aria-hidden="true"></i></span>
                            <div>
                                <strong class="gsl-player-identity">
                                    <?php if ($player && $playerAvatar !== ''): ?>
                                    <img src="<?= esc($playerAvatar) ?>" alt="Profile photo" class="gsl-player-avatar">
                                    <?php endif; ?>
                                    <span><?= $player ? esc((string) ($player['fullName'] ?? $player['email'] ?? 'Player')) : 'Guest' ?></span>
                                </strong>
                                <span><?= $player ? 'Profile saved and ready for play.' : 'Press Play to set up your name and school.' ?></span>
                            </div>
                        </li>
                        <li class="gsl-mode-item">
                            <span class="gsl-mode-icon"><i class="fa-solid fa-id-card" aria-hidden="true"></i></span>
                            <div>
                                <strong>Profile</strong>
                                <span><?= $player ? ($isProfileComplete ? 'Complete and ready to play.' : 'Incomplete. Fill in your name and school.') : 'Required before the game starts.' ?></span>
                            </div>
                        </li>
                        <li class="gsl-mode-item">
                            <span class="gsl-mode-icon"><i class="fa-solid fa-calendar-day"
                                    aria-hidden="true"></i></span>
                            <div>
                                <strong>Today&apos;s Play</strong>
                                <span>Status: <?= esc($isTopThreeWinner ? 'Top 3 Winner - No Longer Eligible' : $statusLabel) ?></span>
                            </div>
                        </li>
                        <?php if (is_array($todayPlay)): ?>
                        <li class="gsl-mode-item">
                            <span class="gsl-mode-icon"><i class="fa-solid fa-stopwatch" aria-hidden="true"></i></span>
                            <div>
                                <strong>Latest Result</strong>
                                <span>
                                    Correct <?= (int) ($todayPlay['attemptsUsed'] ?? 0) ?>/5,
                                    Time <?= esc($formatElapsed((int) ($todayPlay['elapsedMs'] ?? 0))) ?>,
                                    Score <?= (int) ($todayPlay['score'] ?? 0) ?>
                                </span>
                            </div>
                        </li>
                        <?php if (is_array($todayRank)): ?>
                        <li class="gsl-mode-item">
                            <span class="gsl-mode-icon"><i class="fa-solid fa-trophy" aria-hidden="true"></i></span>
                            <div>
                                <strong>Standing</strong>
                                <span>
                                    <?php if ((string) ($todayRank['status'] ?? '') === 'forfeited'): ?>
                                        Forfeited
                                    <?php elseif (($todayRank['rank'] ?? null) === null): ?>
                                        Unranked
                                    <?php else: ?>
                                        #<?= (int) ($todayRank['rank'] ?? 0) ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </article>

                <article class="gsl-panel gsl-panel-rules">
                    <h2 class="gsl-block-title">Startup Hunt Rules</h2>
                    <ul class="gsl-score-list">
                        <li><strong>Objective:</strong> Guess five startup-related words as quickly as possible. You have up to five attempts for each word.</li>
                        <li><strong>Scoring:</strong> Round score = 50 base points + up to 50 time bonus points, with a maximum of 100 points per word.</li>
                        <li><strong>Base Points:</strong> Base points are 50.</li>
                        <li><strong>Time Bonus:</strong> Time bonus starts at 50 and drops by 1 point about every 2.4 seconds.</li>
                        <li><strong>Penalty Rule:</strong> Wrong guesses reduce the score by 5 points each, up to 50 points total.</li>
                        <li><strong>Color Clues:</strong> Green = correct letter in correct position, Yellow = correct letter in wrong position, Gray = letter is not in the word.</li>
                        <li><strong>Leaderboard:</strong> Top 10 players earn a place based on speed, accuracy, and finish order.</li>
                        <li><strong>Special Prize:</strong> The day&apos;s top 3 players will win special prizes.</li>
                    </ul>
                </article>
            </section>
        </div>

        <div id="gslInfoModal" class="gsl-info-modal" hidden>
            <div class="gsl-info-modal-backdrop" data-close="modal"></div>
            <div class="gsl-info-modal-panel" role="dialog" aria-modal="true" aria-labelledby="gslInfoTitle">
                <button id="gslInfoClose" type="button" class="gsl-info-close"
                    aria-label="Close info modal">&times;</button>
                <h2 id="gslInfoTitle">Daily Startup Hunt Flow</h2>
                <ul class="gsl-info-list">
                    <li><strong>Play first:</strong> Select Play from the lobby.</li>
                    <li><strong>Profile gate:</strong> Your name and school are required once before gameplay.
                    </li>
                    <li><strong>Objective:</strong> Guess five startup-related words as quickly as possible. You have up to five attempts for each word.</li>
                    <li><strong>Color clues:</strong> Green = correct position, Yellow = wrong position, Gray = not in the word.</li>
                    <li><strong>Leaderboard:</strong> Top 10 players earn a place based on speed, accuracy, and finish order.</li>
                    <li><strong>Special Prize:</strong> The day&apos;s top 3 players will win special prizes.</li>
                </ul>
            </div>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/three@0.161.0/build/three.min.js" defer></script>
<script src="<?= base_url('assets/games/guess-startup/js/three-bg.js') ?>" defer></script>
<script src="<?= base_url('assets/games/guess-startup/js/landing.js') ?>" defer></script>