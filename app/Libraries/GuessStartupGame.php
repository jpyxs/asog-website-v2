<?php

namespace App\Libraries;

class GuessStartupGame
{
    private const ROUND_COUNT = 5;
    private const WORDLE_LENGTH = 5;
    private const MAX_ATTEMPTS = 5;

    public function buildSession(int $roundCount = self::ROUND_COUNT): array
    {
        $pool = $this->wordPool();
        $total = count($pool);
        if ($total === 0) {
            return [];
        }

        $target = max(1, min($roundCount, $total));
        $indexes = array_rand($pool, $target);
        if (! is_array($indexes)) {
            $indexes = [$indexes];
        }

        $rounds = [];
        foreach (array_values($indexes) as $offset => $index) {
            $picked = $pool[(int) $index];
            $rounds[] = $this->decorateRound($picked, $offset + 1);
        }

        return $rounds;
    }

    public function dataset(): array
    {
        $flat = [];
        foreach ($this->wordPool() as $row) {
            $flat[] = $this->decorateRound($row, count($flat) + 1);
        }
        return $flat;
    }

    public function sanitizeForClient(array $entry): array
    {
        $safe = $entry;
        unset($safe['content']['answer']);

        if ($entry['game_type'] === 'logo_wordle') {
            $safe['content']['answer_length'] = self::WORDLE_LENGTH;
        }

        return $safe;
    }

    public function evaluateAnswer(array $entry, mixed $answer): bool
    {
        $expected = (string) ($entry['content']['answer'] ?? '');
        $candidate = is_array($answer) ? implode('', $answer) : (string) $answer;

        return $this->normalizeToken($candidate) === $this->normalizeToken($expected);
    }

    public function attemptFeedback(array $entry, mixed $answer): array
    {
        if (($entry['game_type'] ?? '') !== 'logo_wordle') {
            return [];
        }

        $expected = strtoupper($this->normalizeToken((string) ($entry['content']['answer'] ?? '')));
        $guess = strtoupper($this->normalizeToken((string) $answer));
        $requiredLength = self::WORDLE_LENGTH;

        if ($guess === '') {
            return [
                'error' => 'Type a startup name first.',
                'answer_length' => $requiredLength,
            ];
        }

        if (strlen($guess) !== $requiredLength) {
            return [
                'error' => 'Use exactly ' . $requiredLength . ' letters for this round.',
                'answer_length' => $requiredLength,
                'guess_length' => strlen($guess),
            ];
        }

        if (strlen($expected) !== $requiredLength) {
            return [
                'error' => 'Round configuration mismatch. Please continue to the next round.',
                'answer_length' => $requiredLength,
                'guess_length' => strlen($guess),
            ];
        }

        return [
            'guess' => $guess,
            'answer_length' => $requiredLength,
            'tiles' => $this->buildWordleTiles($expected, $guess),
        ];
    }

    public function scoreRound(array $entry, int $elapsedMs, int $wrongGuesses, int $hintsUsed): array
    {
        $elapsedMs = max(0, $elapsedMs);
        $elapsedSeconds = (int) floor($elapsedMs / 1000);

        $wrongGuesses = max(0, $wrongGuesses);
        $attemptsUsed = min(self::MAX_ATTEMPTS, $wrongGuesses + 1);

        $basePoints = 50;
        $timeBonus = max(0, 50 - (int) floor($elapsedMs / 2400));
        $wrongGuessPenalty = min(50, $wrongGuesses * 5);
        $roundScore = max(0, $basePoints + $timeBonus - $wrongGuessPenalty);

        return [
            'base_points' => $basePoints,
            'time_bonus' => $timeBonus,
            'wrong_guess_penalty' => $wrongGuessPenalty,
            'hint_reduction_pct' => 0,
            'round_score' => $roundScore,
            'max_score_per_round' => 100,
            'elapsed_seconds' => $elapsedSeconds,
            'attempts_used' => $attemptsUsed,
        ];
    }

    public function revealAnswer(array $entry): string
    {
        return (string) ($entry['content']['answer'] ?? '');
    }

    public function rewardPayload(array $entry): array
    {
        return [
            'answer' => $this->revealAnswer($entry),
            'fact' => (string) ($entry['education']['fact'] ?? ''),
            'link' => (string) ($entry['education']['link'] ?? ''),
            'link_label' => (string) ($entry['education']['link_label'] ?? 'Read more'),
        ];
    }

    public function maxAttempts(): int
    {
        return self::MAX_ATTEMPTS;
    }

    private function decorateRound(array $round, int $roundNumber): array
    {
        $round['id'] = $roundNumber;
        $round['round_number'] = $roundNumber;
        $round['game_type'] = 'logo_wordle';
        $round['base_points'] = 50;
        $round['max_score'] = 100;
        $round['max_score_per_round'] = 100;
        $round['round_seconds'] = 0;
        $round['max_guesses'] = self::MAX_ATTEMPTS;
        $round['time_bonus'] = [
            'fast_answer_bonus_pct' => 50,
        ];
        $round['penalty_rules'] = [
            'wrong_guess_points' => 5,
            'hint_usage_final_score_reduction_pct_per_hint' => 0,
        ];

        return $round;
    }

    private function answerLength(string $answer): int
    {
        return strlen($this->normalizeToken($answer));
    }

    private function normalizeToken(string $value): string
    {
        $value = strtolower(trim($value));
        return preg_replace('/[^a-z0-9]/', '', $value) ?? '';
    }

    private function buildWordleTiles(string $answer, string $guess): array
    {
        $answerChars = str_split($answer);
        $guessChars = str_split($guess);

        $states = array_fill(0, count($guessChars), 'absent');
        $remaining = [];

        foreach ($answerChars as $idx => $char) {
            if (($guessChars[$idx] ?? '') === $char) {
                $states[$idx] = 'correct';
            } else {
                $remaining[$char] = ($remaining[$char] ?? 0) + 1;
            }
        }

        foreach ($guessChars as $idx => $char) {
            if ($states[$idx] === 'correct') {
                continue;
            }

            if (($remaining[$char] ?? 0) > 0) {
                $states[$idx] = 'present';
                $remaining[$char]--;
            }
        }

        $tiles = [];
        foreach ($guessChars as $idx => $char) {
            $tiles[] = [
                'letter' => $char,
                'state' => $states[$idx],
            ];
        }

        return $tiles;
    }

    private function wordPool(): array
    {
        return [
            $this->word('wd-canva', 'canva', 'easy', ['Design', 'Creator tools', 'Visual content'], 'Canva started as a startup and became a global design platform.', 'https://www.canva.com'),
            $this->word('wd-asana', 'asana', 'easy', ['Tasks', 'Projects', 'Team workflow'], 'Asana scaled from startup roots into a major work-management product.', 'https://asana.com'),
            $this->word('wd-figma', 'figma', 'easy', ['UI/UX', 'Browser', 'Collaboration'], 'Figma reshaped design collaboration for startup teams worldwide.', 'https://www.figma.com'),
            $this->word('wd-slack', 'slack', 'easy', ['Messaging', 'Channels', 'Work chat'], 'Slack grew from a startup into a default communication tool for many teams.', 'https://slack.com'),
            $this->word('wd-plaid', 'plaid', 'medium', ['Fintech', 'API', 'Bank data'], 'Plaid became a core fintech infrastructure startup.', 'https://plaid.com'),
            $this->word('wd-rappi', 'rappi', 'medium', ['Delivery', 'LATAM', 'Super app'], 'Rappi scaled quickly from startup to regional super-app.', 'https://www.rappi.com'),
            $this->word('wd-qonto', 'qonto', 'medium', ['SME banking', 'Europe', 'Finance'], 'Qonto is a fast-growing business banking startup in Europe.', 'https://qonto.com'),
            $this->word('wd-klook', 'klook', 'medium', ['Travel', 'Booking', 'Experiences'], 'Klook grew from startup to leading travel-experience platform.', 'https://www.klook.com'),
            $this->word('wd-skype', 'skype', 'easy', ['Voice', 'Video', 'Internet calls'], 'Skype began as a startup and popularized internet calling.', 'https://en.wikipedia.org/wiki/Skype'),
            $this->word('wd-yahoo', 'yahoo', 'easy', ['Web portal', 'Early internet', 'Search'], 'Yahoo started as an early internet startup and became globally known.', 'https://en.wikipedia.org/wiki/Yahoo'),
            $this->word('wd-baidu', 'baidu', 'medium', ['Search', 'China tech', 'AI'], 'Baidu grew from startup origins into a major technology company.', 'https://en.wikipedia.org/wiki/Baidu'),
            $this->word('wd-adobe', 'adobe', 'medium', ['Creative software', 'Design', 'Digital media'], 'Adobe began as a startup before becoming a global software giant.', 'https://www.adobe.com'),
            $this->word('wd-cisco', 'cisco', 'medium', ['Networking', 'Infrastructure', 'Enterprise'], 'Cisco started as a startup and became a networking leader.', 'https://www.cisco.com'),
            $this->word('wd-apple', 'apple', 'easy', ['Consumer tech', 'Hardware', 'Innovation'], 'Apple started as a startup and became one of the most valuable companies.', 'https://www.apple.com'),
            $this->word('wd-intel', 'intel', 'medium', ['Chips', 'Semiconductors', 'Compute'], 'Intel began as a startup and helped define modern computing.', 'https://www.intel.com'),

            $this->word('wd-agile', 'agile', 'easy', ['Sprints', 'Iteration', 'Product teams'], 'Agile is a core way startups ship quickly and learn fast.', 'https://en.wikipedia.org/wiki/Agile_software_development'),
            $this->word('wd-pivot', 'pivot', 'easy', ['Direction change', 'Strategy', 'PMF'], 'Pivoting helps startups adapt when the first plan is not working.', 'https://www.ycombinator.com/library/5z-the-real-product-market-fit'),
            $this->word('wd-scale', 'scale', 'easy', ['Growth', 'Systems', 'Expansion'], 'Scaling is a major challenge after early startup traction.', 'https://en.wikipedia.org/wiki/Scalability'),
            $this->word('wd-pitch', 'pitch', 'easy', ['Investors', 'Fundraising', 'Deck'], 'A strong pitch increases startup fundraising opportunities.', 'https://www.ycombinator.com/library/4A-how-to-design-a-better-pitch-deck'),
            $this->word('wd-stack', 'stack', 'easy', ['Frontend', 'Backend', 'Tech choices'], 'Choosing the right stack can speed up startup execution.', 'https://12factor.net/'),
            $this->word('wd-cloud', 'cloud', 'easy', ['Deploy', 'Hosting', 'Scale'], 'Cloud services let startups launch and scale faster.', 'https://azure.microsoft.com/en-us/resources/cloud-computing-dictionary/what-is-cloud-computing/'),
            $this->word('wd-scrum', 'scrum', 'medium', ['Ceremonies', 'Backlog', 'Team process'], 'Scrum is widely used in startup product and engineering teams.', 'https://www.scrum.org/resources/what-is-scrum'),
            $this->word('wd-cache', 'cache', 'medium', ['Performance', 'Speed', 'Latency'], 'Caching can dramatically improve product responsiveness.', 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching'),
            $this->word('wd-proxy', 'proxy', 'medium', ['Gateway', 'Network edge', 'Routing'], 'Proxies are common in startup web infrastructure stacks.', 'https://en.wikipedia.org/wiki/Proxy_server'),
            $this->word('wd-query', 'query', 'medium', ['Database', 'Filter', 'Retrieve'], 'Better queries help startups keep apps fast as data grows.', 'https://www.postgresql.org/docs/current/tutorial-select.html'),
            $this->word('wd-build', 'build', 'easy', ['CI/CD', 'Release', 'Compile'], 'Reliable builds help startup teams ship confidently.', 'https://martinfowler.com/articles/continuousIntegration.html'),
            $this->word('wd-scope', 'scope', 'easy', ['Boundaries', 'MVP', 'Priorities'], 'Good scope control helps startups ship meaningful features.', 'https://en.wikipedia.org/wiki/Scope_(project_management)'),
            $this->word('wd-model', 'model', 'medium', ['Revenue', 'Business model', 'Economics'], 'A strong business model drives startup sustainability.', 'https://www.investopedia.com/terms/b/businessmodel.asp'),
            $this->word('wd-angel', 'angel', 'easy', ['Seed capital', 'Investor', 'Mentorship'], 'Angel investors often fund startups at the earliest stage.', 'https://www.investopedia.com/terms/a/angelinvestor.asp'),
            $this->word('wd-audit', 'audit', 'medium', ['Security', 'Compliance', 'Risk'], 'Audits help startups build trust and reduce operational risk.', 'https://owasp.org/www-project-top-ten/'),
            $this->word('wd-buyer', 'buyer', 'easy', ['Customer', 'Conversion', 'Demand'], 'Understanding the buyer is key for product-market fit.', 'https://www.strategyzer.com/books/value-proposition-design'),
            $this->word('wd-coder', 'coder', 'easy', ['Engineering', 'Software', 'Execution'], 'Execution quality is a major startup advantage.', 'https://en.wikipedia.org/wiki/Programmer'),
            $this->word('wd-email', 'email', 'easy', ['Outreach', 'Retention', 'Campaigns'], 'Email remains a high-ROI growth channel for startups.', 'https://www.hubspot.com/marketing-statistics'),
            $this->word('wd-grant', 'grant', 'medium', ['Funding', 'Non-dilutive', 'R&D'], 'Grants can support startups without giving up equity.', 'https://en.wikipedia.org/wiki/Grant_(money)'),
            $this->word('wd-index', 'index', 'medium', ['Lookup', 'Database speed', 'Optimization'], 'Indexes make product data access faster and cheaper.', 'https://www.postgresql.org/docs/current/indexes.html'),
            $this->word('wd-input', 'input', 'easy', ['Forms', 'Validation', 'UX'], 'Valid input handling is crucial for startup app security.', 'https://owasp.org/www-community/Input_Validation'),
            $this->word('wd-legal', 'legal', 'medium', ['Contracts', 'Terms', 'Compliance'], 'Legal fundamentals protect startup growth and fundraising.', 'https://www.investopedia.com/terms/c/compliance.asp'),
            $this->word('wd-login', 'login', 'easy', ['Auth', 'Accounts', 'Security'], 'Secure login flows prevent account takeovers.', 'https://owasp.org/www-project-authentication-cheat-sheet/'),
            $this->word('wd-merge', 'merge', 'easy', ['Git', 'Branches', 'Integration'], 'Frequent clean merges improve engineering velocity.', 'https://git-scm.com/docs/git-merge'),
            $this->word('wd-micro', 'micro', 'medium', ['Services', 'Modular', 'Architecture'], 'Microservice architecture is common in scaling startups.', 'https://martinfowler.com/articles/microservices.html'),
            $this->word('wd-niche', 'niche', 'easy', ['Segment', 'Focused market', 'ICP'], 'Winning a niche market is often step one for startups.', 'https://www.ycombinator.com/library/6f-how-to-get-startup-ideas'),
            $this->word('wd-offer', 'offer', 'easy', ['Pricing', 'Packaging', 'Value'], 'Clear offers improve startup conversion rates.', 'https://www.investopedia.com/terms/v/valueproposition.asp'),
            $this->word('wd-owner', 'owner', 'easy', ['Accountability', 'Leadership', 'Decisions'], 'Clear ownership helps startup teams move faster.', 'https://www.atlassian.com/agile/project-management/project-ownership'),
            $this->word('wd-patch', 'patch', 'medium', ['Fix', 'Security update', 'Release'], 'Fast patching reduces downtime and risk.', 'https://www.cisa.gov/known-exploited-vulnerabilities-catalog'),
            $this->word('wd-price', 'price', 'easy', ['Monetization', 'Revenue', 'Positioning'], 'Pricing is one of the biggest startup growth levers.', 'https://www.ycombinator.com/library/4u-the-power-of-pricing'),
            $this->word('wd-quota', 'quota', 'medium', ['Limits', 'Usage cap', 'Resources'], 'Quotas protect infrastructure from overload.', 'https://learn.microsoft.com/azure/azure-resource-manager/management/azure-subscription-service-limits'),
            $this->word('wd-reach', 'reach', 'easy', ['Audience', 'Distribution', 'Growth'], 'Distribution often determines startup outcomes.', 'https://www.ycombinator.com/library/6m-sales-and-distribution'),
            $this->word('wd-retro', 'retro', 'easy', ['Reflection', 'Sprint review', 'Improvements'], 'Retrospectives help startup teams improve every cycle.', 'https://www.atlassian.com/team-playbook/plays/retrospective'),
            $this->word('wd-sales', 'sales', 'easy', ['Pipeline', 'Revenue', 'Deals'], 'Strong sales execution drives startup survival.', 'https://www.hubspot.com/sales/statistics'),
            $this->word('wd-share', 'share', 'medium', ['Equity', 'Cap table', 'Ownership split'], 'Equity structure matters during startup fundraising.', 'https://www.investopedia.com/terms/c/capitalization-table.asp'),
            $this->word('wd-smart', 'smart', 'easy', ['Goals', 'Metrics', 'Execution'], 'SMART goals keep startup teams focused and aligned.', 'https://www.mindtools.com/a4wo118/smart-goals'),
            $this->word('wd-squad', 'squad', 'easy', ['Cross-functional', 'Autonomy', 'Delivery'], 'Small squads help startups deliver quickly.', 'https://www.atlassian.com/agile/agile-at-scale/spotify'),
            $this->word('wd-stock', 'stock', 'medium', ['Options', 'Compensation', 'Equity'], 'Stock options are common startup talent incentives.', 'https://www.investopedia.com/terms/e/eso.asp'),
            $this->word('wd-story', 'story', 'easy', ['Backlog', 'Requirements', 'Users'], 'Clear user stories reduce product ambiguity.', 'https://www.atlassian.com/agile/project-management/user-stories'),
            $this->word('wd-trust', 'trust', 'easy', ['Brand', 'Credibility', 'Retention'], 'Trust is essential for startup adoption and retention.', 'https://www.nngroup.com/articles/trustworthiness-of-websites/'),
            $this->word('wd-users', 'users', 'easy', ['Engagement', 'DAU/MAU', 'Retention'], 'User growth and retention are key startup metrics.', 'https://www.ycombinator.com/library/3q-how-to-measure-startup-progress'),
            $this->word('wd-value', 'value', 'easy', ['Benefit', 'Outcome', 'Customer value'], 'Startups win when they deliver clear customer value.', 'https://www.strategyzer.com/books/value-proposition-design'),
            $this->word('wd-viral', 'viral', 'medium', ['Word of mouth', 'Growth loops', 'Network effects'], 'Viral loops can reduce customer acquisition costs.', 'https://www.ycombinator.com/library/2f-growth')
        ];
    }

    private function word(string $slug, string $answer, string $difficulty, array $cues, string $fact, string $link): array
    {
        if ($this->answerLength($answer) !== self::WORDLE_LENGTH) {
            throw new \InvalidArgumentException('Wordle answers must be exactly ' . self::WORDLE_LENGTH . ' letters.');
        }

        return [
            'slug' => $slug,
            'difficulty' => $difficulty,
            'content' => [
                'answer' => $answer,
                'prompt' => 'Guess the 5-letter startup word using clues.',
                'word_cues' => $cues,
            ],
            'education' => [
                'fact' => $fact,
                'link' => $link,
                'link_label' => 'Learn more',
            ],
        ];
    }
}
