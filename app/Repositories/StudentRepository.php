<?php

namespace App\Repositories;

use App\Models\{ Inscription, Student };
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class StudentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'matricule',
        'fname',
        'lname',
        'sexe',
        'quarter',
        'born_at',
        "mother_name"
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Student::class;
    }

    public function getPolicy($student_id, $classroom_id) {
        return Inscription::where('student_id', $student_id)->where('classroom_id', $classroom_id)->first();
    }

    public function list($sections) {

        $data = array();

        foreach($sections  as $section) {
            
            foreach($section->groups as $group) {
                                 
                foreach($group->classrooms as $classroom) {

                    if(count($classroom->students) > 0 ) {

                        foreach($classroom->students as $student) {
                            
                            array_push($data, $student);
                            
                        }
                        
                    }
                     

                }

            }

        }
        
        $collection = collect($data)->unique('matricule');
        
        return $collection->values()->all();
    }

}
