<link rel="stylesheet" href="<?= base_url('assets/games/guess-startup/css/landing.css') ?>">

<style>
    .gsl-board-head {
        display: grid;
        justify-items: center;
        gap: 0.08rem;
        margin-bottom: 1rem;
        text-align: center;
    }

    .gsl-board-head .gsl-block-title {
        margin: 0;
        color: #0f4f7c;
        font-size: clamp(1.45rem, 3vw, 2.4rem);
        line-height: 1;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .gsl-board-head .gsl-board-date {
        margin: 0;
        color: #5c7f9b;
        font-size: 0.84rem;
        letter-spacing: 0.12em;
    }
</style>

<?php
$leaderboardRows = is_array($leaderboardRows ?? null) ? $leaderboardRows : [];
$leaderboardDate = (string) ($leaderboardDate ?? date('Y-m-d'));
$todayDate = (string) ($todayDate ?? date('Y-m-d'));
$isGuessStartupEnabled = (bool) ($isGuessStartupEnabled ?? true);
$playerRank = is_array($playerRank ?? null) ? $playerRank : null;
$playerPlay = is_array($playerPlay ?? null) ? $playerPlay : null;
$player = is_array($player ?? null) ? $player : null;
$playerId = (int) ($player['id'] ?? 0);
$isTodayBoard = ($leaderboardDate === $todayDate);
$playerRankValue = is_array($playerRank) ? (int) ($playerRank['rank'] ?? 0) : 0;
$playerInTopTen = $playerRankValue > 0 && $playerRankValue <= 10;
$formatRankLabel = static function (array $row): string {
    $status = (string) ($row['status'] ?? '');
    if ($status === 'forfeited') {
        return 'Forfeited';
    }

    if (($row['rank'] ?? null) === null) {
        return 'Unranked';
    }

    return '#' . (int) ($row['rank'] ?? 0);
};

$formatElapsed = static function (int $elapsedMs): string {
    $seconds = max(0, (int) floor($elapsedMs / 1000));
    $minutes = intdiv($seconds, 60);
    $remain = $seconds % 60;
    return str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $remain, 2, '0', STR_PAD_LEFT);
};
?>

<main id="main">
    <section class="gsl-root gsl-root-leaderboard" data-navhint="light">
        <canvas id="gsThreeCanvas" class="gsl-canvas" aria-hidden="true"></canvas>

        <div class="gsl-shell">
            <header class="gsl-hero-copy">
                <h1 class="gsl-title"><?= $isTodayBoard ? 'Today\'s Leaderboard' : 'Leaderboard for ' . esc($leaderboardDate) ?></h1>
                <p class="gsl-subtitle">Top 10 players are ranked by speed, accuracy, and finish order for the selected day. If you are outside the top 10, your place appears below.</p>
                <?php if (! $isGuessStartupEnabled): ?>
                <p class="gsl-flash gsl-flash-note">Startup Hunt is paused right now, but the leaderboard remains viewable.</p>
                <?php endif; ?>
                <div class="gsl-hero-actions">
                    <a href="<?= site_url('games/guess-the-startup') ?>" class="gsl-btn gsl-btn-primary gsl-nav-link">Back to Game</a>
                    <?php if ($isGuessStartupEnabled): ?>
                    <a href="<?= site_url('games/guess-the-startup/play?autostart=1') ?>" class="gsl-btn gsl-btn-ghost gsl-nav-link">Play</a>
                    <?php else: ?>
                    <button type="button" class="gsl-btn gsl-btn-ghost gsl-btn-disabled" disabled aria-disabled="true">Play Paused</button>
                    <?php endif; ?>
                </div>

                <form class="gsl-filter-form" method="get" action="<?= site_url('games/guess-the-startup/leaderboard') ?>">
                    <label class="gsl-filter-label" for="gslFilterDate">Filter by day</label>
                    <div class="gsl-filter-controls">
                        <input
                            id="gslFilterDate"
                            class="gsl-date-input"
                            type="date"
                            name="date"
                            value="<?= esc($leaderboardDate) ?>"
                            max="<?= esc($todayDate) ?>"
                        >
                        <button type="submit" class="gsl-btn gsl-btn-primary">Apply Day</button>
                        <a href="<?= site_url('games/guess-the-startup/leaderboard') ?>" class="gsl-btn gsl-btn-ghost gsl-nav-link">Today</a>
                    </div>
                </form>
            </header>

            <section class="gsl-info-grid" aria-label="Leaderboard details">
                <article class="gsl-panel gsl-panel-leaderboard">
                    <div class="gsl-board-head">
                        <h2 class="gsl-block-title">Top 10</h2>
                        <p class="gsl-board-date"><?= esc($leaderboardDate) ?></p>
                    </div>
                    <?php if ($leaderboardRows === []): ?>
                        <p class="gsl-empty">No completed plays for this day yet.</p>
                    <?php else: ?>
                        <div class="gsl-table-wrap">
                            <table class="gsl-table" aria-label="Top 10 leaderboard table">
                                <thead>
                                    <tr>
                                        <th scope="col">Rank</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">School</th>
                                        <th scope="col">Correct</th>
                                        <th scope="col">Score</th>
                                        <th scope="col">Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leaderboardRows as $row): ?>
                                        <?php
                                        $rank = (int) ($row['rank'] ?? 0);
                                        $status = (string) ($row['status'] ?? '');
                                        $isSelfRow = $playerId > 0 && (int) ($row['playerId'] ?? 0) === $playerId;
                                        $rankLabel = $formatRankLabel($row);
                                        $tierClass = '';
                                        if ($rank === 1) {
                                            $tierClass = ' gsl-row-top-1';
                                        } elseif ($rank === 2) {
                                            $tierClass = ' gsl-row-top-2';
                                        } elseif ($rank === 3) {
                                            $tierClass = ' gsl-row-top-3';
                                        }
                                        ?>
                                        <tr class="gsl-row<?= $tierClass ?><?= $status === 'forfeited' ? ' gsl-row-forfeited' : '' ?><?= $isSelfRow ? ' gsl-row-self' : '' ?>">
                                            <td class="gsl-cell-rank">
                                                <?php if ($rankLabel === 'Forfeited'): ?>
                                                    <span class="gsl-rank-forfeited">Forfeited</span>
                                                <?php elseif ($rankLabel === 'Unranked'): ?>
                                                    <span class="gsl-rank-unranked">Unranked</span>
                                                <?php else: ?>
                                                    <?= esc($rankLabel) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="gsl-cell-name">
                                                <strong><?= esc((string) ($row['fullName'] ?? 'Player')) ?></strong>
                                                <?php if ($isSelfRow): ?>
                                                    <span class="gsl-you-pill">You</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc((string) ($row['school'] ?? '')) ?></td>
                                            <td class="gsl-cell-score"><?= (int) ($row['attemptsUsed'] ?? 0) ?>/5</td>
                                            <td class="gsl-cell-score"><?= (int) ($row['score'] ?? 0) ?></td>
                                            <td class="gsl-cell-time"><?= esc($formatElapsed((int) ($row['elapsedMs'] ?? 0))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if ($player !== null && is_array($playerRank) && ! $playerInTopTen): ?>
                        <div class="gsl-rank-below" aria-label="Your rank outside top 10">
                            <p class="gsl-rank-below-label">Your Rank (Outside Top 10)</p>
                            <p class="gsl-rank-below-value">
                                <?= esc($formatRankLabel($playerRank)) ?> - <?= esc((string) ($playerRank['fullName'] ?? 'Player')) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </article>
            </section>
        </div>
    </section>
</main>
