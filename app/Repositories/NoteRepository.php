<?php

namespace App\Repositories;

use App\Models\Note;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class NoteRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'sequence_id',
        'course_id',
        'student_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Note::class;
    }

}
