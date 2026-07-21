<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFaqsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'question' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'answer' => [
                'type' => 'TEXT',
            ],
            'sortOrder' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'isPublished' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('sortOrder');
        $this->forge->addKey('isPublished');
        $this->forge->createTable('faqs', true);

        $now = date('Y-m-d H:i:s');
        $items = [
            ['What is the application timeline?', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'],
            ['Who can apply to the program?', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam.'],
            ['What documents are required?', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta.'],
            ['How is the application reviewed?', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas tincidunt lacus at velit. Vivamus luctus egestas leo.'],
            ['Can solo founders apply?', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sodales ligula in libero. Sed dignissim lacinia nunc.'],
            ['Is there a fee to join the program?', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum.'],
            ['How long does the incubation program last?', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mauris. Fusce nec tellus sed augue semper porta.'],
            ['What happens after my startup is accepted?', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris massa. Vestibulum lacinia arcu eget nulla.'],
        ];

        $rows = [];
        foreach ($items as $index => [$question, $answer]) {
            $rows[] = [
                'question'    => $question,
                'answer'      => $answer,
                'sortOrder'   => $index + 1,
                'isPublished' => 1,
                'createdAt'   => $now,
                'updatedAt'   => $now,
            ];
        }

        $this->db->table('faqs')->insertBatch($rows);
    }

    public function down(): void
    {
        $this->forge->dropTable('faqs', true);
    }
}
