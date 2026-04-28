<?php
declare(strict_types=1);

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
            $this->word('wd-colab', 'colab', 'easy', ['Cowork', 'Hub', 'Teams'], 'The Co-Lab is one of the collaboration spaces supporting ASOG TBI builders.', 'https://asogtbi.com/facilities'),
            $this->word('wd-suite', 'suite', 'easy', ['Office', 'Teams', 'Workrooms'], 'The Innovators\' Suite supports startup teams with dedicated workspace.', 'https://asogtbi.com/facilities'),
            $this->word('wd-banox', 'banox', 'medium', ['Incubatee', 'ASOG TBI', 'Startup'], 'Banox is one of the 5-letter startup terms included in the ASOG TBI game set.', 'https://asogtbi.com/incubatees'),
            $this->word('wd-sabay', 'sabay', 'medium', ['Incubatee', 'ASOG TBI', 'Community'], 'Sabay is included as a 5-letter incubatee startup word in this updated pool.', 'https://asogtbi.com/incubatees'),
            $this->word('wd-bicol', 'bicol', 'easy', ['Region', 'CSPC', 'Innovation'], 'ASOG TBI serves innovators in the Bicol region through incubation support.', 'https://asogtbi.com'),
            $this->word('wd-space', 'space', 'easy', ['Cowork', 'Labs', 'Facility'], 'ASOG TBI provides startup-friendly space for building, testing, and collaboration.', 'https://asogtbi.com/facilities'),
            $this->word('wd-proto', 'proto', 'easy', ['MVP', 'Testing', 'Build'], 'Prototype-first execution helps incubatees validate ideas early.', 'https://asogtbi.com/programs'),
            $this->word('wd-msmes', 'MSMEs', 'easy', ['Enterprises', 'Growth', 'Support'], 'ASOG TBI activities support MSMEs through incubation and innovation programs.', 'https://asogtbi.com/programs'),
            $this->word('wd-omefi', 'omefi', 'medium', ['Incubatee', 'ASOG TBI', 'Startup'], 'Omefi is included as one of the 5-letter incubatee startup words in the game.', 'https://asogtbi.com/incubatees'),
            $this->word('wd-equip', 'equip', 'easy', ['Tools', 'Labs', 'Resources'], 'Facility access equips founders to test and improve product concepts.', 'https://asogtbi.com/facilities'),
            $this->word('wd-coach', 'coach', 'easy', ['Mentor', 'Guide', 'Support'], 'Startup coaching is central to incubation and venture readiness.', 'https://asogtbi.com/programs'),
            $this->word('wd-forum', 'forum', 'easy', ['Community', 'Sharing', 'Talks'], 'Founder forums help teams exchange lessons and opportunities.', 'https://asogtbi.com/news'),
            $this->word('wd-event', 'event', 'easy', ['Pitch day', 'Showcase', 'Network'], 'ASOG TBI events connect startups with partners, peers, and supporters.', 'https://asogtbi.com/news'),
            $this->word('wd-ideas', 'ideas', 'easy', ['Concept', 'Solve', 'Create'], 'Incubation starts by turning ideas into validated startup opportunities.', 'https://asogtbi.com/programs'),
            $this->word('wd-booth', 'booth', 'easy', ['Expo', 'Demo', 'Showcase'], 'Demo booths and showcases help startup teams gather market feedback.', 'https://asogtbi.com/news'),
            $this->word('wd-teams', 'teams', 'easy', ['Founders', 'Roles', 'Execution'], 'Strong teams are the backbone of successful incubation outcomes.', 'https://asogtbi.com/programs'),
            $this->word('wd-agile', 'agile', 'easy', ['Sprints', 'Iteration', 'Delivery'], 'Agile routines help startups ship quickly and improve continuously.', 'https://en.wikipedia.org/wiki/Agile_software_development'),
            $this->word('wd-pivot', 'pivot', 'easy', ['Direction', 'Adapt', 'PMF'], 'Founders often pivot to align products with real customer demand.', 'https://www.ycombinator.com/library/5z-the-real-product-market-fit'),
            $this->word('wd-scale', 'scale', 'easy', ['Growth', 'Systems', 'Expansion'], 'Scaling requires product readiness, process discipline, and strong teams.', 'https://en.wikipedia.org/wiki/Scalability'),
            $this->word('wd-pitch', 'pitch', 'easy', ['Deck', 'Investors', 'Story'], 'Clear pitches improve startup fundraising outcomes.', 'https://www.ycombinator.com/library/4A-how-to-design-a-better-pitch-deck'),
            $this->word('wd-stack', 'stack', 'easy', ['Frontend', 'Backend', 'Tools'], 'A practical tech stack improves speed and maintainability.', 'https://12factor.net/'),
            $this->word('wd-cloud', 'cloud', 'easy', ['Hosting', 'Deploy', 'Infra'], 'Cloud services help young ventures launch and scale with less overhead.', 'https://azure.microsoft.com/en-us/resources/cloud-computing-dictionary/what-is-cloud-computing/'),
            $this->word('wd-scrum', 'scrum', 'medium', ['Backlog', 'Sprint', 'Ceremony'], 'Scrum is common in product teams that ship in short cycles.', 'https://www.scrum.org/resources/what-is-scrum'),
            $this->word('wd-cache', 'cache', 'medium', ['Speed', 'Latency', 'Perf'], 'Caching reduces response time and improves user experience.', 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching'),
            $this->word('wd-proxy', 'proxy', 'medium', ['Gateway', 'Routing', 'Network'], 'Proxies are often used in secure and scalable web architectures.', 'https://en.wikipedia.org/wiki/Proxy_server'),
            $this->word('wd-query', 'query', 'medium', ['Data', 'Filter', 'SQL'], 'Better queries keep startup products responsive as usage grows.', 'https://www.postgresql.org/docs/current/tutorial-select.html'),
            $this->word('wd-build', 'build', 'easy', ['Compile', 'Release', 'CI'], 'Reliable build pipelines improve confidence and delivery speed.', 'https://martinfowler.com/articles/continuousIntegration.html'),
            $this->word('wd-scope', 'scope', 'easy', ['MVP', 'Limits', 'Priority'], 'Good scope control keeps small teams focused on high-impact work.', 'https://en.wikipedia.org/wiki/Scope_(project_management)'),
            $this->word('wd-model', 'model', 'medium', ['Revenue', 'Business', 'Value'], 'A healthy business model supports long-term startup sustainability.', 'https://www.investopedia.com/terms/b/businessmodel.asp'),
            $this->word('wd-angel', 'angel', 'easy', ['Seed', 'Capital', 'Investor'], 'Angel investors are common in early-stage startup financing.', 'https://www.investopedia.com/terms/a/angelinvestor.asp'),
            $this->word('wd-audit', 'audit', 'medium', ['Risk', 'Security', 'Checks'], 'Operational and security audits reduce risk as ventures grow.', 'https://owasp.org/www-project-top-ten/'),
            $this->word('wd-buyer', 'buyer', 'easy', ['Customer', 'Demand', 'Segment'], 'Knowing the buyer improves messaging, pricing, and retention.', 'https://www.strategyzer.com/books/value-proposition-design'),
            $this->word('wd-coder', 'coder', 'easy', ['Engineer', 'Software', 'Dev'], 'Strong coding execution is a startup advantage in early stages.', 'https://en.wikipedia.org/wiki/Programmer'),
            $this->word('wd-email', 'email', 'easy', ['Outreach', 'Campaign', 'CRM'], 'Email remains a high-ROI channel for many startup teams.', 'https://www.hubspot.com/marketing-statistics'),
            $this->word('wd-index', 'index', 'medium', ['Lookup', 'DB speed', 'Search'], 'Indexes are critical to keep data access performant.', 'https://www.postgresql.org/docs/current/indexes.html'),
            $this->word('wd-input', 'input', 'easy', ['Forms', 'Validation', 'UX'], 'Input validation protects systems and improves data quality.', 'https://owasp.org/www-community/Input_Validation'),
            $this->word('wd-legal', 'legal', 'medium', ['Terms', 'Contracts', 'Policy'], 'Legal foundations protect founders and business operations.', 'https://www.investopedia.com/terms/c/compliance.asp'),
            $this->word('wd-login', 'login', 'easy', ['Auth', 'Accounts', 'Access'], 'Secure login systems are essential for trust and safety.', 'https://owasp.org/www-project-authentication-cheat-sheet/'),
            $this->word('wd-merge', 'merge', 'easy', ['Git', 'Branch', 'Integrate'], 'Clean merges improve team velocity and reduce release friction.', 'https://git-scm.com/docs/git-merge'),
            $this->word('wd-micro', 'micro', 'medium', ['Service', 'Modular', 'API'], 'Microservice thinking helps teams split complex systems.', 'https://martinfowler.com/articles/microservices.html'),
            $this->word('wd-niche', 'niche', 'easy', ['Segment', 'Focus', 'ICP'], 'Winning a niche is often the first major startup milestone.', 'https://www.ycombinator.com/library/6f-how-to-get-startup-ideas'),
            $this->word('wd-offer', 'offer', 'easy', ['Pricing', 'Package', 'Value'], 'A clear offer improves conversion and product adoption.', 'https://www.investopedia.com/terms/v/valueproposition.asp'),
            $this->word('wd-owner', 'owner', 'easy', ['Accountable', 'Lead', 'Decide'], 'Clear ownership helps teams execute faster with less confusion.', 'https://www.atlassian.com/agile/project-management/project-ownership'),
            $this->word('wd-patch', 'patch', 'medium', ['Fix', 'Update', 'Security'], 'Fast patching minimizes exposure and production incidents.', 'https://www.cisa.gov/known-exploited-vulnerabilities-catalog'),
            $this->word('wd-price', 'price', 'easy', ['Revenue', 'Market', 'Position'], 'Pricing strategy is one of the strongest startup growth levers.', 'https://www.ycombinator.com/library/4u-the-power-of-pricing'),
            $this->word('wd-quota', 'quota', 'medium', ['Limits', 'Usage', 'Capacity'], 'Quotas protect platforms from abuse and resource spikes.', 'https://learn.microsoft.com/azure/azure-resource-manager/management/azure-subscription-service-limits'),
            $this->word('wd-reach', 'reach', 'easy', ['Audience', 'Channel', 'Growth'], 'Distribution and reach shape startup growth outcomes.', 'https://www.ycombinator.com/library/6m-sales-and-distribution'),
            $this->word('wd-route', 'route', 'easy', ['Path', 'Flow', 'Direction'], 'Clear delivery routes help teams move work from backlog to release.', 'https://www.atlassian.com/agile/project-management'),
            $this->word('wd-sales', 'sales', 'easy', ['Leads', 'Deals', 'Pipeline'], 'Consistent sales motion is key for startup survival.', 'https://www.hubspot.com/sales/statistics'),
            $this->word('wd-share', 'share', 'medium', ['Equity', 'Ownership', 'Cap table'], 'Equity sharing affects fundraising and team incentives.', 'https://www.investopedia.com/terms/c/capitalization-table.asp'),
            $this->word('wd-smart', 'smart', 'easy', ['Goals', 'Metrics', 'Focus'], 'SMART goals help founders track meaningful progress.', 'https://www.mindtools.com/a4wo118/smart-goals'),
            $this->word('wd-squad', 'squad', 'easy', ['Team', 'Cross-functional', 'Ship'], 'Small squads can deliver product improvements quickly.', 'https://www.atlassian.com/agile/agile-at-scale/spotify'),
            $this->word('wd-stock', 'stock', 'medium', ['Options', 'Equity', 'Comp'], 'Stock options are common startup compensation incentives.', 'https://www.investopedia.com/terms/e/eso.asp'),
            $this->word('wd-story', 'story', 'easy', ['User', 'Requirement', 'Backlog'], 'Clear stories reduce ambiguity in implementation.', 'https://www.atlassian.com/agile/project-management/user-stories'),
            $this->word('wd-trust', 'trust', 'easy', ['Credibility', 'Brand', 'Retention'], 'Trust drives product adoption and long-term retention.', 'https://www.nngroup.com/articles/trustworthiness-of-websites/'),
            $this->word('wd-users', 'users', 'easy', ['DAU', 'Engagement', 'Retention'], 'User metrics reveal whether product value is real.', 'https://www.ycombinator.com/library/3q-how-to-measure-startup-progress'),
            $this->word('wd-value', 'value', 'easy', ['Benefit', 'Outcome', 'Solve'], 'Startups win by delivering clear customer value.', 'https://www.strategyzer.com/books/value-proposition-design'),
            $this->word('wd-viral', 'viral', 'medium', ['Referral', 'Loop', 'Growth'], 'Viral loops can reduce acquisition costs significantly.', 'https://www.ycombinator.com/library/2f-growth'),
            $this->word('wd-brand', 'brand', 'easy', ['Identity', 'Message', 'Recall'], 'Brand clarity helps startups stand out in noisy markets.', 'https://www.ycombinator.com/library/4e-how-to-build-your-brand'),
            $this->word('wd-brief', 'brief', 'easy', ['Concise', 'Direction', 'Goals'], 'A concise brief aligns product and business teams.', 'https://www.interaction-design.org/literature/article/design-briefs'),
            $this->word('wd-funds', 'funds', 'easy', ['Capital', 'Budget', 'Runway'], 'Fund management is essential for startup runway discipline.', 'https://www.investopedia.com/terms/f/funding.asp'),
            $this->word('wd-trace', 'trace', 'medium', ['Requirements', 'Mapping', 'QA'], 'Traceability helps teams link requirements to implementation and testing.', 'https://en.wikipedia.org/wiki/Requirements_traceability'),
            $this->word('wd-asset', 'asset', 'medium', ['IP', 'Data', 'Value'], 'Core assets often determine startup valuation growth.', 'https://www.investopedia.com/terms/a/asset.asp'),
            $this->word('wd-rival', 'rival', 'medium', ['Competitor', 'Gap', 'Differentiation'], 'Rival analysis helps identify unique positioning.', 'https://www.ycombinator.com/library/4c-competition'),
            $this->word('wd-lease', 'lease', 'medium', ['Space', 'Ops', 'Cost'], 'Lease obligations are a major operations consideration.', 'https://www.investopedia.com/terms/l/lease.asp'),
            $this->word('wd-raise', 'raise', 'easy', ['Round', 'Capital', 'Investors'], 'Raising capital is a major startup milestone.', 'https://www.ycombinator.com/library/7u-how-to-raise-money'),
            $this->word('wd-error', 'error', 'medium', ['Bug', 'Logs', 'Fix'], 'Error handling quality directly impacts reliability.', 'https://www.atlassian.com/incident-management/kpis/common-it-metrics'),
            $this->word('wd-cycle', 'cycle', 'easy', ['Iterate', 'Release', 'Learn'], 'Faster cycles help teams validate ideas sooner.', 'https://en.wikipedia.org/wiki/Iteration'),
            $this->word('wd-frame', 'frame', 'medium', ['Context', 'Problem', 'Approach'], 'Problem framing leads to better startup decisions.', 'https://www.nngroup.com/articles/problem-framing/'),
            $this->word('wd-stake', 'stake', 'medium', ['Share', 'Interest', 'Alignment'], 'Stakeholder alignment is critical in execution.', 'https://www.investopedia.com/terms/s/stakeholder.asp'),
            $this->word('wd-basis', 'basis', 'medium', ['Core', 'Foundation', 'Principles'], 'Healthy fundamentals form the basis of durable ventures.', 'https://en.wikipedia.org/wiki/Business_fundamentals'),
            $this->word('wd-token', 'token', 'medium', ['Access', 'Auth', 'Session'], 'Tokens secure authenticated interactions in modern apps.', 'https://auth0.com/docs/secure/tokens'),
            $this->word('wd-vault', 'vault', 'medium', ['Secrets', 'Secure', 'Storage'], 'Secret vaulting is essential in production systems.', 'https://owasp.org/www-project-web-security-testing-guide/'),
            $this->word('wd-order', 'order', 'easy', ['Flow', 'Process', 'Ops'], 'Orderly process reduces delivery mistakes and delays.', 'https://www.shopify.com/guides/ecommerce/order-management'),
            $this->word('wd-table', 'table', 'medium', ['Data', 'Rows', 'Schema'], 'Data table design impacts product speed and reliability.', 'https://www.postgresql.org/docs/current/tutorial-table.html'),
            $this->word('wd-boost', 'boost', 'easy', ['Accelerate', 'Lift', 'Momentum'], 'Acceleration programs can boost early startup execution.', 'https://www.ycombinator.com/'),
            $this->word('wd-shift', 'shift', 'easy', ['Adjust', 'Change', 'Direction'], 'Strategic shifts help ventures survive changing markets.', 'https://www.ycombinator.com/library/5z-the-real-product-market-fit'),
            $this->word('wd-split', 'split', 'medium', ['Divide', 'Equity', 'Shares'], 'Founder equity splits should be discussed early and clearly.', 'https://www.ycombinator.com/library/7o-co-founder-equity-split'),
            $this->word('wd-track', 'track', 'easy', ['Metrics', 'Monitor', 'Progress'], 'Tracking leading indicators helps teams react faster.', 'https://www.ycombinator.com/library/3q-how-to-measure-startup-progress'),
            $this->word('wd-yield', 'yield', 'medium', ['Return', 'Output', 'Efficiency'], 'Yield metrics measure how effectively resources are used.', 'https://www.investopedia.com/terms/y/yield.asp'),
            $this->word('wd-study', 'study', 'medium', ['Research', 'Validate', 'Data'], 'Market studies reduce assumptions and improve strategy.', 'https://en.wikipedia.org/wiki/Market_research'),
            $this->word('wd-focus', 'focus', 'easy', ['Priority', 'Attention', 'Execution'], 'Focus helps early-stage teams avoid wasted effort.', 'https://www.ycombinator.com/library/5v-we-dont-teach-sales'),
            $this->word('wd-epics', 'epics', 'medium', ['Backlog', 'Stories', 'Planning'], 'Epics organize large features into manageable user stories.', 'https://www.atlassian.com/agile/project-management/epics-stories-themes'),
            $this->word('wd-tools', 'tools', 'easy', ['IDE', 'CI/CD', 'Workflow'], 'Good development tools improve team speed, quality, and consistency.', 'https://en.wikipedia.org/wiki/Programming_tool'),
            $this->word('wd-demos', 'demos', 'easy', ['Prototype', 'Showcase', 'Pitch'], 'Strong demos help founders communicate product value.', 'https://www.ycombinator.com/library/4B-how-to-demo-day'),
            $this->word('wd-ethos', 'ethos', 'medium', ['Culture', 'Values', 'Identity'], 'A clear team ethos helps hiring and execution alignment.', 'https://www.ycombinator.com/library/7c-how-to-manage-a-startup'),
            $this->word('wd-chart', 'chart', 'medium', ['Planning', 'Timeline', 'Scope'], 'Project charts help teams visualize scope, timing, and dependencies.', 'https://en.wikipedia.org/wiki/Gantt_chart'),
            $this->word('wd-tempo', 'tempo', 'easy', ['Pace', 'Rhythm', 'Cadence'], 'Execution tempo often separates strong teams from stalled ones.', 'https://www.ycombinator.com/library/2t-how-to-make-your-idea-sound-cool'),
            $this->word('wd-tasks', 'tasks', 'easy', ['Sprint', 'Tickets', 'Delivery'], 'Breaking work into tasks keeps sprint execution clear and trackable.', 'https://www.atlassian.com/agile/project-management'),
            $this->word('wd-nodes', 'nodes', 'medium', ['Network', 'Infra', 'Links'], 'Product and community networks are built node by node.', 'https://www.ycombinator.com/library/3a-building-communities'),
            $this->word('wd-logos', 'logos', 'medium', ['Identity', 'Brand', 'Visual'], 'Clear visual identity increases brand recall.', 'https://www.ycombinator.com/library/4e-how-to-build-your-brand'),
            $this->word('wd-tests', 'tests', 'easy', ['QA', 'Coverage', 'Reliability'], 'Automated tests reduce regressions and improve release confidence.', 'https://martinfowler.com/bliki/TestPyramid.html'),
            $this->word('wd-claim', 'claim', 'easy', ['Position', 'Own', 'Market'], 'Early positioning claims can become durable advantages.', 'https://www.ycombinator.com/library/6m-sales-and-distribution'),
            $this->word('wd-bolts', 'bolts', 'medium', ['Speed', 'Fast move', 'Execution'], 'Fast-moving teams often outlearn slower competitors.', 'https://www.ycombinator.com/library/5u-tradeoffs'),
            $this->word('wd-pilot', 'pilot', 'easy', ['Trial', 'First run', 'Validate'], 'Pilot launches reduce risk before wider rollout.', 'https://www.strategyzer.com/books/testing-business-ideas'),
            $this->word('wd-debug', 'debug', 'easy', ['Fix', 'Inspect', 'Logs'], 'Good debugging discipline protects product stability.', 'https://developer.mozilla.org/en-US/docs/Learn/Common_questions/Tools_and_setup/What_are_browser_developer_tools'),
            $this->word('wd-learn', 'learn', 'easy', ['Discover', 'Insight', 'Adapt'], 'Startup teams that learn quickly compound advantage.', 'https://www.ycombinator.com/library/2f-growth'),
            $this->word('wd-train', 'train', 'easy', ['Practice', 'Improve', 'Enable'], 'Training improves team quality and delivery consistency.', 'https://en.wikipedia.org/wiki/Training'),
            $this->word('wd-adopt', 'adopt', 'easy', ['Use', 'Onboard', 'Retention'], 'Adoption is a key signal of real customer value.', 'https://www.productplan.com/glossary/product-adoption/'),
            $this->word('wd-churn', 'churn', 'medium', ['Loss', 'Retention', 'Cancel'], 'Reducing churn is critical for sustainable growth.', 'https://www.investopedia.com/terms/c/churnrate.asp'),
            $this->word('wd-leads', 'leads', 'easy', ['Prospects', 'Pipeline', 'Sales'], 'Lead quality often matters more than lead volume.', 'https://www.hubspot.com/sales/lead-generation'),
            $this->word('wd-click', 'click', 'easy', ['CTA', 'Conversion', 'UX'], 'Higher click-through rates can signal stronger messaging.', 'https://mailchimp.com/marketing-glossary/click-through-rate/'),
            $this->word('wd-queue', 'queue', 'medium', ['Backlog', 'Order', 'Wait'], 'Queue design affects latency and user experience.', 'https://en.wikipedia.org/wiki/Queueing_theory'),
            $this->word('wd-ratio', 'ratio', 'medium', ['Metric', 'Compare', 'Health'], 'Financial and product ratios guide decision making.', 'https://www.investopedia.com/terms/r/ratio.asp'),
            $this->word('wd-intel', 'intel', 'easy', ['Tech', 'Chip', 'Company'], 'Intel is a 5-letter global tech brand commonly referenced in startup and innovation contexts.', 'https://www.intel.com/'),
            $this->word('wd-tesla', 'tesla', 'easy', ['EV', 'Energy', 'Tech'], 'Tesla is included as a recognizable 5-letter startup-related tech company name.', 'https://www.tesla.com/'),
            $this->word('wd-baidu', 'baidu', 'easy', ['Search', 'AI', 'China'], 'Baidu is a 5-letter technology company name included in the startup word pool.', 'https://www.baidu.com/'),
            $this->word('wd-costs', 'costs', 'easy', ['Spend', 'Budget', 'Runway'], 'Runway improves when teams manage costs with discipline.', 'https://www.investopedia.com/terms/c/cost-of-capital.asp'),
            $this->word('wd-goals', 'goals', 'easy', ['Targets', 'Milestone', 'Plan'], 'Clear goals keep founders and teams aligned.', 'https://www.mindtools.com/a4wo118/smart-goals'),
            $this->word('wd-plans', 'plans', 'easy', ['Roadmap', 'Priorities', 'Action'], 'Strong plans reduce rework and execution drift.', 'https://www.atlassian.com/agile/project-management'),
            $this->word('wd-draft', 'draft', 'easy', ['Initial', 'Version', 'Review'], 'Drafting quickly enables earlier feedback loops.', 'https://www.nngroup.com/articles/iterative-design/'),
            $this->word('wd-terms', 'terms', 'medium', ['Legal', 'Conditions', 'Policy'], 'Clear terms protect both business and users.', 'https://www.investopedia.com/terms/t/term-sheet.asp'),
            $this->word('wd-speed', 'speed', 'easy', ['Fast', 'Latency', 'Delivery'], 'Speed of learning and shipping is a startup advantage.', 'https://www.ycombinator.com/library/2f-growth'),
            $this->word('wd-solve', 'solve', 'easy', ['Problem', 'Users', 'Value'], 'Great startups solve real problems for specific users.', 'https://www.ycombinator.com/library/6f-how-to-get-startup-ideas')
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