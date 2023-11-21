<?php

namespace App\Repositories;

use App\Models\{Classroom,Transaction, Inscription};
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

    public function transactionListing($currentAcademy, $classroom_id, $amount) {
        
        $classroom = Classroom::find($classroom_id);

        $students = $classroom->students()->wherePivot('academy_id', $currentAcademy->id)->get()->map(function ($student) use ($amount, $currentAcademy) {

            $classroom = $student->classrooms()->wherePivot('academy_id', $currentAcademy->id)->first();

            $inscription = Inscription::find($classroom->pivot->id);

            if($inscription->transactions()->sum('amount') >= $amount) {
                return [
                    'name' => $student->fname . ' ' . $student->lname,
                    'amount' => $inscription->transactions()->sum('amount'),
                ];
            }

        });

        return $students;

    }

}
