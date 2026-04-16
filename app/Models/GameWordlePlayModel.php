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

        $rank = 1;
        foreach ($rows as &$row) {
            $row['rank'] = $rank;
            $rank++;
        }
        unset($row);

        return $rows;
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

        $targetId = (int) ($current['id'] ?? 0);
        $rank = 1;

        foreach ($rows as $row) {
            if ((int) ($row['id'] ?? 0) === $targetId) {
                $row['rank'] = $rank;
                return $row;
            }

            $rank++;
        }

        return null;
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
