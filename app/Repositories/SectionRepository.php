<?php

namespace App\Repositories;

use App\Models\Section;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class SectionRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'slug',
        'name',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Section::class;
    }

    public function list() {
       return Section::where('account_id', Auth::user()->accounts[0]->id)->get();
    }

}
