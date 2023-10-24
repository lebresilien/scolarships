<?php

namespace App\Repositories;

use App\Models\Academy;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class AcademyRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'slug',
        'name',
        'account_id',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Academy::class;
    }

}
