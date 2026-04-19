<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveLegacyIdentityFieldsFromGamePlayers extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('game_players')) {
            return;
        }

        $indexes = $this->db->getIndexData('game_players');
        foreach (['idx_game_players_email_unique', 'idx_game_players_google_sub_unique'] as $indexName) {
            if (isset($indexes[$indexName])) {
                $this->db->query('ALTER TABLE `game_players` DROP INDEX `' . $indexName . '`');
            }
        }

        $fields = $this->db->getFieldNames('game_players');
        foreach (['program', 'email', 'googleSub', 'avatarUrl'] as $field) {
            if (in_array($field, $fields, true)) {
                $this->forge->dropColumn('game_players', $field);
            }
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('game_players')) {
            return;
        }

        $fields = $this->db->getFieldNames('game_players');

        if (! in_array('program', $fields, true)) {
            $this->forge->addColumn('game_players', [
                'program' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                    'null'       => true,
                    'after'      => 'lastName',
                ],
            ]);
        }

        if (! in_array('email', $fields, true)) {
            $this->forge->addColumn('game_players', [
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'school',
                ],
            ]);
        }

        $fields = $this->db->getFieldNames('game_players');
        if (! in_array('googleSub', $fields, true)) {
            $this->forge->addColumn('game_players', [
                'googleSub' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'email',
                ],
            ]);
        }

        $fields = $this->db->getFieldNames('game_players');
        if (! in_array('avatarUrl', $fields, true)) {
            $this->forge->addColumn('game_players', [
                'avatarUrl' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'after'      => 'googleSub',
                ],
            ]);
        }
    }
}