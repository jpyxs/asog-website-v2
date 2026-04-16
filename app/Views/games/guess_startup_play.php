<link rel="stylesheet" href="<?= base_url('assets/games/guess-startup/css/play.css') ?>">

<main id="main">
    <section id="gsBooth" class="gsp-root gsp-root-wordle" data-navhint="light">
        <div class="gsp-scenery" aria-hidden="true">
            <div class="gsp-sun"></div>
            <div class="gsp-cloud-layer">
                <span class="gsp-cloud c1"></span>
                <span class="gsp-cloud c2"></span>
                <span class="gsp-cloud c3"></span>
            </div>
            <div class="gsp-mountain m1"></div>
            <div class="gsp-mountain m2"></div>
            <div class="gsp-mountain m3"></div>
            <div class="gsp-ground"></div>
        </div>

        <div class="gsp-shell">
            <header class="gsp-gamebar" aria-label="Game header">
                <div class="gsp-gamebar-left">
                    <div class="gsp-gamebar-time" aria-live="polite">
                        <span>Time</span>
                        <strong id="gsSessionTimer">00:00</strong>
                    </div>
                </div>
                <div class="gsp-gamebar-center">
                    <div class="gsp-gamebar-score" aria-live="polite">
                        <span>Score</span>
                        <strong id="gsScoreText">0</strong>
                    </div>
                </div>
                <div class="gsp-gamebar-right">
                    <button id="gsScoreInfoBtn" type="button" class="gsp-score-info" aria-label="How scoring works">i</button>
                    <button id="gsExitBtn" type="button" class="gsp-exit-btn">Exit</button>
                </div>
            </header>

            <section id="gsGate" class="gsp-gate" hidden>
                <h1 class="gsp-title">Startup Hunt</h1>
                <p class="gsp-subtitle">In one trial, guess five startup words as quickly as possible. You have up to 5 attempts for each word.</p>
                <button id="gsStartBtn" type="button" class="gs-btn gs-btn-primary">Start Startup Hunt Trial</button>
                <p class="gsp-gate-note">Refresh keeps your active run in this browser. Press Exit to forfeit today&apos;s entry.</p>
            </section>

            <section id="gsRoundPanel" class="gsp-round" hidden>
                <div class="gsp-round-head">
                    <h3 id="gsRoundHeading">Startup Hunt</h3>
                    <span id="gsRoundTimer" class="gsp-round-timer" hidden>00:00</span>
                </div>

                <div id="gsRoundHost" class="gsp-round-host"></div>
                <div id="gsInlineFeedback" class="gsp-feedback" aria-live="polite"></div>

                <div class="gsp-actions">
                    <button id="gsSubmitBtn" type="button" class="gs-btn gs-btn-primary">Lock Answer</button>
                </div>
            </section>

            <section id="gsRewardPanel" class="gsp-reward" hidden>
                <p id="gsRewardState" class="gsp-reward-state">Round Complete</p>
                <h3 id="gsRewardAnswer">Answer</h3>
                <p id="gsRewardFact" class="gsp-reward-fact">Interesting fact goes here.</p>
                <div class="gsp-reward-actions">
                    <a id="gsRewardLink" class="gs-btn gs-btn-ghost" href="#" target="_blank" rel="noopener">Learn More</a>
                    <button id="gsNextBtn" type="button" class="gs-btn gs-btn-primary">Next Round</button>
                </div>
            </section>

            <section id="gsFinalPanel" class="gsp-final" hidden>
                <p class="gsp-final-kicker">Session Complete</p>
                <h3>Trial Maxed Out</h3>
                <p id="gsFinalScore" class="gsp-final-score">Final score: 0</p>
                <p id="gsFinalMessage">You have maxed your trial. Go back to lobby or see leaderboards.</p>
                <div class="gsp-final-actions">
                    <button id="gsRestartBtn" type="button" class="gs-btn gs-btn-primary">Back to Lobby</button>
                    <button id="gsLeaderboardBtn" type="button" class="gs-btn gs-btn-ghost">See Leaderboards</button>
                </div>
            </section>
        </div>

        <div id="gsScoreInfoModal" class="gsp-info-modal" hidden>
            <div class="gsp-info-backdrop" data-close="modal"></div>
            <div class="gsp-info-panel" role="dialog" aria-modal="true" aria-labelledby="gsScoreInfoTitle">
                <button id="gsScoreInfoClose" class="gsp-info-close" type="button" aria-label="Close scoring details">&times;</button>
                <h2 id="gsScoreInfoTitle">Scoring Rules</h2>
                <ul class="gsp-info-list">
                    <li><strong id="gsRuleDifficulty">Objective:</strong> Guess five startup-related words as quickly as possible. You have up to five attempts for each word.</li>
                    <li id="gsRuleScoring">Round score = 50 base points + up to 50 time bonus points. Maximum score per round is 100.</li>
                    <li id="gsRuleBasePoints">Base points are 50.</li>
                    <li id="gsRuleTimeBonus">Time bonus starts at 50 and drops by 1 point about every 2.4 seconds.</li>
                    <li id="gsRuleAttemptBonus">Wrong guesses reduce the score by 5 points each, up to 50 points total.</li>
                    <li><strong id="gsRuleSpeed">Green:</strong> Correct letter in the correct position.</li>
                    <li><strong id="gsRuleAccuracy">Yellow:</strong> Correct letter in the wrong position.</li>
                    <li><strong id="gsRuleHints">Gray:</strong> Letter is not in the word.</li>
                    <li id="gsRuleFairness">Leaderboard: Top 10 players are ranked by speed, accuracy, and finish order. Top 3 win special prizes.</li>
                </ul>
            </div>
        </div>

        <div id="gsExitModal" class="gsp-info-modal" hidden>
            <div class="gsp-info-backdrop" data-close="exit"></div>
            <div class="gsp-info-panel" role="dialog" aria-modal="true" aria-labelledby="gsExitTitle" aria-describedby="gsExitMessage">
                <button id="gsExitClose" class="gsp-info-close" type="button" aria-label="Close exit warning">&times;</button>
                <h2 id="gsExitTitle">Exit current game?</h2>
                <p id="gsExitMessage" class="gsp-info-copy">Your game will be forfeited, and you will no longer be able to play today.</p>
                <div class="gsp-info-actions">
                    <button id="gsExitCancelBtn" type="button" class="gs-btn gs-btn-secondary">Keep Playing</button>
                    <button id="gsExitConfirmBtn" type="button" class="gs-btn gs-btn-primary">Exit and Forfeit</button>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
window.GS_GAME_CONFIG = {
    startUrl: <?= json_encode(site_url('api/games/guess-startup/start')) ?>,
    submitUrl: <?= json_encode(site_url('api/games/guess-startup/submit')) ?>,
    abandonUrl: <?= json_encode(site_url('api/games/guess-startup/abandon')) ?>,
    leaderboardUrl: <?= json_encode(site_url('api/games/guess-startup/leaderboard')) ?>,
    leaderboardPageUrl: <?= json_encode(site_url('games/guess-the-startup/leaderboard')) ?>,
    lobbyUrl: <?= json_encode(site_url('games/guess-the-startup')) ?>,
    csrfHeaderName: <?= json_encode((string) ($csrfHeaderName ?? 'X-CSRF-TOKEN')) ?>,
    csrfCookieName: <?= json_encode((string) ($csrfCookieName ?? 'csrf_cookie_name')) ?>,
    csrfTokenName: <?= json_encode(csrf_token()) ?>,
    csrfHash: <?= json_encode(csrf_hash()) ?>
};

(function () {
    var prevHtmlOverflow = document.documentElement.style.overflow;
    var prevBodyOverflow = document.body.style.overflow;
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';

    window.addEventListener('beforeunload', function () {
        document.documentElement.style.overflow = prevHtmlOverflow;
        document.body.style.overflow = prevBodyOverflow;
    });
})();
</script>

<script src="<?= base_url('assets/games/guess-startup/js/app.js') ?>" defer></script>
