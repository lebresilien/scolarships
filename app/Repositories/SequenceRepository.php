<?php

namespace App\Repositories;

use App\Models\Sequence;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class SequenceRepository extends BaseRepository
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
        return Sequence::class;
    }


}
