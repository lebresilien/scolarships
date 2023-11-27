<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\PolicyRepository;
use App\Traits\ApiResponser;
use App\Services\Service;
use App\Repositories\StudentRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\AbsentRepository;

class PolicyController extends Controller
{

    use ApiResponser;
    /** @var PolicyRepository */
    private $policyRepository;
    private $service;
    private $studentRepository;
    private $transactionRepository;
    private $absentRepository;

    public function __construct(
        TransactionRepository $transactionRepository, 
        Service $service, 
        StudentRepository $studentRepository, 
        PolicyRepository $policyRepository,
        AbsentRepository $absentRepository
    )
    {
        $this->policyRepository = $policyRepository;
        $this->service = $service;
        $this->studentRepository = $studentRepository;
        $this->absentRepository = $absentRepository;
        $this->transactionRepository = $transactionRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'student_id' => ['required', 'exists:students,id'],
            'amount' =>  ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/']
        ]);

         //check if active academy year exists
        if(!$this->service->currentAcademy($request)) return response()->json([
            "errors" => [
                "message" => "Aucune année academique active."
            ]
        ], 422);

        $input = $request->all();

        $policy = $this->policyRepository->all([
            'student_id' => $input['student_id'],
            'academy_id' => $this->service->currentAcademy($request)->id
        ])->first();
        
        //Check duplicate registration
        if($policy) return response()->json([
            "errors" => [
                "message" => "Cet Apprenant est deja inscrit pour l'année scolaire en cours"
            ]
        ], 422);

        $student = $this->studentRepository->find($input['student_id']);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $policy = $this->policyRepository->find($id);

        if(!$policy) return response()->json([
            "errors" => [
                "message" => "Aucun contrat trouvé"
            ]
        ], 400);

        $input = $request->all();

        $this->policyRepository->update($input, $id);

        return response()->noContent();
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

    public function absents($policy_id) {

        $absents = $this->absentRepository->all(['inscription_id' => $policy_id]);

        $policy = $this->policyRepository->find($policy_id);

        return [
            "state" => $absents,
            "surname" => $policy ? $policy->student->fname . ' ' . $policy->student->lname : ''
        ];
    }
}
