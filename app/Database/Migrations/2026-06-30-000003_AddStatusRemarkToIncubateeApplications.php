<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusRemarkToIncubateeApplications extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('statusRemark', 'incubatee_applications')) {
            return;
        }

        $this->forge->addColumn('incubatee_applications', [
            'statusRemark' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'applicationStatus',
            ],
        ]);
    }

    public function down()
    {
        if (! $this->db->fieldExists('statusRemark', 'incubatee_applications')) {
            return;
        }

        $this->forge->dropColumn('incubatee_applications', 'statusRemark');
    }
}
