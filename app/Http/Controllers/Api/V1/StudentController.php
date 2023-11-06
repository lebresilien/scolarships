<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Student, Inscription, Transaction };
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Repositories\StudentRepository;
use App\Repositories\TransactionRepository;
use Earmark;
use App\Traits\ApiResponser;

class StudentController extends Controller
{
    use ApiResponser;
    /** @var StudentRepository */
    private $studentRepository;
    private $service;
    private $transactionRepository;
    
    public function __construct(Service $service, StudentRepository $studentRepository, TransactionRepository $transactionRepository)
    {
        $this->service = $service;
        $this->studentRepository = $studentRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        $students = $this->studentRepository->list($request->user()->accounts[0]->sections);
        $classrooms = collect([]);

        foreach($this->service->classrooms($request) as $classroom) {
            $classrooms->push([
                'value' => $classroom['id'],
                'label' => $classroom['name']
            ]);
        }

        return [
            'state' => $students,
            'additional' => $classrooms
        ];
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
        ]);

        $input = $request->all();

        $find_student = $this->studentRepository->all([
            'lname' =>  $input['lname'],
            'fname' => $input['fname'],
            'sexe' => $input['sexe'],
            'born_at' => $request['born_at']
        ])->first();

        if($find_student) return response()->json([
            "errors" => [
                "message" => "Cet eleve a deja un compte"
            ]
        ], 400);

        $earmark = new Earmark(now()->format('Y'). $request->user()->accounts[0]->matricule, null, 3, 00, null);
        $input['matricule'] = $earmark->get();
        $input['slug'] = Str::slug($input['fname'].' '.$input['lname'], '-');
        
        $student = $this->studentRepository->create($input);  

        //check if active academy year exists
        if(!$this->service->currentAcademy($request)) return response()->json([
            "errors" => [
                "message" => "Aucune année academique active."
            ]
        ], 422);

        $student->classrooms()->attach($input['classroom_id'], ["academy_id" => $this->service->currentAcademy($request)->id]);
        $policy = $this->studentRepository->getPolicy($student->id, $input['classroom_id']);

        //Insert data in transaction table
        $this->transactionRepository->create([
            "inscription_id" => $policy->id,
            "amount" => $input['amount'],
            "name" => "inscription"
        ]);

        return $this->success($student, 'Ajout');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $student = $this->studentRepository->find($id);

        if(!$student) return response()->json([
            "errors" => [
                "message" => "Aucun apprénant trouvé"
            ]
        ], 400);

        return $this->success($student, 'Details');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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

        $student = $this->studentRepository->find($id);

        if(!$student) return response()->json([
            "errors" => [
                "message" => "Aucun eleve trouvé"
            ]
        ], 400);

        DB::beginTransaction();

        try {

            $$this->studentRepository->update([
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
            ], $id);

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
    public function destroy($ids)
    {
        $slugs = explode(';', $ids);

        foreach($slugs as $id) {

            $student = $this->studentRepository->find($id);

            if(!$student) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);

            if($student->notes->count() > 0) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);
        }

        foreach($slugs as $id) {
            $this->studentRepository->delete($id);
        }

        return response()->noContent();
    }

    //student all transactions  and extensions
    public function details(Request $request, $id) {

        //current year transactions
        if(!$this->service->currentAcademy($request)) {
            $foundCurrentYearTransactions = null;
            $foundCurrentYearExtensions = null;
        }else {

            $foundStudent = $this->studentRepository->find($id);
            
            if(!$foundStudent) return response()->json([
                "errors" => [
                    "message" => "Cet apprenant n'existe pas."
                ]
            ], 400);

            //current year transactions
            $foundCurrentYearTransactions = Inscription::where([
                ['academy_id', $this->service->currentAcademy($request)->id],
                ['student_id', $foundStudent->id]
            ])->with('transactions')->first();
            if($foundCurrentYearTransactions) $foundCurrentYearTransactions = $foundCurrentYearTransactions['transactions'];

            //others years transactions
            $foundOtherYearTransactions = Inscription::where([
                ['academy_id', '<>', $this->service->currentAcademy($request)->id],
                ['student_id', $foundStudent->id]
            ])->with('transactions')->first();
            if($foundOtherYearTransactions) $foundOtherYearTransactions = $foundOtherYearTransactions['transactions'];

            //current year extensions
            $foundCurrentYearExtensions = Inscription::where([
                ['academy_id', $this->service->currentAcademy($request)->id],
                ['student_id', $foundStudent->id]
            ])->with('extensions')->first();
            if($foundCurrentYearExtensions) $foundCurrentYearExtensions = $foundCurrentYearExtensions['extensions'];

             //others years extensions
             $foundOtherYearExtensions = Inscription::where([
                ['academy_id', '<>', $this->service->currentAcademy($request)->id],
                ['student_id', $foundStudent->id]
            ])->with('extensions')->first();
            if($foundOtherYearExtensions) $foundOtherYearExtensions = $foundOtherYearExtensions['extensions'];

            return response()->json([
                "current_year_extensions" => $foundCurrentYearExtensions,
                "other_year_extensions" => $foundOtherYearExtensions,
                "current_year_transactions" => $foundCurrentYearTransactions,
                "other_year_transactions" => $foundOtherYearTransactions,
            ]);
        }
    }

    public function fees(Request $request, $classroom_id, $amount) {

        //check if active academy year exists
        $currentAcademy = $this->service->currentAcademy($request);
        if(!$currentAcademy) return response()->json([
            "errors" => [
                "message" => "Aucune année academique active."
            ]
        ], 422);

        return $data = Inscription::where('academy_id', $currentAcademy->id)->get()
               ->map(function($item) {
                    return Transaction::where('inscription_id', $item->id)
                            ->join('inscriptions', 'inscriptions.id', '=', 'transactions.inscription_id')
                            ->join('students', 'students.id', '=', 'inscriptions.student_id')
                            ->selectRaw('students.matricule, students.lname, students.fname, sum(amount) as total_amount')
                            ->where('total_amount', '<=', $amount)
                            ->groupBy('transactions.inscription_id')
                            ->get()[0];         
               });
        
    }
}
