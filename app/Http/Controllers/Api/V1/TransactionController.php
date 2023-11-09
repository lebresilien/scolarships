<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Service;
use App\Models\{ Inscription, Transaction };
use App\Repositories\TransactionRepository;
use App\Traits\ApiResponser;

class TransactionController extends Controller
{
    use ApiResponser;
    /** @var TransactionRepository */
    private $transactionRepository;
    private $service;
    
    public function __construct(Service $service, TransactionRepository $transactionRepository)
    {
        $this->service = $service;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
            'amount' =>  ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'name' => ['required', 'string', 'max:255'],
            'inscription_id' =>  ['required', 'exists:inscriptions,id']
        ]);

        //get current academy year and found if student has registered
        $current_academy = $this->service->currentAcademy($request);
        
        if(!$current_academy) return response()->json([
            "errors" => [
                "message" => "Aucune annee academique active."
            ]
        ], 422);

        $input = $request->all();

        /*$registration = $this->transactionRepository->all([
            "academy_id" => $current_academy->id,
            "student_id" => $input['student_id']
        ])->first();

        if(!$registration) return response()->json([
            "errors" => [
                "message" => "Cet eleve n'est pas inscrit pour l'annee en cours."
            ]
        ], 422);*/

        $trx = $this->transactionRepository->create($input);

        $transaction = [
            "id" => $trx->id,
            "title" => $trx->name,
            "amount" => $trx->amount,
            "created_at" => $trx->created_at->format('Y-m-d'),
            'name' => $trx->inscription->student->fname . ' ' . $trx->inscription->student->lname 
        ];

        return $this->success($transaction, 'Ajout');

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
        //
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

            $trx = $this->transactionRepository->find($id);

            if(!$trx) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);

        }

        foreach($slugs as $id) {
            $this->transactionRepository->delete($id);
        }

        return response()->noContent();
    }

    public function history($policy_id) {
        
        $transactions = $this->transactionRepository->all(['inscription_id' => $policy_id]);

        $data = $transactions->map(function($transaction) {
            return [
                "id" => $transaction->id,
                "title" => $transaction->name,
                "amount" => $transaction->amount,
                "created_at" => $transaction->created_at->format('Y-m-d'),
                'name' => $transaction->inscription->student->fname . ' ' . $transaction->inscription->student->lname 
            ];
        });
        
        return [
            'state' => $data,
        ];
    }
 }
