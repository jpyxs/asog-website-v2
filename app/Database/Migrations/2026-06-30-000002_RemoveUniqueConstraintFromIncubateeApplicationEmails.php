<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUniqueConstraintFromIncubateeApplicationEmails extends Migration
{
    private function uniqueIndexName(string $table, string $column): ?string
    {
        $result = $this->db->query(
            'SHOW INDEX FROM `' . $table . '` WHERE Column_name = ' . $this->db->escape($column) . ' AND Non_unique = 0'
        );

        if ($result === false || $result->getNumRows() === 0) {
            return null;
        }

        $row = $result->getRowArray();

        return is_array($row) ? (string) ($row['Key_name'] ?? '') : null;
    }

    public function up(): void
    {
        $indexName = $this->uniqueIndexName('incubatee_applications', 'applicantEmail');

        if ($indexName !== null && $indexName !== '') {
            $this->db->query('ALTER TABLE `incubatee_applications` DROP INDEX `' . $indexName . '`');
        }
    }

    public function down(): void
    {
        $indexName = $this->uniqueIndexName('incubatee_applications', 'applicantEmail');

        if ($indexName === null || $indexName === '') {
            $this->db->query(
                'ALTER TABLE `incubatee_applications` ADD UNIQUE KEY `incubatee_applications_applicantEmail_unique` (`applicantEmail`)'
            );
        }
    }
}
