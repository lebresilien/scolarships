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

        foreach($sections as $section) {
            
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

    public function currentYearInscription($sections, $currentAcademy) {
        
        //return Student::find(57)->classrooms()->wherePivot('academy_id', 7)->get();
        
        $students = collect([]);

        $sections->map(function ($section) use ($currentAcademy, $students) { 

            $section->groups->map(function ($group) use ($currentAcademy, $students) {

                $group->classrooms->map(function ($classroom) use ($currentAcademy, $students) {

                    $classroom->students->map(function ($student) use ($currentAcademy, $students) {

                        $classroom = $student->classrooms()->wherePivot('academy_id', $currentAcademy->id)->first();

                        $inscription = Inscription::find($classroom->pivot->id);
                        
                        $students->push([
                            'matricule' => $student->matricule,
                            'name' => $student->fname . ' ' . $student->lname,
                            'sexe' => $student->sexe,
                            'cname' => $classroom->name,
                            'amount' => $inscription->transactions()->sum('amount'),
                            'academy_name' => $currentAcademy->name,
                            'policy_id' => $classroom->pivot->id,
                            'status' => $inscription->status,
                            'group' => [
                                'value' => $classroom->id,
                                'label' => $classroom->name
                            ]
                        ]);

                    });

                });

            });

        }); 

        return $students;

    }

}
