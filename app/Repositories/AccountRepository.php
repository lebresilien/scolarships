<?php

namespace App\Repositories;

use App\Models\Account;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class AccountRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'slug',
        'name',
        'user_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Account::class;
    }

}
