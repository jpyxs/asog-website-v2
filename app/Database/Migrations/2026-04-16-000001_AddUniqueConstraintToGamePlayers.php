<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueConstraintToGamePlayers extends Migration
{
    public function up(): void
    {
        // Step 1: Find and deactivate older duplicate entries (keep only the most recent per firstName+lastName+school)
        $sql = "
            UPDATE game_players
            SET isActive = 0
            WHERE id NOT IN (
                SELECT id FROM (
                    SELECT MAX(id) as id
                    FROM game_players
                    WHERE isActive = 1
                    GROUP BY LOWER(firstName), LOWER(lastName), LOWER(school)
                ) as latest
            )
            AND isActive = 1
        ";
        
        try {
            $this->db->query($sql);
        } catch (\Exception $e) {
            // Log but don't fail - we'll still try to add the constraint
            log_message('error', 'Error deactivating duplicates: ' . $e->getMessage());
        }
        
        // Step 2: Add unique composite index on firstName, lastName, and school
        $this->db->query(
            'ALTER TABLE `game_players`
            ADD UNIQUE INDEX `idx_game_players_first_last_school_unique` (
                `firstName`(60),
                `lastName`(60),
                `school`(190)
            )'
        );
    }

    public function down(): void
    {
        if ($this->db->getIndexData('game_players')['idx_game_players_first_last_school_unique'] ?? false) {
            $this->db->query('ALTER TABLE `game_players` DROP INDEX `idx_game_players_first_last_school_unique`');
        }
    }
}

