<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCapolesOrganizationMemberPhotoCache extends Migration
{
    private const SECTION_CORE_TEAM = 'core_team';
    private const OLD_FULL_NAME = 'Eng. Wenceslao D. Gavino';
    private const NEW_FULL_NAME = 'Mr. Kevin B. Capoles';
    private const PHOTO_PATH = 'assets/img/team/Capoles.webp';

    public function up(): void
    {
        if (! $this->db->tableExists('organization_members')) {
            return;
        }

        $this->db->table('organization_members')
            ->where('section', self::SECTION_CORE_TEAM)
            ->groupStart()
                ->where('fullName', self::OLD_FULL_NAME)
                ->orWhere('fullName', self::NEW_FULL_NAME)
                ->orWhere('photoPath', 'assets/img/team/Gavino.png')
                ->orWhere('photoPath', self::PHOTO_PATH)
            ->groupEnd()
            ->update([
                'fullName'      => self::NEW_FULL_NAME,
                'rolePrimary'   => 'Team Member',
                'roleSecondary' => 'ITSO Manager',
                'photoPath'     => self::PHOTO_PATH,
                'updatedAt'     => date('Y-m-d H:i:s'),
            ]);

        $this->bumpOrganizationPublicCache();
    }

    public function down(): void
    {
        $this->bumpOrganizationPublicCache();
    }

    private function bumpOrganizationPublicCache(): void
    {
        service('cache')->save(
            'asog_organization_public_version',
            time() . '_' . random_int(1000, 9999),
            86400
        );
    }
}
