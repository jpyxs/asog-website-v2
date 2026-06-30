<?php

namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table            = 'faqs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'createdAt';
    protected $updatedField     = 'updatedAt';

    protected $allowedFields = [
        'question',
        'answer',
        'sortOrder',
        'isPublished',
    ];

    protected $validationRules = [
        'question'    => 'required|max_length[255]',
        'answer'      => 'required|max_length[5000]',
        'sortOrder'   => 'permit_empty|integer',
        'isPublished' => 'required|in_list[0,1]',
    ];

    public function getPublished(): array
    {
        return $this->where('isPublished', 1)
            ->orderBy('sortOrder', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getAllOrdered(): array
    {
        return $this->orderBy('sortOrder', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getNextSortOrder(): int
    {
        $row = $this->selectMax('sortOrder')->first();

        return ((int) ($row['sortOrder'] ?? 0)) + 1;
    }

    public function normalizeOrder(): void
    {
        foreach ($this->getAllOrdered() as $index => $faq) {
            $expected = $index + 1;
            if ((int) $faq['sortOrder'] !== $expected) {
                $this->update((int) $faq['id'], ['sortOrder' => $expected]);
            }
        }
    }
}
