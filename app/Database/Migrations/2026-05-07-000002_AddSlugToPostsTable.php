<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlugToPostsTable extends Migration
{
    public function up(): void
    {
        if ($this->db->fieldExists('slug', 'posts')) {
            return;
        }

        $this->forge->addColumn('posts', [
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'title',
            ],
        ]);

        $this->db->query("UPDATE `posts` SET `slug` = LOWER(TRIM(BOTH '-' FROM REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`title`, ' ', '-'), '/', '-'), '.', ''), ',', ''), '&', 'and'))) WHERE `slug` IS NULL OR `slug` = ''");

        $this->forge->modifyColumn('posts', [
            'slug' => [
                'name'       => 'slug',
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);
    }

    public function down(): void
    {
        if (! $this->db->fieldExists('slug', 'posts')) {
            return;
        }

        $this->forge->dropColumn('posts', 'slug');
    }
}