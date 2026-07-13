<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApplyFaqVisibilitySetting extends Migration
{
    private const SETTING_KEY = 'apply_show_faqs';

    public function up(): void
    {
        if (! $this->db->tableExists('landing_settings')) {
            return;
        }

        $existing = $this->db->table('landing_settings')
            ->where('settingKey', self::SETTING_KEY)
            ->get()
            ->getRowArray();

        if (is_array($existing)) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('landing_settings')->insert([
            'settingKey'   => self::SETTING_KEY,
            'settingValue' => '1',
            'createdAt'    => $now,
            'updatedAt'    => $now,
        ]);

        $this->clearSettingCache();
    }

    public function down(): void
    {
        if (! $this->db->tableExists('landing_settings')) {
            return;
        }

        $this->db->table('landing_settings')
            ->where('settingKey', self::SETTING_KEY)
            ->delete();

        $this->clearSettingCache();
    }

    private function clearSettingCache(): void
    {
        service('cache')->delete('asog_landing_setting_' . hash('sha256', self::SETTING_KEY));
    }
}
