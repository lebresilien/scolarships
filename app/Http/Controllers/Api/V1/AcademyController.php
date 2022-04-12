<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Academy, Classroom };
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;


class AcademyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
  
        $academies = Academy::where("account_id", $request->user()->accounts[0]->id)
                     ->get();
       
        return response()->json($academies);
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
            'name' => ['required', 'string', 'max:255']
        ]);
        
        $founderAcademy = Academy::where([
            
            ["name", $request->name],
            ["account_id", $request->user()->accounts[0]->id]

        ])->first();

        if($founderAcademy) return response()->json([
            "message" =>  "Academy name already exists.",
            "errors" => [
                "message" => "Academy name already exists"
            ]
        ], 422);

        $founder_active_academy = Academy::where([

           ["account_id", $request->user()->accounts[0]->id],
           ["status", true]

        ])->first();

        if($founder_active_academy)  return response()->json([
            "message" =>  "An active academy already exists.",
            "errors" => [
                "message" => "An active academy already exists."
            ]
        ], 422);

        $academy = Academy::create([
            "name" => $request->name,
            "slug" => Str::slug($request->name, '-'),
            "account_id" => $request->user()->accounts[0]->id
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
        $academy = Academy::findOrFail($id);
        $academy->status = false;
        $academy->save();

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

    public function switchStatus($id) {

        $academy = Academy::find($id);
        $academy->status = false;
        $academy->save();
    }
}
