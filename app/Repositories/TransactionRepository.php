<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class TransactionRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'inscription_id',
        'amount',
        'lname'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Transaction::class;
    }

    public function history($policy_id) {
        
    }

}
