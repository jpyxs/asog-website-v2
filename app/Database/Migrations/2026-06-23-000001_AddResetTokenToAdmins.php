<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResetTokenToAdmins extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('admins', [
            'resetToken' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'resetTokenExpiresAt' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('resetToken');
    }

    public function down(): void
    {
        $this->forge->dropColumn('admins', ['resetToken', 'resetTokenExpiresAt']);
    }
}
