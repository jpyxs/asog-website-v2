<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveReviewedStatusFromApplications extends Migration
{
    public function up()
    {
        // Migrate any orphaned 'reviewed' rows to 'pending' before shrinking the ENUM
        $this->db->query(
            "UPDATE incubatee_applications SET applicationStatus = 'pending' WHERE applicationStatus = 'reviewed'"
        );

        // Remove 'reviewed' from the ENUM
        $this->db->query(
            "ALTER TABLE incubatee_applications
             MODIFY COLUMN applicationStatus ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending'"
        );
    }

    public function down()
    {
        // Restore the original ENUM (no data migration needed on rollback)
        $this->db->query(
            "ALTER TABLE incubatee_applications
             MODIFY COLUMN applicationStatus ENUM('pending','reviewed','accepted','rejected') NOT NULL DEFAULT 'pending'"
        );
    }
}
