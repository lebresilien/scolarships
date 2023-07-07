<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $units = $request->user()->accounts[0]->units;
        return $units;
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
            'name' => ['required', 'string', 'max:255'],
            //'account_id'    => ['required', 'exists:accounts,id']
        ]);

        $founderUnit = Unit::where('name', $request->name)
                           ->where('account_id', $request->user()->accounts[0]->id)
                           ->first();

        if($founderUnit) return response()->json([
            "errors" => [
                "message" => "Cet unité d'enseignement existe deja."
            ]
        ],422);

        Unit::create([
            "name" => $request->name,
            "slug" => Str::slug($request->name),
            "account_id" => $request->user()->accounts[0]->id,
            "description" => $request->description,
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
        $unit = Unit::find($id);
        $this->authorize('view', $unit);

        if(!$unit) return response()->json([
            "errors" => [
                "message" => "Cette unite d'enseignement n\'existe pas."
            ]
        ], 422);

        return $unit;
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
        $unit = Unit::find($id);

        if(!$unit) return response()->json([
            "errors" => [
                "message" => "Aucune unite d\'enseigment touvée."
            ]
        ]);

        $unit->delete();
        return response()->noContent();
    }
}
