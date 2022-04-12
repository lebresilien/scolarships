<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Service;
use App\Models\{ Inscription, Transaction };

class TransactionController extends Controller
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
            'student_id' =>  ['required', 'exists:students,id']
        ]);

        //get current academy year and found if student has registered
        $current_academy = $this->service->currentAcademy($request);
        
        if(!$current_academy) return response()->json([
            "errors" => [
                "message" => "Aucune annee academique active."
            ]
        ], 422);

        $registration = Inscription::where([
            ["academy_id", $current_academy->id],
            ["student_id", $request->student_id]
        ])->first();

        if(!$registration) return response()->json([
            "errors" => [
                "message" => "Cet eleve n'est pas inscrit pour l'annee en cours."
            ]
        ], 422);

        Transaction::create([
            "name" => $request->name,
            "amount" => $request->amount,
            "inscription_id" => $registration->id
        ]);

        return response()->noContent();

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
    public function destroy($id)
    {
        //
    }
}
