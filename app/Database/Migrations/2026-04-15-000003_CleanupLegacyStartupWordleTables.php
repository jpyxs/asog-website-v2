<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanupLegacyStartupWordleTables extends Migration
{
    public function up(): void
    {
        // Keep one canonical game schema by removing legacy table names.
        $this->db->query('DROP TABLE IF EXISTS startupwordleplays');
        $this->db->query('DROP TABLE IF EXISTS startupwordleplayers');
    }

    public function down(): void
    {
        // No-op: legacy tables are intentionally removed.
    }
}