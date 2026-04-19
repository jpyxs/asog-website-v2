<?php

namespace App\Models;

use CodeIgniter\Model;

class GamePlayerModel extends Model
{
    protected $table = 'game_players';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';

    protected $allowedFields = [
        'fullName',
        'firstName',
        'middleName',
        'lastName',
        'school',
        'isActive',
        'lastLoginAt',
    ];

    public function findByGoogleAccount(string $email, string $googleSub): ?array
    {
        return null;
    }

    public function findActiveById(int $id): ?array
    {
        return $this->where('id', $id)
            ->where('isActive', 1)
            ->first();
    }

    public function isProfileComplete(array $player): bool
    {
        $fullName = trim((string) ($player['fullName'] ?? ''));
        $firstName = trim((string) ($player['firstName'] ?? ''));
        $lastName = trim((string) ($player['lastName'] ?? ''));
        $school = trim((string) ($player['school'] ?? ''));

        if ($fullName === '' || $firstName === '' || $lastName === '' || $school === '') {
            return false;
        }

        return true;
    }

    public function findActiveByIdentity(string $firstName, string $lastName, string $school, int $excludePlayerId = 0): ?array
    {
        $firstName = strtolower(trim($firstName));
        $lastName = strtolower(trim($lastName));
        $school = strtolower(trim($school));

        if ($firstName === '' || $lastName === '' || $school === '') {
            return null;
        }

        $sql = "
            SELECT id, fullName, firstName, lastName, school, isActive
            FROM game_players
            WHERE isActive = 1
            AND LOWER(firstName) = ?
            AND LOWER(lastName) = ?
            AND LOWER(school) = ?
        ";
        
        $params = [$firstName, $lastName, $school];
        
        if ($excludePlayerId > 0) {
            $sql .= " AND id != ?";
            $params[] = $excludePlayerId;
        }
        
        $result = $this->db->query($sql, $params)->getResultArray();
        return count($result) > 0 ? $result[0] : null;
    }
}
