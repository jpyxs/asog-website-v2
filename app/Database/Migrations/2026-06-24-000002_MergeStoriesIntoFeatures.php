<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MergeStoriesIntoFeatures extends Migration
{
    public function up(): void
    {
        $this->db->query("UPDATE posts SET category = 'features' WHERE category = 'stories'");
    }

    public function down(): void
    {
        $this->db->query("UPDATE posts SET category = 'stories' WHERE category = 'features'");
    }
}
