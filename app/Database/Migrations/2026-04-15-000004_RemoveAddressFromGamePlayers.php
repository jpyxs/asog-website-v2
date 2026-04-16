<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveAddressFromGamePlayers extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('game_players')) {
            return;
        }

        $fields = $this->db->getFieldNames('game_players');

        if (! in_array('firstName', $fields, true)) {
            $this->forge->addColumn('game_players', [
                'firstName' => [
                    'type' => 'VARCHAR',
                    'constraint' => 60,
                    'null' => true,
                    'after' => 'fullName',
                ],
            ]);
        }

        $fields = $this->db->getFieldNames('game_players');
        if (! in_array('middleName', $fields, true)) {
            $this->forge->addColumn('game_players', [
                'middleName' => [
                    'type' => 'VARCHAR',
                    'constraint' => 60,
                    'null' => true,
                    'after' => 'firstName',
                ],
            ]);
        }

        $fields = $this->db->getFieldNames('game_players');
        if (! in_array('lastName', $fields, true)) {
            $this->forge->addColumn('game_players', [
                'lastName' => [
                    'type' => 'VARCHAR',
                    'constraint' => 60,
                    'null' => true,
                    'after' => 'middleName',
                ],
            ]);
        }

        $fields = $this->db->getFieldNames('game_players');
        if (! in_array('program', $fields, true)) {
            $this->forge->addColumn('game_players', [
                'program' => [
                    'type' => 'VARCHAR',
                    'constraint' => 120,
                    'null' => true,
                    'after' => 'lastName',
                ],
            ]);
        }

        $fields = $this->db->getFieldNames('game_players');
        if (in_array('address', $fields, true)) {
            $builder = $this->db->table('game_players');
            $query = $builder->select('id, address')->get();

            foreach ($query->getResultArray() as $row) {
                $raw = trim((string) ($row['address'] ?? ''));
                if ($raw === '') {
                    continue;
                }

                $decoded = json_decode($raw, true);
                if (! is_array($decoded)) {
                    continue;
                }

                $updates = [];
                $firstName = trim((string) ($decoded['first_name'] ?? ''));
                $middleName = trim((string) ($decoded['middle_name'] ?? ''));
                $lastName = trim((string) ($decoded['last_name'] ?? ''));
                $program = trim((string) ($decoded['program'] ?? ''));

                if ($firstName !== '') {
                    $updates['firstName'] = $firstName;
                }
                if ($middleName !== '') {
                    $updates['middleName'] = $middleName;
                }
                if ($lastName !== '') {
                    $updates['lastName'] = $lastName;
                }
                if ($program !== '') {
                    $updates['program'] = $program;
                }

                if ($updates !== []) {
                    $builder->where('id', (int) $row['id'])->update($updates);
                }
            }

            $this->forge->dropColumn('game_players', 'address');
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('game_players')) {
            return;
        }

        $fields = $this->db->getFieldNames('game_players');
        if (! in_array('address', $fields, true)) {
            $this->forge->addColumn('game_players', [
                'address' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'school',
                ],
            ]);
        }
    }
}
