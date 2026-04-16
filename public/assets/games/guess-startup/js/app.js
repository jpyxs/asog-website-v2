(function () {
    const CONFIG = window.GS_GAME_CONFIG || {};
    const START_URL = String(CONFIG.startUrl || '/api/games/guess-startup/start');
    const SUBMIT_URL = String(CONFIG.submitUrl || '/api/games/guess-startup/submit');
    const ABANDON_URL = String(CONFIG.abandonUrl || '/api/games/guess-startup/abandon');
    const LEADERBOARD_PAGE_URL = String(CONFIG.leaderboardPageUrl || '/games/guess-the-startup/leaderboard');
    const LOBBY_URL = String(CONFIG.lobbyUrl || '/games/guess-the-startup');
    const CSRF_HEADER_NAME = String(CONFIG.csrfHeaderName || 'X-CSRF-TOKEN');
    const CSRF_COOKIE_NAME = String(CONFIG.csrfCookieName || 'csrf_cookie_name');
    const CSRF_TOKEN_NAME = String(CONFIG.csrfTokenName || 'csrf_test_name');
    const INITIAL_CSRF_HASH = String(CONFIG.csrfHash || '');
    const SESSION_STORAGE_KEY = 'gs_startup_active_session_id';
    let csrfHash = INITIAL_CSRF_HASH;

    const dom = {
        gate: document.getElementById('gsGate'),
        startBtn: document.getElementById('gsStartBtn'),
        roundPanel: document.getElementById('gsRoundPanel'),
        rewardPanel: document.getElementById('gsRewardPanel'),
        finalPanel: document.getElementById('gsFinalPanel'),
        scoreText: document.getElementById('gsScoreText'),
        sessionTimer: document.getElementById('gsSessionTimer'),
        roundHeading: document.getElementById('gsRoundHeading'),
        roundTimer: document.getElementById('gsRoundTimer'),
        roundHost: document.getElementById('gsRoundHost'),
        hintBtn: document.getElementById('gsHintBtn'),
        submitBtn: document.getElementById('gsSubmitBtn'),
        feedback: document.getElementById('gsInlineFeedback'),
        rewardState: document.getElementById('gsRewardState'),
        rewardAnswer: document.getElementById('gsRewardAnswer'),
        rewardFact: document.getElementById('gsRewardFact'),
        rewardLink: document.getElementById('gsRewardLink'),
        nextBtn: document.getElementById('gsNextBtn'),
        finalScore: document.getElementById('gsFinalScore'),
        finalMessage: document.getElementById('gsFinalMessage'),
        restartBtn: document.getElementById('gsRestartBtn'),
        leaderboardBtn: document.getElementById('gsLeaderboardBtn'),
        exitBtn: document.getElementById('gsExitBtn'),
        scoreInfoBtn: document.getElementById('gsScoreInfoBtn'),
        scoreInfoModal: document.getElementById('gsScoreInfoModal'),
        scoreInfoClose: document.getElementById('gsScoreInfoClose'),
        scoreInfoBackdrop: document.querySelector('#gsScoreInfoModal .gsp-info-backdrop'),
        exitModal: document.getElementById('gsExitModal'),
        exitClose: document.getElementById('gsExitClose'),
        exitCancelBtn: document.getElementById('gsExitCancelBtn'),
        exitConfirmBtn: document.getElementById('gsExitConfirmBtn'),
        exitBackdrop: document.querySelector('#gsExitModal .gsp-info-backdrop'),
        ruleDifficulty: document.getElementById('gsRuleDifficulty'),
        ruleSpeed: document.getElementById('gsRuleSpeed'),
        ruleAccuracy: document.getElementById('gsRuleAccuracy'),
        ruleHints: document.getElementById('gsRuleHints'),
        ruleFairness: document.getElementById('gsRuleFairness')
    };

    if (!dom.startBtn || !dom.roundHost) {
        return;
    }

    const MODE_LABELS = {
        logo_wordle: 'Startup Hunt',
        startup_clues: 'Startup Hunt Clues',
        filipino_spotlight: 'Filipino Spotlight',
        code_quiz: 'Code Bonus',
        asog_bonus: 'ASOG Bonus',
        charades: 'Charades Sprint'
    };

    const WORDLE_LENGTH = 5;
    const ROUND_ADVANCE_DELAY_MS = 260;
    const LAST_TRIAL_HOLD_MS = 4000;
    const WORDLE_REVEAL_STAGGER_MS = 40;
    const WORDLE_FLIP_DURATION_MS = 280;
    const WORDLE_ALL_GREEN_HOLD_AFTER_ANIMATION_MS = 2000;
    const WORDLE_REVEAL_TOTAL_MS = WORDLE_FLIP_DURATION_MS + (WORDLE_REVEAL_STAGGER_MS * (WORDLE_LENGTH - 1));
    const WORDLE_ALL_GREEN_HOLD_MS = WORDLE_REVEAL_TOTAL_MS + WORDLE_ALL_GREEN_HOLD_AFTER_ANIMATION_MS;
    const TIMED_MODES = new Set(['code_quiz', 'asog_bonus', 'charades']);

    const state = {
        sessionId: '',
        rounds: [],
        roundIndex: 0,
        totalScore: 0,
        roundStartMs: 0,
        timerId: null,
        timerLeft: 0,
        sessionClockId: null,
        sessionElapsedSec: 0,
        sessionClockPaused: false,
        attemptHistory: {},
        hintsUsed: 0,
        revealedHints: 1,
        wordleRows: [],
        wordleDraft: '',
        wordleAnimateRow: -1,
        wordleKeyState: {},
        locked: false,
        started: false,
        finished: false,
        abandoning: false,
        rules: null
    };

    let wordleKeydownHandler = null;

    const params = new URLSearchParams(window.location.search);
    const shouldAutoStart = params.get('autostart') === '1';

    function readStoredSessionId() {
        try {
            return String(window.localStorage.getItem(SESSION_STORAGE_KEY) || '').trim();
        } catch (err) {
            return '';
        }
    }

    function storeSessionId(sessionId) {
        try {
            const value = String(sessionId || '').trim();
            if (value === '') {
                window.localStorage.removeItem(SESSION_STORAGE_KEY);
                return;
            }
            window.localStorage.setItem(SESSION_STORAGE_KEY, value);
        } catch (err) {
            // Ignore storage failures.
        }
    }

    function clearStoredSessionId() {
        storeSessionId('');
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getCookie(name) {
        const escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const match = document.cookie.match(new RegExp('(?:^|; )' + escaped + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function syncCsrfFromResponse(res, payload) {
        const headerToken = res && typeof res.headers?.get === 'function'
            ? String(res.headers.get(CSRF_HEADER_NAME) || '').trim()
            : '';
        if (headerToken) {
            csrfHash = headerToken;
            return;
        }

        if (payload && typeof payload.csrf_hash === 'string' && payload.csrf_hash.trim() !== '') {
            csrfHash = payload.csrf_hash.trim();
        }
    }

    async function postJson(url, body) {
        const payload = (body && typeof body === 'object') ? { ...body } : {};
        const headers = { 'Content-Type': 'application/json' };
        const csrfToken = csrfHash || getCookie(CSRF_COOKIE_NAME) || INITIAL_CSRF_HASH;
        if (csrfToken) {
            headers[CSRF_HEADER_NAME] = csrfToken;
            payload[CSRF_TOKEN_NAME] = csrfToken;
        }

        const res = await fetch(url, {
            method: 'POST',
            headers: headers,
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        });

        let data = {};
        try {
            data = await res.json();
        } catch (err) {
            data = {};
        }

        syncCsrfFromResponse(res, data);

        if (!res.ok) {
            const error = new Error(data && data.error ? data.error : 'Request failed');
            error.status = res.status;
            error.payload = data;
            throw error;
        }

        return data;
    }

    function normalizeWordleGuess(value) {
        return String(value || '')
            .toLowerCase()
            .replace(/[^a-z]/g, '')
            .slice(0, WORDLE_LENGTH);
    }

    function getCurrentRound() {
        return state.rounds[state.roundIndex] || null;
    }

    function modeLabel(mode) {
        return MODE_LABELS[mode] || mode;
    }

    function setFeedback(type, message) {
        if (!dom.feedback) {
            return;
        }

        const map = {
            good: 'gs-feedback-good',
            bad: 'gs-feedback-bad',
            note: 'gs-feedback-note'
        };
        dom.feedback.innerHTML = '<div class="' + (map[type] || map.note) + '">' + escapeHtml(message) + '</div>';
    }

    function clearFeedback() {
        if (!dom.feedback) {
            return;
        }

        dom.feedback.innerHTML = '';
    }

    function updateHud() {
        const round = getCurrentRound();
        if (!round && !dom.scoreText) {
            return;
        }

        if (dom.scoreText) {
            dom.scoreText.textContent = String(state.totalScore);
        }
    }

    function formatTimer(seconds) {
        const value = Math.max(0, Number(seconds || 0));
        const mins = Math.floor(value / 60);
        const secs = value % 60;
        return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }

    function stopTimer() {
        if (state.timerId) {
            clearInterval(state.timerId);
            state.timerId = null;
        }
    }

    function stopSessionClock() {
        if (state.sessionClockId) {
            clearInterval(state.sessionClockId);
            state.sessionClockId = null;
        }
    }

    function startSessionClock(initialElapsedSec) {
        stopSessionClock();
        state.sessionElapsedSec = Math.max(0, Number(initialElapsedSec || 0));
        state.sessionClockPaused = false;
        if (dom.sessionTimer) {
            dom.sessionTimer.textContent = formatTimer(state.sessionElapsedSec);
        }

        state.sessionClockId = setInterval(function () {
            if (state.sessionClockPaused) {
                return;
            }
            state.sessionElapsedSec += 1;
            if (dom.sessionTimer) {
                dom.sessionTimer.textContent = formatTimer(state.sessionElapsedSec);
            }
        }, 1000);
    }

    function setSessionClockPaused(paused) {
        state.sessionClockPaused = !!paused;
    }

    function startTimer(seconds) {
        stopTimer();
        state.timerLeft = Math.max(1, Number(seconds || 60));

        if (dom.roundTimer) {
            dom.roundTimer.textContent = formatTimer(state.timerLeft);
        }

        state.timerId = setInterval(function () {
            state.timerLeft = Math.max(0, state.timerLeft - 1);

            if (dom.roundTimer) {
                dom.roundTimer.textContent = formatTimer(state.timerLeft);
            }

            if (state.timerLeft <= 0) {
                stopTimer();
                autoForfeit('Time is up. Round forfeited.');
            }
        }, 1000);
    }

    function resetRoundRuntime() {
        state.hintsUsed = 0;
        state.revealedHints = 1;
        state.wordleRows = [];
        state.wordleDraft = '';
        state.wordleAnimateRow = -1;
        state.wordleKeyState = {};
        state.locked = false;
        state.roundStartMs = Date.now();
        clearFeedback();
    }

    function normalizeAttemptHistory(raw) {
        const source = raw && typeof raw === 'object' ? raw : {};
        const map = {};

        Object.keys(source).forEach(function (key) {
            const roundId = Number(key);
            if (!Number.isFinite(roundId)) {
                return;
            }

            const rows = Array.isArray(source[key]) ? source[key] : [];
            map[roundId] = rows.filter(function (row) {
                return Array.isArray(row);
            });
        });

        return map;
    }

    function hydrateCurrentWordleState(round) {
        if (!round || round.game_type !== 'logo_wordle') {
            return;
        }

        const roundId = Number(round.id || 0);
        const rows = Array.isArray(state.attemptHistory[roundId]) ? state.attemptHistory[roundId] : [];
        state.wordleRows = rows.slice(0);
        state.wordleDraft = '';
        state.wordleAnimateRow = -1;
        state.wordleKeyState = {};

        state.wordleRows.forEach(function (tiles) {
            updateWordleKeyStateFromTiles(tiles);
        });
    }

    function applyPanelAnimation(panel) {
        if (window.gsap) {
            window.gsap.fromTo(
                panel,
                { autoAlpha: 0, y: 10 },
                { autoAlpha: 1, y: 0, duration: 0.24, ease: 'power2.out' }
            );
            return;
        }

        panel.classList.remove('gs-fade-in');
        void panel.offsetWidth;
        panel.classList.add('gs-fade-in');
    }

    function configureActionButtons(round) {
        const isWordle = round.game_type === 'logo_wordle';
        dom.submitBtn.hidden = isWordle;

        if (!isWordle) {
            dom.submitBtn.textContent = 'Lock Answer';
        }

        if (round.game_type === 'charades') {
            dom.submitBtn.textContent = 'Confirm Choice';
        }
    }

    function renderWordleGrid(maxGuesses) {
        const rows = [];
        const submittedRows = state.wordleRows.length;
        const draft = state.wordleDraft.split('');

        for (let i = 0; i < maxGuesses; i++) {
            const feedbackRow = state.wordleRows[i] || [];
            const tiles = [];

            for (let j = 0; j < WORDLE_LENGTH; j++) {
                const tile = feedbackRow[j] || null;
                let letter = '';
                let classes = 'gs-tile';
                let tileStyle = '';

                if (tile) {
                    letter = tile.letter;
                    classes += ' gs-tile-' + tile.state;
                    if (i === state.wordleAnimateRow) {
                        classes += ' flip';
                        tileStyle = ' style="animation-delay:' + String(j * WORDLE_REVEAL_STAGGER_MS) + 'ms"';
                    }
                } else if (i === submittedRows && draft[j]) {
                    letter = draft[j];
                    classes += ' gs-tile-draft';
                } else {
                    classes += ' gs-tile-empty';
                }

                tiles.push('<span class="' + classes + '"' + tileStyle + '>' + escapeHtml(letter) + '</span>');
            }

            rows.push(
                '<div class="gs-wordle-row" style="grid-template-columns: repeat(' + WORDLE_LENGTH + ', minmax(0, var(--gs-wordle-tile-size, 42px)));">' + tiles.join('') + '</div>'
            );
        }

        return '<div class="gs-wordle-grid">' + rows.join('') + '</div>';
    }

    function renderWordleKeyboard() {
        const rows = [
            ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'],
            ['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'],
            ['Enter', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', 'Back']
        ];

        return rows.map(function (row) {
            return '<div class="gs-key-row">' + row.map(function (key) {
                const normalized = key.toLowerCase() === 'back' ? 'backspace' : key.toLowerCase();
                const special = key.length > 1 ? ' gs-key-special' : '';
                return '<button type="button" class="gs-key' + special + '" data-key="' + normalized + '">' + escapeHtml(key) + '</button>';
            }).join('') + '</div>';
        }).join('');
    }

    function wordleStateRank(value) {
        if (value === 'correct') {
            return 3;
        }
        if (value === 'present') {
            return 2;
        }
        if (value === 'absent') {
            return 1;
        }
        return 0;
    }

    function updateWordleKeyStateFromTiles(tiles) {
        if (!Array.isArray(tiles)) {
            return;
        }

        tiles.forEach(function (tile) {
            const letter = String(tile && tile.letter ? tile.letter : '').toLowerCase();
            const nextState = String(tile && tile.state ? tile.state : '');

            if (!/^[a-z]$/.test(letter)) {
                return;
            }

            const currentState = String(state.wordleKeyState[letter] || '');
            if (wordleStateRank(nextState) >= wordleStateRank(currentState)) {
                state.wordleKeyState[letter] = nextState;
            }
        });
    }

    function areAllWordleTilesCorrect(tiles) {
        if (!Array.isArray(tiles) || tiles.length !== WORDLE_LENGTH) {
            return false;
        }

        return tiles.every(function (tile) {
            return String(tile && tile.state ? tile.state : '') === 'correct';
        });
    }

    function paintWordleKeyboard() {
        const keyWrap = document.getElementById('gsWordleKeys');
        if (!keyWrap) {
            return;
        }

        const keys = keyWrap.querySelectorAll('button[data-key]');
        keys.forEach(function (button) {
            const key = String(button.getAttribute('data-key') || '').toLowerCase();
            if (!/^[a-z]$/.test(key)) {
                return;
            }

            button.classList.remove('gs-key-correct', 'gs-key-present', 'gs-key-absent');

            const stateClass = state.wordleKeyState[key] || '';
            if (stateClass === 'correct') {
                button.classList.add('gs-key-correct');
            } else if (stateClass === 'present') {
                button.classList.add('gs-key-present');
            } else if (stateClass === 'absent') {
                button.classList.add('gs-key-absent');
            }
        });
    }

    function updateWordleBoard(round) {
        const board = document.getElementById('gsWordleBoard');
        if (!board || !round) {
            return;
        }

        board.innerHTML = renderWordleGrid(Number(round.max_guesses || 6));
        paintWordleKeyboard();
    }

    function unbindWordleKeyboard() {
        if (wordleKeydownHandler) {
            document.removeEventListener('keydown', wordleKeydownHandler);
            wordleKeydownHandler = null;
        }
    }

    function pushWordleKey(rawKey, round) {
        const key = String(rawKey || '').toLowerCase();
        if (!round || state.locked) {
            return;
        }

        if (key === 'enter') {
            submitRound(false);
            return;
        }

        if (key === 'backspace') {
            state.wordleDraft = state.wordleDraft.slice(0, -1);
            updateWordleBoard(round);
            return;
        }

        if (/^[a-z]$/.test(key) && state.wordleDraft.length < WORDLE_LENGTH) {
            state.wordleDraft += key.toUpperCase();
            updateWordleBoard(round);
        }
    }

    function bindWordleKeyboard(round) {
        const keyWrap = document.getElementById('gsWordleKeys');
        if (keyWrap) {
            keyWrap.addEventListener('click', function (event) {
                const button = event.target.closest('button[data-key]');
                if (!button) {
                    return;
                }

                pushWordleKey(button.getAttribute('data-key'), round);
            });
        }

        unbindWordleKeyboard();
        wordleKeydownHandler = function (event) {
            const key = String(event.key || '').toLowerCase();
            if (key !== 'enter' && key !== 'backspace' && !/^[a-z]$/.test(key)) {
                return;
            }

            if (dom.roundPanel.hidden) {
                return;
            }

            event.preventDefault();
            pushWordleKey(key, round);
        };
        document.addEventListener('keydown', wordleKeydownHandler);
    }

    function renderLogoWordle(round) {
        const guesses = Number(round.max_guesses || 6);
        const cues = Array.isArray(round.content.word_cues) ? round.content.word_cues : [];
        const cueMarkup = cues.length
            ? '<div class="gs-wordle-cues">' + cues.map(function (cue) {
                return '<span class="gs-wordle-cue">' + escapeHtml(cue) + '</span>';
            }).join('') + '</div>'
            : '';

        dom.roundHost.innerHTML =
            '<div class="gs-logo-wrap">' +
                '<p class="gs-text-soft">' + escapeHtml(round.content.prompt) + '</p>' +
                cueMarkup +
                '<div id="gsWordleBoard">' + renderWordleGrid(guesses) + '</div>' +
                '<div id="gsWordleKeys" class="gs-wordle-keys">' + renderWordleKeyboard() + '</div>' +
                '<p class="gs-wordle-tip">Use the clues. Type and press Enter to submit.</p>' +
            '</div>';

        updateWordleBoard(round);
        bindWordleKeyboard(round);
    }

    function renderClueRound(round) {
        const hints = Array.isArray(round.content.hint_sequence) ? round.content.hint_sequence : [];
        const visibleHints = hints.slice(0, state.revealedHints).map(function (hint) {
            return '<div class="gs-hint-card"><strong>' + escapeHtml(hint.label) + ':</strong> ' + escapeHtml(hint.text) + '</div>';
        }).join('');

        const spotlight = round.game_type === 'filipino_spotlight'
            ? '<span class="gs-spotlight-chip">Philippine Startup Spotlight</span>'
            : '';

        dom.roundHost.innerHTML =
            '<div class="gs-hints">' +
                spotlight +
                '<p class="gs-text-soft">' + escapeHtml(round.content.prompt) + '</p>' +
                visibleHints +
                '<input id="gsAnswerInput" class="gs-guess-input" maxlength="40" autocomplete="off" placeholder="Type your startup guess">' +
            '</div>';

        if (dom.hintBtn && state.revealedHints >= hints.length) {
            dom.hintBtn.disabled = true;
        }
    }

    function renderChoiceRound(round) {
        const options = Array.isArray(round.content.options) ? round.content.options : [];
        const charadeClue = round.game_type === 'charades'
            ? '<div class="gs-charade-clue">' + escapeHtml(round.content.emoji_clue || '') + '</div>'
            : '';
        const bonusContext = round.game_type === 'asog_bonus' && round.content.context
            ? '<p class="gs-asog-context">' + escapeHtml(round.content.context) + '</p>'
            : '';
        const snippet = round.content.snippet
            ? '<pre class="gs-code-snippet"><code>' + escapeHtml(round.content.snippet || '') + '</code></pre>'
            : '';

        dom.roundHost.innerHTML =
            '<div class="gs-code-wrap">' +
                '<p class="gs-text-soft">' + escapeHtml(round.content.prompt) + '</p>' +
                charadeClue +
                bonusContext +
                snippet +
                '<div class="gs-option-grid">' +
                    options.map(function (option, index) {
                        return '<label class="gs-choice"><input type="radio" name="gsOption" value="' + escapeHtml(option) + '" ' + (index === 0 ? 'checked' : '') + '> <span>' + escapeHtml(option) + '</span></label>';
                    }).join('') +
                '</div>' +
            '</div>';
    }

    function renderRound() {
        const round = getCurrentRound();
        if (!round) {
            return;
        }

        updateHud();
        dom.roundHeading.textContent = 'Word ' + String(state.roundIndex + 1) + ' of ' + String(state.rounds.length);
        dom.roundPanel.hidden = false;
        dom.rewardPanel.hidden = true;
        dom.rewardPanel.classList.remove('gsp-reward-success');
        dom.finalPanel.hidden = true;
        configureActionButtons(round);

        if (round.game_type === 'logo_wordle') {
            renderLogoWordle(round);
        } else if (round.game_type === 'startup_clues' || round.game_type === 'filipino_spotlight') {
            unbindWordleKeyboard();
            renderClueRound(round);
        } else {
            unbindWordleKeyboard();
            renderChoiceRound(round);
        }

        applyPanelAnimation(dom.roundPanel);

        const timed = TIMED_MODES.has(round.game_type);
        if (dom.roundTimer) {
            dom.roundTimer.hidden = !timed;
            dom.roundTimer.textContent = timed ? formatTimer(Number(round.round_seconds || 60)) : '';
        }

        if (timed) {
            startTimer(Number(round.round_seconds || 60));
        } else {
            stopTimer();
        }
    }

    function collectAnswer(round) {
        if (round.game_type === 'logo_wordle') {
            return state.wordleDraft.trim();
        }

        if (round.game_type === 'code_quiz' || round.game_type === 'asog_bonus' || round.game_type === 'charades') {
            const selected = dom.roundHost.querySelector('input[name="gsOption"]:checked');
            return selected ? selected.value : '';
        }

        const input = document.getElementById('gsAnswerInput');
        return input ? input.value.trim() : '';
    }

    function showReward(result, wasCorrect) {
        stopTimer();
        unbindWordleKeyboard();
        dom.roundPanel.hidden = true;
        dom.rewardPanel.hidden = false;
        dom.rewardPanel.classList.toggle('gsp-reward-success', !!wasCorrect);

        const reward = result.reward || { answer: '', fact: '', link: '' };
        const scoreText = wasCorrect
            ? 'Correct! +' + String((result.score_breakdown && result.score_breakdown.round_score) || 0) + ' points'
            : (result && result.lost ? 'Round lost. +0 points' : 'Round forfeited. +0 points');

        dom.rewardState.textContent = scoreText;
        dom.rewardAnswer.textContent = reward.answer ? ('Answer: ' + String(reward.answer)) : 'Answer hidden for unsolved round';
        dom.rewardFact.textContent = String(reward.fact || '');

        if (reward.link) {
            dom.rewardLink.hidden = false;
            dom.rewardLink.href = reward.link;
            dom.rewardLink.textContent = String(reward.link_label || 'Learn more');
        } else {
            dom.rewardLink.hidden = true;
            dom.rewardLink.removeAttribute('href');
        }

        const isLastRound = state.roundIndex >= (state.rounds.length - 1);
        dom.nextBtn.textContent = isLastRound ? 'View Final Result' : 'Next Round';

        applyPanelAnimation(dom.rewardPanel);
    }

    function finishSession() {
        state.finished = true;
        clearStoredSessionId();
        setSessionClockPaused(false);
        stopTimer();
        stopSessionClock();
        unbindWordleKeyboard();
        dom.roundPanel.hidden = true;
        dom.rewardPanel.hidden = true;
        dom.finalPanel.hidden = false;
        if (dom.exitBtn) {
            dom.exitBtn.hidden = true;
        }
        dom.finalScore.textContent = 'Final score: ' + String(state.totalScore);
        if (dom.finalMessage) {
            dom.finalMessage.textContent = 'You have maxed your trial. Go back to lobby or see leaderboards.';
        }
        applyPanelAnimation(dom.finalPanel);
    }

    function nextRound() {
        setSessionClockPaused(false);
        state.roundIndex += 1;

        if (state.roundIndex >= state.rounds.length) {
            finishSession();
            return;
        }

        resetRoundRuntime();
        renderRound();
    }

    async function submitRound(forfeit) {
        if (state.locked) {
            return;
        }

        const round = getCurrentRound();
        if (!round) {
            return;
        }

        let answer = collectAnswer(round);
        if (!forfeit && round.game_type === 'logo_wordle') {
            answer = normalizeWordleGuess(answer);
            if (answer.length !== WORDLE_LENGTH) {
                setFeedback('note', 'Enter exactly 5 letters.');
                return;
            }
        }

        if (!forfeit && String(answer).trim() === '') {
            setFeedback('note', 'Enter an answer before submitting.');
            return;
        }

        state.locked = true;

        const payload = {
            session_id: state.sessionId,
            round_id: round.id,
            answer: answer,
            hints_used: state.hintsUsed,
            forfeit: !!forfeit
        };

        try {
            const res = await postJson(SUBMIT_URL, payload);

            state.totalScore = Number(res.total_score || state.totalScore);
            updateHud();

            if (!res.correct) {
                if (res.attempt_feedback && Array.isArray(res.attempt_feedback.tiles) && round.game_type === 'logo_wordle') {
                    state.wordleRows.push(res.attempt_feedback.tiles);
                    state.wordleDraft = '';
                    state.wordleAnimateRow = state.wordleRows.length - 1;
                    updateWordleKeyStateFromTiles(res.attempt_feedback.tiles);
                    updateWordleBoard(round);
                    state.wordleAnimateRow = -1;
                }

                if (res.round_closed || res.forfeited || res.lost) {
                    const closeMessage = res.message || (res.lost ? 'Round lost.' : 'Round closed.');
                    setFeedback('note', closeMessage + ' Moving to next word.');
                    setSessionClockPaused(true);
                    const isLastRoundMiss = state.roundIndex >= (state.rounds.length - 1);
                    const closeDelayMs = res.lost ? LAST_TRIAL_HOLD_MS : ROUND_ADVANCE_DELAY_MS;
                    window.setTimeout(function () {
                        setSessionClockPaused(false);
                        if (isLastRoundMiss) {
                            finishSession();
                            return;
                        }
                        nextRound();
                    }, closeDelayMs);
                } else {
                    clearFeedback();
                    setSessionClockPaused(false);
                    state.locked = false;
                }

                return;
            }

            if (res.attempt_feedback && Array.isArray(res.attempt_feedback.tiles) && round.game_type === 'logo_wordle') {
                state.wordleRows.push(res.attempt_feedback.tiles);
                state.wordleDraft = '';
                state.wordleAnimateRow = state.wordleRows.length - 1;
                updateWordleKeyStateFromTiles(res.attempt_feedback.tiles);
                updateWordleBoard(round);
                state.wordleAnimateRow = -1;
            }

            const solvedWord = state.roundIndex + 1;
            const totalWords = state.rounds.length;
            setFeedback('good', 'Solved! Word ' + String(solvedWord) + ' of ' + String(totalWords) + '.');

            const allGreenSolved = round.game_type === 'logo_wordle'
                && Array.isArray(res.attempt_feedback && res.attempt_feedback.tiles)
                && areAllWordleTilesCorrect(res.attempt_feedback.tiles);

            setSessionClockPaused(true);

            const isLastSolvedRound = state.roundIndex >= (state.rounds.length - 1);
            window.setTimeout(function () {
                setSessionClockPaused(false);
                if (isLastSolvedRound) {
                    finishSession();
                    return;
                }
                nextRound();
            }, allGreenSolved ? WORDLE_ALL_GREEN_HOLD_MS : ROUND_ADVANCE_DELAY_MS);
        } catch (err) {
            setSessionClockPaused(false);
            setFeedback('bad', err.message || 'Submission failed.');
            state.locked = false;
        }
    }

    async function autoForfeit(message) {
        if (state.locked) {
            return;
        }
        setFeedback('note', message || 'Round ended.');
        await submitRound(true);
    }

    function hydrateScoringRules(rules) {
        if (!rules) {
            return;
        }

        if (dom.ruleDifficulty) {
            dom.ruleDifficulty.textContent = 'Objective: ' + String(rules.attempt_policy || 'Guess five startup-related words as quickly as possible. You have up to five attempts for each word.');
        }
        if (document.getElementById('gsRuleScoring')) {
            document.getElementById('gsRuleScoring').textContent = String(rules.scoring_policy || 'Round score = 50 base points + up to 50 time bonus points. Maximum score per round is 100.');
        }
        if (document.getElementById('gsRuleBasePoints')) {
            document.getElementById('gsRuleBasePoints').textContent = String(rules.base_points_policy || 'Base points are 50.');
        }
        if (document.getElementById('gsRuleTimeBonus')) {
            document.getElementById('gsRuleTimeBonus').textContent = String(rules.time_bonus_policy || 'Time bonus starts at 50 and drops by 1 point about every 2.4 seconds.');
        }
        if (document.getElementById('gsRuleAttemptBonus')) {
            document.getElementById('gsRuleAttemptBonus').textContent = String(rules.attempt_bonus_policy || 'Wrong guesses reduce the score by 5 points each, up to 50 points total.');
        }
        if (dom.ruleSpeed) {
            dom.ruleSpeed.textContent = 'Green: Correct letter in the correct position.';
        }
        if (dom.ruleAccuracy) {
            dom.ruleAccuracy.textContent = 'Yellow: Correct letter in the wrong position.';
        }
        if (dom.ruleHints) {
            dom.ruleHints.textContent = 'Gray: Letter is not in the word.';
        }
        if (dom.ruleFairness) {
            dom.ruleFairness.textContent = String(rules.ranking_policy || 'Leaderboard: Top 10 players are ranked by speed, accuracy, and finish order. Top 3 win special prizes.');
        }
    }

    async function startSession() {
        dom.startBtn.disabled = true;
        dom.startBtn.textContent = 'Starting...';
        if (dom.exitBtn) {
            dom.exitBtn.hidden = false;
        }

        try {
            const cachedSessionId = readStoredSessionId();
            const data = await postJson(START_URL, cachedSessionId ? { session_id: cachedSessionId } : {});

            state.sessionId = data.session_id;
            state.rounds = Array.isArray(data.rounds) ? data.rounds : [];
            state.roundIndex = Math.max(0, Number(data.round_index || 0));
            state.totalScore = Math.max(0, Number(data.total_score || 0));
            state.started = true;
            state.finished = false;
            state.rules = data.rules || null;
            state.attemptHistory = normalizeAttemptHistory(data.attempt_history);

            storeSessionId(state.sessionId);

            hydrateScoringRules(state.rules);

            if (!state.rounds.length) {
                throw new Error('No rounds available for this session.');
            }

            const showGameUi = function () {
                dom.gate.hidden = true;
                startSessionClock(Number(data.elapsed_seconds || 0));
                resetRoundRuntime();
                const activeRound = getCurrentRound();
                hydrateCurrentWordleState(activeRound);
                renderRound();
                if (data && data.resumed) {
                    setFeedback('note', 'Resumed your active Startup Hunt run.');
                }
            };

            if (window.gsap) {
                window.gsap.to(dom.gate, {
                    autoAlpha: 0,
                    y: -10,
                    duration: 0.24,
                    ease: 'power2.out',
                    onComplete: showGameUi
                });
            } else {
                showGameUi();
            }
        } catch (err) {
            const payload = err && err.payload ? err.payload : null;
            const gateNote = dom.gate ? dom.gate.querySelector('.gsp-gate-note') : null;
            if (payload && payload.already_played) {
                clearStoredSessionId();
                if (gateNote) {
                    gateNote.textContent = err.message || 'You already used today\'s play. Check the leaderboard and return tomorrow.';
                }
                dom.roundPanel.hidden = true;
                dom.startBtn.textContent = 'Already Played Today';
                dom.startBtn.disabled = true;
                return;
            }

            if (gateNote) {
                gateNote.textContent = err.message || 'Could not start session. Please try again.';
            }
            setFeedback('bad', err.message || 'Could not start session.');
            dom.roundPanel.hidden = false;
        } finally {
            if (!dom.startBtn.disabled) {
                dom.startBtn.disabled = false;
                dom.startBtn.textContent = 'Start Startup Hunt Trial';
            }
        }
    }

    async function abandonSession() {
        if (!state.sessionId || state.finished || state.abandoning) {
            return;
        }

        state.abandoning = true;
        stopTimer();

        try {
            await postJson(ABANDON_URL, { session_id: state.sessionId });
        } catch (err) {
            // Ignore abandon errors because navigation should not be blocked.
        }

        state.sessionId = '';
        clearStoredSessionId();
        state.abandoning = false;
    }

    function sendBeaconAbandon() {
        if (!state.sessionId || state.finished || !navigator.sendBeacon) {
            return;
        }

        const formData = new FormData();
        formData.append('session_id', state.sessionId);
        const csrfToken = getCookie(CSRF_COOKIE_NAME) || INITIAL_CSRF_HASH;
        if (csrfToken) {
            formData.append(CSRF_TOKEN_NAME, csrfToken);
        }

        navigator.sendBeacon(ABANDON_URL, formData);
    }

    function openScoringModal() {
        if (!dom.scoreInfoModal) {
            return;
        }
        dom.scoreInfoModal.hidden = false;
    }

    function closeScoringModal() {
        if (!dom.scoreInfoModal) {
            return;
        }
        dom.scoreInfoModal.hidden = true;
    }

    function openExitModal() {
        if (!dom.exitModal || state.abandoning) {
            return;
        }
        dom.exitModal.hidden = false;
    }

    function closeExitModal() {
        if (!dom.exitModal) {
            return;
        }
        dom.exitModal.hidden = true;
    }

    dom.startBtn.addEventListener('click', function () {
        startSession();
    });

    dom.submitBtn.addEventListener('click', function () {
        submitRound(false);
    });

    dom.nextBtn.addEventListener('click', function () {
        nextRound();
    });

    if (dom.scoreInfoBtn) {
        dom.scoreInfoBtn.addEventListener('click', openScoringModal);
    }

    if (dom.scoreInfoClose) {
        dom.scoreInfoClose.addEventListener('click', closeScoringModal);
    }

    if (dom.scoreInfoBackdrop) {
        dom.scoreInfoBackdrop.addEventListener('click', closeScoringModal);
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeScoringModal();
            closeExitModal();
        }
    });

    if (dom.exitBtn) {
        dom.exitBtn.addEventListener('click', function () {
            openExitModal();
        });
    }

    if (dom.exitClose) {
        dom.exitClose.addEventListener('click', closeExitModal);
    }

    if (dom.exitCancelBtn) {
        dom.exitCancelBtn.addEventListener('click', closeExitModal);
    }

    if (dom.exitBackdrop) {
        dom.exitBackdrop.addEventListener('click', closeExitModal);
    }

    if (dom.exitConfirmBtn) {
        dom.exitConfirmBtn.addEventListener('click', async function () {
            if (state.abandoning) {
                return;
            }

            dom.exitConfirmBtn.disabled = true;
            dom.exitBtn && (dom.exitBtn.disabled = true);

            await abandonSession();
            window.location.href = LOBBY_URL;
        });
    }

    dom.restartBtn.addEventListener('click', function () {
        window.location.href = LOBBY_URL;
    });

    if (dom.leaderboardBtn) {
        dom.leaderboardBtn.addEventListener('click', function () {
            window.location.href = LEADERBOARD_PAGE_URL;
        });
    }

    if (window.gsap) {
        window.gsap.from('.gsp-shell', {
            autoAlpha: 0,
            y: 12,
            duration: 0.28,
            ease: 'power2.out'
        });
    }

    setTimeout(function () {
        startSession();
    }, shouldAutoStart ? 140 : 0);
})();
