<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReplaceGavinoWithCapolesOrganizationMember extends Migration
{
    private const SECTION_CORE_TEAM = 'core_team';

    private const OLD_FULL_NAME = 'Eng. Wenceslao D. Gavino';
    private const OLD_ROLE_PRIMARY = 'Team Member';
    private const OLD_ROLE_SECONDARY = 'ITSO Manager';
    private const OLD_PHOTO_PATH = 'assets/img/team/Gavino.png';

    private const NEW_FULL_NAME = 'Mr. Kevin B. Capoles';
    private const NEW_ROLE_PRIMARY = 'Team Member';
    private const NEW_ROLE_SECONDARY = 'ITSO Manager';
    private const NEW_PHOTO_PATH = 'assets/img/team/Capoles.webp';

    public function up(): void
    {
        if (! $this->db->tableExists('organization_members')) {
            return;
        }

        $this->db->table('organization_members')
            ->where('section', self::SECTION_CORE_TEAM)
            ->where('fullName', self::OLD_FULL_NAME)
            ->update([
                'fullName'      => self::NEW_FULL_NAME,
                'rolePrimary'   => self::NEW_ROLE_PRIMARY,
                'roleSecondary' => self::NEW_ROLE_SECONDARY,
                'photoPath'     => self::NEW_PHOTO_PATH,
                'updatedAt'     => date('Y-m-d H:i:s'),
            ]);

        $this->bumpOrganizationPublicCache();
    }

    public function down(): void
    {
        if (! $this->db->tableExists('organization_members')) {
            return;
        }

        $this->db->table('organization_members')
            ->where('section', self::SECTION_CORE_TEAM)
            ->where('fullName', self::NEW_FULL_NAME)
            ->update([
                'fullName'      => self::OLD_FULL_NAME,
                'rolePrimary'   => self::OLD_ROLE_PRIMARY,
                'roleSecondary' => self::OLD_ROLE_SECONDARY,
                'photoPath'     => self::OLD_PHOTO_PATH,
                'updatedAt'     => date('Y-m-d H:i:s'),
            ]);

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
