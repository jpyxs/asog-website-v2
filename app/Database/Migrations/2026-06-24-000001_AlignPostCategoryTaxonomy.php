<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlignPostCategoryTaxonomy extends Migration
{
    public function up(): void
    {
        $this->db->query("UPDATE posts SET category = 'stories' WHERE category = 'opinions'");
    }

    public function down(): void
    {
        $this->db->query("UPDATE posts SET category = 'opinions' WHERE category = 'stories'");
    }
}
