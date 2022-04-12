<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Student, Inscription, Transaction };
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    private $service;
    
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = array();

        foreach($request->user()->accounts[0]->sections  as $section) {
            
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
       
        //return response()->json($data);
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'lname' => ['required', 'string', 'max:255'],
            'fname' => ['nullable', 'string', 'max:255'],
            'sexe' => ['required', 'string'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'fphone' => ['nullable', 'string', 'max:255'],
            'mphone' => ['required', 'string', 'max:255'],
            'born_at' => ['required', 'date', 'date_format:Y-m-d'],
            'born_place' => ['required', 'string', 'max:255'],
            'allergy' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'quarter' => ['required', 'string', 'max:255'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'amount' =>  ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'type' => ['required', 'string']

        ]);

        DB::beginTransaction();

        try {

            $student;

            if($request->type == "new") {

                $register_number = null; 
            
                do {

                    $register_number = $this->service->generate();

                    $founder_student = Student::where('matricule', $register_number)->exists();
                
                } while($founder_student);

                $find_student = Student::where([

                    [ "lname", $request->lname ],
                    [ "fname", $request->fname ],
                    [ "mother_name", $request->mother_name ],
                    [ "born_at", $request->born_at ]

                ])->first();

                if($find_student) return response()->json([
                    "errors" => [
                        "message" => "Cet eleve a deja un compte"
                    ]
                ], 422);
                
                $student = Student::create([

                    "lname" => $request->lname,
                    "fname" => $request->fname,
                    "matricule" => $register_number,
                    "slug" => Str::slug($request->fname.' '.$request->lname.' '.$register_number, '-'),
                    "sexe" => $request->sexe,
                    "father_name" => $request->father_name,
                    "mother_name" => $request->mother_name,
                    "fphone" => $request->fphone,
                    "mphone" => $request->mphone,
                    "born_at" => $request->born_at,
                    "born_place" => $request->born_place,
                    "allergy" => $request->allergy,
                    "description" => $request->description,
                    "quarter" => $request->quarter,

                ]);  

            } else {

                $student = Student::where('matricule', $request->matricule)->first();

                if(!$student) return response()->json([
                    "errors" => [
                        "message" => "Eleve non trouvé"
                    ]
                ], 422);

            }

            //check if active academy year exists
            if(!$this->service->currentAcademy($request)) return response()->json([
                "errors" => [
                    "message" => "Aucune année academique active."
                ]
            ], 422);

            // check same school year registration duplication  
            $founder_registration = Inscription::where([

                ["academy_id", $this->service->currentAcademy($request)->id],
                ["student_id", $student->id]

            ])->first();
            
            if($founder_registration) return response()->json([
                "errors" => [
                    "message" => "Cet eleve est deja inscris pour l'année scolaire en cours"
                ]
            ], 422);
            
            // insert data in inscription  table
            $inscription = Inscription::create([
                "classroom_id" => $request->classroom_id,
                "academy_id" => $this->service->currentAcademy($request)->id,
                "student_id" => $student->id
            ]);

            // insert data in transaction table
            Transaction::create([
                "inscription_id" => $inscription->id,
                "amount" => $request->amount,
                "name" => "inscription"
            ]);

            DB::commit();

            return response()->noContent();

        } catch(\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $student = Student::where('slug', $slug)->with(['classrooms' => function($req) {
              $req->orderBy('id', 'desc')->first();
        }])->first();

        return response()->json([
            "student" => $student, 
            "classrooms" => $this->service->classrooms($request)
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {

        $request->validate([
            'lname' => ['required', 'string', 'max:255'],
            'fname' => ['nullable', 'string', 'max:255'],
            'sexe' => ['required', 'string'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'fphone' => ['nullable', 'string', 'max:255'],
            'mphone' => ['required', 'string', 'max:255'],
            'born_at' => ['required', 'date', 'date_format:Y-m-d'],
            'born_place' => ['required', 'string', 'max:255'],
            'allergy' => ['nullable', 'string'],
            'quarter' => ['required', 'string', 'max:255'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
        ]);

        $student = Student::where('slug', $slug)->first();

        if(!$student) return response()->json([
            "errors" => [
                "message" => "Aucun eleve trouvé"
            ]
        ], 422);

        DB::beginTransaction();

        try {

            $student->update([
                "lname" => $request->lname,
                "fname" => $request->fname,
                //"slug" => Str::slug($request->fname.' '.$request->lname.' '.$student->matricule, '-'),
                "sexe" => $request->sexe,
                "allergy" => $request->allergy,
                "quarter" => $request->quarter,
                "mother_name" => $request->mother_name,
                "mphone" => $request->mphone,
                "father_name" => $request->father_name,
                "fphone" => $request->fphone,
                "born_at" => $request->born_at,
                "born_place" => $request->born_place,
            ]);

            //check if active academy year exists
            if(!$this->service->currentAcademy($request)) return response()->json([
                "errors" => [
                    "message" => "Aucune année academique active."
                ]
            ], 422);

            //find registration current academic year  
            $founder_registration = Inscription::where([

                ["academy_id", $this->service->currentAcademy($request)->id],
                ["student_id", $student->id]

            ])->first();

            $founder_registration->update(["classroom_id" => $request->classroom_id]);

            DB::commit();

            return response()->noContent();

        } catch(\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
