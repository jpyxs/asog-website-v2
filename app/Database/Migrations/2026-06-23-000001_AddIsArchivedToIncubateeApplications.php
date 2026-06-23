<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsArchivedToIncubateeApplications extends Migration
{
    public function up(): void
    {
        if ($this->db->fieldExists('isArchived', 'incubatee_applications')) {
            return;
        }

        $this->forge->addColumn('incubatee_applications', [
            'isArchived' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'applicationStatus',
            ],
        ]);
    }

    public function down(): void
    {
        if (! $this->db->fieldExists('isArchived', 'incubatee_applications')) {
            return;
        }

        $this->forge->dropColumn('incubatee_applications', 'isArchived');
    }
}
