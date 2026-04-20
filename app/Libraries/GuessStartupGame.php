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
            $this->word('wd-forge', 'forge', 'easy', ['Build', 'Create', 'Success'], 'Great startups forge new paths and create lasting value.', 'https://www.ycombinator.com/library/2x-the-key-to-hiring-great-people'),
            $this->word('wd-quest', 'quest', 'easy', ['Mission', 'Goals', 'Journey'], 'Every startup begins as a quest to solve a problem for users.', 'https://www.ycombinator.com/library/6f-how-to-get-startup-ideas'),
            $this->word('wd-guild', 'guild', 'medium', ['Community', 'Organization', 'Team'], 'Building a strong guild of talented people scales startup growth.', 'https://www.ycombinator.com/library/7c-how-to-manage-a-startup'),
            $this->word('wd-adapt', 'adapt', 'easy', ['Change', 'Flexibility', 'Evolution'], 'Startups that adapt quickly survive market disruptions.', 'https://www.ycombinator.com/library/5z-the-real-product-market-fit'),
            $this->word('wd-skype', 'skype', 'easy', ['Voice', 'Video', 'Internet calls'], 'Skype began as a startup and popularized internet calling.', 'https://en.wikipedia.org/wiki/Skype'),
            $this->word('wd-yahoo', 'yahoo', 'easy', ['Web portal', 'Early internet', 'Search'], 'Yahoo started as an early internet startup and became globally known.', 'https://en.wikipedia.org/wiki/Yahoo'),
            $this->word('wd-baidu', 'baidu', 'medium', ['Search', 'China tech', 'AI'], 'Baidu grew from startup origins into a major technology company.', 'https://en.wikipedia.org/wiki/Baidu'),
            $this->word('wd-adobe', 'adobe', 'medium', ['Creative software', 'Design', 'Digital media'], 'Adobe began as a startup before becoming a global software giant.', 'https://www.adobe.com'),
            $this->word('wd-cisco', 'cisco', 'medium', ['Networking', 'Infrastructure', 'Enterprise'], 'Cisco started as a startup and became a networking leader.', 'https://www.cisco.com'),
            $this->word('wd-apple', 'apple', 'easy', ['Consumer tech', 'Hardware', 'Innovation'], 'Apple started as a startup and became one of the most valuable companies.', 'https://www.apple.com'),
            $this->word('wd-intel', 'intel', 'medium', ['Chips', 'Semiconductors', 'Compute'], 'Intel began as a startup and helped define modern computing.', 'https://www.intel.com'),
            $this->word('wd-tesla', 'tesla', 'easy', ['Electric vehicles', 'Elon Musk', 'Energy'], 'Tesla started as a startup and revolutionized the electric vehicle industry.', 'https://www.tesla.com'),

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
            $this->word('wd-viral', 'viral', 'medium', ['Word of mouth', 'Growth loops', 'Network effects'], 'Viral loops can reduce customer acquisition costs.', 'https://www.ycombinator.com/library/2f-growth'),

            $this->word('wd-brand', 'brand', 'easy', ['Identity', 'Positioning', 'Recognition'], 'Strong branding helps startups stand out in competitive markets.', 'https://www.ycombinator.com/library/4e-how-to-build-your-brand'),
            $this->word('wd-brief', 'brief', 'easy', ['Concise', 'Messaging', 'Clarity'], 'Clear briefs align startup teams on product direction.', 'https://www.interaction-design.org/literature/article/design-briefs'),
            $this->word('wd-funds', 'funds', 'easy', ['Capital', 'Money', 'Financing'], 'Fundraising is essential for startup growth and scale.', 'https://www.investopedia.com/terms/f/funding.asp'),
            $this->word('wd-trade', 'trade', 'medium', ['Commerce', 'Deals', 'Supply chain'], 'Trade-offs define focused product strategy for startups.', 'https://www.ycombinator.com/library/5u-tradeoffs'),
            $this->word('wd-trend', 'trend', 'easy', ['Direction', 'Momentum', 'Pattern'], 'Spotting trends early gives startups competitive advantage.', 'https://en.wikipedia.org/wiki/Trend_analysis'),
            $this->word('wd-asset', 'asset', 'medium', ['Resource', 'Value', 'IP'], 'Key assets like IP and data drive startup valuation.', 'https://www.investopedia.com/terms/a/asset.asp'),
            $this->word('wd-rival', 'rival', 'medium', ['Competitor', 'Opposition', 'Contrast'], 'Analyzing rivals helps startups find untapped opportunities.', 'https://www.ycombinator.com/library/4c-competition'),
            $this->word('wd-lease', 'lease', 'medium', ['Space', 'Real estate', 'Operations'], 'Office leases are a major cost consideration for startups.', 'https://www.investopedia.com/terms/l/lease.asp'),

            $this->word('wd-raise', 'raise', 'easy', ['Funding', 'Capital', 'Investment round'], 'Raising capital is a critical milestone for startup growth.', 'https://www.ycombinator.com/library/7u-how-to-raise-money'),
            $this->word('wd-error', 'error', 'medium', ['Bug', 'Learning', 'Debugging'], 'Handling errors gracefully improves startup product reliability.', 'https://www.investopedia.com/terms/e/error.asp'),
            $this->word('wd-cycle', 'cycle', 'easy', ['Iteration', 'Sprint', 'Release'], 'Fast iteration cycles help startups learn and adapt quickly.', 'https://en.wikipedia.org/wiki/Iteration'),
            $this->word('wd-frame', 'frame', 'medium', ['Strategy', 'Perspective', 'Context'], 'Reframing problems helps startup teams find innovative solutions.', 'https://www.nngroup.com/articles/problem-framing/'),
            $this->word('wd-stake', 'stake', 'medium', ['Ownership', 'Equity', 'Commitment'], 'Stakeholder alignment is crucial for startup success.', 'https://www.investopedia.com/terms/s/stakeholder.asp'),
            $this->word('wd-basis', 'basis', 'medium', ['Foundation', 'Fundamentals', 'Core'], 'Strong fundamentals provide the basis for sustainable startups.', 'https://en.wikipedia.org/wiki/Business_fundamentals'),
            $this->word('wd-token', 'token', 'medium', ['Cryptocurrency', 'Incentive', 'Blockchain'], 'Tokenomics design affects adoption in crypto startups.', 'https://www.investopedia.com/terms/t/token.asp'),
            $this->word('wd-vault', 'vault', 'medium', ['Secure', 'Storage', 'Safe'], 'Secure vaults protect startup customer data and privacy.', 'https://owasp.org/www-project-web-security-testing-guide/'),
            $this->word('wd-order', 'order', 'easy', ['Process', 'Sequence', 'Customer purchase'], 'Efficient order processing drives startup e-commerce success.', 'https://www.shopify.com/guides/ecommerce/order-management'),
            $this->word('wd-table', 'table', 'medium', ['Cap table', 'Data', 'Structure'], 'Cap tables show equity ownership in startup structure.', 'https://www.investopedia.com/terms/c/capitalization-table.asp'),
            $this->word('wd-boost', 'boost', 'easy', ['Accelerator', 'Growth', 'Momentum'], 'Accelerators boost early-stage startups with mentorship and capital.', 'https://www.ycombinator.com/'),
            $this->word('wd-spark', 'spark', 'easy', ['Idea', 'Innovation', 'Inspiration'], 'Great ideas spark startup movements that change industries.', 'https://www.ycombinator.com/library/6f-how-to-get-startup-ideas'),
            $this->word('wd-shift', 'shift', 'easy', ['Change', 'Pivot', 'Innovation'], 'Strategic shifts help startups adapt to market changes.', 'https://www.ycombinator.com/library/5z-the-real-product-market-fit'),
            $this->word('wd-split', 'split', 'medium', ['Division', 'Equity split', 'Separation'], 'Equal splits can lead to disputes in startup founding teams.', 'https://www.ycombinator.com/library/7o-co-founder-equity-split'),
            $this->word('wd-track', 'track', 'easy', ['Metrics', 'Monitor', 'Measure'], 'Tracking metrics helps startups measure progress and adjust course.', 'https://www.ycombinator.com/library/3q-how-to-measure-startup-progress'),
            $this->word('wd-yield', 'yield', 'medium', ['Return', 'Revenue', 'Profit'], 'Maximizing yield on invested capital is critical for startups.', 'https://www.investopedia.com/terms/y/yield.asp'),
            $this->word('wd-hired', 'hired', 'easy', ['Recruitment', 'Talent', 'Team building'], 'Great talent is hired through network and employer branding.', 'https://www.ycombinator.com/library/2x-the-key-to-hiring-great-people'),
            $this->word('wd-group', 'group', 'easy', ['Team', 'Collective', 'Community'], 'Building strong groups and communities drives startup engagement.', 'https://www.ycombinator.com/library/3a-building-communities'),
            $this->word('wd-study', 'study', 'medium', ['Research', 'Analysis', 'Learning'], 'Market studies help startups validate product-market fit.', 'https://en.wikipedia.org/wiki/Market_research'),
            $this->word('wd-focus', 'focus', 'easy', ['Attention', 'Concentration', 'Priority'], 'Staying focused on core metrics separates winning startups.', 'https://www.ycombinator.com/library/5v-we-dont-teach-sales'),

            $this->word('wd-nexus', 'nexus', 'medium', ['Hub', 'Connection', 'Network'], 'A nexus of talent and resources attracts more startup opportunities.', 'https://www.ycombinator.com/library/3a-building-communities'),
            $this->word('wd-bonus', 'bonus', 'easy', ['Incentive', 'Reward', 'Extra value'], 'Bonuses and equity incentivize startup team performance.', 'https://www.investopedia.com/terms/e/eso.asp'),
            $this->word('wd-demos', 'demos', 'easy', ['Showcase', 'Prototype', 'Presentation'], 'Great demos convince investors to fund startups.', 'https://www.ycombinator.com/library/4B-how-to-demo-day'),
            $this->word('wd-ethos', 'ethos', 'medium', ['Culture', 'Values', 'Philosophy'], 'Strong ethos attracts aligned founders and team members.', 'https://www.ycombinator.com/library/7c-how-to-manage-a-startup'),
            $this->word('wd-atlas', 'atlas', 'medium', ['Guide', 'Reference', 'Map'], 'An atlas of startup resources helps founders navigate challenges.', 'https://www.ycombinator.com/'),
            $this->word('wd-tempo', 'tempo', 'easy', ['Pace', 'Speed', 'Rhythm'], 'Setting the right tempo keeps startup execution on track.', 'https://www.ycombinator.com/library/2t-how-to-make-your-idea-sound-cool'),
            $this->word('wd-flame', 'flame', 'easy', ['Passion', 'Fire', 'Energy'], 'Flame and drive separate thriving startups from the rest.', 'https://www.ycombinator.com/library/5t-how-to-get-startup-ideas'),
            $this->word('wd-nodes', 'nodes', 'medium', ['Network', 'Infrastructure', 'Connections'], 'Startup networks are built by connecting key nodes of talent.', 'https://www.ycombinator.com/library/3a-building-communities'),
            $this->word('wd-kudos', 'kudos', 'easy', ['Recognition', 'Praise', 'Achievement'], 'Kudos from peers and investors validate startup success.', 'https://www.ycombinator.com/library/2r-why-startups-succeed'),
            $this->word('wd-logos', 'logos', 'medium', ['Branding', 'Identity', 'Logic'], 'Strong logos and branding make startups memorable.', 'https://www.ycombinator.com/library/4e-how-to-build-your-brand'),
            $this->word('wd-realm', 'realm', 'easy', ['Domain', 'Market', 'Industry'], 'Finding an underserved realm gives startups clear advantage.', 'https://www.ycombinator.com/library/6f-how-to-get-startup-ideas'),
            $this->word('wd-claim', 'claim', 'easy', ['Stake', 'Territory', 'Assertion'], 'Startups must claim their market before competitors arrive.', 'https://www.ycombinator.com/library/6m-sales-and-distribution'),
            $this->word('wd-hunts', 'hunts', 'medium', ['Search', 'Pursuit', 'Discovery'], 'Talent hunts and discovery missions build strong startup teams.', 'https://www.ycombinator.com/library/2x-the-key-to-hiring-great-people'),
            $this->word('wd-bolts', 'bolts', 'medium', ['Speed', 'Momentum', 'Connections'], 'Startups that bolt through execution beat slower competitors.', 'https://www.ycombinator.com/library/5u-tradeoffs')
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