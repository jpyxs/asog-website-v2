<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsArchivedToContactMessages extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('contact_messages', [
            'isArchived' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'isRead',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('contact_messages', 'isArchived');
    }
}
