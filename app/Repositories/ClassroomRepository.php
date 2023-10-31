<?php

namespace App\Repositories;

use App\Models\Classroom;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class ClassroomRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'slug',
        'name'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Classroom::class;
    }

}
