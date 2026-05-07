<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostSlugHistoryTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'postId' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'oldSlug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('postId');
        $this->forge->addUniqueKey('oldSlug', 'idx_post_slug_history_oldSlug');
        $this->forge->createTable('post_slug_history', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('post_slug_history', true);
    }
}
