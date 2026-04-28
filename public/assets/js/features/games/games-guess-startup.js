(function () {
    const dom = {
        startBtn: document.getElementById('gsStartBtn'),
        gameCard: document.getElementById('gsGameCard'),
        scoreband: document.getElementById('gsScoreband'),
        roundText: document.getElementById('gsRoundText'),
        typeText: document.getElementById('gsTypeText'),
        difficultyText: document.getElementById('gsDifficultyText'),
        totalText: document.getElementById('gsTotalText'),
        roundMeta: document.getElementById('gsRoundMeta'),
        roundBody: document.getElementById('gsRoundBody'),
        hintsPanel: document.getElementById('gsHintsPanel'),
        feedback: document.getElementById('gsFeedback'),
        hintBtn: document.getElementById('gsHintBtn'),
        submitBtn: document.getElementById('gsSubmitBtn'),
        nextBtn: document.getElementById('gsNextBtn'),
        summary: document.getElementById('gsSummary'),
        finalText: document.getElementById('gsFinalText'),
        restartBtn: document.getElementById('gsRestartBtn')
    };

    const state = {
        sessionId: '',
        rounds: [],
        roundIndex: 0,
        totalScore: 0,
        wrongGuesses: 0,
        hintsUsed: 0,
        attempts: 0,
        answered: false,
        startTs: 0,
        revealedHintIdx: 0
    };

    const maxAttemptsByType = {
        wordle: 5,
        rapid_fire: 3,
        scrambled: 3,
        clue_reveal: 3,
        logo_blur: 3,
        tagline_match: 2,
        crossword: 3
    };

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function postJson(url, body) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });

        const data = await res.json();
        if (!res.ok) {
            throw new Error(data && data.error ? data.error : 'Request failed');
        }

        return data;
    }

    function currentRound() {
        return state.rounds[state.roundIndex];
    }

    function resetRoundState() {
        state.wrongGuesses = 0;
        state.hintsUsed = 0;
        state.attempts = 0;
        state.answered = false;
        state.revealedHintIdx = 0;
        state.startTs = Date.now();
        dom.hintsPanel.innerHTML = '';
        dom.feedback.innerHTML = '';
        dom.nextBtn.hidden = true;
        dom.submitBtn.hidden = false;
        dom.hintBtn.disabled = false;
    }

    function renderScoreband() {
        const round = currentRound();
        dom.roundText.textContent = String(state.roundIndex + 1) + ' / ' + String(state.rounds.length);
        dom.typeText.textContent = round.game_type;
        dom.difficultyText.textContent = round.difficulty;
        dom.totalText.textContent = String(state.totalScore);
    }

    function renderRound() {
        const round = currentRound();
        if (!round) {
            finishSession();
            return;
        }

        renderScoreband();

        dom.roundMeta.textContent = 'Base: ' + round.base_points + ' | Max: ' + round.max_score_per_round + ' | Wrong guess: -10';

        if (round.game_type === 'wordle') {
            dom.roundBody.innerHTML =
                '<div class="gs-label">Category hint: ' + escapeHtml(round.content.category_hint) + '</div>' +
                '<input id="gsAnswerInput" class="gs-input" maxlength="20" placeholder="Enter startup name">';
            return;
        }

        if (round.game_type === 'scrambled') {
            dom.roundBody.innerHTML =
                '<div class="gs-label">Unscramble: ' + escapeHtml(round.content.scrambled_word) + '</div>' +
                '<input id="gsAnswerInput" class="gs-input" maxlength="30" placeholder="Your answer">';
            return;
        }

        if (round.game_type === 'clue_reveal') {
            dom.roundBody.innerHTML =
                '<div class="gs-clues" id="gsClues"></div>' +
                '<button id="gsRevealClueBtn" class="gs-btn gs-btn-ghost" type="button">Reveal Next Clue</button>' +
                '<input id="gsAnswerInput" class="gs-input" maxlength="40" placeholder="Your guess">';

            document.getElementById('gsRevealClueBtn').addEventListener('click', function () {
                revealClue(round);
            });

            revealClue(round);
            return;
        }

        if (round.game_type === 'logo_blur') {
            dom.roundBody.innerHTML =
                '<div class="gs-logo-stage" id="gsLogoStage" data-step="0">' +
                '<div class="gs-label">Blurred Logo Description</div>' +
                '<div class="gs-logo-text">' + escapeHtml(round.content.logo_description) + '</div>' +
                '</div>' +
                '<button id="gsClarifyBtn" class="gs-btn gs-btn-ghost" type="button">Increase Clarity</button>' +
                '<input id="gsAnswerInput" class="gs-input" maxlength="40" placeholder="Startup name">';

            document.getElementById('gsClarifyBtn').addEventListener('click', function () {
                const stage = document.getElementById('gsLogoStage');
                const step = Math.min(3, Number(stage.dataset.step || '0') + 1);
                stage.dataset.step = String(step);
            });
            return;
        }

        if (round.game_type === 'tagline_match') {
            const opts = (round.content.options || []).map(function (opt, idx) {
                return '<label class="gs-option"><input name="gsOption" type="radio" value="' + escapeHtml(opt) + '" ' + (idx === 0 ? 'checked' : '') + '> ' + escapeHtml(opt) + '</label>';
            }).join('');

            dom.roundBody.innerHTML =
                '<div class="gs-label">Tagline: "' + escapeHtml(round.content.tagline) + '"</div>' +
                '<div class="gs-option-grid">' + opts + '</div>';
            return;
        }

        if (round.game_type === 'rapid_fire') {
            const blocks = (round.content.rounds || []).map(function (slot) {
                return '<div class="gs-clue-item"><strong>Clue ' + slot.slot + ':</strong> ' + escapeHtml(slot.clue) + '</div>' +
                    '<input class="gs-input gs-rapid-input" data-slot="' + slot.slot + '" placeholder="Answer ' + slot.slot + '">';
            }).join('');
            dom.roundBody.innerHTML = '<div class="gs-clues">' + blocks + '</div>';
            return;
        }

        dom.roundBody.innerHTML =
            '<div class="gs-label">Crossword clue: ' + escapeHtml(round.content.clue) + ' (' + round.content.answer_length + ' letters)</div>' +
            '<input id="gsAnswerInput" class="gs-input" maxlength="40" placeholder="Your answer">';
    }

    function revealClue(round) {
        const clues = round.content.clues || [];
        const target = document.getElementById('gsClues');
        if (!target) {
            return;
        }

        if (state.revealedHintIdx >= clues.length) {
            return;
        }

        const idx = state.revealedHintIdx;
        const item = document.createElement('div');
        item.className = 'gs-clue-item';
        item.textContent = 'Clue ' + (idx + 1) + ': ' + clues[idx];
        target.appendChild(item);
        state.revealedHintIdx += 1;
    }

    function collectAnswer(round) {
        if (round.game_type === 'rapid_fire') {
            const values = Array.from(dom.roundBody.querySelectorAll('.gs-rapid-input')).map(function (input) {
                return input.value.trim();
            });
            return values;
        }

        if (round.game_type === 'tagline_match') {
            const checked = dom.roundBody.querySelector('input[name="gsOption"]:checked');
            return checked ? checked.value : '';
        }

        const input = document.getElementById('gsAnswerInput');
        return input ? input.value.trim() : '';
    }

    function showFeedback(cls, text) {
        dom.feedback.innerHTML = '<div class="' + cls + '">' + escapeHtml(text) + '</div>';
    }

    function maxAttempts(round) {
        return maxAttemptsByType[round.game_type] || 3;
    }

    async function submitAnswer() {
        const round = currentRound();
        if (!round || state.answered) {
            return;
        }

        const answer = collectAnswer(round);
        state.attempts += 1;

        const payload = {
            session_id: state.sessionId,
            round_id: round.id,
            answer: answer,
            elapsed_ms: Date.now() - state.startTs,
            wrong_guesses: state.wrongGuesses,
            hints_used: state.hintsUsed
        };

        if ((round.game_type !== 'rapid_fire' && !String(answer).trim()) || (round.game_type === 'rapid_fire' && (!Array.isArray(answer) || answer.some(function (x) { return !String(x).trim(); })))) {
            showFeedback('gs-note', 'Enter your answer first.');
            state.attempts -= 1;
            return;
        }

        try {
            const res = await postJson('/api/games/guess-startup/submit', payload);

            if (!res.correct) {
                state.wrongGuesses += 1;
                if (state.attempts >= maxAttempts(round)) {
                    await forfeitRound();
                    return;
                }
                showFeedback('gs-bad', 'Incorrect. Attempts left: ' + (maxAttempts(round) - state.attempts));
                return;
            }

            state.answered = true;
            state.totalScore = Number(res.total_score || state.totalScore);
            renderScoreband();
            dom.submitBtn.hidden = true;
            dom.nextBtn.hidden = false;
            dom.hintBtn.disabled = true;

            const b = res.score_breakdown || {};
            showFeedback('gs-ok', 'Correct! +' + (b.round_score || 0) + ' points. Answer: ' + (Array.isArray(res.revealed_answer) ? res.revealed_answer.join(', ') : String(res.revealed_answer || '')));
        } catch (err) {
            showFeedback('gs-bad', err.message || 'Submission failed.');
            state.attempts -= 1;
        }
    }

    async function forfeitRound() {
        const round = currentRound();
        const payload = {
            session_id: state.sessionId,
            round_id: round.id,
            forfeit: true,
            elapsed_ms: Date.now() - state.startTs,
            wrong_guesses: state.wrongGuesses,
            hints_used: state.hintsUsed
        };

        try {
            const res = await postJson('/api/games/guess-startup/submit', payload);
            state.answered = true;
            dom.submitBtn.hidden = true;
            dom.nextBtn.hidden = false;
            dom.hintBtn.disabled = true;
            const answer = Array.isArray(res.revealed_answer) ? res.revealed_answer.join(', ') : String(res.revealed_answer || '');
            showFeedback('gs-note', 'Round forfeited. Correct answer: ' + answer);
        } catch (err) {
            showFeedback('gs-bad', err.message || 'Could not forfeit round.');
        }
    }

    function useHint() {
        const round = currentRound();
        if (!round || state.answered) {
            return;
        }

        const hints = round.hints || [];
        if (state.hintsUsed >= hints.length) {
            showFeedback('gs-note', 'No more hints available this round.');
            return;
        }

        const text = hints[state.hintsUsed];
        const row = document.createElement('div');
        row.className = 'gs-hint-item';
        row.textContent = 'Hint ' + (state.hintsUsed + 1) + ': ' + text;
        dom.hintsPanel.appendChild(row);
        state.hintsUsed += 1;
    }

    function nextRound() {
        state.roundIndex += 1;
        if (state.roundIndex >= state.rounds.length) {
            finishSession();
            return;
        }

        resetRoundState();
        renderRound();
    }

    function finishSession() {
        dom.gameCard.hidden = true;
        dom.summary.hidden = false;
        dom.finalText.textContent = 'Final score: ' + String(state.totalScore);
    }

    async function startSession() {
        dom.startBtn.disabled = true;
        dom.startBtn.textContent = 'Starting...';

        try {
            const data = await postJson('/api/games/guess-startup/start', {});
            state.sessionId = data.session_id;
            state.rounds = data.rounds || [];
            state.roundIndex = 0;
            state.totalScore = 0;

            if (!state.rounds.length) {
                throw new Error('No rounds generated.');
            }

            dom.scoreband.hidden = false;
            dom.gameCard.hidden = false;
            dom.summary.hidden = true;
            resetRoundState();
            renderRound();
        } catch (err) {
            showFeedback('gs-bad', err.message || 'Unable to start session.');
            dom.gameCard.hidden = false;
        } finally {
            dom.startBtn.disabled = false;
            dom.startBtn.textContent = 'Start Session';
        }
    }

    if (dom.startBtn) {
        dom.startBtn.addEventListener('click', startSession);
    }

    if (dom.submitBtn) {
        dom.submitBtn.addEventListener('click', submitAnswer);
    }

    if (dom.nextBtn) {
        dom.nextBtn.addEventListener('click', nextRound);
    }

    if (dom.hintBtn) {
        dom.hintBtn.addEventListener('click', useHint);
    }

    if (dom.restartBtn) {
        dom.restartBtn.addEventListener('click', function () {
            state.sessionId = '';
            state.rounds = [];
            state.roundIndex = 0;
            state.totalScore = 0;
            dom.summary.hidden = true;
            dom.scoreband.hidden = true;
            dom.gameCard.hidden = true;
            dom.hintsPanel.innerHTML = '';
            dom.feedback.innerHTML = '';
        });
    }
})();
