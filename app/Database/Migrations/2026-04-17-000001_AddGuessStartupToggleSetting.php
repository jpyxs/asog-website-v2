<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGuessStartupToggleSetting extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('landing_settings')) {
            return;
        }

        $existing = $this->db->table('landing_settings')
            ->where('settingKey', 'guessStartupEnabled')
            ->get()
            ->getRowArray();

        if (is_array($existing)) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('landing_settings')->insert([
            'settingKey' => 'guessStartupEnabled',
            'settingValue' => '1',
            'createdAt' => $now,
            'updatedAt' => $now,
        ]);
    }

    public function down(): void
    {
        if (! $this->db->tableExists('landing_settings')) {
            return;
        }

        $this->db->table('landing_settings')
            ->where('settingKey', 'guessStartupEnabled')
            ->delete();
    }
}
