<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedOrganizationMembers extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('organization_members')) {
            return;
        }

        if ($this->db->table('organization_members')->countAllResults() > 0) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $rows = [
            // Core Team
            ['section' => 'core_team', 'fullName' => 'Ms. Cherry Lyn M. Odsinada', 'rolePrimary' => 'Project Leader', 'roleSecondary' => null, 'mentorCategory' => null, 'photoPath' => 'assets/img/team/Odsinada.png', 'isFeatured' => 1, 'sortOrder' => 1, 'isPublished' => 1],
            ['section' => 'core_team', 'fullName' => 'Ms. Rosel O. Onesa', 'rolePrimary' => 'Team Member', 'roleSecondary' => 'Marketing & Communications Lead', 'mentorCategory' => null, 'photoPath' => 'assets/img/team/Onesa.png', 'isFeatured' => 0, 'sortOrder' => 2, 'isPublished' => 1],
            ['section' => 'core_team', 'fullName' => 'Ms. Kaela Marie N. Fortuno', 'rolePrimary' => 'Team Member', 'roleSecondary' => 'AI Expert', 'mentorCategory' => null, 'photoPath' => 'assets/img/team/Fortuno.png', 'isFeatured' => 0, 'sortOrder' => 3, 'isPublished' => 1],
            ['section' => 'core_team', 'fullName' => 'Eng. Wenceslao D. Gavino', 'rolePrimary' => 'Team Member', 'roleSecondary' => 'ITSO Manager', 'mentorCategory' => null, 'photoPath' => 'assets/img/team/Gavino.png', 'isFeatured' => 0, 'sortOrder' => 4, 'isPublished' => 1],

            // TBI Staff
            ['section' => 'tbi_staff', 'fullName' => 'Ms. Rachelle Ann A. Hernando', 'rolePrimary' => 'TBI Manager', 'roleSecondary' => 'Project Development Officer', 'mentorCategory' => null, 'photoPath' => 'assets/img/team/Hernando.png', 'isFeatured' => 1, 'sortOrder' => 1, 'isPublished' => 1],
            ['section' => 'tbi_staff', 'fullName' => 'Mr. Mark Andrian D. Pontanal', 'rolePrimary' => 'Innovation and Community Officer', 'roleSecondary' => 'Project Technical Assistant', 'mentorCategory' => null, 'photoPath' => 'assets/img/team/Pontanal.png', 'isFeatured' => 0, 'sortOrder' => 2, 'isPublished' => 1],
            ['section' => 'tbi_staff', 'fullName' => 'Mr. Vencel Angelo R. Sanglay', 'rolePrimary' => 'Creative Technologies and Digital Engagement Officer', 'roleSecondary' => 'Project Technical Assistant', 'mentorCategory' => null, 'photoPath' => 'assets/img/team/Sanglay.png', 'isFeatured' => 0, 'sortOrder' => 3, 'isPublished' => 1],

            // Interns
            ['section' => 'intern', 'fullName' => 'Duke Zairus Arnante', 'rolePrimary' => 'Intern', 'roleSecondary' => null, 'mentorCategory' => null, 'photoPath' => 'assets/img/team/interns/Arnante.webp', 'isFeatured' => 0, 'sortOrder' => 1, 'isPublished' => 1],
            ['section' => 'intern', 'fullName' => 'Jan Andrew R. Barte', 'rolePrimary' => 'Intern', 'roleSecondary' => null, 'mentorCategory' => null, 'photoPath' => 'assets/img/team/interns/Barte.webp', 'isFeatured' => 0, 'sortOrder' => 2, 'isPublished' => 1],
            ['section' => 'intern', 'fullName' => 'Liza Mae B. Cleofe', 'rolePrimary' => 'Intern', 'roleSecondary' => null, 'mentorCategory' => null, 'photoPath' => 'assets/img/team/interns/Cleofe.webp', 'isFeatured' => 0, 'sortOrder' => 3, 'isPublished' => 1],
            ['section' => 'intern', 'fullName' => 'Asi Neo Garcia', 'rolePrimary' => 'Intern', 'roleSecondary' => null, 'mentorCategory' => null, 'photoPath' => 'assets/img/team/interns/Garcia.webp', 'isFeatured' => 0, 'sortOrder' => 4, 'isPublished' => 1],
            ['section' => 'intern', 'fullName' => 'Lily Rose Julianes', 'rolePrimary' => 'Intern', 'roleSecondary' => null, 'mentorCategory' => null, 'photoPath' => 'assets/img/team/interns/Julianes.webp', 'isFeatured' => 0, 'sortOrder' => 5, 'isPublished' => 1],
            ['section' => 'intern', 'fullName' => 'Johnlerein B. Manaog', 'rolePrimary' => 'Intern', 'roleSecondary' => null, 'mentorCategory' => null, 'photoPath' => 'assets/img/team/interns/Manaog.webp', 'isFeatured' => 0, 'sortOrder' => 6, 'isPublished' => 1],

            // Mentors — Business
            ['section' => 'mentor', 'fullName' => 'Ms. Cherry Lyn M. Odsinada', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Business', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 1, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Dr. Niño Martin P. Obrero', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Business', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 2, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Ms. Anjelica N. Ampongan', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Business', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 3, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Dr. Crezel B. Obrero', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Business', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 4, 'isPublished' => 1],

            // Mentors — Artificial Intelligence
            ['section' => 'mentor', 'fullName' => 'Dr. Challiz D. Omorog', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Artificial Intelligence', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 1, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Ms. Rosel O. Onesa', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Artificial Intelligence', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 2, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Ms. Kaela Marie N. Fortuno', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Artificial Intelligence', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 3, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Mr. Joseph Jessie S. Oñate', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Artificial Intelligence', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 4, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Ms. Tiffany Lyn O. Pandes', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Artificial Intelligence', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 5, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Mr. Allan O. Ibo, Jr.', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Artificial Intelligence', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 6, 'isPublished' => 1],

            // Mentors — Engineering
            ['section' => 'mentor', 'fullName' => 'Dr. Harold Jan R. Terano', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Engineering', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 1, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Engr. Rizza T. Loquias', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Engineering', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 2, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Engr. Keith Marlon R. Tabal', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Engineering', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 3, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Engr. Jose Eduardo II B. Cerillo', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Engineering', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 4, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Engr. Roner P. Abanil', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Engineering', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 5, 'isPublished' => 1],

            // Mentors — Prototyping
            ['section' => 'mentor', 'fullName' => 'Dr. Lalaine M. Lastrollo', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Prototyping', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 1, 'isPublished' => 1],

            // Mentors — Financial Management
            ['section' => 'mentor', 'fullName' => 'Mr. Roque B. Cruz II', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Financial Management', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 1, 'isPublished' => 1],
            ['section' => 'mentor', 'fullName' => 'Ms. Christine Margoux M. Sirios', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Financial Management', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 2, 'isPublished' => 1],

            // Mentors — Value Chain
            ['section' => 'mentor', 'fullName' => 'Ms. Rosalie R. Axinto', 'rolePrimary' => null, 'roleSecondary' => null, 'mentorCategory' => 'Value Chain', 'photoPath' => null, 'isFeatured' => 0, 'sortOrder' => 1, 'isPublished' => 1],
        ];

        foreach ($rows as &$row) {
            $row['createdAt'] = $now;
            $row['updatedAt'] = $now;
        }
        unset($row);

        $this->db->table('organization_members')->insertBatch($rows);
    }

    public function down(): void
    {
        if ($this->db->tableExists('organization_members')) {
            $this->db->table('organization_members')->truncate();
        }
    }
}
