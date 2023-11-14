<?php

namespace App\Repositories;

use App\Models\{ Inscription, Student, Academy };
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

    public function currentYearInscription($sections, $currentAcademy, $type) {
        
        $students = collect([]);

        $sections->map(function ($section) use ($currentAcademy, $students, $type) { 

            $section->groups->map(function ($group) use ($currentAcademy, $students, $type) {

                $group->classrooms->map(function ($classroom) use ($currentAcademy, $students, $type) {

                    if($type === '1') {

                        $classroom->students()->wherePivot('academy_id', $currentAcademy->id)->get()->map(function ($student) use ($currentAcademy, $students) {

                            $classroom = $student->classrooms()->wherePivot('academy_id', $currentAcademy->id)->first();

                            $inscription = Inscription::find($classroom->pivot->id);
                    
                            $students->push([
                                'id' => $student->id,
                                'matricule' => $student->matricule,
                                'name' => $student->fname . ' ' . $student->lname,
                                'sexe' => $student->sexe,
                                'cname' => $classroom->name,
                                'amount' => $inscription->transactions()->sum('amount') . ' FCFA',
                                'academy_name' => $currentAcademy->name,
                                'policy_id' => $classroom->pivot->id,
                                'status' => $inscription->status,
                                'group' => [
                                    'value' => $classroom->id,
                                    'label' => $classroom->name
                                ]
                            ]);
        
                        });

                    } else {

                        //Student who are already register for academic year
                        $register_students = Inscription::where('academy_id', $currentAcademy->id)->get();

                        $classroom->students()->whereNotIn('students.id', $register_students->pluck('student_id'))->get()->map(function ($student) use ($students) {
                            $students->push([
                                'id' => $student->id,
                                'name' => $student->fname . ' ' . $student->lname,
                            ]);
                        });
                       
                    }

                });

            });

        }); 

        
        return $students;

    }

    public function editPolicy($policy_id, $input) {

        $policy = Inscription::find($policy_id);

        $policy->classroom_id = $input['classroom_id'];
        $policy->status = $input['status'];

        $policy->save();

    }

}
