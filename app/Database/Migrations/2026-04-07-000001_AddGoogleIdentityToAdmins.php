<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGoogleIdentityToAdmins extends Migration
{
    private function uniqueIndexExists(string $table, string $column): bool
    {
        $result = $this->db->query(
            'SHOW INDEX FROM `' . $table . '` WHERE Column_name = ' . $this->db->escape($column) . ' AND Non_unique = 0'
        );

        return $result !== false && $result->getNumRows() > 0;
    }

    public function up(): void
    {
        $fields = [
            'googleEmail' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'email',
            ],
            'googleSub' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'googleEmail',
            ],
        ];

        $missingFields = [];

        foreach ($fields as $fieldName => $definition) {
            if (! $this->db->fieldExists($fieldName, 'admins')) {
                $missingFields[$fieldName] = $definition;
            }
        }

        if ($missingFields !== []) {
            $this->forge->addColumn('admins', $missingFields);
        }

        if (! $this->uniqueIndexExists('admins', 'googleEmail')) {
            $this->db->query('ALTER TABLE `admins` ADD UNIQUE KEY `admins_googleEmail_unique` (`googleEmail`)');
        }

        if (! $this->uniqueIndexExists('admins', 'googleSub')) {
            $this->db->query('ALTER TABLE `admins` ADD UNIQUE KEY `admins_googleSub_unique` (`googleSub`)');
        }
    }

    public function down(): void
    {
        if ($this->uniqueIndexExists('admins', 'googleEmail')) {
            $this->db->query('ALTER TABLE `admins` DROP INDEX `admins_googleEmail_unique`');
        }

        if ($this->uniqueIndexExists('admins', 'googleSub')) {
            $this->db->query('ALTER TABLE `admins` DROP INDEX `admins_googleSub_unique`');
        }

        $dropFields = [];

        if ($this->db->fieldExists('googleEmail', 'admins')) {
            $dropFields[] = 'googleEmail';
        }

        if ($this->db->fieldExists('googleSub', 'admins')) {
            $dropFields[] = 'googleSub';
        }

        if ($dropFields !== []) {
            $this->forge->dropColumn('admins', $dropFields);
        }
    }
}