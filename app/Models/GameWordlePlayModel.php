<?php

namespace App\Models;

use CodeIgniter\Model;

class GameWordlePlayModel extends Model
{
    private const STATUS_IN_PROGRESS = 'in_progress';
    private const STATUS_SOLVED = 'solved';
    private const STATUS_FORFEITED = 'forfeited';

    protected $table = 'game_wordle_plays';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';

    protected $allowedFields = [
        'playerId',
        'playDate',
        'status',
        'attemptsUsed',
        'elapsedMs',
        'score',
        'startedAt',
        'finishedAt',
    ];

    public function findByPlayerAndDate(int $playerId, string $playDate): ?array
    {
        return $this->where('playerId', $playerId)
            ->where('playDate', $playDate)
            ->first();
    }

    public function hasCompletedPlayForDate(int $playerId, string $playDate): bool
    {
        $row = $this->select('status')
            ->where('playerId', $playerId)
            ->where('playDate', $playDate)
            ->first();

        if (! is_array($row)) {
            return false;
        }

        $status = (string) ($row['status'] ?? '');
        return in_array($status, [self::STATUS_SOLVED, self::STATUS_FORFEITED], true);
    }

    public function getTopByDate(string $playDate, int $limit = 10): array
    {
        $rows = $this->select('game_wordle_plays.*, game_players.fullName, game_players.school')
            ->join('game_players', 'game_players.id = game_wordle_plays.playerId', 'left')
            ->where('game_wordle_plays.playDate', $playDate)
            ->whereIn('game_wordle_plays.status', [self::STATUS_SOLVED, self::STATUS_FORFEITED])
            ->orderBy(
                'CASE WHEN game_wordle_plays.status = ' . $this->db->escape(self::STATUS_SOLVED) . ' THEN 0 ELSE 1 END',
                'ASC',
                false
            )
            ->orderBy('game_wordle_plays.attemptsUsed', 'DESC')
            ->orderBy('game_wordle_plays.score', 'DESC')
            ->orderBy('game_wordle_plays.elapsedMs', 'ASC')
            ->orderBy('game_wordle_plays.finishedAt', 'ASC')
            ->findAll($limit);

        return $this->decorateLeaderboardRows($rows);
    }

    public function getRankByDateAndPlay(int $playerId, string $playDate): ?array
    {
        $current = $this->select('id')
            ->where('playerId', $playerId)
            ->where('playDate', $playDate)
            ->whereIn('status', [self::STATUS_SOLVED, self::STATUS_FORFEITED])
            ->first();

        if (! is_array($current)) {
            return null;
        }

        $rows = $this->select('game_wordle_plays.*, game_players.fullName, game_players.school')
            ->join('game_players', 'game_players.id = game_wordle_plays.playerId', 'left')
            ->where('game_wordle_plays.playDate', $playDate)
            ->whereIn('game_wordle_plays.status', [self::STATUS_SOLVED, self::STATUS_FORFEITED])
            ->orderBy(
                'CASE WHEN game_wordle_plays.status = ' . $this->db->escape(self::STATUS_SOLVED) . ' THEN 0 ELSE 1 END',
                'ASC',
                false
            )
            ->orderBy('game_wordle_plays.attemptsUsed', 'DESC')
            ->orderBy('game_wordle_plays.score', 'DESC')
            ->orderBy('game_wordle_plays.elapsedMs', 'ASC')
            ->orderBy('game_wordle_plays.finishedAt', 'ASC')
            ->orderBy('game_wordle_plays.id', 'ASC')
            ->findAll();

        $rows = $this->decorateLeaderboardRows($rows);
        $targetId = (int) ($current['id'] ?? 0);

        foreach ($rows as $row) {
            if ((int) ($row['id'] ?? 0) === $targetId) {
                return $row;
            }
        }

        return null;
    }

    public function hasNameAlreadyWonTopThreeForDate(string $fullName, string $playDate): bool
    {
        $normalizedName = strtolower(trim($fullName));
                $playDate = trim($playDate);

                if ($normalizedName === '' || $playDate === '') {
            return false;
        }

        $sql = <<<'SQL'
SELECT 1
FROM (
    SELECT
        gwp.playerId,
        ROW_NUMBER() OVER (
            PARTITION BY gwp.playDate
            ORDER BY gwp.attemptsUsed DESC, gwp.score DESC, gwp.elapsedMs ASC, gwp.finishedAt ASC, gwp.id ASC
        ) AS row_num
    FROM game_wordle_plays gwp
    WHERE gwp.status = ?
      AND gwp.playDate = ?
      AND gwp.score > 0
) ranked
INNER JOIN game_players gp ON gp.id = ranked.playerId
WHERE ranked.row_num <= 3
  AND LOWER(TRIM(gp.fullName)) = ?
LIMIT 1
SQL;

        $result = $this->db->query($sql, [self::STATUS_SOLVED, $playDate, $normalizedName])->getRowArray();
        return is_array($result);
    }

    public function hasNameRegistrationForDate(string $fullName, string $playDate): bool
    {
        $normalizedName = strtolower(trim($fullName));
        $playDate = trim($playDate);

        if ($normalizedName === '' || $playDate === '') {
            return false;
        }

        $sql = <<<'SQL'
SELECT 1
FROM game_wordle_plays gwp
INNER JOIN game_players gp ON gp.id = gwp.playerId
WHERE gwp.playDate = ?
  AND LOWER(TRIM(gp.fullName)) = ?
LIMIT 1
SQL;

        $result = $this->db->query($sql, [$playDate, $normalizedName])->getRowArray();
        return is_array($result);
    }

    private function decorateLeaderboardRows(array $rows): array
    {
        $decorated = [];
        $lastRank = 0;
        $zeroScoreRank = null;

        foreach ($rows as $row) {
            $status = (string) ($row['status'] ?? '');
            if ($status === self::STATUS_FORFEITED) {
                $row['rank'] = null;
                $row['rankLabel'] = 'Unranked';
                $decorated[] = $row;
                continue;
            }

            $score = (int) ($row['score'] ?? 0);

            // Keep non-zero plays fully ordered (time-aware), but tie all zero-score completions.
            if ($score > 0) {
                $lastRank++;
                $row['rank'] = $lastRank;
                $row['rankLabel'] = '#' . $lastRank;
                $decorated[] = $row;
                continue;
            }

            if ($zeroScoreRank === null) {
                $lastRank++;
                $zeroScoreRank = $lastRank;
            }

            $row['rank'] = $zeroScoreRank;
            $row['rankLabel'] = '#' . $zeroScoreRank;
            $decorated[] = $row;
        }

        return $decorated;
    }

    public function markAsSolved(int $playId, int $attemptsUsed, int $elapsedMs, int $score): bool
    {
        return $this->update($playId, [
            'status' => self::STATUS_SOLVED,
            'attemptsUsed' => max(0, min(5, $attemptsUsed)),
            'elapsedMs' => max(0, $elapsedMs),
            'score' => max(0, $score),
            'finishedAt' => date('Y-m-d H:i:s'),
        ]);
    }

    public function markAsForfeited(int $playId, int $attemptsUsed, int $elapsedMs): bool
    {
        return $this->update($playId, [
            'status' => self::STATUS_FORFEITED,
            'attemptsUsed' => max(0, min(5, $attemptsUsed)),
            'elapsedMs' => max(0, $elapsedMs),
            'score' => 0,
            'finishedAt' => date('Y-m-d H:i:s'),
        ]);
    }

    public function statusInProgress(): string
    {
        return self::STATUS_IN_PROGRESS;
    }

    public function statusSolved(): string
    {
        return self::STATUS_SOLVED;
    }

    public function statusForfeited(): string
    {
        return self::STATUS_FORFEITED;
    }
}
