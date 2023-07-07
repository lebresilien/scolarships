<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\{ Building, Account };
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BuildingController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buildings = Building::where('account_id', Auth::user()->accounts[0]->id)->get();
        return $buildings;
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
            'name' => ['required', 'string', 'max:255', 'unique:buildings'],
        ]);

        $building = Building::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'description' => $request->description,
            'account_id' => Auth::user()->accounts[0]->id
        ]);

        //return $this->success($section);
        return response()->noContent();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $building = $this->verify($slug);

        if(!$building)  return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Batiment non trouvé"
            ]
        ], 400);

        $collection = collect([]);

        foreach($building->classrooms as $classroom) {
            $collection->push([
                'name' => $classroom->name,
                'description' => $classroom->description
            ]);
        }

        return $this->success($collection);
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
        $building = $this->verify($slug);

        if(!$building)  return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Batiment non trouvé"
            ]
        ], 400);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:buildings,name,'.$building->id]
        ]);
        
        $input = $request->all();
       
        $input['slug'] = $building->slug;

        $building->update($input);

        return $this->success($building);
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

    private function verify($slug) {

        $building = Building::where('slug', $slug)->first();

        return $building;

    }
}
