<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGamePlayersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fullName' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'firstName' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
            ],
            'middleName' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
            ],
            'lastName' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
            ],
            'school' => [
                'type'       => 'VARCHAR',
                'constraint' => 190,
                'null'       => true,
            ],
            'isActive' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'lastLoginAt' => [
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
    $this->forge->addUniqueKey(['firstName', 'lastName', 'school'], 'idx_game_players_first_last_school_unique');
        $this->forge->addKey('isActive');

        $this->forge->createTable('game_players', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('game_players', true);
    }
}
