<?php

namespace App\Repositories;

use App\Models\{ Inscription };
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class PolicyRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'classroom_id',
        'student_id',
        'status',
        'academy_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Inscription::class;
    }

}
