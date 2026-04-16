<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGameWordlePlaysTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'playerId' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'playDate' => [
                'type' => 'DATE',
            ],
            'answerWord' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'in_progress',
            ],
            'attemptsUsed' => [
                'type'       => 'TINYINT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'elapsedMs' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'score' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'startedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'finishedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['playerId', 'playDate'], 'idx_game_wordle_player_day_unique');
        $this->forge->addKey(['playDate', 'status']);
        $this->forge->addKey('score');
        $this->forge->addKey('finishedAt');
        $this->forge->addForeignKey('playerId', 'game_players', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('game_wordle_plays', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('game_wordle_plays', true);
    }
}
