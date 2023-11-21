<?php

namespace App\Repositories;

use App\Models\{Absent};
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class AbsentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'inscription_id',
        'hour',
        'date',
        'staus'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Absent::class;
    }

}
